<?php

namespace App\Models;

use CodeIgniter\Model;

class NpsAccountModel extends Model
{
    protected $table            = 'nps_accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'pran',
        'subscriber_name',
        'pfm_name',
        'investment_choice',
        'alloc_equity',
        'alloc_corporate_debt',
        'alloc_govt_bonds',
        'alloc_alternative',
        'scheme_code_equity',
        'scheme_code_corporate_debt',
        'scheme_code_govt_bonds',
        'scheme_code_alternative',
        'nav_last_updated',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get complete NPS Portfolio metrics for a user.
     * Consolidates Tier 1 PRAN, scheme portfolios (E, C, G, A),
     * contributions, quarterly unit deductions, and valuation.
     */
    public function getNpsPortfolio(int $userId): ?array
    {
        $account = $this->where('user_id', $userId)
                        ->where('status', 'ACTIVE')
                        ->first();

        if (!$account) {
            return null;
        }

        $accountId = (int) $account['id'];
        $db = \Config\Database::connect();

        // Scheme definitions
        $schemesMeta = [
            'SCHEME_E' => [
                'code'        => 'SCHEME_E',
                'scheme_code' => $account['scheme_code_equity'] ?? null,
                'name'        => "{$account['pfm_name']} - Scheme E (Equity)",
                'short_title' => 'Scheme E (Equity)',
                'badge'       => 'bg-primary-subtle text-primary border-primary-subtle',
                'target_pct'  => (float) $account['alloc_equity'],
            ],
            'SCHEME_C' => [
                'code'        => 'SCHEME_C',
                'scheme_code' => $account['scheme_code_corporate_debt'] ?? null,
                'name'        => "{$account['pfm_name']} - Scheme C (Corporate Debt)",
                'short_title' => 'Scheme C (Corporate Debt)',
                'badge'       => 'bg-info-subtle text-info-emphasis border-info-subtle',
                'target_pct'  => (float) $account['alloc_corporate_debt'],
            ],
            'SCHEME_G' => [
                'code'        => 'SCHEME_G',
                'scheme_code' => $account['scheme_code_govt_bonds'] ?? null,
                'name'        => "{$account['pfm_name']} - Scheme G (Govt Securities)",
                'short_title' => 'Scheme G (Govt Securities)',
                'badge'       => 'bg-success-subtle text-success border-success-subtle',
                'target_pct'  => (float) $account['alloc_govt_bonds'],
            ],
            'SCHEME_A' => [
                'code'        => 'SCHEME_A',
                'scheme_code' => $account['scheme_code_alternative'] ?? null,
                'name'        => "{$account['pfm_name']} - Scheme A (Alternative Assets)",
                'short_title' => 'Scheme A (Alternative)',
                'badge'       => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                'target_pct'  => (float) $account['alloc_alternative'],
            ],
        ];

        $schemeRows = [];
        $totalCurrentValue  = 0.0;
        $totalInvested      = 0.0;
        $totalDeductedUnits = 0.0;
        $totalDeductedValue = 0.0;

        foreach ($schemesMeta as $schemeKey => $meta) {
            // Aggregate all units for this scheme
            $unitRows = $db->table('nps_scheme_units')
                ->where('nps_account_id', $accountId)
                ->where('scheme_type', $schemeKey)
                ->orderBy('transaction_date', 'ASC')
                ->get()
                ->getResultArray();

            $grossBoughtUnits = 0.0;
            $deductedUnits    = 0.0;
            $investedAmount   = 0.0;
            $latestNav        = 10.0000;
            $navDate          = null;

            foreach ($unitRows as $u) {
                $unitsVal = (float) $u['units'];
                if ($u['transaction_type'] === 'CONTRIBUTION') {
                    $grossBoughtUnits += $unitsVal;
                    $investedAmount   += (float) $u['allocated_amount'];
                } elseif ($u['transaction_type'] === 'UNIT_DEDUCTION') {
                    // units are stored as negative (or absolute deduction)
                    $deductedUnits += abs($unitsVal);
                }

                // Track the latest current_nav recorded
                if (!empty($u['current_nav'])) {
                    $latestNav = (float) $u['current_nav'];
                    $navDate   = $u['nav_date'] ?? $u['transaction_date'];
                }
            }

            // Net active units = purchased units minus quarterly fee units deducted
            $netActiveUnits = max(0.0, $grossBoughtUnits - $deductedUnits);
            $currentVal     = $netActiveUnits * $latestNav;
            $avgNav         = $grossBoughtUnits > 0 ? ($investedAmount / $grossBoughtUnits) : 0.0;
            $unrealizedPnl  = $currentVal - $investedAmount;
            $returnPct      = $investedAmount > 0 ? (($unrealizedPnl / $investedAmount) * 100) : 0.0;
            $deductedVal    = $deductedUnits * $latestNav;

            $meta['active_units']       = $netActiveUnits;
            $meta['gross_units']        = $grossBoughtUnits;
            $meta['deducted_units']     = $deductedUnits;
            $meta['deducted_value']     = $deductedVal;
            $meta['invested_amount']    = $investedAmount;
            $meta['avg_nav']            = $avgNav;
            $meta['current_nav']        = $latestNav;
            $meta['nav_date']           = $navDate;
            $meta['current_value']      = $currentVal;
            $meta['unrealized_pnl']     = $unrealizedPnl;
            $meta['return_pct']         = $returnPct;

            $schemeRows[$schemeKey] = $meta;

            $totalCurrentValue  += $currentVal;
            $totalInvested      += $investedAmount;
            $totalDeductedUnits += $deductedUnits;
            $totalDeductedValue += $deductedVal;
        }

        // Calculate actual portfolio percentage weight of each scheme
        foreach ($schemeRows as $k => &$row) {
            $row['actual_pct'] = $totalCurrentValue > 0 ? (($row['current_value'] / $totalCurrentValue) * 100) : 0.0;
        }

        $totalPnl       = $totalCurrentValue - $totalInvested;
        $overallReturn  = $totalInvested > 0 ? (($totalPnl / $totalInvested) * 100) : 0.0;

        return [
            'account' => $account,
            'schemes' => $schemeRows,
            'summary' => [
                'total_current_value'   => $totalCurrentValue,
                'total_invested'        => $totalInvested,
                'total_pnl'             => $totalPnl,
                'overall_return_pct'    => $overallReturn,
                'total_deducted_units'  => $totalDeductedUnits,
                'total_deducted_value'  => $totalDeductedValue,
            ],
        ];
    }
}

