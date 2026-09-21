<?php

if (!function_exists('format_inr')) {
    /**
     * Format a number into the Indian Rupee system (e.g. 12,34,567.89).
     */
    function format_inr(float|int|string|null $number, int $decimals = 2, bool $showSymbol = true): string
    {
        if ($number === null || $number === '') {
            $number = 0.0;
        }

        $num = (float) $number;
        $isNegative = $num < 0;
        $num = abs($num);

        $formattedNumber = number_format($num, $decimals, '.', '');
        $parts = explode('.', $formattedNumber);
        $intPart = $parts[0];
        $decPart = isset($parts[1]) ? '.' . $parts[1] : '';

        // Indian comma grouping: last 3 digits, then groups of 2
        if (strlen($intPart) > 3) {
            $lastThree = substr($intPart, -3);
            $rest = substr($intPart, 0, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $intPart = $rest . ',' . $lastThree;
        }

        $result = $intPart . $decPart;
        $sign = $isNegative ? '-' : '';
        $symbol = $showSymbol ? '₹ ' : '';

        return $sign . $symbol . $result;
    }
}

if (!function_exists('format_inr_short')) {
    /**
     * Format into compact Indian denominations (Crores, Lakhs, Thousands).
     */
    function format_inr_short(float|int|string|null $number): string
    {
        if ($number === null || $number === '') {
            $number = 0.0;
        }

        $num = (float) $number;
        $isNegative = $num < 0;
        $num = abs($num);
        $sign = $isNegative ? '-' : '';

        if ($num >= 10000000) {
            return $sign . '₹ ' . number_format($num / 10000000, 2) . ' Cr';
        }
        if ($num >= 100000) {
            return $sign . '₹ ' . number_format($num / 100000, 2) . ' L';
        }
        if ($num >= 1000) {
            return $sign . '₹ ' . number_format($num / 1000, 1) . ' K';
        }

        return $sign . '₹ ' . number_format($num, 2);
    }
}

if (!function_exists('format_pnl')) {
    /**
     * Render a color-coded P&L badge/element with Rupee symbol and return percentage.
     */
    function format_pnl(float|int|string|null $amount, ?float $percent = null, bool $badge = false): string
    {
        $val = (float) ($amount ?? 0);
        $pctText = $percent !== null ? ' (' . ($percent >= 0 ? '+' : '') . number_format($percent, 2) . '%)' : '';
        $formatted = format_inr(abs($val));

        if ($val > 0) {
            $text = '+' . $formatted . $pctText;
            if ($badge) {
                return '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-arrow-up-right me-1"></i>' . $text . '</span>';
            }
            return '<span class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>' . $text . '</span>';
        }

        if ($val < 0) {
            $text = '-' . $formatted . $pctText;
            if ($badge) {
                return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-arrow-down-right me-1"></i>' . $text . '</span>';
            }
            return '<span class="text-danger fw-semibold"><i class="bi bi-arrow-down-right me-1"></i>' . $text . '</span>';
        }

        $text = $formatted . ($percent !== null ? ' (0.00%)' : '');
        if ($badge) {
            return '<span class="badge bg-light text-secondary border px-2 py-1">' . $text . '</span>';
        }
        return '<span class="text-secondary fw-semibold">' . $text . '</span>';
    }
}

if (!function_exists('get_financial_year')) {
    /**
     * Determine the Financial Year string (e.g. FY 2025-26) for a given date and FY start month.
     */
    function get_financial_year(?string $dateStr = null, int $fyStartMonth = 4): string
    {
        $timestamp = $dateStr ? strtotime($dateStr) : time();
        $month = (int) date('n', $timestamp);
        $year = (int) date('Y', $timestamp);

        if ($fyStartMonth === 1) {
            // Calendar year
            return 'CY ' . $year;
        }

        if ($month >= $fyStartMonth) {
            $startYear = $year;
            $endYear = substr((string) ($year + 1), -2);
        } else {
            $startYear = $year - 1;
            $endYear = substr((string) $year, -2);
        }

        return 'FY ' . $startYear . '-' . $endYear;
    }
}

if (!function_exists('get_fy_ranges')) {
    /**
     * Return date boundaries and labels for Current FY and Last FY based on user's FY start month.
     */
    function get_fy_ranges(int $fyStartMonth = 4): array
    {
        $now = time();
        $month = (int) date('n', $now);
        $year = (int) date('Y', $now);

        if ($month >= $fyStartMonth) {
            $currentStartYear = $year;
        } else {
            $currentStartYear = $year - 1;
        }
        $lastStartYear = $currentStartYear - 1;

        $currentStart = sprintf('%04d-%02d-01', $currentStartYear, $fyStartMonth);
        $currentEnd = date('Y-m-d', strtotime("+1 year -1 day", strtotime($currentStart)));

        $lastStart = sprintf('%04d-%02d-01', $lastStartYear, $fyStartMonth);
        $lastEnd = date('Y-m-d', strtotime("+1 year -1 day", strtotime($lastStart)));

        return [
            'current' => [
                'label' => get_financial_year($currentStart, $fyStartMonth),
                'start' => $currentStart,
                'end'   => $currentEnd,
            ],
            'last' => [
                'label' => get_financial_year($lastStart, $fyStartMonth),
                'start' => $lastStart,
                'end'   => $lastEnd,
            ],
        ];
    }
}


