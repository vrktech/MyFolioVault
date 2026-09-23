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
use CodeIgniter\Database\BaseConnection;
use Exception;

class Reports extends BaseController
{
    protected BaseConnection $db;

    public function __construct()
    {
        helper(['currency']);
        $this->db = \Config\Database::connect();
    }

    /**
     * Resolve active financial year date range from request or defaults.
     */
    protected function getActiveDateRange(): array
    {
        $fyRanges = get_fy_ranges();
        $selectedRange = strtoupper(trim((string)$this->request->getGet('fy')));

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
        $sDate = $range['startDate'];
        $eDate = $range['endDate'];
        $modules = [];

        // Fetch consolidated standalone broker & platform expenses
        $expenseModel = new ExpenseModel();
        $consExpenses = $expenseModel->getModuleExpensesGrouped($userId, $sDate, $eDate);

        // 1. Equities
        $eqBuy = $this->applyDateFilter(
            $this->db->table('equity_transactions')->where(['user_id' => $userId, 'transaction_type' => 'BUY']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage + stt_taxes) as exp')->get()->getRowArray();

        $eqSell = $this->applyDateFilter(
            $this->db->table('equity_transactions')->where(['user_id' => $userId, 'transaction_type' => 'SELL']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage + stt_taxes) as exp')->get()->getRowArray();

        $eqDiv = $this->applyDateFilter(
            $this->db->table('equity_dividends')->where('user_id', $userId),
            'dividend_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as gross, SUM(tds_deducted) as tds')->get()->getRowArray();

        $eqCons     = (float)($consExpenses['equity']['total'] ?? 0);
        $eqPurchase = (float)($eqBuy['amt'] ?? 0);
        $eqSold     = (float)($eqSell['amt'] ?? 0);
        $eqIncome   = (float)($eqDiv['gross'] ?? 0);
        $eqExpense  = (float)($eqBuy['exp'] ?? 0) + (float)($eqSell['exp'] ?? 0) + (float)($eqDiv['tds'] ?? 0) + $eqCons;
        $eqActivity = (int)($eqBuy['cnt'] ?? 0) + (int)($eqSell['cnt'] ?? 0) + (int)($eqDiv['cnt'] ?? 0) + (int)($consExpenses['equity']['count'] ?? 0);

        $modules['equities'] = [
            'key'         => 'equities',
            'name'        => 'Equities',
            'icon'        => 'bi bi-graph-up-arrow',
            'color'       => 'primary',
            'badge'       => 'Stocks',
            'purchase'    => $eqPurchase,
            'sold'        => $eqSold,
            'income'      => $eqIncome,
            'income_desc' => 'Cash Dividends',
            'expense'     => $eqExpense,
            'net_flow'    => ($eqSold + $eqIncome) - ($eqPurchase + $eqExpense),
            'activity'    => $eqActivity,
            'ledger_url'  => base_url('equities?tab=transactions'),
        ];

        // Equities Sector Breakdown for Capital Activity
        $sectorBuys = $this->applyDateFilter(
            $this->db->table('equity_transactions')
                ->join('equities', 'equities.id = equity_transactions.equity_id')
                ->where(['equity_transactions.user_id' => $userId, 'equity_transactions.transaction_type' => 'BUY']),
            'equity_transactions.transaction_date', $sDate, $eDate
        )->select("COALESCE(NULLIF(TRIM(equities.sector), ''), 'Unclassified') as sector_name, SUM(equity_transactions.total_amount) as amt, SUM(equity_transactions.brokerage + equity_transactions.stt_taxes) as exp, COUNT(*) as cnt")
        ->groupBy('sector_name')->get()->getResultArray();

        $sectorSells = $this->applyDateFilter(
            $this->db->table('equity_transactions')
                ->join('equities', 'equities.id = equity_transactions.equity_id')
                ->where(['equity_transactions.user_id' => $userId, 'equity_transactions.transaction_type' => 'SELL']),
            'equity_transactions.transaction_date', $sDate, $eDate
        )->select("COALESCE(NULLIF(TRIM(equities.sector), ''), 'Unclassified') as sector_name, SUM(equity_transactions.total_amount) as amt, SUM(equity_transactions.brokerage + equity_transactions.stt_taxes) as exp, COUNT(*) as cnt")
        ->groupBy('sector_name')->get()->getResultArray();

        $sectorDivs = $this->applyDateFilter(
            $this->db->table('equity_dividends')
                ->join('equities', 'equities.id = equity_dividends.equity_id')
                ->where('equity_dividends.user_id', $userId),
            'equity_dividends.dividend_date', $sDate, $eDate
        )->select("COALESCE(NULLIF(TRIM(equities.sector), ''), 'Unclassified') as sector_name, SUM(equity_dividends.total_amount) as gross, SUM(equity_dividends.tds_deducted) as tds, COUNT(*) as cnt")
        ->groupBy('sector_name')->get()->getResultArray();

        $equitiesSectors = [];
        foreach ($sectorBuys as $b) {
            $sec = $b['sector_name'];
            if (!isset($equitiesSectors[$sec])) {
                $equitiesSectors[$sec] = ['name' => $sec, 'purchase' => 0.0, 'sold' => 0.0, 'income' => 0.0, 'expense' => 0.0, 'activity' => 0];
            }
            $equitiesSectors[$sec]['purchase'] += (float)$b['amt'];
            $equitiesSectors[$sec]['expense']  += (float)$b['exp'];
            $equitiesSectors[$sec]['activity'] += (int)$b['cnt'];
        }
        foreach ($sectorSells as $s) {
            $sec = $s['sector_name'];
            if (!isset($equitiesSectors[$sec])) {
                $equitiesSectors[$sec] = ['name' => $sec, 'purchase' => 0.0, 'sold' => 0.0, 'income' => 0.0, 'expense' => 0.0, 'activity' => 0];
            }
            $equitiesSectors[$sec]['sold']     += (float)$s['amt'];
            $equitiesSectors[$sec]['expense']  += (float)$s['exp'];
            $equitiesSectors[$sec]['activity'] += (int)$s['cnt'];
        }
        foreach ($sectorDivs as $d) {
            $sec = $d['sector_name'];
            if (!isset($equitiesSectors[$sec])) {
                $equitiesSectors[$sec] = ['name' => $sec, 'purchase' => 0.0, 'sold' => 0.0, 'income' => 0.0, 'expense' => 0.0, 'activity' => 0];
            }
            $equitiesSectors[$sec]['income']   += (float)$d['gross'];
            $equitiesSectors[$sec]['expense']  += (float)$d['tds'];
            $equitiesSectors[$sec]['activity'] += (int)$d['cnt'];
        }
        foreach ($equitiesSectors as $sec => $info) {
            $equitiesSectors[$sec]['net_flow'] = ($info['sold'] + $info['income']) - ($info['purchase'] + $info['expense']);
        }
        uasort($equitiesSectors, fn($a, $b) => ($b['purchase'] + $b['sold']) <=> ($a['purchase'] + $a['sold']));

        // 2. InvITs & REITs
        $reitBuy = $this->applyDateFilter(
            $this->db->table('reit_invit_transactions')->where(['user_id' => $userId, 'transaction_type' => 'BUY']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage + stt_taxes) as exp')->get()->getRowArray();

        $reitSell = $this->applyDateFilter(
            $this->db->table('reit_invit_transactions')->where(['user_id' => $userId, 'transaction_type' => 'SELL']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage + stt_taxes) as exp')->get()->getRowArray();

        $reitDist = $this->applyDateFilter(
            $this->db->table('reit_invit_distributions')->where('user_id', $userId),
            'payout_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as gross, SUM(tds_deducted) as tds')->get()->getRowArray();

        $reitCons     = (float)($consExpenses['reit_invit']['total'] ?? 0);
        $reitPurchase = (float)($reitBuy['amt'] ?? 0);
        $reitSold     = (float)($reitSell['amt'] ?? 0);
        $reitIncome   = (float)($reitDist['gross'] ?? 0);
        $reitExpense  = (float)($reitBuy['exp'] ?? 0) + (float)($reitSell['exp'] ?? 0) + (float)($reitDist['tds'] ?? 0) + $reitCons;
        $reitActivity = (int)($reitBuy['cnt'] ?? 0) + (int)($reitSell['cnt'] ?? 0) + (int)($reitDist['cnt'] ?? 0) + (int)($consExpenses['reit_invit']['count'] ?? 0);

        $modules['reits_invits'] = [
            'key'         => 'reits_invits',
            'name'        => 'InvITs & REITs',
            'icon'        => 'bi bi-buildings',
            'color'       => 'info',
            'badge'       => 'Trusts',
            'purchase'    => $reitPurchase,
            'sold'        => $reitSold,
            'income'      => $reitIncome,
            'income_desc' => 'Quarterly Distributions',
            'expense'     => $reitExpense,
            'net_flow'    => ($reitSold + $reitIncome) - ($reitPurchase + $reitExpense),
            'activity'    => $reitActivity,
            'ledger_url'  => base_url('reits-invits?tab=transactions'),
        ];

        // 3. ETFs
        $etfBuy = $this->applyDateFilter(
            $this->db->table('etf_transactions')->where(['user_id' => $userId, 'transaction_type' => 'BUY']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage + stt_taxes) as exp')->get()->getRowArray();

        $etfSell = $this->applyDateFilter(
            $this->db->table('etf_transactions')->where(['user_id' => $userId, 'transaction_type' => 'SELL']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage + stt_taxes) as exp')->get()->getRowArray();

        $etfCons     = (float)($consExpenses['etf']['total'] ?? 0);
        $etfPurchase = (float)($etfBuy['amt'] ?? 0);
        $etfSold     = (float)($etfSell['amt'] ?? 0);
        $etfIncome   = 0.0;
        $etfExpense  = (float)($etfBuy['exp'] ?? 0) + (float)($etfSell['exp'] ?? 0) + $etfCons;
        $etfActivity = (int)($etfBuy['cnt'] ?? 0) + (int)($etfSell['cnt'] ?? 0) + (int)($consExpenses['etf']['count'] ?? 0);

        $modules['etfs'] = [
            'key'         => 'etfs',
            'name'        => 'Exchange Traded Funds',
            'icon'        => 'bi bi-pie-chart',
            'color'       => 'success',
            'badge'       => 'ETFs',
            'purchase'    => $etfPurchase,
            'sold'        => $etfSold,
            'income'      => $etfIncome,
            'income_desc' => 'Accumulated in NAV',
            'expense'     => $etfExpense,
            'net_flow'    => ($etfSold + $etfIncome) - ($etfPurchase + $etfExpense),
            'activity'    => $etfActivity,
            'ledger_url'  => base_url('etfs?tab=transactions'),
        ];

        // 4. Bonds (SGBs, G-Secs, Corporate Bonds)
        $bondBuy = $this->applyDateFilter(
            $this->db->table('bond_transactions')->where(['user_id' => $userId, 'transaction_type' => 'BUY']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage_charges) as exp')->get()->getRowArray();

        $bondExit = $this->applyDateFilter(
            $this->db->table('bond_transactions')
                ->where('user_id', $userId)
                ->whereIn('transaction_type', ['SELL', 'MATURITY_REDEMPTION']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(total_amount) as amt, SUM(brokerage_charges) as exp')->get()->getRowArray();

        $bondInt = $this->applyDateFilter(
            $this->db->table('bond_interest_payouts')->where('user_id', $userId),
            'payout_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(gross_interest) as gross, SUM(tds_deducted) as tds')->get()->getRowArray();

        $bondCons     = (float)($consExpenses['bond']['total'] ?? 0);
        $bondPurchase = (float)($bondBuy['amt'] ?? 0);
        $bondSold     = (float)($bondExit['amt'] ?? 0);
        $bondIncome   = (float)($bondInt['gross'] ?? 0);
        $bondExpense  = (float)($bondBuy['exp'] ?? 0) + (float)($bondExit['exp'] ?? 0) + (float)($bondInt['tds'] ?? 0) + $bondCons;
        $bondActivity = (int)($bondBuy['cnt'] ?? 0) + (int)($bondExit['cnt'] ?? 0) + (int)($bondInt['cnt'] ?? 0) + (int)($consExpenses['bond']['count'] ?? 0);

        $modules['bonds'] = [
            'key'         => 'bonds',
            'name'        => 'Bonds & Fixed Income',
            'icon'        => 'bi bi-bank',
            'color'       => 'dark',
            'badge'       => 'Bonds',
            'purchase'    => $bondPurchase,
            'sold'        => $bondSold,
            'income'      => $bondIncome,
            'income_desc' => 'Coupon Interest',
            'expense'     => $bondExpense,
            'net_flow'    => ($bondSold + $bondIncome) - ($bondPurchase + $bondExpense),
            'activity'    => $bondActivity,
            'ledger_url'  => base_url('bonds?tab=transactions'),
        ];

        // 5. Mutual Funds
        $mfBuy = $this->applyDateFilter(
            $this->db->table('mutual_fund_transactions')
                ->where('user_id', $userId)
                ->whereIn('transaction_type', ['BUY_SIP', 'BUY_LUMPSUM']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(amount) as amt, SUM(charges) as exp')->get()->getRowArray();

        $mfRedeem = $this->applyDateFilter(
            $this->db->table('mutual_fund_transactions')
                ->where(['user_id' => $userId, 'transaction_type' => 'REDEEM']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(amount) as amt, SUM(charges) as exp')->get()->getRowArray();

        $mfCons     = (float)($consExpenses['mutual_fund']['total'] ?? 0);
        $mfPurchase = (float)($mfBuy['amt'] ?? 0);
        $mfSold     = (float)($mfRedeem['amt'] ?? 0);
        $mfIncome   = 0.0;
        $mfExpense  = (float)($mfBuy['exp'] ?? 0) + (float)($mfRedeem['exp'] ?? 0) + $mfCons;
        $mfActivity = (int)($mfBuy['cnt'] ?? 0) + (int)($mfRedeem['cnt'] ?? 0) + (int)($consExpenses['mutual_fund']['count'] ?? 0);

        $modules['mutual_funds'] = [
            'key'         => 'mutual_funds',
            'name'        => 'Mutual Funds',
            'icon'        => 'bi bi-collection',
            'color'       => 'warning',
            'badge'       => 'MF Schemes',
            'purchase'    => $mfPurchase,
            'sold'        => $mfSold,
            'income'      => $mfIncome,
            'income_desc' => 'Accumulated in NAV',
            'expense'     => $mfExpense,
            'net_flow'    => ($mfSold + $mfIncome) - ($mfPurchase + $mfExpense),
            'activity'    => $mfActivity,
            'ledger_url'  => base_url('mutual-funds?tab=transactions'),
        ];

        // 6. NPS (National Pension System)
        $npsContrib = $this->applyDateFilter(
            $this->db->table('nps_transactions')
                ->where(['user_id' => $userId, 'transaction_type' => 'CONTRIBUTION']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(gross_amount) as amt, SUM(optional_cash_charges) as exp')->get()->getRowArray();

        $npsWithdr = $this->applyDateFilter(
            $this->db->table('nps_transactions')
                ->where(['user_id' => $userId, 'transaction_type' => 'WITHDRAWAL']),
            'transaction_date', $sDate, $eDate
        )->select('COUNT(*) as cnt, SUM(gross_amount) as amt, SUM(optional_cash_charges) as exp')->get()->getRowArray();

        $npsPurchase = (float)($npsContrib['amt'] ?? 0);
        $npsSold     = (float)($npsWithdr['amt'] ?? 0);
        $npsIncome   = 0.0;
        $npsExpense  = (float)($npsContrib['exp'] ?? 0) + (float)($npsWithdr['exp'] ?? 0);
        $npsActivity = (int)($npsContrib['cnt'] ?? 0) + (int)($npsWithdr['cnt'] ?? 0);

        $modules['nps'] = [
            'key'         => 'nps',
            'name'        => 'National Pension System (Tier 1)',
            'icon'        => 'bi bi-shield-check',
            'color'       => 'secondary',
            'badge'       => 'Retirement',
            'purchase'    => $npsPurchase,
            'sold'        => $npsSold,
            'income'      => $npsIncome,
            'income_desc' => 'Accumulated in NAV',
            'expense'     => $npsExpense,
            'net_flow'    => ($npsSold + $npsIncome) - ($npsPurchase + $npsExpense),
            'activity'    => $npsActivity,
            'ledger_url'  => base_url('nps?tab=transactions'),
        ];

        // Portfolio Consolidated Totals
        $totals = [
            'purchase' => array_sum(array_column($modules, 'purchase')),
            'sold'     => array_sum(array_column($modules, 'sold')),
            'income'   => array_sum(array_column($modules, 'income')),
            'expense'  => array_sum(array_column($modules, 'expense')),
            'activity' => array_sum(array_column($modules, 'activity')),
        ];
        $totals['net_flow'] = ($totals['sold'] + $totals['income']) - ($totals['purchase'] + $totals['expense']);

        // Income breakdown
        $incomeBreakdown = [
            'dividends'     => $eqIncome,
            'distributions' => $reitIncome,
            'bond_coupons'  => $bondIncome,
        ];

        // Expense breakdown
        $expenseBreakdown = [
            'brokerage_stt' => (float)($eqBuy['exp'] ?? 0) + (float)($eqSell['exp'] ?? 0) +
                               (float)($reitBuy['exp'] ?? 0) + (float)($reitSell['exp'] ?? 0) +
                               (float)($etfBuy['exp'] ?? 0) + (float)($etfSell['exp'] ?? 0) +
                               (float)($bondBuy['exp'] ?? 0) + (float)($bondExit['exp'] ?? 0),
            'tds_deducted'  => (float)($eqDiv['tds'] ?? 0) + (float)($reitDist['tds'] ?? 0) + (float)($bondInt['tds'] ?? 0),
            'charges_fees'  => (float)($mfBuy['exp'] ?? 0) + (float)($mfRedeem['exp'] ?? 0) + (float)($npsContrib['exp'] ?? 0),
        ];

        return [
            'modules'          => $modules,
            'totals'           => $totals,
            'incomeBreakdown'  => $incomeBreakdown,
            'expenseBreakdown' => $expenseBreakdown,
            'equitiesSectors'  => $equitiesSectors,
        ];
    }

    public function cashflow()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getCashflowData($userId, $range);

        return view('reports/cashflow', array_merge([
            'title'     => 'Portfolio Cash Flow & Capital Activity Report - RupeeFolio',
            'activeTab' => 'cashflow',
            'range'     => $range,
        ], $data));
    }

    // =========================================================================
    // 2. CAPITAL GAINS & TAX REPORT (LTCG / STCG BY MODULE)
    // =========================================================================

    public function getTaxData(int $userId, array $range): array
    {
        $sDate   = $range['startDate'];
        $eDate   = $range['endDate'];
        $modules = [];

        // 1. Equities Capital Gains
        $eqQuery = $this->db->table('equity_capital_gains')->where('user_id', $userId);
        $eqLots  = $this->applyDateFilter($eqQuery, 'sell_date', $sDate, $eDate)->get()->getResultArray();

        $eqStcg = 0.0;
        $eqLtcg = 0.0;
        $eqGain = 0.0;
        $eqSales = 0.0;
        $eqCost = 0.0;
        foreach ($eqLots as $l) {
            $g = (float)$l['realized_gain'];
            $eqGain += $g;
            if ($l['gain_type'] === 'LTCG') {
                $eqLtcg += $g;
            } else {
                $eqStcg += $g;
            }
            $q = (float)$l['quantity_matched'];
            $eqSales += ($q * (float)$l['sell_price']);
            $eqCost  += ($q * (float)$l['buy_price']);
        }

        $modules['equities'] = [
            'name'       => 'Equities',
            'icon'       => 'bi bi-graph-up-arrow',
            'color'      => 'primary',
            'tax_rule'   => 'Sec 111A (STCG 20%) • Sec 112A (LTCG 12.5% > ₹1.25L)',
            'stcg'       => $eqStcg,
            'ltcg'       => $eqLtcg,
            'exempt'     => 0.0,
            'total_gain' => $eqGain,
            'sales'      => $eqSales,
            'cost'       => $eqCost,
            'lots'       => count($eqLots),
            'log_url'    => base_url('equities?tab=capitalgains'),
        ];

        // 2. InvITs & REITs Capital Gains
        $reitQuery = $this->db->table('reit_invit_capital_gains')->where('user_id', $userId);
        $reitLots  = $this->applyDateFilter($reitQuery, 'sell_date', $sDate, $eDate)->get()->getResultArray();

        $reitStcg = 0.0;
        $reitLtcg = 0.0;
        $reitGain = 0.0;
        $reitSales = 0.0;
        $reitCost = 0.0;
        foreach ($reitLots as $l) {
            $g = (float)$l['realized_gain'];
            $reitGain += $g;
            if ($l['gain_type'] === 'LTCG') {
                $reitLtcg += $g;
            } else {
                $reitStcg += $g;
            }
            $q = (float)$l['quantity_matched'];
            $reitSales += ($q * (float)$l['sell_price']);
            $reitCost  += ($q * (float)$l['buy_price']);
        }

        $modules['reits_invits'] = [
            'name'       => 'InvITs & REITs',
            'icon'       => 'bi bi-buildings',
            'color'      => 'info',
            'tax_rule'   => 'Business Trust: Sec 111A (STCG 20%) • Sec 112A (LTCG 12.5%)',
            'stcg'       => $reitStcg,
            'ltcg'       => $reitLtcg,
            'exempt'     => 0.0,
            'total_gain' => $reitGain,
            'sales'      => $reitSales,
            'cost'       => $reitCost,
            'lots'       => count($reitLots),
            'log_url'    => base_url('reits-invits?tab=capitalgains'),
        ];

        // 3. ETFs Capital Gains
        $etfQuery = $this->db->table('etf_capital_gains')->where('user_id', $userId);
        $etfLots  = $this->applyDateFilter($etfQuery, 'sell_date', $sDate, $eDate)->get()->getResultArray();

        $etfStcg = 0.0;
        $etfLtcg = 0.0;
        $etfGain = 0.0;
        $etfSales = 0.0;
        $etfCost = 0.0;
        foreach ($etfLots as $l) {
            $g = (float)$l['realized_gain'];
            $etfGain += $g;
            if ($l['gain_type'] === 'LTCG') {
                $etfLtcg += $g;
            } else {
                $etfStcg += $g;
            }
            $q = (float)$l['quantity_matched'];
            $etfSales += ($q * (float)$l['sell_price']);
            $etfCost  += ($q * (float)$l['buy_price']);
        }

        $modules['etfs'] = [
            'name'       => 'Exchange Traded Funds',
            'icon'       => 'bi bi-pie-chart',
            'color'      => 'success',
            'tax_rule'   => 'Sec 111A (STCG 20%) • Sec 112A (LTCG 12.5% > ₹1.25L)',
            'stcg'       => $etfStcg,
            'ltcg'       => $etfLtcg,
            'exempt'     => 0.0,
            'total_gain' => $etfGain,
            'sales'      => $etfSales,
            'cost'       => $etfCost,
            'lots'       => count($etfLots),
            'log_url'    => base_url('etfs?tab=capitalgains'),
        ];

        // 4. Bonds Capital Gains
        $bondQuery = $this->db->table('bond_capital_gains')->where('user_id', $userId);
        $bondLots  = $this->applyDateFilter($bondQuery, 'exit_date', $sDate, $eDate)->get()->getResultArray();

        $bondStcg   = 0.0;
        $bondLtcg   = 0.0;
        $bondExempt = 0.0;
        $bondGain   = 0.0;
        $bondSales  = 0.0;
        $bondCost   = 0.0;
        foreach ($bondLots as $l) {
            $g = (float)$l['realized_gain'];
            $bondGain += $g;
            if ($l['gain_type'] === 'EXEMPT_SGB_MATURITY') {
                $bondExempt += $g;
            } elseif ($l['gain_type'] === 'LTCG') {
                $bondLtcg += $g;
            } else {
                $bondStcg += $g;
            }
            $q = (float)$l['quantity_matched'];
            $bondSales += ($q * (float)$l['exit_price']);
            $bondCost  += ($q * (float)$l['buy_price']);
        }

        $modules['bonds'] = [
            'name'       => 'Bonds & Fixed Income',
            'icon'       => 'bi bi-bank',
            'color'      => 'dark',
            'tax_rule'   => 'SGB Maturity: 100% Tax-Exempt u/s 47(viic) • Listed: 12.5% LTCG',
            'stcg'       => $bondStcg,
            'ltcg'       => $bondLtcg,
            'exempt'     => $bondExempt,
            'total_gain' => $bondGain,
            'sales'      => $bondSales,
            'cost'       => $bondCost,
            'lots'       => count($bondLots),
            'log_url'    => base_url('bonds?tab=capitalgains'),
        ];

        // 5. Mutual Funds Capital Gains
        $mfQuery = $this->db->table('mutual_fund_capital_gains')->where('user_id', $userId);
        $mfLots  = $this->applyDateFilter($mfQuery, 'redeem_date', $sDate, $eDate)->get()->getResultArray();

        $mfStcg = 0.0;
        $mfLtcg = 0.0;
        $mfGain = 0.0;
        $mfSales = 0.0;
        $mfCost = 0.0;
        foreach ($mfLots as $l) {
            $g = (float)$l['realized_gain'];
            $mfGain += $g;
            if ($l['gain_type'] === 'LTCG') {
                $mfLtcg += $g;
            } else {
                $mfStcg += $g;
            }
            $u = (float)$l['units_matched'];
            $mfSales += ($u * (float)$l['redeem_nav']);
            $mfCost  += ($u * (float)$l['buy_nav']);
        }

        $modules['mutual_funds'] = [
            'name'       => 'Mutual Funds',
            'icon'       => 'bi bi-collection',
            'color'      => 'warning',
            'tax_rule'   => 'Equity MF: Sec 111A/112A • Debt MF: Slab rate',
            'stcg'       => $mfStcg,
            'ltcg'       => $mfLtcg,
            'exempt'     => 0.0,
            'total_gain' => $mfGain,
            'sales'      => $mfSales,
            'cost'       => $mfCost,
            'lots'       => count($mfLots),
            'log_url'    => base_url('mutual-funds?tab=capitalgains'),
        ];

        // 6. NPS Note (Tax-deferred)
        $modules['nps'] = [
            'name'       => 'National Pension System (Tier 1)',
            'icon'       => 'bi bi-shield-check',
            'color'      => 'secondary',
            'tax_rule'   => 'Tax-Deferred • 60% Maturity Withdrawal is 100% Tax-Exempt u/s 10(12A)',
            'stcg'       => 0.0,
            'ltcg'       => 0.0,
            'exempt'     => 0.0,
            'total_gain' => 0.0,
            'sales'      => 0.0,
            'cost'       => 0.0,
            'lots'       => 0,
            'log_url'    => base_url('nps'),
        ];

        // Grand Totals
        $totals = [
            'total_gain' => array_sum(array_column($modules, 'total_gain')),
            'stcg'       => array_sum(array_column($modules, 'stcg')),
            'ltcg'       => array_sum(array_column($modules, 'ltcg')),
            'exempt'     => array_sum(array_column($modules, 'exempt')),
            'sales'      => array_sum(array_column($modules, 'sales')),
            'cost'       => array_sum(array_column($modules, 'cost')),
            'lots'       => array_sum(array_column($modules, 'lots')),
        ];

        // Estimated Indian Tax Calculations (Post Budget 2024 provisions)
        $equityStcg = $eqStcg + $etfStcg + $reitStcg;
        $estTaxStcg = max(0, $equityStcg * 0.20);

        $equityLtcg = $eqLtcg + $etfLtcg + $reitLtcg;
        $taxableLtcg = max(0, $equityLtcg - 125000);
        $estTaxLtcg = $taxableLtcg * 0.125;

        $estTax = [
            'equity_stcg'      => $equityStcg,
            'est_tax_stcg'     => $estTaxStcg,
            'equity_ltcg'      => $equityLtcg,
            'exemption_limit'  => 125000.0,
            'taxable_ltcg'     => $taxableLtcg,
            'est_tax_ltcg'     => $estTaxLtcg,
            'total_est_tax'    => $estTaxStcg + $estTaxLtcg,
            'cess_4_percent'   => ($estTaxStcg + $estTaxLtcg) * 0.04,
            'final_tax_payble' => ($estTaxStcg + $estTaxLtcg) * 1.04,
        ];

        return [
            'modules' => $modules,
            'totals'  => $totals,
            'estTax'  => $estTax,
        ];
    }

    public function tax()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getTaxData($userId, $range);

        return view('reports/tax', array_merge([
            'title'     => 'Capital Gains & Tax Audit Report (LTCG / STCG) - RupeeFolio',
            'activeTab' => 'tax',
            'range'     => $range,
        ], $data));
    }

    // =========================================================================
    // 3. PASSIVE INCOME & DISTRIBUTION REPORT
    // =========================================================================

    public function getIncomeData(int $userId, array $range): array
    {
        $sDate = $range['startDate'];
        $eDate = $range['endDate'];

        // 1. Equities Dividends
        $eqDivQuery = $this->db->table('equity_dividends')
            ->select('equity_dividends.*, equities.company_name, equities.symbol')
            ->join('equities', 'equities.id = equity_dividends.equity_id')
            ->where('equity_dividends.user_id', $userId);
        $eqDividends = $this->applyDateFilter($eqDivQuery, 'dividend_date', $sDate, $eDate)
            ->orderBy('dividend_date', 'DESC')->get()->getResultArray();

        // 2. Bonds Coupon Interest
        $bondIntQuery = $this->db->table('bond_interest_payouts')
            ->select('bond_interest_payouts.*, bonds.bond_name, bonds.isin, bonds.bond_symbol')
            ->join('bonds', 'bonds.id = bond_interest_payouts.bond_id')
            ->where('bond_interest_payouts.user_id', $userId);
        $bondCoupons = $this->applyDateFilter($bondIntQuery, 'payout_date', $sDate, $eDate)
            ->orderBy('payout_date', 'DESC')->get()->getResultArray();

        // 3. REITs / InvITs Distributions
        $reitDistQuery = $this->db->table('reit_invit_distributions')
            ->select('reit_invit_distributions.*, reits_invits.trust_name, reits_invits.symbol, reits_invits.trust_type')
            ->join('reits_invits', 'reits_invits.id = reit_invit_distributions.trust_id')
            ->where('reit_invit_distributions.user_id', $userId);
        $reitDists = $this->applyDateFilter($reitDistQuery, 'payout_date', $sDate, $eDate)
            ->orderBy('payout_date', 'DESC')->get()->getResultArray();

        // Totals
        $eqGross   = array_sum(array_column($eqDividends, 'total_amount'));
        $eqTds     = array_sum(array_column($eqDividends, 'tds_deducted'));
        $eqNet     = $eqGross - $eqTds;

        $bondGross = array_sum(array_column($bondCoupons, 'gross_interest'));
        $bondTds   = array_sum(array_column($bondCoupons, 'tds_deducted'));
        $bondNet   = array_sum(array_column($bondCoupons, 'net_interest'));

        $reitGross = array_sum(array_column($reitDists, 'total_amount'));
        $reitInt   = array_sum(array_column($reitDists, 'interest_component'));
        $reitDiv   = array_sum(array_column($reitDists, 'dividend_component'));
        $reitRoc   = array_sum(array_column($reitDists, 'return_of_capital'));
        $reitOther = array_sum(array_column($reitDists, 'other_income'));
        $reitTds   = array_sum(array_column($reitDists, 'tds_deducted'));
        $reitNet   = array_sum(array_column($reitDists, 'net_received'));

        $grandTotals = [
            'gross'   => $eqGross + $bondGross + $reitGross,
            'tds'     => $eqTds + $bondTds + $reitTds,
            'net'     => $eqNet + $bondNet + $reitNet,
            'payouts' => count($eqDividends) + count($bondCoupons) + count($reitDists),
        ];

        return [
            'eqDividends' => $eqDividends,
            'bondCoupons' => $bondCoupons,
            'reitDists'   => $reitDists,
            'eqTotals'    => ['gross' => $eqGross, 'tds' => $eqTds, 'net' => $eqNet, 'count' => count($eqDividends)],
            'bondTotals'  => ['gross' => $bondGross, 'tds' => $bondTds, 'net' => $bondNet, 'count' => count($bondCoupons)],
            'reitTotals'  => [
                'gross'    => $reitGross,
                'interest' => $reitInt,
                'dividend' => $reitDiv,
                'roc'      => $reitRoc,
                'other'    => $reitOther,
                'tds'      => $reitTds,
                'net'      => $reitNet,
                'count'    => count($reitDists),
            ],
            'grandTotals' => $grandTotals,
        ];
    }

    public function income()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getIncomeData($userId, $range);

        return view('reports/income', array_merge([
            'title'     => 'Passive Income & Distribution Report - RupeeFolio',
            'activeTab' => 'income',
            'range'     => $range,
        ], $data));
    }

    // =========================================================================
    // 4. EXPENSES & TRANSACTION FRICTION REPORT
    // =========================================================================

    public function getExpensesData(int $userId, array $range): array
    {
        $sDate = $range['startDate'];
        $eDate = $range['endDate'];

        // Equities friction
        $eqFriction = $this->applyDateFilter(
            $this->db->table('equity_transactions')->where('user_id', $userId),
            'transaction_date', $sDate, $eDate
        )->select('SUM(brokerage) as brokerage, SUM(stt_taxes) as stt')->get()->getRowArray();

        $eqTds = $this->applyDateFilter(
            $this->db->table('equity_dividends')->where('user_id', $userId),
            'dividend_date', $sDate, $eDate
        )->select('SUM(tds_deducted) as tds')->get()->getRowArray();

        // ETFs friction
        $etfFriction = $this->applyDateFilter(
            $this->db->table('etf_transactions')->where('user_id', $userId),
            'transaction_date', $sDate, $eDate
        )->select('SUM(brokerage) as brokerage, SUM(stt_taxes) as stt')->get()->getRowArray();

        // MF friction
        $mfFriction = $this->applyDateFilter(
            $this->db->table('mutual_fund_transactions')->where('user_id', $userId),
            'transaction_date', $sDate, $eDate
        )->select('SUM(charges) as charges')->get()->getRowArray();

        // NPS friction
        $npsFriction = $this->applyDateFilter(
            $this->db->table('nps_transactions')->where('user_id', $userId),
            'transaction_date', $sDate, $eDate
        )->select('SUM(optional_cash_charges) as charges')->get()->getRowArray();

        // Bonds friction
        $bondFriction = $this->applyDateFilter(
            $this->db->table('bond_transactions')->where('user_id', $userId),
            'transaction_date', $sDate, $eDate
        )->select('SUM(brokerage_charges) as brokerage')->get()->getRowArray();

        $bondTds = $this->applyDateFilter(
            $this->db->table('bond_interest_payouts')->where('user_id', $userId),
            'payout_date', $sDate, $eDate
        )->select('SUM(tds_deducted) as tds')->get()->getRowArray();

        // REITs friction
        $reitFriction = $this->applyDateFilter(
            $this->db->table('reit_invit_transactions')->where('user_id', $userId),
            'transaction_date', $sDate, $eDate
        )->select('SUM(brokerage) as brokerage, SUM(stt_taxes) as stt')->get()->getRowArray();

        $reitTds = $this->applyDateFilter(
            $this->db->table('reit_invit_distributions')->where('user_id', $userId),
            'payout_date', $sDate, $eDate
        )->select('SUM(tds_deducted) as tds')->get()->getRowArray();

        $moduleExpenses = [
            'equities' => [
                'key'       => 'equities',
                'name'      => 'Equities',
                'icon'      => 'bi bi-graph-up-arrow',
                'brokerage' => (float)($eqFriction['brokerage'] ?? 0),
                'stt'       => (float)($eqFriction['stt'] ?? 0),
                'tds'       => (float)($eqTds['tds'] ?? 0),
                'other'     => 0.0,
            ],
            'reits_invits' => [
                'key'       => 'reits_invits',
                'name'      => 'InvITs & REITs',
                'icon'      => 'bi bi-buildings',
                'brokerage' => (float)($reitFriction['brokerage'] ?? 0),
                'stt'       => (float)($reitFriction['stt'] ?? 0),
                'tds'       => (float)($reitTds['tds'] ?? 0),
                'other'     => 0.0,
            ],
            'etfs' => [
                'key'       => 'etfs',
                'name'      => 'Exchange Traded Funds',
                'icon'      => 'bi bi-pie-chart',
                'brokerage' => (float)($etfFriction['brokerage'] ?? 0),
                'stt'       => (float)($etfFriction['stt'] ?? 0),
                'tds'       => 0.0,
                'other'     => 0.0,
            ],
            'bonds' => [
                'key'       => 'bonds',
                'name'      => 'Bonds & Fixed Income',
                'icon'      => 'bi bi-bank',
                'brokerage' => (float)($bondFriction['brokerage'] ?? 0),
                'stt'       => 0.0,
                'tds'       => (float)($bondTds['tds'] ?? 0),
                'other'     => 0.0,
            ],
            'mutual_funds' => [
                'key'       => 'mutual_funds',
                'name'      => 'Mutual Funds',
                'icon'      => 'bi bi-collection',
                'brokerage' => 0.0,
                'stt'       => 0.0,
                'tds'       => 0.0,
                'other'     => (float)($mfFriction['charges'] ?? 0),
            ],
            'nps' => [
                'key'       => 'nps',
                'name'      => 'National Pension System (Tier 1)',
                'icon'      => 'bi bi-shield-check',
                'brokerage' => 0.0,
                'stt'       => 0.0,
                'tds'       => 0.0,
                'other'     => (float)($npsFriction['charges'] ?? 0),
            ],
        ];

        // Add consolidated standalone broker fees, STT & taxes, and platform charges
        $expenseModel = new ExpenseModel();
        $cons         = $expenseModel->getModuleExpensesGrouped($userId, $sDate, $eDate);
        $consSummary  = $expenseModel->getExpensesSummary($userId, $sDate, $eDate);

        $mapping = [
            'equities'     => 'equity',
            'reits_invits' => 'reit_invit',
            'etfs'         => 'etf',
            'bonds'        => 'bond',
            'mutual_funds' => 'mutual_fund',
        ];

        foreach ($mapping as $reportKey => $modKey) {
            if (isset($cons[$modKey])) {
                $moduleExpenses[$reportKey]['brokerage'] += $cons[$modKey]['brokerage'];
                $moduleExpenses[$reportKey]['stt']       += $cons[$modKey]['stt'];
                $moduleExpenses[$reportKey]['other']     += $cons[$modKey]['other'];
            }
        }

        foreach ($moduleExpenses as $k => $me) {
            $moduleExpenses[$k]['total'] = $me['brokerage'] + $me['stt'] + $me['tds'] + $me['other'];
        }

        $grandExpenses = [
            'brokerage' => array_sum(array_column($moduleExpenses, 'brokerage')),
            'stt'       => array_sum(array_column($moduleExpenses, 'stt')),
            'tds'       => array_sum(array_column($moduleExpenses, 'tds')),
            'other'     => array_sum(array_column($moduleExpenses, 'other')),
            'total'     => array_sum(array_column($moduleExpenses, 'total')),
        ];

        return [
            'moduleExpenses'      => $moduleExpenses,
            'grandExpenses'       => $grandExpenses,
            'consolidatedSummary' => $consSummary,
        ];
    }

    public function expenses()
    {
        $userId = (int)session()->get('userId');
        $range  = $this->getActiveDateRange();
        $data   = $this->getExpensesData($userId, $range);

        return view('reports/expenses', array_merge([
            'title'     => 'Expenses & Statutory Friction Report - RupeeFolio',
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
            'title'                    => 'Asset Allocation & Portfolio Valuation Snapshot - RupeeFolio',
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
        $filename = 'rupeefolio_cashflow_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'RupeeFolio - Portfolio Cash Flow & Activity Report',
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
        $filename = 'rupeefolio_tax_capital_gains_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'RupeeFolio - Capital Gains & Tax Report (LTCG / STCG)',
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
        $filename = 'rupeefolio_passive_income_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'RupeeFolio - Passive Income & Distribution Report',
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
        $filename = 'rupeefolio_expenses_friction_report_' . strtolower($range['key']) . '_' . date('Ymd_His') . '.csv';

        $metadata = [
            'RupeeFolio - Expenses & Statutory Friction Report',
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

