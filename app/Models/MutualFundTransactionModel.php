<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class MutualFundTransactionModel extends Model
{
    protected $table            = 'mutual_fund_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'mutual_fund_id',
        'transaction_type',
        'transaction_date',
        'units',
        'remaining_units',
        'nav',
        'amount',
        'charges',
        'net_amount',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Record a Mutual Fund BUY transaction (SIP or Lumpsum).
     */
    public function recordBuy(
        int $userId,
        int $fundId,
        string $type,
        string $date,
        float $units,
        float $nav,
        float $amount,
        float $charges = 0.0,
        ?string $notes = null
    ): int {
        $netAmount = $amount + $charges;

        $data = [
            'user_id'          => $userId,
            'mutual_fund_id'   => $fundId,
            'transaction_type' => in_array($type, ['BUY_SIP', 'BUY_LUMPSUM']) ? $type : 'BUY_LUMPSUM',
            'transaction_date' => $date,
            'units'            => $units,
            'remaining_units'  => $units, // Initial full lot unredeemed
            'nav'              => $nav,
            'amount'           => $amount,
            'charges'          => $charges,
            'net_amount'       => $netAmount,
            'notes'            => $notes,
        ];

        return $this->insert($data);
    }

    /**
     * Record a Mutual Fund REDEEM / SELL transaction using First-In, First-Out (FIFO).
     */
    public function recordRedeemFIFO(
        int $userId,
        int $fundId,
        string $date,
        float $redeemUnits,
        float $redeemNav,
        float $charges = 0.0,
        ?string $notes = null
    ): array {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Fetch unredeemed BUY lots in chronological order (FIFO)
        $buyLots = $this->where('user_id', $userId)
                        ->where('mutual_fund_id', $fundId)
                        ->whereIn('transaction_type', ['BUY_SIP', 'BUY_LUMPSUM'])
                        ->where('remaining_units >', 0.0001)
                        ->orderBy('transaction_date', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->findAll();

        $availableUnits = (float) array_sum(array_column($buyLots, 'remaining_units'));

        if ($redeemUnits > ($availableUnits + 0.0001)) {
            throw new Exception("Insufficient units to redeem. Available: " . number_format($availableUnits, 4) . ", Attempted: " . number_format($redeemUnits, 4));
        }

        $grossAmount = $redeemUnits * $redeemNav;
        $netProceeds = $grossAmount - $charges;

        // 2. Insert the REDEEM transaction record
        $redeemData = [
            'user_id'          => $userId,
            'mutual_fund_id'   => $fundId,
            'transaction_type' => 'REDEEM',
            'transaction_date' => $date,
            'units'            => $redeemUnits,
            'remaining_units'  => 0.0000,
            'nav'              => $redeemNav,
            'amount'           => $grossAmount,
            'charges'          => $charges,
            'net_amount'       => $netProceeds,
            'notes'            => $notes,
        ];

        $redeemId = $this->insert($redeemData);

        // 3. Match against BUY lots in FIFO sequence
        $neededUnits = $redeemUnits;
        $redeemDateTimestamp = strtotime($date);
        $matchedLots = [];

        foreach ($buyLots as $lot) {
            if ($neededUnits <= 0.0001) {
                break;
            }

            $lotRemaining = (float) $lot['remaining_units'];
            $matchUnits = min($lotRemaining, $neededUnits);

            // Holding period in days
            $buyDateTimestamp = strtotime($lot['transaction_date']);
            $holdingDays = max(0, (int) round(($redeemDateTimestamp - $buyDateTimestamp) / 86400));

            // Standard Indian Tax rule: <= 365 days = STCG, > 365 days = LTCG
            $gainType = $holdingDays <= 365 ? 'STCG' : 'LTCG';

            // Pro-rated exit load / charges
            $lotCharges = $redeemUnits > 0 ? ($charges * ($matchUnits / $redeemUnits)) : 0.0;

            // Realized Gain = (Units × Redeem NAV) - (Units × Purchase NAV) - Charges
            $realizedGain = ($matchUnits * $redeemNav) - ($matchUnits * (float) $lot['nav']) - $lotCharges;

            // Log FIFO Capital Gains audit record
            $db->table('mutual_fund_capital_gains')->insert([
                'user_id'               => $userId,
                'mutual_fund_id'        => $fundId,
                'redeem_transaction_id' => $redeemId,
                'buy_transaction_id'    => $lot['id'],
                'units_matched'         => $matchUnits,
                'buy_date'              => $lot['transaction_date'],
                'buy_nav'               => $lot['nav'],
                'redeem_date'           => $date,
                'redeem_nav'            => $redeemNav,
                'holding_days'          => $holdingDays,
                'gain_type'             => $gainType,
                'realized_gain'         => $realizedGain,
                'created_at'            => date('Y-m-d H:i:s'),
            ]);

            // Decrement remaining_units on BUY lot
            $newRemaining = max(0.0, $lotRemaining - $matchUnits);
            $this->update($lot['id'], ['remaining_units' => $newRemaining]);

            $matchedLots[] = [
                'buy_id'        => $lot['id'],
                'matched_units' => $matchUnits,
                'buy_date'      => $lot['transaction_date'],
                'buy_nav'       => (float) $lot['nav'],
                'holding_days'  => $holdingDays,
                'gain_type'     => $gainType,
                'realized_gain' => $realizedGain,
            ];

            $neededUnits -= $matchUnits;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception('Failed to record mutual fund redemption and FIFO matching.');
        }

        return [
            'redeem_id'    => $redeemId,
            'matched_lots' => $matchedLots,
        ];
    }

    /**
     * Get transaction history with joined mutual fund metadata.
     */
    public function getTransactionsWithFund(int $userId, ?int $fundId = null): array
    {
        $builder = $this->select('mutual_fund_transactions.*, mutual_funds.amfi_code, mutual_funds.scheme_name, mutual_funds.folio_number, mutual_funds.category')
                        ->join('mutual_funds', 'mutual_funds.id = mutual_fund_transactions.mutual_fund_id')
                        ->where('mutual_fund_transactions.user_id', $userId);

        if ($fundId !== null) {
            $builder->where('mutual_fund_transactions.mutual_fund_id', $fundId);
        }

        return $builder->orderBy('mutual_fund_transactions.transaction_date', 'DESC')
                       ->orderBy('mutual_fund_transactions.id', 'DESC')
                       ->findAll();
    }

    /**
     * Rebuild FIFO lot allocation and capital gains records from scratch for a given Mutual Fund.
     * Called whenever a transaction is updated or deleted.
     */
    public function rebuildFifoLotsAndCapitalGains(int $userId, int $fundId): void
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Reset all BUY lots: remaining_units = units
        $db->table('mutual_fund_transactions')
           ->where('user_id', $userId)
           ->where('mutual_fund_id', $fundId)
           ->whereIn('transaction_type', ['BUY_SIP', 'BUY_LUMPSUM'])
           ->update(['remaining_units' => new \CodeIgniter\Database\RawSql('units')]);

        // 2. Remove all capital gains records for this Mutual Fund
        $db->table('mutual_fund_capital_gains')
           ->where('user_id', $userId)
           ->where('mutual_fund_id', $fundId)
           ->delete();

        // 3. Fetch all REDEEM transactions in chronological sequence
        $redeemTransactions = $db->table('mutual_fund_transactions')
           ->where('user_id', $userId)
           ->where('mutual_fund_id', $fundId)
           ->where('transaction_type', 'REDEEM')
           ->orderBy('transaction_date', 'ASC')
           ->orderBy('id', 'ASC')
           ->get()
           ->getResultArray();

        foreach ($redeemTransactions as $redeem) {
            $redeemId = (int) $redeem['id'];
            $neededUnits = (float) $redeem['units'];
            $redeemNav = (float) $redeem['nav'];
            $redeemDateTimestamp = strtotime($redeem['transaction_date']);
            $charges = (float) ($redeem['charges'] ?? 0);

            // Find available BUY lots
            $availableBuyLots = $this->where('user_id', $userId)
                                     ->where('mutual_fund_id', $fundId)
                                     ->whereIn('transaction_type', ['BUY_SIP', 'BUY_LUMPSUM'])
                                     ->where('remaining_units >', 0.0001)
                                     ->orderBy('transaction_date', 'ASC')
                                     ->orderBy('id', 'ASC')
                                     ->findAll();

            $totalAvailable = (float) array_sum(array_column($availableBuyLots, 'remaining_units'));
            if ($neededUnits > ($totalAvailable + 0.0001)) {
                throw new Exception("Inventory validation error: On {$redeem['transaction_date']}, you redeemed " . number_format($neededUnits, 4) . " units, but only " . number_format($totalAvailable, 4) . " units were available. Cannot save this change.");
            }

            foreach ($availableBuyLots as $lot) {
                if ($neededUnits <= 0.0001) break;

                $lotRemaining = (float) $lot['remaining_units'];
                $matchUnits = min($lotRemaining, $neededUnits);

                $buyDateTimestamp = strtotime($lot['transaction_date']);
                $holdingDays = max(0, (int) round(($redeemDateTimestamp - $buyDateTimestamp) / 86400));
                $gainType = $holdingDays <= 365 ? 'STCG' : 'LTCG';

                $lotCharges = $redeem['units'] > 0 ? ($charges * ($matchUnits / (float) $redeem['units'])) : 0.0;
                $realizedGain = ($matchUnits * $redeemNav) - ($matchUnits * (float) $lot['nav']) - $lotCharges;

                $db->table('mutual_fund_capital_gains')->insert([
                    'user_id'               => $userId,
                    'mutual_fund_id'        => $fundId,
                    'redeem_transaction_id' => $redeemId,
                    'buy_transaction_id'    => (int) $lot['id'],
                    'units_matched'         => $matchUnits,
                    'buy_date'              => $lot['transaction_date'],
                    'buy_nav'               => (float) $lot['nav'],
                    'redeem_date'           => $redeem['transaction_date'],
                    'redeem_nav'            => $redeemNav,
                    'holding_days'          => $holdingDays,
                    'gain_type'             => $gainType,
                    'realized_gain'         => $realizedGain,
                    'created_at'            => date('Y-m-d H:i:s'),
                ]);

                $newRemaining = max(0.0, $lotRemaining - $matchUnits);
                $this->update($lot['id'], ['remaining_units' => $newRemaining]);

                $neededUnits -= $matchUnits;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception("Failed to rebalance mutual fund FIFO lots.");
        }
    }
}

