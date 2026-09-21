<?php

namespace App\Models;

use CodeIgniter\Model;

class EquityModel extends Model
{
    protected $table            = 'equities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'symbol',
        'company_name',
        'exchange',
        'isin',
        'sector',
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
     * Get all stock holdings for a user with FIFO-based metrics and summary totals.
     */
    public function getHoldingsWithMetrics(int $userId): array
    {
        $stocks = $this->where('user_id', $userId)->orderBy('company_name', 'ASC')->findAll();
        $db = \Config\Database::connect();

        $holdings = [];
        $totalCurrentValue = 0.0;
        $totalInvested = 0.0;
        $totalUnrealizedPnl = 0.0;
        $totalRealizedPnl = 0.0;
        $totalStcg = 0.0;
        $totalLtcg = 0.0;
        $totalDividends = 0.0;

        foreach ($stocks as $stock) {
            $stockId = (int) $stock['id'];

            // 1. Calculate active unconsumed shares from BUY lots (FIFO lots)
            $buyLots = $db->table('equity_transactions')
                ->where('equity_id', $stockId)
                ->where('user_id', $userId)
                ->where('transaction_type', 'BUY')
                ->where('remaining_quantity >', 0)
                ->orderBy('transaction_date', 'ASC')
                ->get()
                ->getResultArray();

            $activeQty = 0;
            $investedCost = 0.0;

            foreach ($buyLots as $lot) {
                $qty = (int) $lot['remaining_quantity'];
                $price = (float) $lot['price'];
                $activeQty += $qty;
                $investedCost += ($qty * $price);
            }

            $cmp = (float) $stock['current_price'];
            $currentValue = $activeQty * $cmp;
            $avgBuyPrice = $activeQty > 0 ? ($investedCost / $activeQty) : 0.0;
            $unrealizedPnl = $activeQty > 0 ? ($currentValue - $investedCost) : 0.0;
            $unrealizedPnlPercent = $investedCost > 0 ? (($unrealizedPnl / $investedCost) * 100) : 0.0;

            // 2. Realized P&L from FIFO Capital Gains table
            $cgRow = $db->table('equity_capital_gains')
                ->select('SUM(realized_gain) as total_gain, 
                          SUM(CASE WHEN gain_type = "STCG" THEN realized_gain ELSE 0 END) as stcg,
                          SUM(CASE WHEN gain_type = "LTCG" THEN realized_gain ELSE 0 END) as ltcg')
                ->where('equity_id', $stockId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $realizedGain = (float) ($cgRow['total_gain'] ?? 0);
            $stockStcg = (float) ($cgRow['stcg'] ?? 0);
            $stockLtcg = (float) ($cgRow['ltcg'] ?? 0);

            // 3. Dividends
            $divRow = $db->table('equity_dividends')
                ->select('SUM(total_amount) as total_div')
                ->where('equity_id', $stockId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();
            $dividends = (float) ($divRow['total_div'] ?? 0);

            $stock['active_quantity']        = $activeQty;
            $stock['avg_buy_price']          = $avgBuyPrice;
            $stock['invested_value']         = $investedCost;
            $stock['current_value']          = $currentValue;
            $stock['unrealized_pnl']         = $unrealizedPnl;
            $stock['unrealized_pnl_percent'] = $unrealizedPnlPercent;
            $stock['realized_pnl']           = $realizedGain;
            $stock['stcg']                   = $stockStcg;
            $stock['ltcg']                   = $stockLtcg;
            $stock['total_dividends']        = $dividends;
            $stock['total_gain']             = $unrealizedPnl + $realizedGain + $dividends;

            $holdings[] = $stock;

            // Accumulate portfolio aggregates
            $totalCurrentValue += $currentValue;
            $totalInvested     += $investedCost;
            $totalUnrealizedPnl += $unrealizedPnl;
            $totalRealizedPnl  += $realizedGain;
            $totalStcg         += $stockStcg;
            $totalLtcg         += $stockLtcg;
            $totalDividends    += $dividends;
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
                'total_current_value'      => $totalCurrentValue,
                'total_invested'           => $totalInvested,
                'total_unrealized_pnl'     => $totalUnrealizedPnl,
                'unrealized_pnl_percent'   => $overallUnrealizedPercent,
                'total_realized_pnl'       => $totalRealizedPnl,
                'total_stcg'               => $totalStcg,
                'total_ltcg'               => $totalLtcg,
                'total_dividends'          => $totalDividends,
                'total_net_gain'           => $totalUnrealizedPnl + $totalRealizedPnl + $totalDividends,
                'stocks_count'             => count($stocks),
                'active_holdings_count'    => count($activeHoldings),
                'past_holdings_count'      => count($pastHoldings),
            ],
        ];
    }

    /**
     * Find stock by symbol, exchange, and user.
     */
    public function findBySymbol(string $symbol, string $exchange, int $userId): ?array
    {
        return $this->where('user_id', $userId)
                    ->where('symbol', strtoupper(trim($symbol)))
                    ->where('exchange', strtoupper(trim($exchange)))
                    ->first();
    }
}

