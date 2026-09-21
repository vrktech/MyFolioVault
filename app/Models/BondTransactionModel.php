<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class BondTransactionModel extends Model
{
    protected $table            = 'bond_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'bond_id',
        'transaction_type',
        'transaction_date',
        'quantity',
        'remaining_quantity',
        'price',
        'brokerage_charges',
        'accrued_interest',
        'total_amount',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Record a Bond BUY transaction (Initial or Secondary Market).
     */
    public function recordBuy(
        int $userId,
        int $bondId,
        string $date,
        float $quantity,
        float $price,
        float $charges = 0.0,
        float $accruedInterest = 0.0,
        ?string $notes = null
    ): int {
        $totalAmount = ($quantity * $price) + $charges + $accruedInterest;

        $data = [
            'user_id'            => $userId,
            'bond_id'            => $bondId,
            'transaction_type'   => 'BUY',
            'transaction_date'   => $date,
            'quantity'           => $quantity,
            'remaining_quantity' => $quantity, // FIFO open lot
            'price'              => $price,
            'brokerage_charges'  => $charges,
            'accrued_interest'   => $accruedInterest,
            'total_amount'       => $totalAmount,
            'notes'              => $notes,
        ];

        return $this->insert($data);
    }

    /**
     * Record a secondary market SELL transaction with Chronological FIFO lot matching.
     */
    public function recordSellFIFO(
        int $userId,
        int $bondId,
        string $sellDate,
        float $sellQty,
        float $sellPrice,
        float $charges = 0.0,
        ?string $notes = null
    ): array {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Fetch available BUY lots in FIFO order (oldest first)
        $buyLots = $this->where('bond_id', $bondId)
                        ->where('user_id', $userId)
                        ->where('transaction_type', 'BUY')
                        ->where('remaining_quantity >', 0)
                        ->orderBy('transaction_date', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->findAll();

        $totalAvailable = array_sum(array_column($buyLots, 'remaining_quantity'));
        if ($sellQty > $totalAvailable + 0.0001) {
            throw new Exception("Insufficient bond quantity. Available: {$totalAvailable}, requested sell: {$sellQty}");
        }

        $grossProceeds = $sellQty * $sellPrice;
        $netProceeds   = max(0.0, $grossProceeds - $charges);

        // 2. Insert SELL transaction
        $sellTxId = $this->insert([
            'user_id'            => $userId,
            'bond_id'            => $bondId,
            'transaction_type'   => 'SELL',
            'transaction_date'   => $sellDate,
            'quantity'           => $sellQty,
            'remaining_quantity' => 0.0000,
            'price'              => $sellPrice,
            'brokerage_charges'  => $charges,
            'accrued_interest'   => 0.00,
            'total_amount'       => $netProceeds,
            'notes'              => $notes,
        ]);

        // 3. FIFO Matching
        $neededQty   = $sellQty;
        $matchedLots = [];

        foreach ($buyLots as $lot) {
            if ($neededQty <= 0.00001) {
                break;
            }

            $availableInLot = (float) $lot['remaining_quantity'];
            $matchQty       = min($neededQty, $availableInLot);

            // Update lot remaining_quantity
            $newRemaining = $availableInLot - $matchQty;
            $this->update($lot['id'], ['remaining_quantity' => $newRemaining]);

            // Calculate holding period & capital gain
            $buyDate     = $lot['transaction_date'];
            $holdingDays = (int) ceil((strtotime($sellDate) - strtotime($buyDate)) / 86400);
            $buyPrice    = (float) $lot['price'];

            // Listed bonds holding threshold: 365 days
            $gainType = ($holdingDays > 365) ? 'LTCG' : 'STCG';

            $proportionateCharges = $sellQty > 0 ? ($charges * ($matchQty / $sellQty)) : 0.0;
            $lotProceeds          = ($matchQty * $sellPrice) - $proportionateCharges;
            $costBasis            = $matchQty * $buyPrice;
            $realizedGain         = $lotProceeds - $costBasis;

            // Record capital gains audit row
            $db->table('bond_capital_gains')->insert([
                'user_id'             => $userId,
                'bond_id'             => $bondId,
                'exit_transaction_id' => $sellTxId,
                'buy_transaction_id'  => $lot['id'],
                'buy_date'            => $buyDate,
                'exit_date'           => $sellDate,
                'holding_days'        => $holdingDays,
                'quantity_matched'    => $matchQty,
                'buy_price'           => $buyPrice,
                'exit_price'          => $sellPrice,
                'realized_gain'       => $realizedGain,
                'gain_type'           => $gainType,
                'created_at'          => date('Y-m-d H:i:s'),
            ]);

            $matchedLots[] = [
                'lot_id'        => $lot['id'],
                'match_qty'     => $matchQty,
                'buy_date'      => $buyDate,
                'holding_days'  => $holdingDays,
                'gain_type'     => $gainType,
                'realized_gain' => $realizedGain,
            ];

            $neededQty -= $matchQty;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception('Failed to record bond sale and FIFO matching.');
        }

        return [
            'sell_id'      => $sellTxId,
            'matched_lots' => $matchedLots,
        ];
    }

    /**
     * Record a Maturity Principal Redemption (e.g. RBI SGB final redemption or G-Sec maturity).
     * SGB maturity redemptions are flagged as 'EXEMPT_SGB_MATURITY' under Section 47(viic).
     */
    public function recordRedemption(
        int $userId,
        int $bondId,
        string $redemptionDate,
        float $quantity,
        float $redemptionPrice,
        ?string $notes = null
    ): array {
        $db = \Config\Database::connect();
        $db->transStart();

        $bond = $db->table('bonds')->where('id', $bondId)->get()->getRowArray();
        if (!$bond) {
            throw new Exception('Bond not found.');
        }

        $isSgb = ($bond['category'] === 'SGB');

        // Fetch available BUY lots
        $buyLots = $this->where('bond_id', $bondId)
                        ->where('user_id', $userId)
                        ->where('transaction_type', 'BUY')
                        ->where('remaining_quantity >', 0)
                        ->orderBy('transaction_date', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->findAll();

        $totalAvailable = array_sum(array_column($buyLots, 'remaining_quantity'));
        if ($quantity > $totalAvailable + 0.0001) {
            throw new Exception("Insufficient bond quantity to redeem. Available: {$totalAvailable}, requested: {$quantity}");
        }

        $totalProceeds = $quantity * $redemptionPrice;

        // Insert REDEMPTION transaction
        $redeemTxId = $this->insert([
            'user_id'            => $userId,
            'bond_id'            => $bondId,
            'transaction_type'   => 'REDEMPTION',
            'transaction_date'   => $redemptionDate,
            'quantity'           => $quantity,
            'remaining_quantity' => 0.0000,
            'price'              => $redemptionPrice,
            'brokerage_charges'  => 0.00,
            'accrued_interest'   => 0.00,
            'total_amount'       => $totalProceeds,
            'notes'              => $notes ?: ($isSgb ? 'RBI SGB Maturity Principal Redemption (Sec 47(viic) Tax-Exempt)' : 'Maturity Principal Redemption'),
        ]);

        $neededQty   = $quantity;
        $matchedLots = [];

        foreach ($buyLots as $lot) {
            if ($neededQty <= 0.00001) {
                break;
            }

            $availableInLot = (float) $lot['remaining_quantity'];
            $matchQty       = min($neededQty, $availableInLot);

            $newRemaining = $availableInLot - $matchQty;
            $this->update($lot['id'], ['remaining_quantity' => $newRemaining]);

            $buyDate      = $lot['transaction_date'];
            $holdingDays  = (int) ceil((strtotime($redemptionDate) - strtotime($buyDate)) / 86400);
            $buyPrice     = (float) $lot['price'];
            $realizedGain = ($matchQty * $redemptionPrice) - ($matchQty * $buyPrice);

            // SGB maturity redemption is completely tax exempt for individuals u/s 47(viic)
            $gainType = $isSgb ? 'EXEMPT_SGB_MATURITY' : (($holdingDays > 365) ? 'LTCG' : 'STCG');

            $db->table('bond_capital_gains')->insert([
                'user_id'             => $userId,
                'bond_id'             => $bondId,
                'exit_transaction_id' => $redeemTxId,
                'buy_transaction_id'  => $lot['id'],
                'buy_date'            => $buyDate,
                'exit_date'           => $redemptionDate,
                'holding_days'        => $holdingDays,
                'quantity_matched'    => $matchQty,
                'buy_price'           => $buyPrice,
                'exit_price'          => $redemptionPrice,
                'realized_gain'       => $realizedGain,
                'gain_type'           => $gainType,
                'created_at'          => date('Y-m-d H:i:s'),
            ]);

            $matchedLots[] = [
                'lot_id'        => $lot['id'],
                'match_qty'     => $matchQty,
                'gain_type'     => $gainType,
                'realized_gain' => $realizedGain,
            ];

            $neededQty -= $matchQty;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception('Failed to record bond redemption.');
        }

        return [
            'redemption_id' => $redeemTxId,
            'matched_lots'  => $matchedLots,
        ];
    }

    /**
     * Get transaction history with joined bond metadata.
     */
    public function getTransactionsWithBond(int $userId, ?int $bondId = null): array
    {
        $builder = $this->select('bond_transactions.*, bonds.bond_name, bonds.isin, bonds.bond_symbol, bonds.category')
                        ->join('bonds', 'bonds.id = bond_transactions.bond_id')
                        ->where('bond_transactions.user_id', $userId);

        if ($bondId !== null) {
            $builder->where('bond_transactions.bond_id', $bondId);
        }

        return $builder->orderBy('bond_transactions.transaction_date', 'DESC')
                       ->orderBy('bond_transactions.id', 'DESC')
                       ->findAll();
    }

    /**
     * Rebuild FIFO lot allocation and capital gains records from scratch for a given Bond.
     * Handles BUY, SELL, and REDEMPTION with SGB 47(viic) tax exemption.
     * Called whenever a transaction is updated or deleted.
     */
    public function rebuildFifoLotsAndCapitalGains(int $userId, int $bondId): void
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Reset all BUY lots: remaining_quantity = quantity
        $db->table('bond_transactions')
           ->where('user_id', $userId)
           ->where('bond_id', $bondId)
           ->where('transaction_type', 'BUY')
           ->update(['remaining_quantity' => new \CodeIgniter\Database\RawSql('quantity')]);

        // 2. Clear capital gains table for this bond and user
        $db->table('bond_capital_gains')
           ->where('user_id', $userId)
           ->where('bond_id', $bondId)
           ->delete();

        // 3. Check if bond is SGB
        $bond = $db->table('bonds')->where('id', $bondId)->get()->getRowArray();
        $isSgb = ($bond['category'] ?? '') === 'SGB';

        // 4. Fetch all exit transactions (SELL & REDEMPTION) in chronological sequence
        $exitTransactions = $db->table('bond_transactions')
           ->where('user_id', $userId)
           ->where('bond_id', $bondId)
           ->whereIn('transaction_type', ['SELL', 'REDEMPTION'])
           ->orderBy('transaction_date', 'ASC')
           ->orderBy('id', 'ASC')
           ->get()
           ->getResultArray();

        foreach ($exitTransactions as $exit) {
            $exitTxId = (int) $exit['id'];
            $exitDate = $exit['transaction_date'];
            $exitPrice = (float) $exit['price'];
            $neededQty = (float) $exit['quantity'];
            $charges = (float) ($exit['brokerage_charges'] ?? 0);
            $isRedeem = ($exit['transaction_type'] === 'REDEMPTION');

            // Find available BUY lots
            $availableBuyLots = $this->where('user_id', $userId)
                                     ->where('bond_id', $bondId)
                                     ->where('transaction_type', 'BUY')
                                     ->where('remaining_quantity >', 0.0001)
                                     ->orderBy('transaction_date', 'ASC')
                                     ->orderBy('id', 'ASC')
                                     ->findAll();

            $totalAvailable = (float) array_sum(array_column($availableBuyLots, 'remaining_quantity'));
            if ($neededQty > ($totalAvailable + 0.0001)) {
                $typeLabel = strtolower($exit['transaction_type']);
                throw new Exception("Inventory validation error: On {$exitDate}, you recorded a {$typeLabel} of {$neededQty} units, but only {$totalAvailable} units were available. Cannot save this change.");
            }

            foreach ($availableBuyLots as $lot) {
                if ($neededQty <= 0.0001) break;

                $availableInLot = (float) $lot['remaining_quantity'];
                $matchQty = min($neededQty, $availableInLot);

                $buyDate = $lot['transaction_date'];
                $buyPrice = (float) $lot['price'];
                $holdingDays = (int) ceil((strtotime($exitDate) - strtotime($buyDate)) / 86400);

                // SGB maturity redemption is completely tax exempt for individuals u/s 47(viic)
                $gainType = ($isSgb && $isRedeem) ? 'EXEMPT_SGB_MATURITY' : (($holdingDays > 365) ? 'LTCG' : 'STCG');

                $proportionateCharges = ($exit['quantity'] > 0) ? ($charges * ($matchQty / (float) $exit['quantity'])) : 0.0;
                $lotProceeds = ($matchQty * $exitPrice) - $proportionateCharges;
                $costBasis = $matchQty * $buyPrice;
                $realizedGain = $lotProceeds - $costBasis;

                $db->table('bond_capital_gains')->insert([
                    'user_id'             => $userId,
                    'bond_id'             => $bondId,
                    'exit_transaction_id' => $exitTxId,
                    'buy_transaction_id'  => (int) $lot['id'],
                    'buy_date'            => $buyDate,
                    'exit_date'           => $exitDate,
                    'holding_days'        => $holdingDays,
                    'quantity_matched'    => $matchQty,
                    'buy_price'           => $buyPrice,
                    'exit_price'          => $exitPrice,
                    'realized_gain'       => $realizedGain,
                    'gain_type'           => $gainType,
                    'created_at'          => date('Y-m-d H:i:s'),
                ]);

                $newRemaining = max(0.0, $availableInLot - $matchQty);
                $this->update($lot['id'], ['remaining_quantity' => $newRemaining]);

                $neededQty -= $matchQty;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new Exception("Failed to rebalance bond FIFO lots.");
        }
    }
}

