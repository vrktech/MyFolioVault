<?php
/**
 * MyFolioVault - Financial & Portfolio Calculation Helpers
 *
 * Centralized math and statutory financial functions for capital markets,
 * P&L metrics, FIFO holding duration, and tax classification.
 */

if (!function_exists('calculate_pnl_metrics')) {
    /**
     * Calculate unrealized/realized P&L absolute amount and percentage.
     *
     * @param float|int|string|null $currentValue Current valuation or exit proceeds
     * @param float|int|string|null $investedCost Total acquisition cost
     * @return array{pnl: float, pnl_percent: float}
     */
    function calculate_pnl_metrics(float|int|string|null $currentValue, float|int|string|null $investedCost): array
    {
        $current  = (float) ($currentValue ?? 0);
        $invested = (float) ($investedCost ?? 0);

        $pnl = $current - $invested;
        $pnlPercent = $invested > 0 ? (($pnl / $invested) * 100) : 0.0;

        return [
            'pnl'         => $pnl,
            'pnl_percent' => round($pnlPercent, 4),
        ];
    }
}

if (!function_exists('calculate_holding_days')) {
    /**
     * Compute holding period in calendar days between two dates.
     *
     * @param string|int|null $startDate Buy / Allotment date
     * @param string|int|null $endDate   Sell / Transfer date
     * @return int Holding period in days (>= 0)
     */
    function calculate_holding_days(string|int|null $startDate, string|int|null $endDate): int
    {
        if (empty($startDate) || empty($endDate)) {
            return 0;
        }

        $startTs = is_numeric($startDate) ? (int) $startDate : strtotime((string) $startDate);
        $endTs   = is_numeric($endDate)   ? (int) $endDate   : strtotime((string) $endDate);

        if ($startTs === false || $endTs === false) {
            return 0;
        }

        return max(0, (int) round(($endTs - $startTs) / 86400));
    }
}

if (!function_exists('classify_gain_type')) {
    /**
     * Classify capital gain as STCG or LTCG according to Indian Income Tax rules.
     *
     * Default threshold for listed equities, equity mutual funds, ETFs, and REITs/InvITs is 365 days (12 months).
     *
     * @param int $holdingDays Duration in calendar days
     * @param int $thresholdDays Threshold in days (365 for equities/REITs/ETFs)
     * @return string 'STCG' or 'LTCG'
     */
    function classify_gain_type(int $holdingDays, int $thresholdDays = 365): string
    {
        return $holdingDays > $thresholdDays ? 'LTCG' : 'STCG';
    }
}

if (!function_exists('calculate_weighted_avg_price')) {
    /**
     * Calculate updated Weighted Average Price (WAP) upon lot addition.
     *
     * @param float|int $currentQty Existing units held
     * @param float|int $currentWap Existing average purchase price
     * @param float|int $newQty     New units added
     * @param float|int $newPrice   Price per unit of new tranche
     * @return float New blended weighted average price
     */
    function calculate_weighted_avg_price(
        float|int $currentQty,
        float|int $currentWap,
        float|int $newQty,
        float|int $newPrice
    ): float {
        $totalQty = (float) $currentQty + (float) $newQty;
        if ($totalQty <= 0) {
            return 0.0;
        }

        $totalCost = ((float) $currentQty * (float) $currentWap) + ((float) $newQty * (float) $newPrice);
        return round($totalCost / $totalQty, 4);
    }
}

if (!function_exists('apportion_charges')) {
    /**
     * Apportion brokerage, STT, or other transaction friction proportionately to a matched lot.
     *
     * @param float     $totalCharges Total order-level charges
     * @param float|int $matchedQty   Quantity matched in the specific FIFO lot
     * @param float|int $totalSellQty Total order quantity
     * @return float Proportionate charges allocated to the lot
     */
    function apportion_charges(float $totalCharges, float|int $matchedQty, float|int $totalSellQty): float
    {
        $totalQty = (float) $totalSellQty;
        if ($totalQty <= 0 || $totalCharges <= 0) {
            return 0.0;
        }

        return round($totalCharges * ((float) $matchedQty / $totalQty), 2);
    }
}

