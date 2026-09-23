<?php

namespace App\Controllers;

use App\Models\NpsAccountModel;
use App\Models\NpsSchemeUnitModel;
use App\Models\NpsTransactionModel;
use App\Models\UserModel;
use Exception;

class Nps extends BaseController
{
    protected NpsAccountModel $accountModel;
    protected NpsTransactionModel $transactionModel;
    protected NpsSchemeUnitModel $schemeUnitModel;
    protected UserModel $userModel;

    public function __construct()
    {
        helper(['currency']);
        $this->accountModel     = new NpsAccountModel();
        $this->transactionModel = new NpsTransactionModel();
        $this->schemeUnitModel  = new NpsSchemeUnitModel();
        $this->userModel        = new UserModel();
    }

    /**
     * NPS Tier 1 Dashboard: Executive summary, asset class distribution,
     * scheme portfolios, contribution history, and quarterly fee deductions.
     */
    public function index()
    {
        $userId = (int) session()->get('userId');

        $portfolio = $this->accountModel->getNpsPortfolio($userId);
        if (!$portfolio) {
            return redirect()->to('/nps/new')->with('info', 'Please set up your Tier 1 PRAN account to get started.');
        }

        $accountId     = (int) $portfolio['account']['id'];
        $contributions = $this->transactionModel->getContributions($userId, $accountId);
        $deductions    = $this->transactionModel->getQuarterlyUnitDeductions($userId, $accountId);

        return view('nps/index', [
            'title'         => 'NPS Tier 1 Portfolio - MyFolioVault',
            'account'       => $portfolio['account'],
            'schemes'       => $portfolio['schemes'],
            'summary'       => $portfolio['summary'],
            'contributions' => $contributions,
            'deductions'    => $deductions,
        ]);
    }

    /**
     * Display First-Time Setup form for Tier 1 PRAN.
     */
    public function newAccount()
    {
        $userId = (int) session()->get('userId');
        $existing = $this->accountModel->where('user_id', $userId)->where('status', 'ACTIVE')->first();
        if ($existing) {
            return redirect()->to('/nps')->with('info', 'Your Tier 1 PRAN account is already active.');
        }

        $user = $this->userModel->find($userId);

        return view('nps/new_account', [
            'title'        => 'Setup NPS Tier 1 Account - MyFolioVault',
            'defaultName'  => $user['name'] ?? '',
        ]);
    }

    /**
     * Process First-Time PRAN Setup and initial contribution.
     */
    public function createAccount()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'pran'              => 'required|min_length[10]|max_length[20]',
            'subscriber_name'   => 'required|min_length[2]|max_length[100]',
            'pfm_name'          => 'required|max_length[100]',
            'alloc_equity'      => 'required|numeric',
            'alloc_corp_debt'   => 'required|numeric',
            'alloc_govt_bonds'  => 'required|numeric',
            'alloc_alternative' => 'permit_empty|numeric',
            'transaction_date'  => 'required|valid_date',
            'gross_amount'      => 'required|numeric|greater_than[0]',
            'contribution_type' => 'required|in_list[VOLUNTARY,EMPLOYEE,EMPLOYER]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $allocE = (float) $this->request->getPost('alloc_equity');
        $allocC = (float) $this->request->getPost('alloc_corp_debt');
        $allocG = (float) $this->request->getPost('alloc_govt_bonds');
        $allocA = (float) ($this->request->getPost('alloc_alternative') ?: 0);

        $totalAlloc = $allocE + $allocC + $allocG + $allocA;
        if (abs($totalAlloc - 100.0) > 0.01) {
            return redirect()->back()->withInput()->with(
                'error',
                "Total asset allocation must equal exactly 100%. Current total: {$totalAlloc}%."
            );
        }

        $pran           = trim($this->request->getPost('pran'));
        $subscriberName = trim($this->request->getPost('subscriber_name'));
        $pfmName        = trim($this->request->getPost('pfm_name'));
        $choice         = $this->request->getPost('investment_choice') ?: 'ACTIVE';

