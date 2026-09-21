<?php

namespace App\Controllers;

use App\Models\ReitInvitCapitalGainsModel;
use App\Models\ReitInvitDistributionModel;
use App\Models\ReitInvitModel;
use App\Models\ReitInvitTransactionModel;
use App\Models\UserModel;
use Exception;

class ReitsInvits extends BaseController
{
    protected ReitInvitModel $trustModel;
    protected ReitInvitTransactionModel $transactionModel;
    protected ReitInvitDistributionModel $distributionModel;
    protected ReitInvitCapitalGainsModel $capitalGainsModel;
    protected UserModel $userModel;

    public function __construct()
    {
        helper(['currency']);
        $this->trustModel         = new ReitInvitModel();
        $this->transactionModel   = new ReitInvitTransactionModel();
        $this->distributionModel  = new ReitInvitDistributionModel();
        $this->capitalGainsModel  = new ReitInvitCapitalGainsModel();
        $this->userModel          = new UserModel();
    }

    /**
     * REITs & InvITs Dashboard: Holdings, live CMP, distribution income ledger, and FIFO tax log.
     */
    public function index()
    {
        $userId = (int) session()->get('userId');

        $metricsData   = $this->trustModel->getHoldingsWithMetrics($userId);
        $transactions  = $this->transactionModel->getTransactionsWithTrust($userId);
        $distributions = $this->distributionModel->getDistributionsWithTrust($userId);
        $capitalGains  = $this->capitalGainsModel->getCapitalGainsWithTrust($userId);

        return view('reits_invits/index', [
            'title'         => 'InvITs & REITs Portfolio - WealthPulse',
            'holdings'      => $metricsData['holdings'],
            'summary'       => $metricsData['summary'],
            'transactions'  => $transactions,
            'distributions' => $distributions,
            'capitalGains'  => $capitalGains,
        ]);
    }

    /**
     * Display New Trust Entry form.
     */
    public function newTrust()
    {
        return view('reits_invits/new_trust', [
            'title' => 'New REIT / InvIT Purchase - WealthPulse',
        ]);
    }

    /**
     * Process First-Time REIT/InvIT Acquisition.
     */
    public function createTrust()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'trust_name'       => 'required|min_length[3]|max_length[150]',
            'symbol'           => 'required|min_length[2]|max_length[30]',
            'isin'             => 'required|min_length[5]|max_length[20]',
            'trust_type'       => 'required|in_list[REIT,INVIT]',
            'exchange'         => 'required|in_list[NSE,BSE]',
            'sponsor'          => 'permit_empty|max_length[100]',
            'transaction_date' => 'required|valid_date',
            'quantity'         => 'required|numeric|greater_than[0]',
            'price'            => 'required|numeric|greater_than[0]',
            'brokerage'        => 'permit_empty|numeric',
            'stt_taxes'        => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $symbol   = strtoupper(trim($this->request->getPost('symbol')));
        $exchange = strtoupper(trim($this->request->getPost('exchange')));

        // Check if trust already exists
        $existing = $this->trustModel->where('user_id', $userId)
                                     ->where('symbol', $symbol)
                                     ->where('exchange', $exchange)
                                     ->first();
        if ($existing) {
            return redirect()->back()->withInput()->with(
                'error',
                "Trust '{$symbol}' ({$exchange}) already exists in your portfolio! Please use 'Add Transaction' on the dashboard."
            );
        }

        $name      = trim($this->request->getPost('trust_name'));
        $isin      = strtoupper(trim($this->request->getPost('isin')));
        $type      = $this->request->getPost('trust_type');
        $sponsor   = trim($this->request->getPost('sponsor')) ?: null;