if (!function_exists('resolve_active_fy_range')) {
    /**
     * Resolve active financial year date range from request or defaults.
     *
     * @param string|null $requestedKey Selected range key ('CURRENT_FY', 'LAST_FY', 'ALL')
     * @return array{key: string, label: string, shortLabel: string, startDate: string|null, endDate: string|null, fyRanges: array}
     */
    function resolve_active_fy_range(?string $requestedKey = null): array
    {
        $fyRanges = get_fy_ranges();
        $selectedRange = strtoupper(trim((string) ($requestedKey ?? (request()->getGet('fy') ?? 'CURRENT_FY'))));

        if ($selectedRange === 'LAST_FY') {
            return [
                'key'        => 'LAST_FY',
                'label'      => 'Last FY (' . $fyRanges['last']['label'] . ')',
                'shortLabel' => $fyRanges['last']['label'],
                'startDate'  => $fyRanges['last']['start'],
                'endDate'    => $fyRanges['last']['end'],
                'fyRanges'   => $fyRanges,
            ];
        }

        if ($selectedRange === 'ALL') {
            return [
                'key'        => 'ALL',
                'label'      => 'All Time (Full Portfolio History)',
                'shortLabel' => 'All Time',
                'startDate'  => null,
                'endDate'    => null,
                'fyRanges'   => $fyRanges,
            ];
        }

        // Default to Current FY
        return [
            'key'        => 'CURRENT_FY',
            'label'      => 'Current FY (' . $fyRanges['current']['label'] . ')',
            'shortLabel' => $fyRanges['current']['label'],
            'startDate'  => $fyRanges['current']['start'],
            'endDate'    => $fyRanges['current']['end'],
            'fyRanges'   => $fyRanges,
        ];
    }
}

if (!function_exists('get_portfolio_modules_config')) {
    /**
     * Centralized metadata configuration for all 6 investment modules.
     *
     * @return array<string, array{key: string, title: string, subtitle: string, icon: string, color: string, unit_label: string, url: string, new_url: string}>
     */
    function get_portfolio_modules_config(): array
    {
        return [
            'equities' => [
                'key'        => 'equities',
                'title'      => 'Equities',
                'subtitle'   => 'Direct Equity Stocks (NSE/BSE)',
                'icon'       => 'bi bi-graph-up-arrow',
                'color'      => 'primary',
                'unit_label' => 'Stocks',
                'url'        => base_url('equities'),
                'new_url'    => base_url('equities/new'),
            ],
            'reits_invits' => [
                'key'        => 'reits_invits',
                'title'      => 'InvITs & REITs',
                'subtitle'   => 'Real Estate & Infra Trusts',
                'icon'       => 'bi bi-buildings',
                'color'      => 'info',
                'unit_label' => 'Trusts',
                'url'        => base_url('reits-invits'),
                'new_url'    => base_url('reits-invits/new'),
            ],
            'etfs' => [
                'key'        => 'etfs',
                'title'      => 'Exchange Traded Funds',
                'subtitle'   => 'Index, Gold, Silver & Liquid ETFs',
                'icon'       => 'bi bi-pie-chart',
                'color'      => 'success',
                'unit_label' => 'ETFs',
                'url'        => base_url('etfs'),
                'new_url'    => base_url('etfs/new'),
            ],
            'bonds' => [
                'key'        => 'bonds',
                'title'      => 'Bonds & Fixed Income',
                'subtitle'   => 'SGBs, G-Secs, SDLs & Corporate NCDs',
                'icon'       => 'bi bi-receipt',
                'color'      => 'danger',
                'unit_label' => 'Bonds',
                'url'        => base_url('bonds'),
                'new_url'    => base_url('bonds/new'),
            ],
            'mutual_funds' => [
                'key'        => 'mutual_funds',
                'title'      => 'Mutual Funds',
                'subtitle'   => 'Direct/Regular Equity & Debt Folios',
                'icon'       => 'bi bi-briefcase',
                'color'      => 'warning',
                'unit_label' => 'Funds',
                'url'        => base_url('mutual-funds'),
                'new_url'    => base_url('mutual-funds/new'),
            ],
            'nps' => [
                'key'        => 'nps',
                'title'      => 'NPS (Tier 1)',
                'subtitle'   => 'National Pension System Pension Account',
                'icon'       => 'bi bi-shield-check',
                'color'      => 'secondary',
                'unit_label' => 'Account',
                'url'        => base_url('nps'),
                'new_url'    => base_url('nps/new-account'),
            ],
        ];
    }
}


