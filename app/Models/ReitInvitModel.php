<?php

namespace App\Models;

use CodeIgniter\Model;

class ReitInvitModel extends Model
{
    protected $table            = 'reits_invits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'trust_name',
        'symbol',
        'isin',
        'trust_type',
        'exchange',
        'sponsor',
        'current_price',
        'prev_close',
        'last_price_update',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all REITs and InvITs with active units, FIFO cost basis, CMP valuation,
     * cumulative distributions received, and realized capital gains.
     */
    public function getHoldingsWithMetrics(int $userId): array
    {
        $trusts = $this->where('user_id', $userId)->orderBy('symbol', 'ASC')->findAll();
        $db = \Config\Database::connect();

        $holdings = [];
        $totalCurrentValue        = 0.0;
        $totalInvested            = 0.0;
        $totalUnrealizedPnl       = 0.0;
        $totalDistributionsEarned = 0.0;
        $totalRealizedPnl         = 0.0;
        $totalStcg                = 0.0;
        $totalLtcg                = 0.0;

        foreach ($trusts as $trust) {
            $trustId = (int) $trust['id'];

            // 1. Calculate active unredeemed units from BUY lots (FIFO lots)
            $buyLots = $db->table('reit_invit_transactions')
                ->where('trust_id', $trustId)
                ->where('user_id', $userId)
                ->where('transaction_type', 'BUY')
                ->where('remaining_quantity >', 0)
                ->orderBy('transaction_date', 'ASC')
                ->get()
                ->getResultArray();

            $activeUnits  = 0.0;
            $investedCost = 0.0;

            foreach ($buyLots as $lot) {
                $q = (float) $lot['remaining_quantity'];
                $p = (float) $lot['price'];
                $activeUnits += $q;
                $investedCost += ($q * $p);
            }

            $cmp = (float) $trust['current_price'];
            $currentValue = $activeUnits * $cmp;
            $avgBuyPrice = $activeUnits > 0 ? ($investedCost / $activeUnits) : 0.0;
            $unrealizedPnl = $activeUnits > 0 ? ($currentValue - $investedCost) : 0.0;
            $unrealizedPnlPercent = $investedCost > 0 ? (($unrealizedPnl / $investedCost) * 100) : 0.0;

            // 2. Cumulative distributions received (interest, dividend, ROC)
            $distRow = $db->table('reit_invit_distributions')
                ->select('SUM(net_received) AS total_distributions,
                          SUM(interest_component) AS total_interest,
                          SUM(dividend_component) AS total_dividend,
                          SUM(return_of_capital) AS total_roc,
                          SUM(tds_deducted) AS total_tds,
                          MAX(payout_date) AS last_distribution_date')
                ->where('trust_id', $trustId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $trustDistEarned       = (float) ($distRow['total_distributions'] ?? 0);
            $trustInterestEarned   = (float) ($distRow['total_interest'] ?? 0);
            $trustDividendEarned   = (float) ($distRow['total_dividend'] ?? 0);
            $trustRocEarned        = (float) ($distRow['total_roc'] ?? 0);
            $trustTdsDeducted      = (float) ($distRow['total_tds'] ?? 0);
            $lastDistributionDate  = $distRow['last_distribution_date'] ?? null;

            // 3. Realized Capital Gains from secondary market sales
            $cgRow = $db->table('reit_invit_capital_gains')
                ->select('SUM(realized_gain) AS total_gain,
                          SUM(CASE WHEN gain_type = "STCG" THEN realized_gain ELSE 0 END) AS stcg,
                          SUM(CASE WHEN gain_type = "LTCG" THEN realized_gain ELSE 0 END) AS ltcg')
                ->where('trust_id', $trustId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $realizedGain = (float) ($cgRow['total_gain'] ?? 0);
            $trustStcg    = (float) ($cgRow['stcg'] ?? 0);
            $trustLtcg    = (float) ($cgRow['ltcg'] ?? 0);

            $trust['active_units']           = $activeUnits;
            $trust['avg_buy_price']          = $avgBuyPrice;
            $trust['invested_value']         = $investedCost;
            $trust['current_value']          = $currentValue;
            $trust['unrealized_pnl']         = $unrealizedPnl;
            $trust['unrealized_pnl_percent'] = $unrealizedPnlPercent;
            $trust['distributions_earned']   = $trustDistEarned;
            $trust['interest_earned']        = $trustInterestEarned;
            $trust['dividend_earned']        = $trustDividendEarned;
            $trust['roc_earned']             = $trustRocEarned;
            $trust['tds_deducted']           = $trustTdsDeducted;
            $trust['last_distribution_date'] = $lastDistributionDate;
            $trust['realized_pnl']           = $realizedGain;
            $trust['stcg']                   = $trustStcg;
            $trust['ltcg']                   = $trustLtcg;
            $trust['total_return']           = $unrealizedPnl + $realizedGain + $trustDistEarned;

            $holdings[] = $trust;

            // Accumulate portfolio totals
            $totalCurrentValue        += $currentValue;
            $totalInvested            += $investedCost;
            $totalUnrealizedPnl       += $unrealizedPnl;
            $totalDistributionsEarned += $trustDistEarned;
            $totalRealizedPnl         += $realizedGain;
            $totalStcg                += $trustStcg;
            $totalLtcg                += $trustLtcg;
        }

        $overallUnrealizedPercent = $totalInvested > 0 ? (($totalUnrealizedPnl / $totalInvested) * 100) : 0.0;
        $totalNetReturn = $totalUnrealizedPnl + $totalRealizedPnl + $totalDistributionsEarned;

        $activeHoldings = array_values(array_filter($holdings, fn($h) => (float)$h['active_units'] > 0.0001));
        $pastHoldings   = array_values(array_filter($holdings, fn($h) => (float)$h['active_units'] <= 0.0001));

        return [
            'holdings'        => $activeHoldings,
            'active_holdings' => $activeHoldings,
            'past_holdings'   => $pastHoldings,
            'all_holdings'    => $holdings,
            'summary'  => [
                'total_current_value'        => $totalCurrentValue,
                'total_invested'             => $totalInvested,
                'total_unrealized_pnl'       => $totalUnrealizedPnl,
                'unrealized_pnl_percent'     => $overallUnrealizedPercent,
                'total_distributions_earned' => $totalDistributionsEarned,
                'total_realized_pnl'         => $totalRealizedPnl,
                'total_stcg'                 => $totalStcg,
                'total_ltcg'                 => $totalLtcg,
                'total_net_return'           => $totalNetReturn,
                'trusts_count'               => count($trusts),
                'active_holdings_count'      => count($activeHoldings),
                'past_holdings_count'        => count($pastHoldings),
            ],
        ];
    }
}

