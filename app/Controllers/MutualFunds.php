<?php

namespace App\Controllers;

use App\Models\MutualFundCapitalGainsModel;
use App\Models\MutualFundModel;
use App\Models\MutualFundTransactionModel;
use Exception;

class MutualFunds extends BaseController
{
    protected MutualFundModel $fundModel;
    protected MutualFundTransactionModel $transactionModel;
    protected MutualFundCapitalGainsModel $capitalGainsModel;

    public function __construct()
    {
        helper(['currency']);
        $this->fundModel          = new MutualFundModel();
        $this->transactionModel   = new MutualFundTransactionModel();
        $this->capitalGainsModel  = new MutualFundCapitalGainsModel();
    }

    /**
     * Mutual Funds Dashboard: Portfolio valuation, active folios, and FIFO tax audit logs.
     */
    public function index()
    {
        $userId = (int) session()->get('userId');

        $metricsData  = $this->fundModel->getHoldingsWithMetrics($userId);
        $transactions = $this->transactionModel->getTransactionsWithFund($userId);
        $capitalGains = $this->capitalGainsModel->getCapitalGainsWithFund($userId);

        return view('mutual_funds/index', [
            'title'          => 'Mutual Funds Portfolio - RupeeFolio',
            'holdings'       => $metricsData['holdings'],
            'activeHoldings' => $metricsData['active_holdings'] ?? $metricsData['holdings'],
            'pastHoldings'   => $metricsData['past_holdings'] ?? [],
            'summary'        => $metricsData['summary'],
            'transactions'   => $transactions,
            'capitalGains'   => $capitalGains,
        ]);
    }

    /**
     * Display New Mutual Fund Entry form.
     */
    public function newFund()
    {
        return view('mutual_funds/new_fund', [
            'title' => 'New Mutual Fund Purchase - RupeeFolio',
        ]);
    }

    /**
     * AJAX/API endpoint: Lookup scheme details directly from AMFI endpoint (api.mfapi.in).
     */
    public function lookupAmfi(string $code)
    {
        $cleanCode = preg_replace('/[^0-9]/', '', $code);
        if (empty($cleanCode)) {
            return $this->response->setJSON(['status' => 'ERROR', 'message' => 'Invalid AMFI Code.']);
        }

        $url = "https://api.mfapi.in/mf/{$cleanCode}/latest";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return $this->response->setJSON(['status' => 'ERROR', 'message' => 'Could not connect to AMFI server.']);
        }

        $json = json_decode($response, true);
        if (!isset($json['status']) || $json['status'] !== 'SUCCESS') {
            return $this->response->setJSON(['status' => 'ERROR', 'message' => 'Scheme not found for this AMFI code.']);
        }

        $meta = $json['meta'] ?? [];
        $latest = $json['data'][0] ?? [];