        $txDate         = $this->request->getPost('transaction_date');
        $grossAmount    = (float) $this->request->getPost('gross_amount');
        $optionalCharges= (float) ($this->request->getPost('optional_cash_charges') ?: 0);
        $netAmount      = max(0.0, $grossAmount - $optionalCharges);
        $contribType    = $this->request->getPost('contribution_type') ?: 'VOLUNTARY';
        $ackNo          = trim($this->request->getPost('acknowledgement_no')) ?: null;
        $notes          = trim($this->request->getPost('notes')) ?: 'Initial Tier 1 contribution';

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Insert PRAN Account
        $accountId = $this->accountModel->insert([
            'user_id'              => $userId,
            'pran'                 => $pran,
            'subscriber_name'            => $subscriberName,
            'pfm_name'                   => $pfmName,
            'investment_choice'          => $choice,
            'alloc_equity'               => $allocE,
            'alloc_corporate_debt'       => $allocC,
            'alloc_govt_bonds'           => $allocG,
            'alloc_alternative'          => $allocA,
            'scheme_code_equity'         => trim($this->request->getPost('scheme_code_equity') ?? '') ?: null,
            'scheme_code_corporate_debt' => trim($this->request->getPost('scheme_code_corporate_debt') ?? '') ?: null,
            'scheme_code_govt_bonds'     => trim($this->request->getPost('scheme_code_govt_bonds') ?? '') ?: null,
            'scheme_code_alternative'    => trim($this->request->getPost('scheme_code_alternative') ?? '') ?: null,
            'status'                     => 'ACTIVE',
        ]);

