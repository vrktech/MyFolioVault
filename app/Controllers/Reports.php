<?php

namespace App\Controllers;

use App\Models\BondCapitalGainsModel;
use App\Models\BondModel;
use App\Models\EquityCapitalGainsModel;
use App\Models\EquityModel;
use App\Models\EtfCapitalGainsModel;
use App\Models\EtfModel;
use App\Models\ExpenseModel;
use App\Models\MutualFundCapitalGainsModel;
use App\Models\MutualFundModel;
use App\Models\NpsAccountModel;
use App\Models\ReitInvitCapitalGainsModel;
use App\Models\ReitInvitModel;
use App\Services\ReportService;
use CodeIgniter\Database\BaseConnection;
use Exception;

class Reports extends BaseController
{
    protected BaseConnection $db;
    protected ReportService $reportService;

    public function __construct()
    {
        helper(['currency']);
        $this->db = \Config\Database::connect();
        $this->reportService = new ReportService($this->db);
    }

    /**
     * Resolve active financial year date range from request or defaults.
     */
    protected function getActiveDateRange(): array
    {
        return resolve_active_fy_range($this->request->getGet('fy'));
    }

    /**
     * Helper to apply date filtering on a DB Query Builder.
     */
    protected function applyDateFilter($builder, string $dateColumn, ?string $startDate, ?string $endDate)
    {
        if ($startDate !== null && $endDate !== null) {
            $builder->where("{$dateColumn} >=", $startDate)
                    ->where("{$dateColumn} <=", $endDate);
        }
        return $builder;
    }

    // =========================================================================
    // 1. CASH FLOW & CAPITAL ACTIVITY REPORT
    // =========================================================================

    public function getCashflowData(int $userId, array $range): array
    {
        return $this->reportService->getCashflowData($userId, $range);
    }

    public function cashflow()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getCashflowData($userId, $range);

