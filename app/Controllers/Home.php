<?php

namespace App\Controllers;

use App\Models\BondModel;
use App\Models\EquityModel;
use App\Models\EtfModel;
use App\Models\ExpenseModel;
use App\Models\MutualFundModel;
use App\Models\NpsAccountModel;
use App\Models\ReitInvitModel;
use CodeIgniter\CodeIgniter;
use Config\Database;

class Home extends BaseController
{
    public function index(): string
    {
        $session = session();
        $userId  = (int)($session->get('userId') ?? 0);

        helper(['currency']);

        $modules = [];
        $totalInvested = 0.0;
        $totalCurrent  = 0.0;
        $totalHoldings = 0;

        if ($userId > 0) {
            $eqModel   = new EquityModel();
            $reitModel = new ReitInvitModel();
            $etfModel  = new EtfModel();
            $bondModel = new BondModel();
            $mfModel   = new MutualFundModel();
            $npsModel  = new NpsAccountModel();

            $eqData      = $eqModel->getHoldingsWithMetrics($userId);
            $eqSummary   = $eqData['summary'] ?? [];

            $reitSummary = $reitModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
            $etfSummary  = $etfModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
            $bondSummary = $bondModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
            $mfSummary   = $mfModel->getHoldingsWithMetrics($userId)['summary'] ?? [];
            $npsData     = $npsModel->getNpsPortfolio($userId);
            $npsSummary  = $npsData['summary'] ?? [];

            // Standard Module Order
            $modules = [
                'equities' => [
                    'key'            => 'equities',
                    'title'          => 'Equities',
                    'subtitle'       => 'Direct Equity Stocks (NSE/BSE)',
                    'icon'           => 'bi bi-graph-up-arrow',
                    'color'          => 'primary',
                    'invested'       => (float)($eqSummary['total_invested'] ?? 0),
                    'current'        => (float)($eqSummary['total_current_value'] ?? 0),
                    'unrealized'     => (float)($eqSummary['total_unrealized_pnl'] ?? 0),
                    'unrealized_pct' => (float)($eqSummary['unrealized_pnl_percent'] ?? 0),
                    'holdings_count' => (int)($eqSummary['active_holdings_count'] ?? 0),
                    'url'            => base_url('equities'),
                    'new_url'        => base_url('equities/new'),
                    'unit_label'     => 'Stocks',
                ],
                'reits_invits' => [
                    'key'            => 'reits_invits',
                    'title'          => 'InvITs & REITs',
                    'subtitle'       => 'Real Estate & Infra Trusts',
                    'icon'           => 'bi bi-buildings',
                    'color'          => 'info',
                    'invested'       => (float)($reitSummary['total_invested'] ?? 0),
                    'current'        => (float)($reitSummary['total_current_value'] ?? 0),
                    'unrealized'     => (float)($reitSummary['total_unrealized_pnl'] ?? 0),
                    'unrealized_pct' => (float)($reitSummary['unrealized_pnl_percent'] ?? 0),
                    'holdings_count' => (int)($reitSummary['active_holdings_count'] ?? 0),
                    'url'            => base_url('reits-invits'),
                    'new_url'        => base_url('reits-invits/new'),
                    'unit_label'     => 'Trusts',
                ],
                'etfs' => [
                    'key'            => 'etfs',
                    'title'          => 'Exchange Traded Funds',
                    'subtitle'       => 'Index, Gold, Silver & Liquid ETFs',
                    'icon'           => 'bi bi-pie-chart',
                    'color'          => 'success',
                    'invested'       => (float)($etfSummary['total_invested'] ?? 0),
                    'current'        => (float)($etfSummary['total_current_value'] ?? 0),
                    'unrealized'     => (float)($etfSummary['total_unrealized_pnl'] ?? 0),
                    'unrealized_pct' => (float)($etfSummary['unrealized_pnl_percent'] ?? 0),
                    'holdings_count' => (int)($etfSummary['active_holdings_count'] ?? 0),
                    'url'            => base_url('etfs'),
                    'new_url'        => base_url('etfs/new'),
                    'unit_label'     => 'ETFs',
                ],
                'bonds' => [
                    'key'            => 'bonds',
                    'title'          => 'Bonds & Fixed Income',
                    'subtitle'       => 'SGBs, G-Secs, SDLs & Corporate NCDs',
                    'icon'           => 'bi bi-receipt',
                    'color'          => 'danger',
                    'invested'       => (float)($bondSummary['total_invested'] ?? 0),
                    'current'        => (float)($bondSummary['total_current_value'] ?? 0),
                    'unrealized'     => (float)($bondSummary['total_unrealized_pnl'] ?? 0),
                    'unrealized_pct' => (float)($bondSummary['unrealized_pnl_percent'] ?? 0),
                    'holdings_count' => (int)($bondSummary['active_holdings_count'] ?? 0),
                    'url'            => base_url('bonds'),
                    'new_url'        => base_url('bonds/new'),
                    'unit_label'     => 'Bonds',
                ],
                'mutual_funds' => [
                    'key'            => 'mutual_funds',
                    'title'          => 'Mutual Funds',
                    'subtitle'       => 'Direct/Regular Equity & Debt Folios',
                    'icon'           => 'bi bi-briefcase',
                    'color'          => 'warning',
                    'invested'       => (float)($mfSummary['total_invested'] ?? 0),
                    'current'        => (float)($mfSummary['total_current_value'] ?? 0),
                    'unrealized'     => (float)($mfSummary['total_unrealized_pnl'] ?? 0),
                    'unrealized_pct' => (float)($mfSummary['unrealized_pnl_percent'] ?? 0),
                    'holdings_count' => (int)($mfSummary['active_holdings_count'] ?? 0),
                    'url'            => base_url('mutual-funds'),
                    'new_url'        => base_url('mutual-funds/new'),
                    'unit_label'     => 'Funds',
                ],
                'nps' => [
                    'key'            => 'nps',
                    'title'          => 'NPS (Tier 1)',
                    'subtitle'       => 'National Pension System Pension Account',
                    'icon'           => 'bi bi-shield-check',
                    'color'          => 'secondary',
                    'invested'       => (float)($npsSummary['total_invested'] ?? 0),
                    'current'        => (float)($npsSummary['total_current_value'] ?? 0),
                    'unrealized'     => (float)($npsSummary['total_pnl'] ?? 0),
                    'unrealized_pct' => (float)($npsSummary['overall_return_pct'] ?? 0),
                    'holdings_count' => !empty($npsData['account']) ? 1 : 0,
                    'url'            => base_url('nps'),
                    'new_url'        => base_url('nps/new-account'),
                    'unit_label'     => 'Account',
                ],
            ];

            $expenseModel = new ExpenseModel();
            $expensesSummary = $expenseModel->getExpensesSummary($userId);

            foreach ($modules as $m) {
                $totalInvested += $m['invested'];
                $totalCurrent  += $m['current'];
                $totalHoldings += $m['holdings_count'];
            }
        } else {
            $expensesSummary = [
                'total_count'     => 0,
                'total_amount'    => 0.0,
                'total_brokerage' => 0.0,
                'total_stt'       => 0.0,
                'total_platform'  => 0.0,
            ];
        }

        $totalPnl    = $totalCurrent - $totalInvested;
        $totalPnlPct = $totalInvested > 0 ? (($totalPnl / $totalInvested) * 100) : 0.0;

        // Fetch DB & System Info
        $db = Database::connect();
        $dbVersion = 'Unknown';
        $dbDriver  = 'MySQLi';
        try {
            $dbVersion = $db->getVersion();
            $dbDriver  = $db->DBDriver;
        } catch (\Throwable $e) {
            // Silently fallback if connection issue
        }

        $systemInfo = [
            'ci_version'    => CodeIgniter::CI_VERSION,
            'php_version'   => PHP_VERSION,
            'db_driver'     => $dbDriver,
            'db_version'    => $dbVersion,
            'environment'   => ENVIRONMENT,
            'timezone'      => date_default_timezone_get(),
            'currency'      => 'INR (₹)',
            'last_update'   => '2026-09-23',
            'server_os'     => PHP_OS_FAMILY . ' (' . php_uname('s') . ')',
        ];

        return view('welcome', [
            'title'           => 'Welcome - MyFolioVault',
            'userName'        => $session->get('userName') ?? 'Investor',
            'userEmail'       => $session->get('userEmail') ?? '',
            'modules'         => $modules,
            'totalInvested'   => $totalInvested,
            'totalCurrent'    => $totalCurrent,
            'totalPnl'        => $totalPnl,
            'totalPnlPct'     => $totalPnlPct,
            'totalHoldings'   => $totalHoldings,
            'expensesSummary' => $expensesSummary,
            'systemInfo'      => $systemInfo,
        ]);
    }