        // Determine Financial Year
        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);
        $financialYear = get_financial_year($txDate, $fyStartMonth);

        // 2. Insert Transaction Header
        $txId = $this->transactionModel->insert([
            'user_id'               => $userId,
            'nps_account_id'        => $accountId,
            'transaction_type'      => 'CONTRIBUTION',
            'contribution_type'     => $contribType,
            'transaction_date'      => $txDate,
            'acknowledgement_no'    => $ackNo,
            'gross_amount'          => $grossAmount,
            'optional_cash_charges' => $optionalCharges,
            'net_amount'            => $netAmount,
            'financial_year'        => $financialYear,
            'notes'                 => $notes,
        ]);

        // 3. Scheme Allotments (manual or calculated)
        $schemeData = [
            'SCHEME_E' => [
                'name'    => "{$pfmName} - Scheme E (Equity)",
                'amount'  => (float) ($this->request->getPost('amount_scheme_e') ?: ($netAmount * ($allocE / 100))),
                'nav'     => (float) ($this->request->getPost('nav_scheme_e') ?: 0),
                'units'   => (float) ($this->request->getPost('units_scheme_e') ?: 0),
            ],
            'SCHEME_C' => [
                'name'    => "{$pfmName} - Scheme C (Corporate Debt)",
                'amount'  => (float) ($this->request->getPost('amount_scheme_c') ?: ($netAmount * ($allocC / 100))),
                'nav'     => (float) ($this->request->getPost('nav_scheme_c') ?: 0),
                'units'   => (float) ($this->request->getPost('units_scheme_c') ?: 0),
            ],
            'SCHEME_G' => [
                'name'    => "{$pfmName} - Scheme G (Govt Securities)",
                'amount'  => (float) ($this->request->getPost('amount_scheme_g') ?: ($netAmount * ($allocG / 100))),
                'nav'     => (float) ($this->request->getPost('nav_scheme_g') ?: 0),
                'units'   => (float) ($this->request->getPost('units_scheme_g') ?: 0),
            ],
            'SCHEME_A' => [
                'name'    => "{$pfmName} - Scheme A (Alternative Assets)",
                'amount'  => (float) ($this->request->getPost('amount_scheme_a') ?: ($netAmount * ($allocA / 100))),
                'nav'     => (float) ($this->request->getPost('nav_scheme_a') ?: 0),
                'units'   => (float) ($this->request->getPost('units_scheme_a') ?: 0),
            ],
        ];

        foreach ($schemeData as $code => $sd) {
            if ($sd['units'] > 0 && $sd['nav'] > 0) {
                $this->schemeUnitModel->insert([
                    'user_id'            => $userId,
                    'nps_account_id'     => $accountId,
                    'nps_transaction_id' => $txId,
                    'scheme_type'        => $code,
                    'scheme_name'        => $sd['name'],
                    'transaction_type'   => 'CONTRIBUTION',
                    'transaction_date'   => $txDate,
                    'allocated_amount'   => $sd['amount'],
                    'nav'                => $sd['nav'],
                    'units'              => $sd['units'],
                    'remaining_units'    => $sd['units'],
                    'current_nav'        => $sd['nav'],
                    'nav_date'           => $txDate,
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create NPS Tier 1 account.');
        }

        return redirect()->to('/nps')->with('success', "NPS Tier 1 account for PRAN {$pran} registered successfully!");
    }

    /**
     * Minimal Popup Modal Handler:
     * Adds subsequent Contribution (Voluntary / Employer) OR Quarterly Unit Deduction.
     */
    public function addTransaction()
    {
        $userId = (int) session()->get('userId');

        $account = $this->accountModel->where('user_id', $userId)->where('status', 'ACTIVE')->first();
        if (!$account) {
            return redirect()->to('/nps/new')->with('error', 'No active NPS account found.');
        }

        $accountId  = (int) $account['id'];
        $actionType = strtoupper(trim($this->request->getPost('action_type')));

        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);

        if ($actionType === 'CONTRIBUTION') {
            $rules = [
                'transaction_date'  => 'required|valid_date',
                'gross_amount'      => 'required|numeric|greater_than[0]',
                'contribution_type' => 'required|in_list[VOLUNTARY,EMPLOYEE,EMPLOYER]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/nps')->with('errors', $this->validator->getErrors());
            }

            $txDate          = $this->request->getPost('transaction_date');
            $grossAmount     = (float) $this->request->getPost('gross_amount');
            $optionalCharges = (float) ($this->request->getPost('optional_cash_charges') ?: 0);
            $netAmount       = max(0.0, $grossAmount - $optionalCharges);
            $contribType     = $this->request->getPost('contribution_type') ?: 'VOLUNTARY';
            $ackNo           = trim($this->request->getPost('acknowledgement_no')) ?: null;
            $notes           = trim($this->request->getPost('notes')) ?: null;
            $financialYear   = get_financial_year($txDate, $fyStartMonth);

            $db = \Config\Database::connect();
            $db->transStart();

            // Insert Transaction Header
            $txId = $this->transactionModel->insert([
                'user_id'               => $userId,
                'nps_account_id'        => $accountId,
                'transaction_type'      => 'CONTRIBUTION',
                'contribution_type'     => $contribType,
                'transaction_date'      => $txDate,
                'acknowledgement_no'    => $ackNo,
                'gross_amount'          => $grossAmount,
                'optional_cash_charges' => $optionalCharges,
                'net_amount'            => $netAmount,
                'financial_year'        => $financialYear,
                'notes'                 => $notes,
            ]);

            // Schemes allotments with user-edited NAV and Units
            $schemes = ['SCHEME_E', 'SCHEME_C', 'SCHEME_G', 'SCHEME_A'];
            $allottedCount = 0;

            foreach ($schemes as $code) {
                $unitsKey  = 'units_' . strtolower($code);
                $navKey    = 'nav_' . strtolower($code);
                $amountKey = 'amount_' . strtolower($code);

                $units  = (float) ($this->request->getPost($unitsKey) ?: 0);
                $nav    = (float) ($this->request->getPost($navKey) ?: 0);
                $amount = (float) ($this->request->getPost($amountKey) ?: 0);

                if ($units > 0 && $nav > 0) {
                    $schemeTitle = match ($code) {
                        'SCHEME_E' => "{$account['pfm_name']} - Scheme E (Equity)",
                        'SCHEME_C' => "{$account['pfm_name']} - Scheme C (Corporate Debt)",
                        'SCHEME_G' => "{$account['pfm_name']} - Scheme G (Govt Securities)",
                        'SCHEME_A' => "{$account['pfm_name']} - Scheme A (Alternative Assets)",
                    };

                    $this->schemeUnitModel->insert([
                        'user_id'            => $userId,
                        'nps_account_id'     => $accountId,
                        'nps_transaction_id' => $txId,
                        'scheme_type'        => $code,
                        'scheme_name'        => $schemeTitle,
                        'transaction_type'   => 'CONTRIBUTION',
                        'transaction_date'   => $txDate,
                        'allocated_amount'   => $amount > 0 ? $amount : ($units * $nav),
                        'nav'                => $nav,
                        'units'              => $units,
                        'remaining_units'    => $units,
                        'current_nav'        => $nav,
                        'nav_date'           => $txDate,
                    ]);

                    $allottedCount++;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/nps')->with('error', 'Failed to record contribution.');
            }

            return redirect()->to('/nps')->with(
                'success',
                "Recorded {$contribType} contribution of " . format_inr($grossAmount) . " across {$allottedCount} scheme(s)."
            );
        }

        if ($actionType === 'UNIT_DEDUCTION') {
            $rules = [
                'deduct_date' => 'required|valid_date',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/nps')->with('errors', $this->validator->getErrors());
            }

            $deductDate    = $this->request->getPost('deduct_date');
            $quarterNotes  = trim($this->request->getPost('quarter_notes')) ?: 'Quarterly CRA/POP service charge deduction';
            $ackNo         = trim($this->request->getPost('acknowledgement_no')) ?: null;
            $financialYear = get_financial_year($deductDate, $fyStartMonth);

            $db = \Config\Database::connect();
            $db->transStart();

            $txId = $this->transactionModel->insert([
                'user_id'               => $userId,
                'nps_account_id'        => $accountId,
                'transaction_type'      => 'UNIT_DEDUCTION',
                'contribution_type'     => 'VOLUNTARY',
                'transaction_date'      => $deductDate,
                'acknowledgement_no'    => $ackNo,
                'gross_amount'          => 0.00,
                'optional_cash_charges' => 0.00,
                'net_amount'            => 0.00,
                'financial_year'        => $financialYear,
                'notes'                 => $quarterNotes,
            ]);

            $schemes = ['SCHEME_E', 'SCHEME_C', 'SCHEME_G', 'SCHEME_A'];
            $totalUnitsDeducted = 0.0;

            foreach ($schemes as $code) {
                $deductUnitsKey = 'deduct_units_' . strtolower($code);
                $deductNavKey   = 'deduct_nav_' . strtolower($code);

                $unitsVal = (float) ($this->request->getPost($deductUnitsKey) ?: 0);
                $navVal   = (float) ($this->request->getPost($deductNavKey) ?: 0);

                if ($unitsVal > 0) {
                    $schemeTitle = match ($code) {
                        'SCHEME_E' => "{$account['pfm_name']} - Scheme E (Equity)",
                        'SCHEME_C' => "{$account['pfm_name']} - Scheme C (Corporate Debt)",
                        'SCHEME_G' => "{$account['pfm_name']} - Scheme G (Govt Securities)",
                        'SCHEME_A' => "{$account['pfm_name']} - Scheme A (Alternative Assets)",
                    };

                    // Store deducted units as negative value
                    $negativeUnits = -1 * abs($unitsVal);

                    $this->schemeUnitModel->insert([
                        'user_id'            => $userId,
                        'nps_account_id'     => $accountId,
                        'nps_transaction_id' => $txId,
                        'scheme_type'        => $code,
                        'scheme_name'        => $schemeTitle,
                        'transaction_type'   => 'UNIT_DEDUCTION',
                        'transaction_date'   => $deductDate,
                        'allocated_amount'   => 0.00,
                        'nav'                => $navVal > 0 ? $navVal : 10.0000,
                        'units'              => $negativeUnits,
                        'remaining_units'    => 0.0000,
                        'current_nav'        => $navVal > 0 ? $navVal : 10.0000,
                        'nav_date'           => $deductDate,
                    ]);

                    $totalUnitsDeducted += $unitsVal;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/nps')->with('error', 'Failed to record quarterly unit deduction.');
            }

            return redirect()->to('/nps')->with(
                'success',
                "Recorded quarterly fee deduction of " . number_format($totalUnitsDeducted, 4) . " units across schemes."
            );
        }

        return redirect()->to('/nps')->with('error', 'Invalid transaction action.');
    }

    /**
     * Update scheme NAVs (supports both manual modal submit and npsnav.in auto-update).
     */
    public function updateNavs()
    {
        $userId = (int) session()->get('userId');

        $account = $this->accountModel->where('user_id', $userId)->where('status', 'ACTIVE')->first();
        if (!$account) {
            return redirect()->to('/nps')->with('error', 'No active NPS account found.');
        }

        // If manual NAVs were posted via modal:
        if ($this->request->getMethod() === 'post' && $this->request->getPost('nav_e') !== null) {
            $accountId  = (int) $account['id'];
            $navDate    = $this->request->getPost('nav_date') ?: date('Y-m-d');
            $schemeNavs = [
                'SCHEME_E' => (float) ($this->request->getPost('nav_e') ?: 0),
                'SCHEME_C' => (float) ($this->request->getPost('nav_c') ?: 0),
                'SCHEME_G' => (float) ($this->request->getPost('nav_g') ?: 0),
                'SCHEME_A' => (float) ($this->request->getPost('nav_a') ?: 0),
            ];

            $this->schemeUnitModel->updateCurrentNavs($accountId, $schemeNavs, $navDate);
            $this->accountModel->update($accountId, ['nav_last_updated' => $navDate]);

            return redirect()->to('/nps')->with('success', 'Latest scheme NAVs updated successfully!');
        }

        // Otherwise auto-update from npsnav.in API
        return $this->autoUpdateNavsFromApi($account);
    }

    /**
     * Delete NPS Account and all child transactions.
     */
    public function deleteAccount(int $id)
    {
        $userId = (int) session()->get('userId');
        $account = $this->accountModel->where('user_id', $userId)->find($id);

        if (!$account) {
            return redirect()->to('/nps')->with('error', 'NPS account not found.');
        }

        $this->accountModel->delete($id);

        return redirect()->to('/nps')->with('success', "PRAN {$account['pran']} account removed.");
    }

    /**
     * Edit NPS Tier 1 account metadata & target allocation.
     */
    public function updateAccount()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('account_id');

        $account = $this->accountModel->where('user_id', $userId)->find($id);
        if (!$account) {
            return redirect()->to('/nps')->with('error', 'NPS account not found.');
        }

        $rules = [
            'subscriber_name'      => 'required|min_length[2]|max_length[100]',
            'pfm_name'             => 'required|min_length[2]|max_length[100]',
            'investment_choice'    => 'required|in_list[ACTIVE,AUTO]',
            'alloc_equity'         => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'alloc_corporate_debt' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'alloc_govt_bonds'     => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'alloc_alternative'    => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/nps')->with('errors', $this->validator->getErrors());
        }

        $allocE = (float) $this->request->getPost('alloc_equity');
        $allocC = (float) $this->request->getPost('alloc_corporate_debt');
        $allocG = (float) $this->request->getPost('alloc_govt_bonds');
        $allocA = (float) ($this->request->getPost('alloc_alternative') ?: 0);

        $totalAlloc = $allocE + $allocC + $allocG + $allocA;
        if (abs($totalAlloc - 100.0) > 0.01) {
            return redirect()->to('/nps')->with('error', "Total asset allocation must equal 100%. Currently: {$totalAlloc}%.");
        }

        $this->accountModel->update($id, [
            'subscriber_name'            => trim($this->request->getPost('subscriber_name')),
            'pfm_name'                   => trim($this->request->getPost('pfm_name')),
            'investment_choice'          => $this->request->getPost('investment_choice'),
            'alloc_equity'               => $allocE,
            'alloc_corporate_debt'       => $allocC,
            'alloc_govt_bonds'           => $allocG,
            'alloc_alternative'          => $allocA,
            'scheme_code_equity'         => trim($this->request->getPost('scheme_code_equity') ?? '') ?: null,
            'scheme_code_corporate_debt' => trim($this->request->getPost('scheme_code_corporate_debt') ?? '') ?: null,
            'scheme_code_govt_bonds'     => trim($this->request->getPost('scheme_code_govt_bonds') ?? '') ?: null,
            'scheme_code_alternative'    => trim($this->request->getPost('scheme_code_alternative') ?? '') ?: null,
        ]);

        return redirect()->to('/nps')->with('success', "NPS account details and allocation updated successfully.");
    }

    /**
     * Update an existing NPS Contribution or Fee Deduction transaction.
     */
    public function updateTransaction()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('transaction_id');

        $tx = $this->transactionModel->where('user_id', $userId)->find($id);
        if (!$tx) {
            return redirect()->to('/nps')->with('error', 'Transaction not found.');
        }

        $accountId = (int) $tx['nps_account_id'];
        $account   = $this->accountModel->where('user_id', $userId)->find($accountId);

        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);

        $db = \Config\Database::connect();
        $db->transStart();

        if ($tx['transaction_type'] === 'CONTRIBUTION') {
            $rules = [
                'transaction_date'  => 'required|valid_date',
                'gross_amount'      => 'required|numeric|greater_than[0]',
                'contribution_type' => 'required|in_list[VOLUNTARY,EMPLOYEE,EMPLOYER]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/nps')->with('errors', $this->validator->getErrors());
            }

            $txDate          = $this->request->getPost('transaction_date');
            $grossAmount     = (float) $this->request->getPost('gross_amount');
            $optionalCharges = (float) ($this->request->getPost('optional_cash_charges') ?: 0);
            $netAmount       = max(0.0, $grossAmount - $optionalCharges);
            $contribType     = $this->request->getPost('contribution_type') ?: 'VOLUNTARY';
            $ackNo           = trim($this->request->getPost('acknowledgement_no')) ?: null;
            $notes           = trim($this->request->getPost('notes')) ?: null;
            $financialYear   = get_financial_year($txDate, $fyStartMonth);

            $this->transactionModel->update($id, [
                'transaction_date'      => $txDate,
                'contribution_type'     => $contribType,
                'acknowledgement_no'    => $ackNo,
                'gross_amount'          => $grossAmount,
                'optional_cash_charges' => $optionalCharges,
                'net_amount'            => $netAmount,
                'financial_year'        => $financialYear,
                'notes'                 => $notes,
            ]);

            // Remove existing scheme units for this transaction
            $db->table('nps_scheme_units')->where('nps_transaction_id', $id)->delete();

            // Re-insert updated scheme allotments
            $schemes = ['SCHEME_E', 'SCHEME_C', 'SCHEME_G', 'SCHEME_A'];
            foreach ($schemes as $code) {
                $unitsKey  = 'units_' . strtolower($code);
                $navKey    = 'nav_' . strtolower($code);
                $amountKey = 'amount_' . strtolower($code);

                $units  = (float) ($this->request->getPost($unitsKey) ?: 0);
                $nav    = (float) ($this->request->getPost($navKey) ?: 0);
                $amount = (float) ($this->request->getPost($amountKey) ?: 0);

                if ($units > 0 && $nav > 0) {
                    $schemeTitle = match ($code) {
                        'SCHEME_E' => "{$account['pfm_name']} - Scheme E (Equity)",
                        'SCHEME_C' => "{$account['pfm_name']} - Scheme C (Corporate Debt)",
                        'SCHEME_G' => "{$account['pfm_name']} - Scheme G (Govt Securities)",
                        'SCHEME_A' => "{$account['pfm_name']} - Scheme A (Alternative Assets)",
                    };

                    $this->schemeUnitModel->insert([
                        'user_id'            => $userId,
                        'nps_account_id'     => $accountId,
                        'nps_transaction_id' => $id,
                        'scheme_type'        => $code,
                        'scheme_name'        => $schemeTitle,
                        'transaction_type'   => 'CONTRIBUTION',
                        'transaction_date'   => $txDate,
                        'allocated_amount'   => $amount > 0 ? $amount : ($units * $nav),
                        'nav'                => $nav,
                        'units'              => $units,
                        'remaining_units'    => $units,
                        'current_nav'        => $nav,
                        'nav_date'           => $txDate,
                    ]);
                }
            }
        } elseif ($tx['transaction_type'] === 'UNIT_DEDUCTION') {
            $rules = [
                'transaction_date' => 'required|valid_date',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/nps')->with('errors', $this->validator->getErrors());
            }

            $deductDate    = $this->request->getPost('transaction_date');
            $quarterNotes  = trim($this->request->getPost('notes')) ?: 'Quarterly CRA/POP service charge deduction';
            $ackNo         = trim($this->request->getPost('acknowledgement_no')) ?: null;
            $financialYear = get_financial_year($deductDate, $fyStartMonth);

            $this->transactionModel->update($id, [
                'transaction_date'   => $deductDate,
                'acknowledgement_no' => $ackNo,
                'financial_year'     => $financialYear,
                'notes'              => $quarterNotes,
            ]);

            // Remove existing scheme units for this transaction
            $db->table('nps_scheme_units')->where('nps_transaction_id', $id)->delete();

            $schemes = ['SCHEME_E', 'SCHEME_C', 'SCHEME_G', 'SCHEME_A'];
            foreach ($schemes as $code) {
                $deductUnitsKey = 'deduct_units_' . strtolower($code);
                $deductNavKey   = 'deduct_nav_' . strtolower($code);

                $unitsVal = (float) ($this->request->getPost($deductUnitsKey) ?: 0);
                $navVal   = (float) ($this->request->getPost($deductNavKey) ?: 0);

                if ($unitsVal > 0) {
                    $schemeTitle = match ($code) {
                        'SCHEME_E' => "{$account['pfm_name']} - Scheme E (Equity)",
                        'SCHEME_C' => "{$account['pfm_name']} - Scheme C (Corporate Debt)",
                        'SCHEME_G' => "{$account['pfm_name']} - Scheme G (Govt Securities)",
                        'SCHEME_A' => "{$account['pfm_name']} - Scheme A (Alternative Assets)",
                    };

                    $this->schemeUnitModel->insert([
                        'user_id'            => $userId,
                        'nps_account_id'     => $accountId,
                        'nps_transaction_id' => $id,
                        'scheme_type'        => $code,
                        'scheme_name'        => $schemeTitle,
                        'transaction_type'   => 'UNIT_DEDUCTION',
                        'transaction_date'   => $deductDate,
                        'allocated_amount'   => 0.00,
                        'nav'                => $navVal > 0 ? $navVal : 10.0000,
                        'units'              => -1 * abs($unitsVal),
                        'remaining_units'    => 0.0000,
                        'current_nav'        => $navVal > 0 ? $navVal : 10.0000,
                        'nav_date'           => $deductDate,
                    ]);
                }
            }
        }

        try {
            $this->transactionModel->validateAccountUnits($userId, $accountId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/nps')->with('error', 'Failed to update transaction.');
            }

            return redirect()->to('/nps')->with('success', 'NPS transaction updated successfully.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/nps')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an NPS transaction with inventory underflow check.
     */
    public function deleteTransaction(int $id)
    {
        $userId = (int) session()->get('userId');
        $tx = $this->transactionModel->where('user_id', $userId)->find($id);

        if (!$tx) {
            return redirect()->to('/nps')->with('error', 'Transaction not found.');
        }

        $accountId = (int) $tx['nps_account_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        // Foreign keys cascade delete from nps_scheme_units
        $this->transactionModel->delete($id);

        try {
            $this->transactionModel->validateAccountUnits($userId, $accountId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/nps')->with('error', 'Failed to delete transaction.');
            }

            return redirect()->to('/nps')->with('success', 'NPS transaction deleted successfully.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/nps')->with('error', $e->getMessage());
        }
    }

    /**
     * Auto-Update Current NAVs from npsnav.in API helper.
     */
    protected function autoUpdateNavsFromApi(array $account)
    {
        $schemeCodes = [
            'SCHEME_E' => trim($account['scheme_code_equity'] ?? ''),
            'SCHEME_C' => trim($account['scheme_code_corporate_debt'] ?? ''),
            'SCHEME_G' => trim($account['scheme_code_govt_bonds'] ?? ''),
            'SCHEME_A' => trim($account['scheme_code_alternative'] ?? ''),
        ];

        // Filter out empty scheme codes
        $validCodes = array_filter($schemeCodes);
        if (empty($validCodes)) {
            return redirect()->to('/nps')->with('error', 'Please configure your NPS Scheme Codes in Account Settings first.');
        }

        // Fetch API from https://npsnav.in/api/latest-min
        $apiUrl = 'https://npsnav.in/api/latest-min';
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyFolioVault Portfolio Tracker');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response || $httpCode !== 200) {
            return redirect()->to('/nps')->with('error', "Failed to connect to npsnav.in API (HTTP {$httpCode}). Please check your connection and try again.");
        }

        $data = json_decode($response, true);
        if (!isset($data['data']) || !is_array($data['data'])) {
            return redirect()->to('/nps')->with('error', 'Invalid response format from npsnav.in API.');
        }

        // Build code => nav map
        $navMap = [];
        foreach ($data['data'] as $item) {
            if (isset($item[0], $item[1])) {
                $navMap[$item[0]] = (float) $item[1];
            }
        }

        $lastUpdated = $data['metadata']['lastUpdated'] ?? date('d-m-Y');
        // Convert d-m-Y to Y-m-d for database storage
        $navDate = date('Y-m-d');
        if (!empty($data['metadata']['lastUpdated'])) {
            $parsedDate = date_create_from_format('d-m-Y', $data['metadata']['lastUpdated']);
            if ($parsedDate) {
                $navDate = $parsedDate->format('Y-m-d');
            }
        }

        $db = \Config\Database::connect();
        $updatedSchemes = [];

        foreach ($schemeCodes as $schemeType => $code) {
            if (!empty($code) && isset($navMap[$code])) {
                $newNav = $navMap[$code];
                // Update unit rows for this account and scheme type
                $db->table('nps_scheme_units')
                    ->where('nps_account_id', $account['id'])
                    ->where('scheme_type', $schemeType)
                    ->update([
                        'current_nav' => $newNav,
                        'nav_date'    => $navDate,
                    ]);
                $shortName = match($schemeType) {
                    'SCHEME_E' => 'Scheme E',
                    'SCHEME_C' => 'Scheme C',
                    'SCHEME_G' => 'Scheme G',
                    'SCHEME_A' => 'Scheme A',
                    default    => $schemeType,
                };
                $updatedSchemes[] = "{$shortName}: ₹" . number_format($newNav, 4);
            }
        }

        // Update nav_last_updated on account
        $this->accountModel->update($account['id'], [
            'nav_last_updated' => $navDate,
        ]);

        if (empty($updatedSchemes)) {
            return redirect()->to('/nps')->with('info', "Checked npsnav.in but could not find matching NAVs for codes: " . implode(', ', $validCodes));
        }

        return redirect()->to('/nps')->with('success', "NPS NAVs updated from npsnav.in (as of {$lastUpdated}): " . implode(' | ', $updatedSchemes));
    }
}

