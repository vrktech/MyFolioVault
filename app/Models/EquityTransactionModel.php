<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class EquityTransactionModel extends Model
{
    protected $table            = 'equity_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'equity_id',
        'transaction_type',
        'transaction_date',
        'quantity',
        'remaining_quantity',
        'price',
        'brokerage',
        'stt_taxes',
        'total_amount',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Record a BUY transaction. Initializes remaining_quantity for FIFO tracking.
     */
    public function recordBuy(
        int $userId,
        int $equityId,
        string $date,
        int $quantity,
        float $price,
        float $brokerage = 0.0,
        float $sttTaxes = 0.0,
        ?string $notes = null
    ): int {
        $totalAmount = ($quantity * $price) + $brokerage + $sttTaxes;

        $data = [
            'user_id'            => $userId,
            'equity_id'          => $equityId,
            'transaction_type'   => 'BUY',
            'transaction_date'   => $date,
            'quantity'           => $quantity,
            'remaining_quantity' => $quantity, // Initially entire lot is unsold
            'price'              => $price,
            'brokerage'          => $brokerage,
            'stt_taxes'          => $sttTaxes,
            'total_amount'       => $totalAmount,
            'notes'              => $notes,
        ];

        return $this->insert($data);
    }

    /**
     * Record a SELL transaction using the First-In, First-Out (FIFO) algorithm.
     * Matches against earliest unconsumed BUY lots, determines STCG vs LTCG,
     * and logs to equity_capital_gains.
     */
    public function recordSellFIFO(
        int $userId,
        int $equityId,
        string $date,
        int $sellQuantity,
        float $sellPrice,
        float $brokerage = 0.0,
        float $sttTaxes = 0.0,
        ?string $notes = null
    ): array {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Fetch unconsumed BUY lots sorted chronologically (FIFO order)
        $buyLots = $this->where('user_id', $userId)
                        ->where('equity_id', $equityId)
                        ->where('transaction_type', 'BUY')
                        ->where('remaining_quantity >', 0)
                        ->orderBy('transaction_date', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->findAll();

        $availableQty = array_sum(array_column($buyLots, 'remaining_quantity'));

        if ($sellQuantity > $availableQty) {
            throw new Exception("Insufficient shares to sell. Available: {$availableQty}, Attempted: {$sellQuantity}");
        }

        $totalProceeds = ($sellQuantity * $sellPrice) - $brokerage - $sttTaxes;

        // 2. Insert the SELL transaction record
        $sellData = [
            'user_id'            => $userId,
            'equity_id'          => $equityId,
            'transaction_type'   => 'SELL',
            'transaction_date'   => $date,
            'quantity'           => $sellQuantity,
            'remaining_quantity' => 0,
            'price'              => $sellPrice,
            'brokerage'          => $brokerage,
            'stt_taxes'          => $sttTaxes,
            'total_amount'       => $totalProceeds,
            'notes'              => $notes,
        ];

        $sellId = $this->insert($sellData);

        // 3. Match against BUY lots in FIFO sequence
        $neededQty = $sellQuantity;
        $sellDateTimestamp = strtotime($date);
        $totalCharges = $brokerage + $sttTaxes;
        $matchedLots = [];

        foreach ($buyLots as $lot) {
            if ($neededQty <= 0) {
                break;
            }

            $lotRemaining = (int) $lot['remaining_quantity'];
            $matchQty = min($lotRemaining, $neededQty);

            // Compute holding duration in days
            $buyDateTimestamp = strtotime($lot['transaction_date']);
            $holdingDays = max(0, (int) round(($sellDateTimestamp - $buyDateTimestamp) / 86400));

            // Indian Tax rule: <= 365 days = STCG, > 365 days = LTCG
            $gainType = $holdingDays <= 365 ? 'STCG' : 'LTCG';

            // Pro-rated selling charges allocated to this lot
            $lotCharges = $sellQuantity > 0 ? ($totalCharges * ($matchQty / $sellQuantity)) : 0.0;

            // Realized Gain = (Sale value - Purchase cost) - Allocated charges
            $realizedGain = ($matchQty * $sellPrice) - ($matchQty * (float) $lot['price']) - $lotCharges;

            // Log FIFO Capital Gains audit record
            $db->table('equity_capital_gains')->insert([
                'user_id'             => $userId,
                'equity_id'           => $equityId,
                'sell_transaction_id' => $sellId,
                'buy_transaction_id'  => $lot['id'],
                'quantity_matched'    => $matchQty,
                'buy_date'            => $lot['transaction_date'],
                'buy_price'           => $lot['price'],
                'sell_date'           => $date,
                'sell_price'          => $sellPrice,
                'holding_days'        => $holdingDays,
                'gain_type'           => $gainType,
                'realized_gain'       => $realizedGain,
                'created_at'          => date('Y-m-d H:i:s'),
            ]);

            // Update remaining quantity on the BUY lot
            $newRemaining = $lotRemaining - $matchQty;
            $this->update($lot['id'], ['remaining_quantity' => $newRemaining]);

            $matchedLots[] = [
                'buy_id'        => $lot['id'],
                'matched_qty'   => $matchQty,
                'buy_date'      => $lot['transaction_date'],
                'buy_price'     => (float) $lot['price'],
                'holding_days'  => $holdingDays,
                'gain_type'     => $gainType,
                'realized_gain' => $realizedGain,
            ];

            $neededQty -= $matchQty;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception('Failed to record sell transaction and FIFO matching.');
        }

        return [
            'sell_id'      => $sellId,
            'matched_lots' => $matchedLots,
        ];
    }

    /**
     * Rebuild FIFO lot allocation and capital gains records from scratch for a given equity.
     * Called whenever a transaction is updated or deleted.
     */
    public function rebuildFifoLotsAndCapitalGains(int $userId, int $equityId): void
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Reset all BUY lots: remaining_quantity = quantity
        $db->table('equity_transactions')
           ->where('user_id', $userId)
           ->where('equity_id', $equityId)
           ->where('transaction_type', 'BUY')
           ->update(['remaining_quantity' => new \CodeIgniter\Database\RawSql('quantity')]);

        // 2. Remove all capital gains records for this equity
        $db->table('equity_capital_gains')
           ->where('user_id', $userId)
           ->where('equity_id', $equityId)
           ->delete();

        // 3. Fetch all SELL transactions in chronological sequence
        $sellTransactions = $db->table('equity_transactions')
           ->where('user_id', $userId)
           ->where('equity_id', $equityId)
           ->where('transaction_type', 'SELL')
           ->orderBy('transaction_date', 'ASC')
           ->orderBy('id', 'ASC')
           ->get()
           ->getResultArray();

        foreach ($sellTransactions as $sell) {
            $sellId = (int) $sell['id'];
            $neededQty = (int) $sell['quantity'];
            $sellPrice = (float) $sell['price'];
            $sellDateTimestamp = strtotime($sell['transaction_date']);
            $brokerage = (float) ($sell['brokerage'] ?? 0);
            $stt = (float) ($sell['stt_taxes'] ?? 0);
            $totalCharges = $brokerage + $stt;

            // Find available BUY lots
            $availableBuyLots = $this->where('user_id', $userId)
                                     ->where('equity_id', $equityId)
                                     ->where('transaction_type', 'BUY')
                                     ->where('remaining_quantity >', 0)
                                     ->orderBy('transaction_date', 'ASC')
                                     ->orderBy('id', 'ASC')
                                     ->findAll();

            $totalAvailable = array_sum(array_column($availableBuyLots, 'remaining_quantity'));
            if ($totalAvailable < $neededQty) {
                throw new Exception("Inventory validation error: On {$sell['transaction_date']}, you sold {$sell['quantity']} shares, but only {$totalAvailable} shares were available. Cannot save this change.");
            }

            foreach ($availableBuyLots as $lot) {
                if ($neededQty <= 0) break;

                $lotRemaining = (int) $lot['remaining_quantity'];
                $matchQty = min($lotRemaining, $neededQty);

                $buyDateTimestamp = strtotime($lot['transaction_date']);
                $holdingDays = max(0, (int) round(($sellDateTimestamp - $buyDateTimestamp) / 86400));
                $gainType = $holdingDays <= 365 ? 'STCG' : 'LTCG';

                $propCharges = $sell['quantity'] > 0 ? ($totalCharges * ($matchQty / $sell['quantity'])) : 0.0;
                $costBasis = $matchQty * (float) $lot['price'];
                $netProceeds = ($matchQty * $sellPrice) - $propCharges;
                $realizedGain = $netProceeds - $costBasis;

                $db->table('equity_capital_gains')->insert([
                    'user_id'             => $userId,
                    'equity_id'           => $equityId,
                    'sell_transaction_id' => $sellId,
                    'buy_transaction_id'  => $lot['id'],
                    'quantity_matched'    => $matchQty,
                    'buy_date'            => $lot['transaction_date'],
                    'buy_price'           => $lot['price'],
                    'sell_date'           => $sell['transaction_date'],
                    'sell_price'          => $sellPrice,
                    'holding_days'        => $holdingDays,
                    'gain_type'           => $gainType,
                    'realized_gain'       => $realizedGain,
                    'created_at'          => date('Y-m-d H:i:s'),
                ]);

                $newRemaining = $lotRemaining - $matchQty;
                $this->update($lot['id'], ['remaining_quantity' => $newRemaining]);

                $neededQty -= $matchQty;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception("Failed to rebuild FIFO lots for equity.");
        }
    }

    /**
     * Get transaction history with joined stock metadata.
     */
    public function getTransactionsWithStock(int $userId, ?int $equityId = null): array
    {
        $builder = $this->select('equity_transactions.*, equities.symbol, equities.company_name, equities.exchange')
                        ->join('equities', 'equities.id = equity_transactions.equity_id')
                        ->where('equity_transactions.user_id', $userId);

        if ($equityId !== null) {
            $builder->where('equity_transactions.equity_id', $equityId);
        }

        return $builder->orderBy('equity_transactions.transaction_date', 'DESC')
                       ->orderBy('equity_transactions.id', 'DESC')
                       ->findAll();
    }
}