        return view('reports/cashflow', array_merge([
            'title'     => 'Portfolio Cash Flow & Capital Activity Report - MyFolioVault',
            'activeTab' => 'cashflow',
            'range'     => $range,
        ], $data));
    }

    // =========================================================================
    // 2. CAPITAL GAINS & TAX REPORT (LTCG / STCG BY MODULE)
    // =========================================================================

    public function getTaxData(int $userId, array $range): array
    {
        return $this->reportService->getTaxData($userId, $range);
    }

    public function tax()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getTaxData($userId, $range);

        return view('reports/tax', array_merge([
            'title'     => 'Capital Gains Report (LTCG / STCG) - MyFolioVault',
            'activeTab' => 'tax',
            'range'     => $range,
        ], $data));
    }

    // =========================================================================
    // 3. PASSIVE INCOME & DISTRIBUTION REPORT
    // =========================================================================

    public function getIncomeData(int $userId, array $range): array
    {
        return $this->reportService->getIncomeData($userId, $range);
    }

    public function income()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getIncomeData($userId, $range);

        return view('reports/income', array_merge([
            'title'     => 'Passive Income & Distribution Report - MyFolioVault',
            'activeTab' => 'income',
            'range'     => $range,
        ], $data));
    }

    // =========================================================================
    // 4. EXPENSES & TRANSACTION FRICTION REPORT
    // =========================================================================

    public function getExpensesData(int $userId, array $range): array
    {
        return $this->reportService->getExpensesData($userId, $range);
    }

    public function expenses()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getExpensesData($userId, $range);

        return view('reports/expenses', array_merge([
            'title'     => 'Expenses & Statutory Friction Report - MyFolioVault',
            'activeTab' => 'expenses',
            'range'     => $range,
        ], $data));
    }

    // =========================================================================
    // 5. ASSET ALLOCATION & VALUATION SNAPSHOT
    // =========================================================================

    public function allocation()
    {
        $userId = $this->getUserId();

        $eqModel   = new EquityModel();
        $reitModel = new ReitInvitModel();
        $etfModel  = new EtfModel();
        $bondModel = new BondModel();
        $mfModel   = new MutualFundModel();
        $npsModel  = new NpsAccountModel();

        $eqSummary   = $eqModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
        $reitSummary = $reitModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
        $etfSummary  = $etfModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
        $bondSummary = $bondModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
        $mfSummary   = $mfModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
        $npsSummary  = $npsModel->getNpsPortfolio($userId)['summary'] ?? [];

        $allocations = [
            'equities' => [
                'key'            => 'equities',
                'name'           => 'Direct Equities',
                'icon'           => 'bi bi-graph-up-arrow',
                'color'          => 'primary',
                'invested'       => (float)($eqSummary['total_invested'] ?? 0),
                'current'        => (float)($eqSummary['total_current_value'] ?? 0),
                'unrealized'     => (float)($eqSummary['total_unrealized_pnl'] ?? 0),
                'unrealized_pct' => (float)($eqSummary['unrealized_pnl_percent'] ?? 0),
                'holdings_count' => (int)($eqSummary['active_holdings_count'] ?? 0),
                'count'          => (int)($eqSummary['active_holdings_count'] ?? 0),
                'url'            => base_url('equities'),
            ],
            'reits' => [
                'key'            => 'reits_invits',
                'name'           => 'InvITs & REITs',
                'icon'           => 'bi bi-buildings',
                'color'          => 'info',
                'invested'       => (float)($reitSummary['total_invested'] ?? 0),
                'current'        => (float)($reitSummary['total_current_value'] ?? 0),
                'unrealized'     => (float)($reitSummary['total_unrealized_pnl'] ?? 0),
                'unrealized_pct' => (float)($reitSummary['unrealized_pnl_percent'] ?? 0),
                'holdings_count' => (int)($reitSummary['active_holdings_count'] ?? 0),
                'count'          => (int)($reitSummary['active_holdings_count'] ?? 0),
                'url'            => base_url('reits-invits'),
            ],
            'etfs' => [
                'key'            => 'etfs',
                'name'           => 'ETFs',
                'icon'           => 'bi bi-pie-chart',
                'color'          => 'success',
                'invested'       => (float)($etfSummary['total_invested'] ?? 0),
                'current'        => (float)($etfSummary['total_current_value'] ?? 0),
                'unrealized'     => (float)($etfSummary['total_unrealized_pnl'] ?? 0),
                'unrealized_pct' => (float)($etfSummary['unrealized_pnl_percent'] ?? 0),
                'holdings_count' => (int)($etfSummary['active_holdings_count'] ?? 0),
                'count'          => (int)($etfSummary['active_holdings_count'] ?? 0),
                'url'            => base_url('etfs'),
            ],
            'bonds' => [
                'key'            => 'bonds',
                'name'           => 'Bonds & Fixed Income',
                'icon'           => 'bi bi-receipt',
                'color'          => 'danger',
                'invested'       => (float)($bondSummary['total_invested'] ?? 0),
                'current'        => (float)($bondSummary['total_current_value'] ?? 0),
                'unrealized'     => (float)($bondSummary['total_unrealized_pnl'] ?? 0),
                'unrealized_pct' => (float)($bondSummary['unrealized_pnl_percent'] ?? 0),
                'holdings_count' => (int)($bondSummary['active_holdings_count'] ?? 0),
                'count'          => (int)($bondSummary['active_holdings_count'] ?? 0),
                'url'            => base_url('bonds'),
            ],
            'mutual_funds' => [
                'key'            => 'mutual_funds',
                'name'           => 'Mutual Funds',
                'icon'           => 'bi bi-briefcase',
                'color'          => 'warning',
                'invested'       => (float)($mfSummary['total_invested'] ?? 0),
                'current'        => (float)($mfSummary['total_current_value'] ?? 0),
                'unrealized'     => (float)($mfSummary['total_unrealized_pnl'] ?? 0),
                'unrealized_pct' => (float)($mfSummary['unrealized_pnl_percent'] ?? 0),
                'holdings_count' => (int)($mfSummary['active_holdings_count'] ?? 0),
                'count'          => (int)($mfSummary['active_holdings_count'] ?? 0),
                'url'            => base_url('mutual-funds'),
            ],
            'nps' => [
                'key'            => 'nps',
                'name'           => 'NPS Tier 1',
                'icon'           => 'bi bi-shield-lock',
                'color'          => 'dark',
                'invested'       => (float)($npsSummary['total_invested'] ?? 0),
                'current'        => (float)($npsSummary['total_current_value'] ?? 0),
                'unrealized'     => (float)($npsSummary['total_unrealized_pnl'] ?? 0),
                'unrealized_pct' => (float)($npsSummary['unrealized_pnl_percent'] ?? 0),
                'holdings_count' => (int)($npsSummary['total_schemes'] ?? 0),
                'count'          => (int)($npsSummary['total_schemes'] ?? 0),
                'url'            => base_url('nps'),
            ],
        ];

        $totalInvested = array_sum(array_column($allocations, 'invested'));
        $totalCurrent  = array_sum(array_column($allocations, 'current'));
        $totalPnl      = $totalCurrent - $totalInvested;
        $totalPnlPct   = $totalInvested > 0 ? ($totalPnl / $totalInvested) * 100 : 0.0;

        foreach ($allocations as $k => $v) {
            $allocations[$k]['unrealized']      = $v['current'] - $v['invested'];
            $allocations[$k]['unrealized_pct']  = $v['invested'] > 0 ? (($v['current'] - $v['invested']) / $v['invested']) * 100 : 0.0;
            $allocations[$k]['pnl']             = $allocations[$k]['unrealized'];
            $allocations[$k]['pnl_pct']         = $allocations[$k]['unrealized_pct'];
            $allocations[$k]['share_pct']       = $totalCurrent > 0 ? ($v['current'] / $totalCurrent) * 100 : 0.0;
            $allocations[$k]['invest_share_pct']= $totalInvested > 0 ? ($v['invested'] / $totalInvested) * 100 : 0.0;
        }

        // Sector Allocation for Direct Equities
        $eqHoldings = $eqModel->getHoldingsWithMetrics($userId)['holdings'] ?? [];
        $equitiesSectorAllocation = [];
        foreach ($eqHoldings as $h) {
            $sec = !empty($h['sector']) ? $h['sector'] : (!empty($h['sector_name']) ? $h['sector_name'] : 'Diversified / Unclassified');
            if (!isset($equitiesSectorAllocation[$sec])) {
                $equitiesSectorAllocation[$sec] = [
                    'name'           => $sec,
                    'invested'       => 0.0,
                    'current'        => 0.0,
                    'count'          => 0,
                    'holdings_count' => 0,
                ];
            }
            $inv = (float)($h['invested_value'] ?? $h['invested_amount'] ?? 0);
            $cur = (float)($h['current_value'] ?? 0);
            $equitiesSectorAllocation[$sec]['invested'] += $inv;
            $equitiesSectorAllocation[$sec]['current']  += $cur;
            $equitiesSectorAllocation[$sec]['count']++;
            $equitiesSectorAllocation[$sec]['holdings_count']++;
        }

        foreach ($equitiesSectorAllocation as $sec => $data) {
            $unreal    = $data['current'] - $data['invested'];
            $unrealPct = $data['invested'] > 0 ? ($unreal / $data['invested']) * 100 : 0.0;
            $sharePct  = $totalCurrent > 0 ? ($data['current'] / $totalCurrent) * 100 : 0.0;
            $eqSharePct= (float)($eqSummary['total_current_value'] ?? 0) > 0 ? ($data['current'] / (float)$eqSummary['total_current_value']) * 100 : 0.0;

            $equitiesSectorAllocation[$sec]['unrealized']        = $unreal;
            $equitiesSectorAllocation[$sec]['unrealized_pct']    = $unrealPct;
            $equitiesSectorAllocation[$sec]['share_pct']         = $sharePct;
            $equitiesSectorAllocation[$sec]['equity_share_pct']  = $eqSharePct;
        }
        uasort($equitiesSectorAllocation, fn($a, $b) => $b['current'] <=> $a['current']);

        return view('reports/allocation', [
            'title'                    => 'Asset Allocation & Portfolio Valuation Snapshot - MyFolioVault',
            'activeTab'                => 'allocation',
            'allocations'              => $allocations,
            'totalInvested'            => $totalInvested,
            'totalCurrent'             => $totalCurrent,
            'totalPnl'                 => $totalPnl,
            'totalPnlPct'              => $totalPnlPct,
            'equitiesSectorAllocation' => $equitiesSectorAllocation,
        ]);
    }

    // =========================================================================
    // 6. CSV EXPORTS
    // =========================================================================

    public function exportCashflow()
    {
        $userId = $this->getUserId();
        $range  = $this->getActiveDateRange();
        $data   = $this->getCashflowData($userId, $range);
        $filename = 'myfoliovault_cashflow_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'MyFolioVault - Portfolio Cash Flow & Activity Report',
            'Reporting Period: ' . $range['label'],
            'Exported On: ' . date('d-M-Y H:i:s'),
        ];

        $headers = ['Module / Asset Class', 'Total Purchase (INR)', 'Total Sold (INR)', 'Total Income (INR)', 'Total Expense (INR)', 'Net Cashflow (INR)', 'Activity Count'];

        $rows = [];
        foreach ($data['modules'] as $m) {
            $rows[] = [
                $m['name'],
                $m['purchase'],
                $m['sold'],
                $m['income'],
                $m['expense'],
                $m['net_flow'],
                $m['activity'],
            ];
        }

        $t = $data['totals'];
        $rows[] = [
            'PORTFOLIO TOTAL',
            $t['purchase'],
            $t['sold'],
            $t['income'],
            $t['expense'],
            $t['net_flow'],
            $t['activity'],
        ];

        csv_download($filename, $headers, $rows, $metadata);
    }

    public function exportTax()
    {
        $userId = $this->getUserId();
        $range  = $this->getActiveDateRange();
        $data   = $this->getTaxData($userId, $range);
        $filename = 'myfoliovault_tax_capital_gains_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'MyFolioVault - Capital Gains & Tax Report (LTCG / STCG)',
            'Reporting Period: ' . $range['label'],
            'Exported On: ' . date('d-M-Y H:i:s'),
        ];

        $headers = ['Module / Asset Class', 'STCG (INR)', 'LTCG (INR)', 'Exempt Gains (INR)', 'Total Realized P&L (INR)', 'Matched Sales (INR)', 'Cost Basis (INR)', 'Tax Lots Count'];

        $rows = [];
        foreach ($data['modules'] as $m) {
            $rows[] = [
                $m['name'],
                $m['stcg'],
                $m['ltcg'],
                $m['exempt'],
                $m['total_gain'],
                $m['sales'],
                $m['cost'],
                $m['lots'],
            ];
        }

        $t = $data['totals'];
        $rows[] = [
            'CONSOLIDATED TOTAL',
            $t['stcg'],
            $t['ltcg'],
            $t['exempt'],
            $t['total_gain'],
            $t['sales'],
            $t['cost'],
            $t['lots'],
        ];

        csv_download($filename, $headers, $rows, $metadata);
    }

    public function exportIncome()
    {
        $userId = $this->getUserId();
        $range  = $this->getActiveDateRange();
        $data   = $this->getIncomeData($userId, $range);
        $filename = 'myfoliovault_passive_income_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'MyFolioVault - Passive Income & Distribution Report',
            'Reporting Period: ' . $range['label'],
            'Exported On: ' . date('d-M-Y H:i:s'),
        ];

        $headers = ['Module / Asset Class', 'Gross Income (INR)', 'TDS Withheld (INR)', 'Net Received (INR)', 'Payouts Count'];

        $rows = [
            ['Equities (Cash Dividends)', $data['eqTotals']['gross'], $data['eqTotals']['tds'], $data['eqTotals']['net'], $data['eqTotals']['count']],
            ['Bonds (Coupon Interest)', $data['bondTotals']['gross'], $data['bondTotals']['tds'], $data['bondTotals']['net'], $data['bondTotals']['count']],
            ['InvITs & REITs (Distributions)', $data['reitTotals']['gross'], $data['reitTotals']['tds'], $data['reitTotals']['net'], $data['reitTotals']['count']],
        ];

        $gt = $data['grandTotals'];
        $rows[] = ['TOTAL PASSIVE INCOME', $gt['gross'], $gt['tds'], $gt['net'], $gt['payouts']];

        csv_download($filename, $headers, $rows, $metadata);
    }

    public function exportExpenses()
    {
        $userId = $this->getUserId();
        $range  = $this->getActiveDateRange();
        $data   = $this->getExpensesData($userId, $range);
        $filename = 'myfoliovault_expenses_friction_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'MyFolioVault - Expenses & Statutory Friction Report',
            'Reporting Period: ' . $range['label'],
            'Exported On: ' . date('d-M-Y H:i:s'),
        ];

        $headers = ['Module / Asset Class', 'Brokerage (INR)', 'STT & Taxes (INR)', 'TDS Deducted (INR)', 'Platform Charges (INR)', 'Total Friction (INR)'];

        $rows = [];
        foreach ($data['moduleExpenses'] as $me) {
            $rows[] = [
                $me['name'],
                $me['brokerage'],
                $me['stt'],
                $me['tds'],
                $me['other'],
                $me['total'],
            ];
        }

        $ge = $data['grandExpenses'];
        $rows[] = [
            'TOTAL EXPENSES & FRICTION',
            $ge['brokerage'],
            $ge['stt'],
            $ge['tds'],
            $ge['other'],
            $ge['total'],
        ];

        csv_download($filename, $headers, $rows, $metadata);
    }
}

