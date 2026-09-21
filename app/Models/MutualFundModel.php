<?php

namespace App\Models;

use CodeIgniter\Model;

class MutualFundModel extends Model
{
    protected $table            = 'mutual_funds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'amfi_code',
        'scheme_name',
        'folio_number',
        'category',
        'fund_house',
        'current_nav',
        'nav_date',
        'isin',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all Mutual Fund holdings for a user with FIFO-based metrics and portfolio aggregates.
     */
    public function getHoldingsWithMetrics(int $userId): array
    {
        $funds = $this->where('user_id', $userId)->orderBy('scheme_name', 'ASC')->findAll();
        $db = \Config\Database::connect();

        $holdings = [];
        $totalCurrentValue = 0.0;
        $totalInvested = 0.0;
        $totalUnrealizedPnl = 0.0;
        $totalRealizedPnl = 0.0;
        $totalStcg = 0.0;
        $totalLtcg = 0.0;

        foreach ($funds as $fund) {
            $fundId = (int) $fund['id'];

            // 1. Calculate active unredeemed units from BUY lots (FIFO lots)
            $buyLots = $db->table('mutual_fund_transactions')
                ->where('mutual_fund_id', $fundId)
                ->where('user_id', $userId)
                ->whereIn('transaction_type', ['BUY_SIP', 'BUY_LUMPSUM'])
                ->where('remaining_units >', 0)
                ->orderBy('transaction_date', 'ASC')
                ->get()
                ->getResultArray();

            $activeUnits = 0.0;
            $investedCost = 0.0;

            foreach ($buyLots as $lot) {
                $u = (float) $lot['remaining_units'];
                $nav = (float) $lot['nav'];
                $activeUnits += $u;
                $investedCost += ($u * $nav);
            }

            $currentNav = (float) $fund['current_nav'];
            $currentValue = $activeUnits * $currentNav;
            $avgPurchaseNav = $activeUnits > 0 ? ($investedCost / $activeUnits) : 0.0;
            $unrealizedPnl = $activeUnits > 0 ? ($currentValue - $investedCost) : 0.0;
            $unrealizedPnlPercent = $investedCost > 0 ? (($unrealizedPnl / $investedCost) * 100) : 0.0;

            // 2. Realized P&L from FIFO Capital Gains table
            $cgRow = $db->table('mutual_fund_capital_gains')
                ->select('SUM(realized_gain) as total_gain, 
                          SUM(CASE WHEN gain_type = "STCG" THEN realized_gain ELSE 0 END) as stcg,
                          SUM(CASE WHEN gain_type = "LTCG" THEN realized_gain ELSE 0 END) as ltcg')
                ->where('mutual_fund_id', $fundId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $realizedGain = (float) ($cgRow['total_gain'] ?? 0);
            $fundStcg = (float) ($cgRow['stcg'] ?? 0);
            $fundLtcg = (float) ($cgRow['ltcg'] ?? 0);

            $fund['active_units']           = $activeUnits;
            $fund['avg_purchase_nav']       = $avgPurchaseNav;
            $fund['invested_value']         = $investedCost;
            $fund['current_value']          = $currentValue;
            $fund['unrealized_pnl']         = $unrealizedPnl;
            $fund['unrealized_pnl_percent'] = $unrealizedPnlPercent;
            $fund['realized_pnl']           = $realizedGain;
            $fund['stcg']                   = $fundStcg;
            $fund['ltcg']                   = $fundLtcg;
            $fund['total_gain']             = $unrealizedPnl + $realizedGain;

            $holdings[] = $fund;

            // Accumulate portfolio totals
            $totalCurrentValue  += $currentValue;
            $totalInvested      += $investedCost;
            $totalUnrealizedPnl += $unrealizedPnl;
            $totalRealizedPnl   += $realizedGain;
            $totalStcg          += $fundStcg;
            $totalLtcg          += $fundLtcg;
        }

        $overallUnrealizedPercent = $totalInvested > 0 ? (($totalUnrealizedPnl / $totalInvested) * 100) : 0.0;

        return [
            'holdings' => $holdings,
            'summary'  => [
                'total_current_value'    => $totalCurrentValue,
                'total_invested'         => $totalInvested,
                'total_unrealized_pnl'   => $totalUnrealizedPnl,
                'unrealized_pnl_percent' => $overallUnrealizedPercent,
                'total_realized_pnl'     => $totalRealizedPnl,
                'total_stcg'             => $totalStcg,
                'total_ltcg'             => $totalLtcg,
                'total_net_gain'         => $totalUnrealizedPnl + $totalRealizedPnl,
                'funds_count'            => count($funds),
                'active_holdings_count'  => count(array_filter($holdings, fn($h) => $h['active_units'] > 0.0001)),
            ],
        ];
    }

    /**
     * Find fund by AMFI code, Folio number, and user.
     */
    public function findByCodeAndFolio(string $amfiCode, string $folioNumber, int $userId): ?array
    {
        return $this->where('user_id', $userId)
                    ->where('amfi_code', trim($amfiCode))
                    ->where('folio_number', trim($folioNumber))
                    ->first();
    }
}