        $txDate    = $this->request->getPost('transaction_date');
        $quantity  = (float) $this->request->getPost('quantity');
        $price     = (float) $this->request->getPost('price');
        $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
        $stt       = (float) ($this->request->getPost('stt_taxes') ?: 0);
        $notes     = trim($this->request->getPost('notes')) ?: 'Initial trust acquisition';

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Insert into reits_invits master
        $trustId = $this->trustModel->insert([
            'user_id'           => $userId,
            'trust_name'        => $name,
            'symbol'            => $symbol,
            'isin'              => $isin,
            'trust_type'        => $type,
            'exchange'          => $exchange,
            'sponsor'           => $sponsor,
            'current_price'     => $price,
            'prev_close'        => $price,
            'last_price_update' => date('Y-m-d H:i:s'),
        ]);

        // 2. Record initial BUY lot
        $this->transactionModel->recordBuy(
            $userId,
            $trustId,
            $txDate,
            $quantity,
            $price,
            $brokerage,
            $stt,
            $notes
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to register new trust.');
        }

        return redirect()->to('/reits-invits')->with(
            'success',
            "{$type} '{$name}' added with initial lot of " . number_format($quantity, 4) . ' units!'
        );
    }

    /**
     * Minimal Popup Modal Handler: Buy More, Sell (FIFO), or Record Distribution.
     */
    public function addTransaction()
    {
        $userId = (int) session()->get('userId');

        $trustId    = (int) $this->request->getPost('trust_id');
        $actionType = strtoupper(trim($this->request->getPost('action_type')));

        $trust = $this->trustModel->where('user_id', $userId)->find($trustId);
        if (!$trust) {
            return redirect()->to('/reits-invits')->with('error', 'Trust not found.');
        }

        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);

        // ACTION 1: BUY MORE
        if ($actionType === 'BUY') {
            $rules = [
                'transaction_date' => 'required|valid_date',
                'quantity'         => 'required|numeric|greater_than[0]',
                'price'            => 'required|numeric|greater_than[0]',
                'brokerage'        => 'permit_empty|numeric',
                'stt_taxes'        => 'permit_empty|numeric',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/reits-invits')->with('errors', $this->validator->getErrors());
            }

            $date      = $this->request->getPost('transaction_date');
            $qty       = (float) $this->request->getPost('quantity');
            $price     = (float) $this->request->getPost('price');
            $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
            $stt       = (float) ($this->request->getPost('stt_taxes') ?: 0);
            $notes     = trim($this->request->getPost('notes')) ?: null;

            $this->transactionModel->recordBuy($userId, $trustId, $date, $qty, $price, $brokerage, $stt, $notes);

            return redirect()->to('/reits-invits')->with(
                'success',
                "Added purchase lot of " . number_format($qty, 4) . " units of {$trust['symbol']} @ " . format_inr($price) . "."
            );
        }

        // ACTION 2: SELL (FIFO)
        if ($actionType === 'SELL') {
            $rules = [
                'transaction_date' => 'required|valid_date',
                'quantity'         => 'required|numeric|greater_than[0]',
                'price'            => 'required|numeric|greater_than[0]',
                'brokerage'        => 'permit_empty|numeric',
                'stt_taxes'        => 'permit_empty|numeric',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/reits-invits')->with('errors', $this->validator->getErrors());
            }

            $date      = $this->request->getPost('transaction_date');
            $qty       = (float) $this->request->getPost('quantity');
            $price     = (float) $this->request->getPost('price');
            $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
            $stt       = (float) ($this->request->getPost('stt_taxes') ?: 0);
            $notes     = trim($this->request->getPost('notes')) ?: null;

            try {
                $result = $this->transactionModel->recordSellFIFO($userId, $trustId, $date, $qty, $price, $brokerage, $stt, $notes);
                $totalGain = array_sum(array_column($result['matched_lots'], 'realized_gain'));
                $sign = $totalGain >= 0 ? '+' : '';

                return redirect()->to('/reits-invits')->with(
                    'success',
                    "Sold " . number_format($qty, 4) . " units of {$trust['symbol']} @ " . format_inr($price) . 
                    ". Realized P&L: {$sign}" . format_inr($totalGain) . ' (FIFO tax audit logged).'
                );
            } catch (Exception $e) {
                return redirect()->to('/reits-invits')->with('error', $e->getMessage());
            }
        }

        // ACTION 3: RECORD DISTRIBUTION
        if ($actionType === 'DISTRIBUTION') {
            $rules = [
                'payout_date'        => 'required|valid_date',
                'total_amount'       => 'required|numeric|greater_than[0]',
                'net_received'       => 'required|numeric|greater_than[0]',
                'eligible_units'     => 'permit_empty|numeric|greater_than_equal_to[0]',
                'dpu'                => 'permit_empty|numeric|greater_than_equal_to[0]',
                'interest_component' => 'permit_empty|numeric',
                'dividend_component' => 'permit_empty|numeric',
                'return_of_capital'  => 'permit_empty|numeric',
                'tds_deducted'       => 'permit_empty|numeric',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/reits-invits')->with('errors', $this->validator->getErrors());
            }

            $payoutDate    = $this->request->getPost('payout_date');
            $recordDate    = $this->request->getPost('record_date') ?: null;
            $eligibleUnits = (float) ($this->request->getPost('eligible_units') ?: 0);
            $dpu           = (float) ($this->request->getPost('dpu') ?: 0);
            $totalAmount   = (float) $this->request->getPost('total_amount');
            $interest      = (float) ($this->request->getPost('interest_component') ?: 0);
            $dividend      = (float) ($this->request->getPost('dividend_component') ?: 0);
            $roc           = (float) ($this->request->getPost('return_of_capital') ?: 0);
            $other         = (float) ($this->request->getPost('other_income') ?: 0);
            $tds           = (float) ($this->request->getPost('tds_deducted') ?: 0);
            $netReceived   = (float) $this->request->getPost('net_received');
            $quarterDesc   = trim($this->request->getPost('quarter_description')) ?: null;
            $notes         = trim($this->request->getPost('notes')) ?: null;
            $fy            = get_financial_year($payoutDate, $fyStartMonth);

            // Auto-calculate DPU if not explicitly provided
            if ($dpu <= 0 && $eligibleUnits > 0 && $totalAmount > 0) {
                $dpu = round($totalAmount / $eligibleUnits, 4);
            }

            $this->distributionModel->recordDistribution(
                $userId,
                $trustId,
                $payoutDate,
                $totalAmount,
                $interest,
                $dividend,
                $roc,
                $other,
                $tds,
                $netReceived,
                $quarterDesc,
                $fy,
                $recordDate,
                $notes,
                $eligibleUnits,
                $dpu
            );

            return redirect()->to('/reits-invits')->with(
                'success',
                "Recorded quarterly distribution of " . format_inr($netReceived) . " for {$trust['symbol']} (FY {$fy})."
            );
        }

        return redirect()->to('/reits-invits')->with('error', 'Invalid transaction action.');
    }

    /**
     * Refresh live market prices via Yahoo Finance API.
     */
    public function refreshPrices()
    {
        $userId = (int) session()->get('userId');
        $trusts = $this->trustModel->where('user_id', $userId)->findAll();

        if (empty($trusts)) {
            return redirect()->to('/reits-invits')->with('info', 'No trusts in portfolio to refresh.');
        }

        $updatedCount = 0;

        foreach ($trusts as $trust) {
            $suffix = ($trust['exchange'] === 'BSE') ? '.BO' : '.NS';
            $yahooTicker = urlencode($trust['symbol'] . $suffix);
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$yahooTicker}?interval=1d&range=1d";

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

            if ($response) {
                $data = json_decode($response, true);
                $meta = $data['chart']['result'][0]['meta'] ?? null;

                if ($meta && isset($meta['regularMarketPrice'])) {
                    $newPrice  = (float) $meta['regularMarketPrice'];
                    $prevClose = (float) ($meta['previousClose'] ?? $newPrice);

                    $this->trustModel->update($trust['id'], [
                        'current_price'     => $newPrice,
                        'prev_close'        => $prevClose,
                        'last_price_update' => date('Y-m-d H:i:s'),
                    ]);
                    $updatedCount++;
                }
            }
        }

        return redirect()->to('/reits-invits')->with(
            'success',
            "Live CMP refreshed for {$updatedCount} of " . count($trusts) . ' trust(s) via Yahoo Finance.'
        );
    }

    /**
     * Delete Trust from portfolio.
     */
    public function deleteTrust(int $id)
    {
        $userId = (int) session()->get('userId');
        $trust  = $this->trustModel->where('user_id', $userId)->find($id);

        if (!$trust) {
            return redirect()->to('/reits-invits')->with('error', 'Trust not found.');
        }

        $this->trustModel->delete($id);

        return redirect()->to('/reits-invits')->with('success', "Trust {$trust['symbol']} removed from portfolio.");
    }

    /**
     * Update REIT / InvIT Metadata.
     */
    public function updateTrust()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('trust_id');

        $trust = $this->trustModel->where('user_id', $userId)->find($id);
        if (!$trust) {
            return redirect()->to('/reits-invits')->with('error', 'Trust not found.');
        }

        $rules = [
            'trust_name'    => 'required|min_length[3]|max_length[150]',
            'symbol'        => 'required|min_length[2]|max_length[30]',
            'isin'          => 'required|min_length[5]|max_length[20]',
            'trust_type'    => 'required|in_list[REIT,INVIT]',
            'exchange'      => 'required|in_list[NSE,BSE]',
            'sponsor'       => 'permit_empty|max_length[100]',
            'current_price' => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/reits-invits')->with('errors', $this->validator->getErrors());
        }

        $data = [
            'trust_name' => trim($this->request->getPost('trust_name')),
            'symbol'     => strtoupper(trim($this->request->getPost('symbol'))),
            'isin'       => strtoupper(trim($this->request->getPost('isin'))),
            'trust_type' => $this->request->getPost('trust_type'),
            'exchange'   => strtoupper(trim($this->request->getPost('exchange'))),
            'sponsor'    => trim($this->request->getPost('sponsor')) ?: null,
        ];

        if ($this->request->getPost('current_price') !== null && $this->request->getPost('current_price') !== '') {
            $data['current_price'] = (float) $this->request->getPost('current_price');
        }

        $this->trustModel->update($id, $data);

        return redirect()->to('/reits-invits')->with('success', "Trust '{$data['symbol']}' details updated successfully.");
    }

    /**
     * Edit an existing REIT/InvIT transaction with automated FIFO recalculation.
     */
    public function updateTransaction()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('transaction_id');

        $tx = $this->transactionModel->where('user_id', $userId)->find($id);
        if (!$tx) {
            return redirect()->to('/reits-invits')->with('error', 'Transaction not found.');
        }

        $rules = [
            'transaction_date' => 'required|valid_date',
            'quantity'         => 'required|numeric|greater_than[0]',
            'price'            => 'required|numeric|greater_than[0]',
            'brokerage'        => 'permit_empty|numeric',
            'stt_taxes'        => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/reits-invits')->with('errors', $this->validator->getErrors());
        }

        $date      = $this->request->getPost('transaction_date');
        $quantity  = (float) $this->request->getPost('quantity');
        $price     = (float) $this->request->getPost('price');
        $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
        $sttTaxes  = (float) ($this->request->getPost('stt_taxes') ?: 0);
        $notes     = trim($this->request->getPost('notes')) ?: null;

        $totalAmount = ($tx['transaction_type'] === 'BUY')
            ? ($quantity * $price) + $brokerage + $sttTaxes
            : max(0.0, ($quantity * $price) - $brokerage - $sttTaxes);

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->update($id, [
            'transaction_date' => $date,
            'quantity'         => $quantity,
            'price'            => $price,
            'brokerage'        => $brokerage,
            'stt_taxes'        => $sttTaxes,
            'total_amount'     => $totalAmount,
            'notes'            => $notes,
        ]);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, (int) $tx['trust_id']);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/reits-invits')->with('error', 'Failed to update transaction.');
            }

            return redirect()->to('/reits-invits')->with('success', 'Transaction updated successfully with FIFO lot rebalancing.');
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/reits-invits')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an existing REIT/InvIT transaction with automated FIFO recalculation.
     */
    public function deleteTransaction(int $id)
    {
        $userId = (int) session()->get('userId');
        $tx = $this->transactionModel->where('user_id', $userId)->find($id);

        if (!$tx) {
            return redirect()->to('/reits-invits')->with('error', 'Transaction not found.');
        }

        $trustId = (int) $tx['trust_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->delete($id);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, $trustId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/reits-invits')->with('error', 'Failed to delete transaction.');
            }

            return redirect()->to('/reits-invits')->with('success', 'Transaction removed successfully with FIFO lot rebalancing.');
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/reits-invits')->with('error', $e->getMessage());
        }
    }

    /**
     * Edit an existing distribution record.
     */
    public function updateDistribution()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('distribution_id');

        $dist = $this->distributionModel->where('user_id', $userId)->find($id);
        if (!$dist) {
            return redirect()->to('/reits-invits')->with('error', 'Distribution record not found.');
        }

        $rules = [
            'payout_date'        => 'required|valid_date',
            'record_date'        => 'permit_empty|valid_date',
            'eligible_units'     => 'permit_empty|numeric|greater_than_equal_to[0]',
            'dpu'                => 'permit_empty|numeric|greater_than_equal_to[0]',
            'total_amount'       => 'required|numeric|greater_than[0]',
            'net_received'       => 'required|numeric|greater_than[0]',
            'interest_component' => 'permit_empty|numeric',
            'dividend_component' => 'permit_empty|numeric',
            'return_of_capital'  => 'permit_empty|numeric',
            'other_income'       => 'permit_empty|numeric',
            'tds_deducted'       => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/reits-invits')->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);

        $payoutDate    = $this->request->getPost('payout_date');
        $recordDate    = $this->request->getPost('record_date') ?: null;
        $eligibleUnits = (float) ($this->request->getPost('eligible_units') ?: 0);
        $dpu           = (float) ($this->request->getPost('dpu') ?: 0);
        $totalAmount   = (float) $this->request->getPost('total_amount');
        $interest      = (float) ($this->request->getPost('interest_component') ?: 0);
        $dividend      = (float) ($this->request->getPost('dividend_component') ?: 0);
        $roc           = (float) ($this->request->getPost('return_of_capital') ?: 0);
        $other         = (float) ($this->request->getPost('other_income') ?: 0);
        $tds           = (float) ($this->request->getPost('tds_deducted') ?: 0);
        $netReceived   = (float) $this->request->getPost('net_received');
        $quarterDesc   = trim($this->request->getPost('quarter_description')) ?: null;
        $notes         = trim($this->request->getPost('notes')) ?: null;
        $fy            = get_financial_year($payoutDate, $fyStartMonth);

        if ($dpu <= 0 && $eligibleUnits > 0 && $totalAmount > 0) {
            $dpu = round($totalAmount / $eligibleUnits, 4);
        }

        $this->distributionModel->update($id, [
            'record_date'         => $recordDate,
            'payout_date'         => $payoutDate,
            'eligible_units'      => $eligibleUnits,
            'dpu'                 => $dpu,
            'total_amount'        => $totalAmount,
            'interest_component'  => $interest,
            'dividend_component'  => $dividend,
            'return_of_capital'   => $roc,
            'other_income'        => $other,
            'tds_deducted'        => $tds,
            'net_received'        => $netReceived,
            'quarter_description' => $quarterDesc,
            'financial_year'      => $fy,
            'notes'               => $notes,
        ]);

        return redirect()->to('/reits-invits')->with('success', "Distribution entry updated successfully (FY {$fy}).");
    }

    /**
     * Delete an existing distribution record.
     */
    public function deleteDistribution(int $id)
    {
        $userId = (int) session()->get('userId');
        $dist   = $this->distributionModel->where('user_id', $userId)->find($id);

        if (!$dist) {
            return redirect()->to('/reits-invits')->with('error', 'Distribution record not found.');
        }

        $this->distributionModel->delete($id);

        return redirect()->to('/reits-invits')->with('success', 'Distribution record deleted successfully.');
    }
}