        return $this->response->setJSON([
            'status'          => 'SUCCESS',
            'scheme_name'     => $meta['scheme_name'] ?? '',
            'fund_house'      => $meta['fund_house'] ?? '',
            'scheme_category' => $meta['scheme_category'] ?? '',
            'isin'            => $meta['isin_growth'] ?? '',
            'nav'             => (float) ($latest['nav'] ?? 0),
            'nav_date'        => $latest['date'] ?? '',
        ]);
    }

    /**
     * Process First-Time Mutual Fund Acquisition.
     */
    public function createFund()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'amfi_code'        => 'required|min_length[3]|max_length[20]',
            'scheme_name'      => 'required|min_length[2]|max_length[180]',
            'folio_number'     => 'required|min_length[2]|max_length[50]',
            'category'         => 'required|max_length[80]',
            'fund_house'       => 'permit_empty|max_length[100]',
            'transaction_type' => 'required|in_list[BUY_SIP,BUY_LUMPSUM]',
            'transaction_date' => 'required|valid_date',
            'units'            => 'required|decimal',
            'nav'              => 'required|decimal',
            'amount'           => 'required|decimal',
            'charges'          => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amfiCode    = trim($this->request->getPost('amfi_code'));
        $folioNumber = trim($this->request->getPost('folio_number'));
        $schemeName  = trim($this->request->getPost('scheme_name'));
        $category    = trim($this->request->getPost('category'));
        $fundHouse   = trim($this->request->getPost('fund_house')) ?: null;
        $type        = $this->request->getPost('transaction_type');
        $date        = $this->request->getPost('transaction_date');
        $units       = (float) $this->request->getPost('units');
        $nav         = (float) $this->request->getPost('nav');
        $amount      = (float) $this->request->getPost('amount');
        $charges     = (float) ($this->request->getPost('charges') ?: 0);
        $notes       = trim($this->request->getPost('notes')) ?: null;

        // Check if folio already exists for this scheme
        $existing = $this->fundModel->findByCodeAndFolio($amfiCode, $folioNumber, $userId);
        if ($existing) {
            return redirect()->back()->withInput()->with(
                'error',
                "Folio '{$folioNumber}' for AMFI {$amfiCode} is already registered! Please use 'Add Transaction' on the dashboard."
            );
        }

        // Insert new scheme into mutual_funds master table
        $fundId = $this->fundModel->insert([
            'user_id'      => $userId,
            'amfi_code'    => $amfiCode,
            'scheme_name'  => $schemeName,
            'folio_number' => $folioNumber,
            'category'     => $category,
            'fund_house'   => $fundHouse,
            'current_nav'  => $nav,
            'nav_date'     => $date,
        ]);

        // Record initial BUY lot in mutual_fund_transactions (FIFO remaining_units = units)
        $this->transactionModel->recordBuy(
            $userId,
            $fundId,
            $type,
            $date,
            $units,
            $nav,
            $amount,
            $charges,
            $notes ?: 'Initial investment lot'
        );

        return redirect()->to('/mutual-funds')->with(
            'success',
            "Mutual Fund '{$schemeName}' added with initial lot of " . number_format($units, 4) . ' units!'
        );
    }

    /**
     * Minimal Popup Modal Handler: Buy (SIP / Lumpsum) or Redeem (FIFO).
     */
    public function addTransaction()
    {
        $userId = (int) session()->get('userId');

        $fundId     = (int) $this->request->getPost('mutual_fund_id');
        $actionType = strtoupper(trim($this->request->getPost('action_type')));

        $fund = $this->fundModel->where('user_id', $userId)->find($fundId);
        if (!$fund) {
            return redirect()->to('/mutual-funds')->with('error', 'Mutual Fund scheme not found.');
        }

        if ($actionType === 'BUY') {
            $rules = [
                'buy_type'         => 'required|in_list[BUY_SIP,BUY_LUMPSUM]',
                'transaction_date' => 'required|valid_date',
                'units'            => 'required|decimal',
                'nav'              => 'required|decimal',
                'amount'           => 'required|decimal',
                'charges'          => 'permit_empty|decimal',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/mutual-funds')->with('errors', $this->validator->getErrors());
            }

            $buyType = $this->request->getPost('buy_type');
            $date    = $this->request->getPost('transaction_date');
            $units   = (float) $this->request->getPost('units');
            $nav     = (float) $this->request->getPost('nav');
            $amount  = (float) $this->request->getPost('amount');
            $charges = (float) ($this->request->getPost('charges') ?: 0);
            $notes   = trim($this->request->getPost('notes')) ?: null;

            $this->transactionModel->recordBuy($userId, $fundId, $buyType, $date, $units, $nav, $amount, $charges, $notes);

            $typeLabel = $buyType === 'BUY_SIP' ? 'SIP installment' : 'Lumpsum';

            return redirect()->to('/mutual-funds')->with(
                'success',
                "Recorded {$typeLabel} purchase of " . number_format($units, 4) . " units @ " . format_inr($nav) . " (FIFO lot created)."
            );
        }

        if ($actionType === 'REDEEM') {
            $rules = [
                'transaction_date' => 'required|valid_date',
                'units'            => 'required|decimal',
                'nav'              => 'required|decimal',
                'charges'          => 'permit_empty|decimal',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/mutual-funds')->with('errors', $this->validator->getErrors());
            }

            $date    = $this->request->getPost('transaction_date');
            $units   = (float) $this->request->getPost('units');
            $nav     = (float) $this->request->getPost('nav');
            $charges = (float) ($this->request->getPost('charges') ?: 0);
            $notes   = trim($this->request->getPost('notes')) ?: null;

            try {
                $result = $this->transactionModel->recordRedeemFIFO(
                    $userId,
                    $fundId,
                    $date,
                    $units,
                    $nav,
                    $charges,
                    $notes
                );

                $totalGain = array_sum(array_column($result['matched_lots'], 'realized_gain'));
                $gainSign = $totalGain >= 0 ? '+' : '';
                $msg = "Redeemed " . number_format($units, 4) . " units of {$fund['scheme_name']} @ " . format_inr($nav) . 
                       ". Realized P&L: {$gainSign}" . format_inr($totalGain) . ' (FIFO tax audit logged).';

                return redirect()->to('/mutual-funds')->with('success', $msg);
            } catch (Exception $e) {
                return redirect()->to('/mutual-funds')->with('error', $e->getMessage());
            }
        }

        return redirect()->to('/mutual-funds')->with('error', 'Invalid transaction action.');
    }

    /**
     * Refresh live NAVs from official AMFI open endpoint (api.mfapi.in).
     */
    public function refreshNavs()
    {
        $userId = (int) session()->get('userId');
        $funds  = $this->fundModel->where('user_id', $userId)->findAll();

        if (empty($funds)) {
            return redirect()->to('/mutual-funds')->with('info', 'No mutual funds in portfolio to refresh.');
        }

        $updatedCount = 0;
        $amfiCache = [];

        foreach ($funds as $fund) {
            $amfiCode = trim($fund['amfi_code']);

            if (isset($amfiCache[$amfiCode])) {
                $data = $amfiCache[$amfiCode];
            } else {
                $url = "https://api.mfapi.in/mf/{$amfiCode}/latest";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

                $response = curl_exec($ch);
                curl_close($ch);

                $data = null;
                if ($response) {
                    $json = json_decode($response, true);
                    if (isset($json['status']) && $json['status'] === 'SUCCESS' && !empty($json['data'])) {
                        $data = $json['data'][0];
                        $amfiCache[$amfiCode] = $data;
                    }
                }
            }

            if ($data && isset($data['nav'])) {
                $newNav = (float) $data['nav'];
                $navDate = isset($data['date']) ? date('Y-m-d', strtotime($data['date'])) : date('Y-m-d');

                $this->fundModel->update($fund['id'], [
                    'current_nav' => $newNav,
                    'nav_date'    => $navDate,
                ]);
                $updatedCount++;
            }
        }

        return redirect()->to('/mutual-funds')->with(
            'success',
            "Live AMFI NAVs refreshed for {$updatedCount} of " . count($funds) . ' scheme(s) via AMFI open API.'
        );
    }

    /**
     * Delete a Mutual Fund from portfolio.
     */
    public function deleteFund(int $id)
    {
        $userId = (int) session()->get('userId');
        $fund   = $this->fundModel->where('user_id', $userId)->find($id);

        if (!$fund) {
            return redirect()->to('/mutual-funds')->with('error', 'Fund not found.');
        }

        $this->fundModel->delete($id);

        return redirect()->to('/mutual-funds')->with('success', "Scheme {$fund['scheme_name']} removed from portfolio.");
    }

    /**
     * Edit existing Mutual Fund scheme metadata.
     */
    public function updateFund()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('fund_id');

        $fund = $this->fundModel->where('user_id', $userId)->find($id);
        if (!$fund) {
            return redirect()->to('/mutual-funds')->with('error', 'Mutual Fund not found.');
        }

        $rules = [
            'scheme_name'  => 'required|min_length[3]|max_length[200]',
            'category'     => 'required|in_list[Equity - Large Cap,Equity - Mid Cap,Equity - Small Cap,Equity - Flexi Cap,Equity - ELSS,Hybrid - Aggressive,Hybrid - Balanced Advantage,Debt - Liquid,Debt - Short Term,Index Fund]',
            'folio_number' => 'permit_empty|max_length[50]',
            'fund_house'   => 'permit_empty|max_length[100]',
            'amfi_code'    => 'permit_empty|max_length[20]',
            'isin'         => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/mutual-funds')->with('errors', $this->validator->getErrors());
        }

        $this->fundModel->update($id, [
            'scheme_name'  => trim($this->request->getPost('scheme_name')),
            'category'     => trim($this->request->getPost('category')),
            'folio_number' => trim($this->request->getPost('folio_number')) ?: null,
            'fund_house'   => trim($this->request->getPost('fund_house')) ?: null,
            'amfi_code'    => trim($this->request->getPost('amfi_code')) ?: null,
            'isin'         => strtoupper(trim($this->request->getPost('isin'))) ?: null,
        ]);

        return redirect()->to('/mutual-funds')->with('success', "Scheme '{$fund['scheme_name']}' updated successfully.");
    }

    /**
     * Edit an existing Mutual Fund transaction with automated FIFO recalculation.
     */
    public function updateTransaction()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('transaction_id');

        $tx = $this->transactionModel->where('user_id', $userId)->find($id);
        if (!$tx) {
            return redirect()->to('/mutual-funds')->with('error', 'Transaction not found.');
        }

        $rules = [
            'transaction_date' => 'required|valid_date',
            'units'            => 'required|decimal',
            'nav'              => 'required|decimal',
            'charges'          => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/mutual-funds')->with('errors', $this->validator->getErrors());
        }

        $date    = $this->request->getPost('transaction_date');
        $units   = (float) $this->request->getPost('units');
        $nav     = (float) $this->request->getPost('nav');
        $charges = (float) ($this->request->getPost('charges') ?: 0);
        $notes   = trim($this->request->getPost('notes')) ?: null;

        if ($units <= 0 || $nav <= 0) {
            return redirect()->to('/mutual-funds')->with('error', 'Units and NAV must be greater than zero.');
        }

        $amount    = $units * $nav;
        $netAmount = in_array($tx['transaction_type'], ['BUY_SIP', 'BUY_LUMPSUM'])
            ? $amount + $charges
            : $amount - $charges;

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->update($id, [
            'transaction_date' => $date,
            'units'            => $units,
            'nav'              => $nav,
            'amount'           => $amount,
            'charges'          => $charges,
            'net_amount'       => $netAmount,
            'notes'            => $notes,
        ]);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, (int) $tx['mutual_fund_id']);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/mutual-funds')->with('error', 'Failed to update transaction.');
            }

            return redirect()->to('/mutual-funds')->with('success', "Mutual fund transaction updated successfully with FIFO lot rebalancing.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/mutual-funds')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an existing Mutual Fund transaction with automated FIFO recalculation.
     */
    public function deleteTransaction(int $id)
    {
        $userId = (int) session()->get('userId');
        $tx = $this->transactionModel->where('user_id', $userId)->find($id);

        if (!$tx) {
            return redirect()->to('/mutual-funds')->with('error', 'Transaction not found.');
        }

        $fundId = (int) $tx['mutual_fund_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->delete($id);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, $fundId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/mutual-funds')->with('error', 'Failed to delete transaction.');
            }

            return redirect()->to('/mutual-funds')->with('success', "Mutual fund transaction deleted successfully with FIFO lot rebalancing.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/mutual-funds')->with('error', $e->getMessage());
        }
    }
}

