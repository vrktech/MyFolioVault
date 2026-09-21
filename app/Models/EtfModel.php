<?php

namespace App\Models;

use CodeIgniter\Model;

class EtfModel extends Model
{
    protected $table            = 'etfs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'symbol',
        'etf_name',
        'category',
        'amc_name',
        'exchange',
        'isin',
        'current_price',
        'previous_close',
        'price_updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all ETF holdings for a user with FIFO-based metrics and portfolio aggregates.
     */
    public function getHoldingsWithMetrics(int $userId): array
    {
        $etfs = $this->where('user_id', $userId)->orderBy('etf_name', 'ASC')->findAll();
        $db = \Config\Database::connect();

        $holdings = [];
        $totalCurrentValue = 0.0;
        $totalInvested = 0.0;
        $totalUnrealizedPnl = 0.0;
        $totalRealizedPnl = 0.0;
        $totalStcg = 0.0;
        $totalLtcg = 0.0;

        foreach ($etfs as $etf) {
            $etfId = (int) $etf['id'];

            // 1. Calculate active unconsumed units from BUY lots (FIFO lots)
            $buyLots = $db->table('etf_transactions')
                ->where('etf_id', $etfId)
                ->where('user_id', $userId)
                ->where('transaction_type', 'BUY')
                ->where('remaining_quantity >', 0)
                ->orderBy('transaction_date', 'ASC')
                ->get()
                ->getResultArray();

            $activeUnits = 0;
            $investedCost = 0.0;

            foreach ($buyLots as $lot) {
                $units = (int) $lot['remaining_quantity'];
                $price = (float) $lot['price'];
                $activeUnits += $units;
                $investedCost += ($units * $price);
            }

            $cmp = (float) $etf['current_price'];
            $currentValue = $activeUnits * $cmp;
            $avgBuyPrice = $activeUnits > 0 ? ($investedCost / $activeUnits) : 0.0;
            $unrealizedPnl = $activeUnits > 0 ? ($currentValue - $investedCost) : 0.0;
            $unrealizedPnlPercent = $investedCost > 0 ? (($unrealizedPnl / $investedCost) * 100) : 0.0;

            // 2. Realized P&L from FIFO Capital Gains table
            $cgRow = $db->table('etf_capital_gains')
                ->select('SUM(realized_gain) as total_gain, 
                          SUM(CASE WHEN gain_type = "STCG" THEN realized_gain ELSE 0 END) as stcg,
                          SUM(CASE WHEN gain_type = "LTCG" THEN realized_gain ELSE 0 END) as ltcg')
                ->where('etf_id', $etfId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $realizedGain = (float) ($cgRow['total_gain'] ?? 0);
            $etfStcg = (float) ($cgRow['stcg'] ?? 0);
            $etfLtcg = (float) ($cgRow['ltcg'] ?? 0);

            $etf['active_quantity']        = $activeUnits;
            $etf['avg_buy_price']          = $avgBuyPrice;
            $etf['invested_value']         = $investedCost;
            $etf['current_value']          = $currentValue;
            $etf['unrealized_pnl']         = $unrealizedPnl;
            $etf['unrealized_pnl_percent'] = $unrealizedPnlPercent;
            $etf['realized_pnl']           = $realizedGain;
            $etf['stcg']                   = $etfStcg;
            $etf['ltcg']                   = $etfLtcg;
            $etf['total_gain']             = $unrealizedPnl + $realizedGain;

            $holdings[] = $etf;

            // Accumulate portfolio totals
            $totalCurrentValue  += $currentValue;
            $totalInvested      += $investedCost;
            $totalUnrealizedPnl += $unrealizedPnl;
            $totalRealizedPnl   += $realizedGain;
            $totalStcg          += $etfStcg;
            $totalLtcg          += $etfLtcg;
        }

        $overallUnrealizedPercent = $totalInvested > 0 ? (($totalUnrealizedPnl / $totalInvested) * 100) : 0.0;

        $activeHoldings = array_values(array_filter($holdings, fn($h) => (int)$h['active_quantity'] > 0));
        $pastHoldings   = array_values(array_filter($holdings, fn($h) => (int)$h['active_quantity'] <= 0));

        return [
            'holdings'        => $activeHoldings,
            'active_holdings' => $activeHoldings,
            'past_holdings'   => $pastHoldings,
            'all_holdings'    => $holdings,
            'summary'  => [
                'total_current_value'    => $totalCurrentValue,
                'total_invested'         => $totalInvested,
                'total_unrealized_pnl'   => $totalUnrealizedPnl,
                'unrealized_pnl_percent' => $overallUnrealizedPercent,
                'total_realized_pnl'     => $totalRealizedPnl,
                'total_stcg'             => $totalStcg,
                'total_ltcg'             => $totalLtcg,
                'total_net_gain'         => $totalUnrealizedPnl + $totalRealizedPnl,
                'etfs_count'             => count($etfs),
                'active_holdings_count'  => count($activeHoldings),
                'past_holdings_count'    => count($pastHoldings),
            ],
        ];
    }

    /**
     * Find ETF by symbol, exchange, and user.
     */
    public function findBySymbol(string $symbol, string $exchange, int $userId): ?array
    {
        return $this->where('user_id', $userId)
                    ->where('symbol', strtoupper(trim($symbol)))
                    ->where('exchange', strtoupper(trim($exchange)))
                    ->first();
    }
}

