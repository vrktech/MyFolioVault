<?php

namespace App\Models\Traits;

/**
 * Trait FifoTransactionTrait
 *
 * Provides shared First-In, First-Out (FIFO) lot matching, holding duration
 * calculation, charge apportionment, and capital gains classification.
 */
trait FifoTransactionTrait
{
    /**
     * Match a sell quantity against available buy lots in chronological order (FIFO).
     *
     * @param array     $buyLots          Array of buy lots with ['id', 'transaction_date', 'remaining_quantity', 'price']
     * @param float|int $sellQuantity     Quantity to sell
     * @param float     $sellPrice        Selling price per unit
     * @param float     $totalSellCharges Total transaction charges (brokerage + STT + taxes)
     * @param string    $sellDate         Date of the sale (YYYY-MM-DD)
     * @param int       $holdingThreshold Days threshold for LTCG (default: 365)
     * @return array{matched_lots: array, total_matched: float, total_realized_gain: float}
     */
    public function matchLotsFIFO(
        array $buyLots,
        float|int $sellQuantity,
        float $sellPrice,
        float $totalSellCharges,
        string $sellDate,
        int $holdingThreshold = 365
    ): array {
        $neededQty = (float) $sellQuantity;
        $matchedLots = [];
        $totalRealizedGain = 0.0;
        $totalMatched = 0.0;

        foreach ($buyLots as $lot) {
            if ($neededQty <= 0) {
                break;
            }

            $lotRemaining = (float) ($lot['remaining_quantity'] ?? 0);
            if ($lotRemaining <= 0) {
                continue;
            }

            $matchQty = min($lotRemaining, $neededQty);
            $buyPrice = (float) ($lot['price'] ?? 0);
            $buyDate  = (string) ($lot['transaction_date'] ?? $sellDate);

            // Compute holding days & classification using financial helpers
            $holdingDays = function_exists('calculate_holding_days')
                ? calculate_holding_days($buyDate, $sellDate)
                : max(0, (int) round((strtotime($sellDate) - strtotime($buyDate)) / 86400));

            $gainType = function_exists('classify_gain_type')
                ? classify_gain_type($holdingDays, $holdingThreshold)
                : ($holdingDays > $holdingThreshold ? 'LTCG' : 'STCG');

            // Apportion selling charges
            $lotCharges = function_exists('apportion_charges')
                ? apportion_charges($totalSellCharges, $matchQty, $sellQuantity)
                : ($sellQuantity > 0 ? ($totalSellCharges * ($matchQty / (float) $sellQuantity)) : 0.0);

            // Realized Gain = (Proceeds - Cost Basis) - Allocated Selling Charges
            $grossProceeds = $matchQty * $sellPrice;
            $costBasis     = $matchQty * $buyPrice;
            $realizedGain  = $grossProceeds - $costBasis - $lotCharges;

            $newRemaining = $lotRemaining - $matchQty;

            $matchedLots[] = [
                'buy_id'         => (int) $lot['id'],
                'matched_qty'    => $matchQty,
                'lot_remaining'  => $lotRemaining,
                'new_remaining'  => $newRemaining,
                'buy_date'       => $buyDate,
                'buy_price'      => $buyPrice,
                'sell_date'      => $sellDate,
                'sell_price'     => $sellPrice,
                'holding_days'   => $holdingDays,
                'gain_type'      => $gainType,
                'allocated_fees' => $lotCharges,
                'realized_gain'  => round($realizedGain, 2),
            ];

            $totalRealizedGain += $realizedGain;
            $totalMatched      += $matchQty;
            $neededQty         -= $matchQty;
        }

        return [
            'matched_lots'        => $matchedLots,
            'total_matched'       => $totalMatched,
            'total_realized_gain' => round($totalRealizedGain, 2),
        ];
    }
}

