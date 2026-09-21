<?php

namespace App\Models;

use CodeIgniter\Model;

class BondModel extends Model
{
    protected $table            = 'bonds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'bond_name',
        'isin',
        'bond_symbol',
        'category',
        'issuer',
        'face_value',
        'coupon_rate',
        'interest_frequency',
        'issue_date',
        'maturity_date',
        'current_market_price',
        'cmp_date',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all bonds with active units, FIFO cost basis, CMP valuation,
     * cumulative interest earned, last interest paid date, and realized gains.
     */
    public function getHoldingsWithMetrics(int $userId): array
    {
        $bonds = $this->where('user_id', $userId)->orderBy('bond_name', 'ASC')->findAll();
        $db = \Config\Database::connect();

        $holdings = [];
        $totalCurrentValue   = 0.0;
        $totalInvested       = 0.0;
        $totalUnrealizedPnl  = 0.0;
        $totalInterestEarned = 0.0;
        $totalRealizedPnl    = 0.0;
        $totalStcg           = 0.0;
        $totalLtcg           = 0.0;
        $totalSgbExempt      = 0.0;

        foreach ($bonds as $bond) {
            $bondId = (int) $bond['id'];

            // 1. Calculate active unredeemed units/grams from BUY lots (FIFO lots)
            $buyLots = $db->table('bond_transactions')
                ->where('bond_id', $bondId)
                ->where('user_id', $userId)
                ->where('transaction_type', 'BUY')
                ->where('remaining_quantity >', 0)
                ->orderBy('transaction_date', 'ASC')
                ->get()
                ->getResultArray();

            $activeQty     = 0.0;
            $investedCost  = 0.0;

            foreach ($buyLots as $lot) {
                $q = (float) $lot['remaining_quantity'];
                $p = (float) $lot['price'];
                $activeQty += $q;
                $investedCost += ($q * $p);
            }

            $cmp = (float) $bond['current_market_price'];
            $currentValue = $activeQty * $cmp;
            $avgBuyPrice = $activeQty > 0 ? ($investedCost / $activeQty) : 0.0;
            $unrealizedPnl = $activeQty > 0 ? ($currentValue - $investedCost) : 0.0;
            $unrealizedPnlPercent = $investedCost > 0 ? (($unrealizedPnl / $investedCost) * 100) : 0.0;

            // 2. Cumulative interest earned & Last Interest Paid Date
            $interestRow = $db->table('bond_interest_payouts')
                ->select('SUM(net_interest) AS total_interest, SUM(gross_interest) AS gross_interest, MAX(payout_date) AS last_interest_paid_date')
                ->where('bond_id', $bondId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $bondInterestEarned   = (float) ($interestRow['total_interest'] ?? 0);
            $lastInterestPaidDate = $interestRow['last_interest_paid_date'] ?? null;

            // 3. Realized Capital Gains & Maturity Redemptions
            $cgRow = $db->table('bond_capital_gains')
                ->select('SUM(realized_gain) AS total_gain,
                          SUM(CASE WHEN gain_type = "STCG" THEN realized_gain ELSE 0 END) AS stcg,
                          SUM(CASE WHEN gain_type = "LTCG" THEN realized_gain ELSE 0 END) AS ltcg,
                          SUM(CASE WHEN gain_type = "EXEMPT_SGB_MATURITY" THEN realized_gain ELSE 0 END) AS sgb_exempt')
                ->where('bond_id', $bondId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $realizedGain = (float) ($cgRow['total_gain'] ?? 0);
            $bondStcg     = (float) ($cgRow['stcg'] ?? 0);
            $bondLtcg     = (float) ($cgRow['ltcg'] ?? 0);
            $bondExempt   = (float) ($cgRow['sgb_exempt'] ?? 0);

            // Days to maturity calculation
            $maturityDate = $bond['maturity_date'];
            $daysToMaturity = (int) ceil((strtotime($maturityDate) - time()) / 86400);

            $bond['active_quantity']        = $activeQty;
            $bond['avg_buy_price']          = $avgBuyPrice;
            $bond['invested_value']         = $investedCost;
            $bond['current_value']          = $currentValue;
            $bond['unrealized_pnl']         = $unrealizedPnl;
            $bond['unrealized_pnl_percent'] = $unrealizedPnlPercent;
            $bond['interest_earned']        = $bondInterestEarned;
            $bond['last_interest_paid_date']= $lastInterestPaidDate;
            $bond['realized_pnl']           = $realizedGain;
            $bond['stcg']                   = $bondStcg;
            $bond['ltcg']                   = $bondLtcg;
            $bond['sgb_exempt']             = $bondExempt;
            $bond['total_return']           = $unrealizedPnl + $realizedGain + $bondInterestEarned;
            $bond['days_to_maturity']       = $daysToMaturity;

            $holdings[] = $bond;

            // Accumulate portfolio totals
            $totalCurrentValue   += $currentValue;
            $totalInvested       += $investedCost;
            $totalUnrealizedPnl  += $unrealizedPnl;
            $totalInterestEarned += $bondInterestEarned;
            $totalRealizedPnl    += $realizedGain;
            $totalStcg           += $bondStcg;
            $totalLtcg           += $bondLtcg;
            $totalSgbExempt      += $bondExempt;
        }

        $overallUnrealizedPercent = $totalInvested > 0 ? (($totalUnrealizedPnl / $totalInvested) * 100) : 0.0;
        $totalNetReturn = $totalUnrealizedPnl + $totalRealizedPnl + $totalInterestEarned;

        return [
            'holdings' => $holdings,
            'summary'  => [
                'total_current_value'    => $totalCurrentValue,
                'total_invested'         => $totalInvested,
                'total_unrealized_pnl'   => $totalUnrealizedPnl,
                'unrealized_pnl_percent' => $overallUnrealizedPercent,
                'total_interest_earned'  => $totalInterestEarned,
                'total_realized_pnl'     => $totalRealizedPnl,
                'total_stcg'             => $totalStcg,
                'total_ltcg'             => $totalLtcg,
                'total_sgb_exempt'       => $totalSgbExempt,
                'total_net_return'       => $totalNetReturn,
                'bonds_count'            => count($bonds),
                'active_holdings_count'  => count(array_filter($holdings, fn($h) => $h['active_quantity'] > 0.0001)),
            ],
        ];
    }
}