    /**
     * Export all active portfolio holdings across all 6 asset classes to CSV.
     */
    public function exportAllHoldings()
    {
        $session = session();
        $userId  = (int)($session->get('userId') ?? 0);

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $eqModel   = new EquityModel();
        $reitModel = new ReitInvitModel();
        $etfModel  = new EtfModel();
        $bondModel = new BondModel();
        $mfModel   = new MutualFundModel();
        $npsModel  = new NpsAccountModel();

        $headers = [
            'Asset Class',
            'Symbol / Code',
            'Asset Name',
            'Category / Sector',
            'Identifier / Folio / ISIN',
            'Quantity / Units',
            'Avg Buy Price / NAV (INR)',
            'Current Price / NAV (INR)',
            'Invested Value (INR)',
            'Current Value (INR)',
            'Unrealized P&L (INR)',
            'Return (%)'
        ];

        $rows = [];
        $totalInvested = 0.0;
        $totalCurrent  = 0.0;
        $totalPnl      = 0.0;

        // 1. Equities
        $eqData = $eqModel->getHoldingsWithMetrics($userId);
        $activeEquities = $eqData['active_holdings'] ?? [];
        foreach ($activeEquities as $h) {
            $qty = (int)($h['active_quantity'] ?? 0);
            if ($qty <= 0) continue;
            $invested = (float)($h['invested_value'] ?? 0);
            $current  = (float)($h['current_value'] ?? 0);
            $pnl      = (float)($h['unrealized_pnl'] ?? 0);
            $totalInvested += $invested;
            $totalCurrent  += $current;
            $totalPnl      += $pnl;

            $rows[] = [
                'Equities',
                $h['symbol'] ?? '',
                $h['company_name'] ?? '',
                $h['sector'] ?: '—',
                $h['exchange'] ?? 'NSE',
                $qty,
                (float)($h['avg_buy_price'] ?? 0),
                (float)($h['current_price'] ?? 0),
                $invested,
                $current,
                $pnl,
                (float)($h['unrealized_pnl_percent'] ?? 0),
            ];
        }

        // 2. REITs & InvITs
        $reitData = $reitModel->getHoldingsWithMetrics($userId);
        $activeReits = $reitData['active_holdings'] ?? [];
        foreach ($activeReits as $h) {
            $units = (int)($h['active_units'] ?? 0);
            if ($units <= 0) continue;
            $invested = (float)($h['invested_value'] ?? 0);
            $current  = (float)($h['current_value'] ?? 0);
            $pnl      = (float)($h['unrealized_pnl'] ?? 0);
            $totalInvested += $invested;
            $totalCurrent  += $current;
            $totalPnl      += $pnl;

            $rows[] = [
                'REITs & InvITs',
                $h['symbol'] ?? '',
                $h['trust_name'] ?? '',
                $h['trust_type'] ?? 'REIT',
                $h['exchange'] ?? 'NSE',
                $units,
                (float)($h['avg_buy_price'] ?? 0),
                (float)($h['current_price'] ?? 0),
                $invested,
                $current,
                $pnl,
                (float)($h['unrealized_pnl_percent'] ?? 0),
            ];
        }

        // 3. ETFs
        $etfData = $etfModel->getHoldingsWithMetrics($userId);
        $activeEtfs = $etfData['active_holdings'] ?? [];
        foreach ($activeEtfs as $h) {
            $qty = (int)($h['active_quantity'] ?? 0);
            if ($qty <= 0) continue;
            $invested = (float)($h['invested_value'] ?? 0);
            $current  = (float)($h['current_value'] ?? 0);
            $pnl      = (float)($h['unrealized_pnl'] ?? 0);
            $totalInvested += $invested;
            $totalCurrent  += $current;
            $totalPnl      += $pnl;

            $rows[] = [
                'ETFs',
                $h['symbol'] ?? '',
                $h['etf_name'] ?? '',
                $h['category'] ?? '—',
                $h['exchange'] ?? 'NSE',
                $qty,
                (float)($h['avg_buy_price'] ?? 0),
                (float)($h['current_price'] ?? 0),
                $invested,
                $current,
                $pnl,
                (float)($h['unrealized_pnl_percent'] ?? 0),
            ];
        }

        // 4. Bonds
        $bondData = $bondModel->getHoldingsWithMetrics($userId);
        $activeBonds = $bondData['active_holdings'] ?? [];
        foreach ($activeBonds as $h) {
            $qty = (float)($h['active_quantity'] ?? 0);
            if ($qty <= 0) continue;
            $invested = (float)($h['invested_value'] ?? 0);
            $current  = (float)($h['current_value'] ?? 0);
            $pnl      = (float)($h['unrealized_pnl'] ?? 0);
            $totalInvested += $invested;
            $totalCurrent  += $current;
            $totalPnl      += $pnl;

            $rows[] = [
                'Bonds',
                $h['bond_symbol'] ?: ($h['isin'] ?? ''),
                $h['bond_name'] ?? '',
                $h['category'] ?? '',
                $h['isin'] ?? '',
                $qty,
                (float)($h['avg_buy_price'] ?? 0),
                (float)($h['current_market_price'] ?? $h['face_value'] ?? 0),
                $invested,
                $current,
                $pnl,
                (float)($h['unrealized_pnl_percent'] ?? 0),
            ];
        }

        // 5. Mutual Funds
        $mfData = $mfModel->getHoldingsWithMetrics($userId);
        $activeMfs = $mfData['active_holdings'] ?? [];
        foreach ($activeMfs as $h) {
            $units = (float)($h['active_units'] ?? 0);
            if ($units <= 0.0001) continue;
            $invested = (float)($h['invested_value'] ?? 0);
            $current  = (float)($h['current_value'] ?? 0);
            $pnl      = (float)($h['unrealized_pnl'] ?? 0);
            $totalInvested += $invested;
            $totalCurrent  += $current;
            $totalPnl      += $pnl;

            $rows[] = [
                'Mutual Funds',
                $h['amfi_code'] ?? '',
                $h['scheme_name'] ?? '',
                $h['category'] ?? '',
                $h['folio_number'] ?? '',
                number_format($units, 4, '.', ''),
                (float)($h['avg_purchase_nav'] ?? 0),
                (float)($h['current_nav'] ?? 0),
                $invested,
                $current,
                $pnl,
                (float)($h['unrealized_pnl_percent'] ?? 0),
            ];
        }

        // 6. NPS Tier 1
        $npsData = $npsModel->getNpsPortfolio($userId);
        $schemes = $npsData['schemes'] ?? [];
        $account = $npsData['account'] ?? [];
        $pran = !empty($account['pran']) ? 'PRAN: ' . $account['pran'] : 'Tier 1';
        foreach ($schemes as $s) {
            $units = (float)($s['active_units'] ?? 0);
            if ($units <= 0.0001) continue;
            $invested = (float)($s['invested_amount'] ?? 0);
            $current  = (float)($s['current_value'] ?? 0);
            $pnl      = (float)($s['unrealized_pnl'] ?? 0);
            $totalInvested += $invested;
            $totalCurrent  += $current;
            $totalPnl      += $pnl;

            $rows[] = [
                'NPS',
                $s['scheme_code'] ?? $s['code'] ?? '',
                $s['name'] ?? '',
                'Tier 1 (' . ($s['code'] ?? '') . ')',
                $pran,
                number_format($units, 4, '.', ''),
                (float)($s['avg_nav'] ?? 0),
                (float)($s['current_nav'] ?? 0),
                $invested,
                $current,
                $pnl,
                (float)($s['return_pct'] ?? 0),
            ];
        }

        $metadata = [
            ['MyFolioVault - Consolidated Active Portfolio Holdings'],
            ['Generated on:', date('Y-m-d H:i:s')],
            ['Total Active Holdings:', count($rows)],
            ['Total Invested Value (INR):', $totalInvested],
            ['Total Current Value (INR):', $totalCurrent],
            ['Total Unrealized P&L (INR):', $totalPnl]
        ];

        $filename = 'all_portfolio_holdings_' . date('Y-m-d') . '.csv';
        csv_download($filename, $headers, $rows, $metadata);
    }
}
