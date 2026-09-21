<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class ReitInvitTransactionModel extends Model
{
    protected $table            = 'reit_invit_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'trust_id',
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
     * Record a BUY transaction for a REIT or InvIT.
     */
    public function recordBuy(
        int $userId,
        int $trustId,
        string $date,
        float $quantity,
        float $price,
        float $brokerage = 0.0,
        float $sttTaxes = 0.0,
        ?string $notes = null
    ): int {
        $totalAmount = ($quantity * $price) + $brokerage + $sttTaxes;

        $data = [
            'user_id'            => $userId,
            'trust_id'           => $trustId,
            'transaction_type'   => 'BUY',
            'transaction_date'   => $date,
            'quantity'           => $quantity,
            'remaining_quantity' => $quantity, // Initial full lot unredeemed
            'price'              => $price,
            'brokerage'          => $brokerage,
            'stt_taxes'          => $sttTaxes,
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
        int $trustId,
        string $sellDate,
        float $sellQty,
        float $sellPrice,
        float $brokerage = 0.0,
        float $sttTaxes = 0.0,
        ?string $notes = null
    ): array {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Fetch available BUY lots in FIFO order (oldest first)
        $buyLots = $this->where('trust_id', $trustId)
                        ->where('user_id', $userId)
                        ->where('transaction_type', 'BUY')
                        ->where('remaining_quantity >', 0)
                        ->orderBy('transaction_date', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->findAll();

        $totalAvailable = array_sum(array_column($buyLots, 'remaining_quantity'));
        if ($sellQty > $totalAvailable + 0.0001) {
            throw new Exception("Insufficient units. Available: {$totalAvailable}, requested sell: {$sellQty}");
        }

        $totalCharges  = $brokerage + $sttTaxes;
        $grossProceeds = $sellQty * $sellPrice;
        $netProceeds   = max(0.0, $grossProceeds - $totalCharges);

        // 2. Insert SELL transaction
        $sellTxId = $this->insert([
            'user_id'            => $userId,
            'trust_id'           => $trustId,
            'transaction_type'   => 'SELL',
            'transaction_date'   => $sellDate,
            'quantity'           => $sellQty,
            'remaining_quantity' => 0.0000,
            'price'              => $sellPrice,
            'brokerage'          => $brokerage,
            'stt_taxes'          => $sttTaxes,
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

            // Listed business trusts holding threshold for STCG vs LTCG (365 days / 12 months)
            $gainType = ($holdingDays > 365) ? 'LTCG' : 'STCG';

            $proportionateCharges = $sellQty > 0 ? ($totalCharges * ($matchQty / $sellQty)) : 0.0;
            $lotProceeds          = ($matchQty * $sellPrice) - $proportionateCharges;
            $costBasis            = $matchQty * $buyPrice;
            $realizedGain         = $lotProceeds - $costBasis;

            // Record capital gains audit row
            $db->table('reit_invit_capital_gains')->insert([
                'user_id'             => $userId,
                'trust_id'            => $trustId,
                'sell_transaction_id' => $sellTxId,
                'buy_transaction_id'  => $lot['id'],
                'buy_date'            => $buyDate,
                'sell_date'           => $sellDate,
                'holding_days'        => $holdingDays,
                'quantity_matched'    => $matchQty,
                'buy_price'           => $buyPrice,
                'sell_price'          => $sellPrice,
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
            throw new Exception('Failed to record REIT/InvIT sale and FIFO matching.');
        }

        return [
            'sell_id'      => $sellTxId,
            'matched_lots' => $matchedLots,
        ];
    }

    /**
     * Rebuild and re-match FIFO lots and capital gains for a REIT/InvIT from scratch.
     * Ensures data integrity when a transaction is edited or deleted.
     */
    public function rebuildFifoLotsAndCapitalGains(int $userId, int $trustId): void
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Reset all BUY lots to their full original quantity
        $db->table('reit_invit_transactions')
           ->where('user_id', $userId)
           ->where('trust_id', $trustId)
           ->where('transaction_type', 'BUY')
           ->update(['remaining_quantity' => new \CodeIgniter\Database\RawSql('quantity')]);

        // 2. Remove all capital gains records for this trust
        $db->table('reit_invit_capital_gains')
           ->where('user_id', $userId)
           ->where('trust_id', $trustId)
           ->delete();

        // 3. Fetch all SELL transactions in chronological order
        $sellTransactions = $db->table('reit_invit_transactions')
           ->where('user_id', $userId)
           ->where('trust_id', $trustId)
           ->where('transaction_type', 'SELL')
           ->orderBy('transaction_date', 'ASC')
           ->orderBy('id', 'ASC')
           ->get()
           ->getResultArray();

        foreach ($sellTransactions as $sell) {
            $sellId = (int) $sell['id'];
            $sellQty = (float) $sell['quantity'];
            $sellPrice = (float) $sell['price'];
            $sellDate = $sell['transaction_date'];
            $brokerage = (float) ($sell['brokerage'] ?? 0);
            $stt = (float) ($sell['stt_taxes'] ?? 0);
            $totalCharges = $brokerage + $stt;

            $availableLots = $this->where('user_id', $userId)
                                  ->where('trust_id', $trustId)
                                  ->where('transaction_type', 'BUY')
                                  ->where('remaining_quantity >', 0.0001)
                                  ->orderBy('transaction_date', 'ASC')
                                  ->orderBy('id', 'ASC')
                                  ->findAll();

            $totalAvailable = (float) array_sum(array_column($availableLots, 'remaining_quantity'));
            if ($sellQty > $totalAvailable + 0.0001) {
                throw new Exception("Inventory validation error: On {$sellDate}, you sold {$sellQty} units, but only {$totalAvailable} units were available. Cannot save this change.");
            }

            $neededQty = $sellQty;
            foreach ($availableLots as $lot) {
                if ($neededQty <= 0.0001) {
                    break;
                }

                $availableInLot = (float) $lot['remaining_quantity'];
                $matchQty = min($neededQty, $availableInLot);

                $buyDate = $lot['transaction_date'];
                $holdingDays = (int) ceil((strtotime($sellDate) - strtotime($buyDate)) / 86400);
                $gainType = ($holdingDays > 365) ? 'LTCG' : 'STCG';

                $propCharges = $sellQty > 0 ? ($totalCharges * ($matchQty / $sellQty)) : 0.0;
                $lotProceeds = ($matchQty * $sellPrice) - $propCharges;
                $costBasis = $matchQty * (float) $lot['price'];
                $realizedGain = $lotProceeds - $costBasis;

                $db->table('reit_invit_capital_gains')->insert([
                    'user_id'             => $userId,
                    'trust_id'            => $trustId,
                    'sell_transaction_id' => $sellId,
                    'buy_transaction_id'  => $lot['id'],
                    'buy_date'            => $buyDate,
                    'sell_date'           => $sellDate,
                    'holding_days'        => $holdingDays,
                    'quantity_matched'    => $matchQty,
                    'buy_price'           => $lot['price'],
                    'sell_price'          => $sellPrice,
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
            throw new Exception("Failed to rebuild FIFO lots for REIT/InvIT.");
        }
    }

    /**
     * Get transaction history with joined trust metadata.
     */
    public function getTransactionsWithTrust(int $userId, ?int $trustId = null): array
    {
        $builder = $this->select('reit_invit_transactions.*, reits_invits.trust_name, reits_invits.symbol, reits_invits.trust_type, reits_invits.exchange')
                        ->join('reits_invits', 'reits_invits.id = reit_invit_transactions.trust_id')
                        ->where('reit_invit_transactions.user_id', $userId);

        if ($trustId !== null) {
            $builder->where('reit_invit_transactions.trust_id', $trustId);
        }

        return $builder->orderBy('reit_invit_transactions.transaction_date', 'DESC')
                       ->orderBy('reit_invit_transactions.id', 'DESC')
                       ->findAll();
    }
}

