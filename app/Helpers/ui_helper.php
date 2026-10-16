<?php
/**
 * MyFolioVault - UI & Formatting Helpers
 *
 * Provides reusable badge renderers, financial indicators, and UI components
 * across dashboard tables and summary views.
 */

if (!function_exists('category_badge')) {
    /**
     * Render a styled Bootstrap badge for asset categories.
     *
     * @param string $category Asset category (e.g. 'Commodity - Gold', 'SGB', 'REIT', 'Index')
     * @param string $module   Module context ('ETF', 'BOND', 'TRUST')
     * @return string HTML badge markup
     */
    function category_badge(string $category, string $module = 'ETF'): string
    {
        $category = trim($category);

        if (strtoupper($module) === 'BOND') {
            $catBadge = match($category) {
                'SGB'            => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                'GOVT_SECURITY'  => 'bg-success-subtle text-success border-success-subtle',
                'CORPORATE_NCD'  => 'bg-primary-subtle text-primary border-primary-subtle',
                'TAX_FREE'       => 'bg-info-subtle text-info-emphasis border-info-subtle',
                default          => 'bg-light text-secondary border',
            };
            $catLabel = match($category) {
                'SGB'            => 'SGB Gold',
                'GOVT_SECURITY'  => 'G-Sec',
                'CORPORATE_NCD'  => 'Corp NCD',
                'TAX_FREE'       => 'Tax-Free',
                default          => esc($category),
            };

            return '<span class="badge ' . $catBadge . ' border px-1.5 py-0.5" style="font-size: 0.65rem;">' . $catLabel . '</span>';
        }

        if (strtoupper($module) === 'TRUST') {
            $isReit = (strtoupper($category) === 'REIT');
            $badgeClass = $isReit
                ? 'bg-info-subtle text-info-emphasis border-info-subtle'
                : 'bg-primary-subtle text-primary border-primary-subtle';
            $icon = $isReit ? 'bi-building' : 'bi-broadcast-pin';
            $label = $isReit ? 'REIT' : 'InvIT';

            return '<span class="badge ' . $badgeClass . ' border px-2 py-1" style="font-size: 0.72rem;"><i class="bi ' . $icon . ' me-1"></i>' . $label . '</span>';
        }

        // Default to ETF categories
        $catBadge = match($category) {
            'Commodity - Gold'   => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
            'Commodity - Silver' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
            'Index'              => 'bg-primary-subtle text-primary border-primary-subtle',
            'Global'             => 'bg-info-subtle text-info-emphasis border-info-subtle',
            'Sectoral'           => 'bg-dark-subtle text-dark border-dark-subtle',
            default              => 'bg-light text-secondary border',
        };

        return '<span class="badge ' . $catBadge . ' border px-2 py-1 small">' . esc($category) . '</span>';
    }
}

if (!function_exists('gain_loss_badge')) {
    /**
     * Render a color-coded profit or loss pill.
     *
     * @param float $amount   Realized or unrealized gain in INR
     * @param float|null $percent Return percentage
     * @return string HTML badge markup
     */
    function gain_loss_badge(float $amount, ?float $percent = null): string
    {
        $isPositive = ($amount >= 0);
        $badgeClass = $isPositive ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle';
        $sign       = $isPositive ? '+' : '';

        $text = $sign . format_inr($amount);
        if ($percent !== null) {
            $text .= ' (' . $sign . number_format($percent, 2) . '%)';
        }

        return '<span class="badge ' . $badgeClass . ' border fw-semibold px-2 py-1">' . $text . '</span>';
    }
}

