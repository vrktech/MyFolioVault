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
}
