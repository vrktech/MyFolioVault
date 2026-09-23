<?php

namespace App\Controllers;

use App\Models\BondCapitalGainsModel;
use App\Models\BondInterestModel;
use App\Models\BondModel;
use App\Models\BondTransactionModel;
use App\Models\UserModel;
use Exception;

class Bonds extends BaseController
{
    protected BondModel $bondModel;
    protected BondTransactionModel $transactionModel;
    protected BondInterestModel $interestModel;
    protected BondCapitalGainsModel $capitalGainsModel;
    protected UserModel $userModel;

    public function __construct()
    {
        helper(['currency']);
        $this->bondModel          = new BondModel();
        $this->transactionModel   = new BondTransactionModel();
        $this->interestModel      = new BondInterestModel();
        $this->capitalGainsModel  = new BondCapitalGainsModel();
        $this->userModel          = new UserModel();
    }

    /**
     * Bonds Dashboard: Holdings, last interest paid dates, coupon ledger, and FIFO tax log.
     */
    public function index()
    {
        $userId = (int) session()->get('userId');

        $metricsData  = $this->bondModel->getHoldingsWithMetrics($userId);
        $transactions = $this->transactionModel->getTransactionsWithBond($userId);
        $payouts      = $this->interestModel->getPayoutsWithBond($userId);
        $capitalGains = $this->capitalGainsModel->getCapitalGainsWithBond($userId);

        return view('bonds/index', [
            'title'          => 'Bonds Portfolio - MyFolioVault',
            'holdings'       => $metricsData['holdings'],
            'activeHoldings' => $metricsData['active_holdings'] ?? $metricsData['holdings'],
            'pastHoldings'   => $metricsData['past_holdings'] ?? [],
            'summary'        => $metricsData['summary'],
            'transactions'   => $transactions,
            'payouts'        => $payouts,
            'capitalGains'   => $capitalGains,
        ]);
    }

    /**
     * Display New Bond Entry form.
     */
    public function newBond()
    {
        return view('bonds/new_bond', [
            'title' => 'New Bond Acquisition - MyFolioVault',
        ]);
    }

    /**
     * Process First-Time Bond Acquisition.
     */
    public function createBond()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'bond_name'          => 'required|min_length[3]|max_length[150]',
            'isin'               => 'required|min_length[5]|max_length[20]',
            'category'           => 'required|in_list[SGB,GOVT_SECURITY,CORPORATE_NCD,TAX_FREE]',
            'issuer'             => 'required|min_length[2]|max_length[100]',
            'face_value'         => 'required|numeric|greater_than[0]',
            'coupon_rate'        => 'required|numeric|greater_than_equal_to[0]',
            'interest_frequency' => 'required|in_list[MONTHLY,QUARTERLY,SEMI_ANNUAL,ANNUAL,CUMULATIVE]',
            'maturity_date'      => 'required|valid_date',
            'transaction_date'   => 'required|valid_date',
            'quantity'           => 'required|numeric|greater_than[0]',
            'price'              => 'required|numeric|greater_than[0]',
            'brokerage_charges'  => 'permit_empty|numeric',
            'accrued_interest'   => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isin     = strtoupper(trim($this->request->getPost('isin')));
        $existing = $this->bondModel->where('user_id', $userId)->where('isin', $isin)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with(
                'error',
                "Bond with ISIN '{$isin}' already exists in your portfolio! Please use 'Add Transaction' on the dashboard."
            );
        }

        $bondName   = trim($this->request->getPost('bond_name'));
        $symbol     = strtoupper(trim($this->request->getPost('bond_symbol'))) ?: null;
        $category   = $this->request->getPost('category');
        $issuer     = trim($this->request->getPost('issuer'));
        $faceValue  = (float) $this->request->getPost('face_value');
        $couponRate = (float) $this->request->getPost('coupon_rate');
        $frequency  = $this->request->getPost('interest_frequency');
        $issueDate  = $this->request->getPost('issue_date') ?: null;
        $maturity   = $this->request->getPost('maturity_date');

        $txDate     = $this->request->getPost('transaction_date');
        $quantity   = (float) $this->request->getPost('quantity');
        $price      = (float) $this->request->getPost('price');
        $charges    = (float) ($this->request->getPost('brokerage_charges') ?: 0);
        $accrued    = (float) ($this->request->getPost('accrued_interest') ?: 0);
        $notes      = trim($this->request->getPost('notes')) ?: 'Initial bond acquisition';

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Insert into bonds master
        $bondId = $this->bondModel->insert([
            'user_id'              => $userId,
            'bond_name'            => $bondName,
            'isin'                 => $isin,
            'bond_symbol'          => $symbol,
            'category'             => $category,
            'issuer'               => $issuer,
            'face_value'           => $faceValue,
            'coupon_rate'          => $couponRate,
            'interest_frequency'   => $frequency,
            'issue_date'           => $issueDate,
            'maturity_date'        => $maturity,
            'current_market_price' => $price,
            'cmp_date'             => $txDate,
        ]);

        // 2. Insert initial BUY lot
        $this->transactionModel->recordBuy(
            $userId,
            $bondId,
            $txDate,
            $quantity,
            $price,
            $charges,
            $accrued,
            $notes
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to register new bond.');
        }

        return redirect()->to('/bonds')->with(
            'success',
            "Bond '{$bondName}' added with initial lot of " . number_format($quantity, 4) . ' units/grams!'
        );
    }

    /**
     * Minimal Popup Modal Handler:
     * Buy More, Sell (FIFO), Record Interest Paid, or Principal Redemption.
     */
    public function addTransaction()
    {
        $userId = (int) session()->get('userId');

        $bondId     = (int) $this->request->getPost('bond_id');
        $actionType = strtoupper(trim($this->request->getPost('action_type')));

        $bond = $this->bondModel->where('user_id', $userId)->find($bondId);
        if (!$bond) {
            return redirect()->to('/bonds')->with('error', 'Bond not found.');
        }

        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);

        // ACTION 1: BUY MORE
        if ($actionType === 'BUY') {
            $rules = [
                'transaction_date'  => 'required|valid_date',
                'quantity'          => 'required|numeric|greater_than[0]',
                'price'             => 'required|numeric|greater_than[0]',
                'brokerage_charges' => 'permit_empty|numeric',
                'accrued_interest'  => 'permit_empty|numeric',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/bonds')->with('errors', $this->validator->getErrors());
            }

            $date    = $this->request->getPost('transaction_date');
            $qty     = (float) $this->request->getPost('quantity');
            $price   = (float) $this->request->getPost('price');
            $charges = (float) ($this->request->getPost('brokerage_charges') ?: 0);
            $accrued = (float) ($this->request->getPost('accrued_interest') ?: 0);
            $notes   = trim($this->request->getPost('notes')) ?: null;

            $this->transactionModel->recordBuy($userId, $bondId, $date, $qty, $price, $charges, $accrued, $notes);

            return redirect()->to('/bonds')->with(
                'success',
                "Added purchase lot of " . number_format($qty, 4) . " units of {$bond['bond_name']} @ " . format_inr($price) . "."
            );
        }

        // ACTION 2: SELL (FIFO)
        if ($actionType === 'SELL') {
            $rules = [
                'transaction_date'  => 'required|valid_date',
                'quantity'          => 'required|numeric|greater_than[0]',
                'price'             => 'required|numeric|greater_than[0]',
                'brokerage_charges' => 'permit_empty|numeric',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/bonds')->with('errors', $this->validator->getErrors());
            }

            $date    = $this->request->getPost('transaction_date');
            $qty     = (float) $this->request->getPost('quantity');
            $price   = (float) $this->request->getPost('price');
            $charges = (float) ($this->request->getPost('brokerage_charges') ?: 0);
            $notes   = trim($this->request->getPost('notes')) ?: null;

            try {
                $result = $this->transactionModel->recordSellFIFO($userId, $bondId, $date, $qty, $price, $charges, $notes);
                $totalGain = array_sum(array_column($result['matched_lots'], 'realized_gain'));
                $sign = $totalGain >= 0 ? '+' : '';

                return redirect()->to('/bonds')->with(
                    'success',
                    "Sold " . number_format($qty, 4) . " units of {$bond['bond_name']} @ " . format_inr($price) . 
                    ". Realized P&L: {$sign}" . format_inr($totalGain) . ' (FIFO tax audit logged).'
                );
            } catch (Exception $e) {
                return redirect()->to('/bonds')->with('error', $e->getMessage());
            }
        }

        // ACTION 3: RECORD INTEREST / COUPON PAID
        if ($actionType === 'INTEREST') {
            $rules = [
                'payout_date'    => 'required|valid_date',
                'gross_interest' => 'required|numeric|greater_than[0]',
                'tds_deducted'   => 'permit_empty|numeric',
                'net_interest'   => 'required|numeric|greater_than[0]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/bonds')->with('errors', $this->validator->getErrors());
            }

            $payoutDate  = $this->request->getPost('payout_date');
            $gross       = (float) $this->request->getPost('gross_interest');
            $tds         = (float) ($this->request->getPost('tds_deducted') ?: 0);
            $net         = (float) $this->request->getPost('net_interest');
            $periodDesc  = trim($this->request->getPost('period_description')) ?: null;
            $notes       = trim($this->request->getPost('notes')) ?: null;
            $fy          = get_financial_year($payoutDate, $fyStartMonth);
            $couponRate  = (float) $bond['coupon_rate'];

            $this->interestModel->recordPayout(
                $userId,
                $bondId,
                $payoutDate,
                $couponRate,
                $gross,
                $tds,
                $net,
                $periodDesc,
                $fy,
                $notes
            );

            return redirect()->to('/bonds')->with(
                'success',
                "Recorded interest payout of " . format_inr($net) . " for {$bond['bond_name']}. Last Interest Paid Date updated to " . date('d-M-Y', strtotime($payoutDate)) . "."
            );
        }

        // ACTION 4: PRINCIPAL REDEMPTION AT MATURITY
        if ($actionType === 'REDEMPTION') {
            $rules = [
                'redemption_date'  => 'required|valid_date',
                'quantity'         => 'required|numeric|greater_than[0]',
                'redemption_price' => 'required|numeric|greater_than[0]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/bonds')->with('errors', $this->validator->getErrors());
            }

            $date  = $this->request->getPost('redemption_date');
            $qty   = (float) $this->request->getPost('quantity');
            $price = (float) $this->request->getPost('redemption_price');
            $notes = trim($this->request->getPost('notes')) ?: null;

            try {
                $result = $this->transactionModel->recordRedemption($userId, $bondId, $date, $qty, $price, $notes);
                $isSgb = ($bond['category'] === 'SGB');
                $exemptNotice = $isSgb ? ' (Section 47(viic) Tax-Exempt Capital Gain)' : '';

                return redirect()->to('/bonds')->with(
                    'success',
                    "Redeemed " . number_format($qty, 4) . " units of {$bond['bond_name']} @ " . format_inr($price) . 
                    ". Principal received: " . format_inr($qty * $price) . "{$exemptNotice}."
                );
            } catch (Exception $e) {
                return redirect()->to('/bonds')->with('error', $e->getMessage());
            }
        }

        return redirect()->to('/bonds')->with('error', 'Invalid transaction action.');
    }

    /**
     * Update Current Market Price (CMP) of a Bond.
     */
    public function updatePrice()
    {
        $userId = (int) session()->get('userId');
        $bondId = (int) $this->request->getPost('bond_id');
        $price  = (float) $this->request->getPost('current_market_price');
        $date   = $this->request->getPost('cmp_date') ?: date('Y-m-d');

        $bond = $this->bondModel->where('user_id', $userId)->find($bondId);
        if (!$bond || $price <= 0) {
            return redirect()->to('/bonds')->with('error', 'Invalid bond or price.');
        }

        $this->bondModel->update($bondId, [
            'current_market_price' => $price,
            'cmp_date'             => $date,
        ]);

        return redirect()->to('/bonds')->with('success', "Updated market price for {$bond['bond_name']} to " . format_inr($price) . ".");
    }

    /**
     * Delete Bond and associated trade history.
     */
    public function deleteBond(int $id)
    {
        $userId = (int) session()->get('userId');
        $bond   = $this->bondModel->where('user_id', $userId)->find($id);

        if (!$bond) {
            return redirect()->to('/bonds')->with('error', 'Bond not found.');
        }

        $this->bondModel->delete($id);

        return redirect()->to('/bonds')->with('success', "Bond {$bond['bond_name']} removed from portfolio.");
    }

    /**
     * Edit Bond metadata.
     */
    public function updateBond()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('bond_id');

        $bond = $this->bondModel->where('user_id', $userId)->find($id);
        if (!$bond) {
            return redirect()->to('/bonds')->with('error', 'Bond not found.');
        }

        $rules = [
            'bond_name'          => 'required|min_length[3]|max_length[150]',
            'isin'               => 'required|min_length[5]|max_length[20]',
            'bond_symbol'        => 'permit_empty|max_length[50]',
            'category'           => 'required|in_list[SGB,G_SEC,GOVT_SECURITY,CORPORATE_NCD,TAX_FREE]',
            'issuer'             => 'permit_empty|max_length[100]',
            'face_value'         => 'required|numeric|greater_than[0]',
            'coupon_rate'        => 'required|numeric|greater_than_equal_to[0]',
            'interest_frequency' => 'required|in_list[MONTHLY,QUARTERLY,SEMI_ANNUAL,ANNUAL,CUMULATIVE]',
            'maturity_date'      => 'required|valid_date',
            'issue_date'         => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/bonds')->with('errors', $this->validator->getErrors());
        }

        $category = $this->request->getPost('category');
        if ($category === 'G_SEC') {
            $category = 'GOVT_SECURITY';
        }

        $this->bondModel->update($id, [
            'bond_name'          => trim($this->request->getPost('bond_name')),
            'isin'               => strtoupper(trim($this->request->getPost('isin'))),
            'bond_symbol'        => trim($this->request->getPost('bond_symbol')) ?: null,
            'category'           => $category,
            'issuer'             => trim($this->request->getPost('issuer')) ?: null,
            'face_value'         => (float) $this->request->getPost('face_value'),
            'coupon_rate'        => (float) $this->request->getPost('coupon_rate'),
            'interest_frequency' => $this->request->getPost('interest_frequency'),
            'maturity_date'      => $this->request->getPost('maturity_date'),
            'issue_date'         => $this->request->getPost('issue_date') ?: null,
        ]);

        return redirect()->to('/bonds')->with('success', "Bond '{$bond['bond_name']}' details updated successfully.");
    }

    /**
     * Edit Bond Transaction with FIFO lot rebalancing.
     */
    public function updateTransaction()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('transaction_id');

        $tx = $this->transactionModel->where('user_id', $userId)->find($id);
        if (!$tx) {
            return redirect()->to('/bonds')->with('error', 'Transaction not found.');
        }

        $rules = [
            'transaction_date'  => 'required|valid_date',
            'quantity'          => 'required|numeric|greater_than[0]',
            'price'             => 'required|numeric|greater_than[0]',
            'brokerage_charges' => 'permit_empty|numeric',
            'accrued_interest'  => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/bonds')->with('errors', $this->validator->getErrors());
        }

        $date            = $this->request->getPost('transaction_date');
        $quantity        = (float) $this->request->getPost('quantity');
        $price           = (float) $this->request->getPost('price');
        $charges         = (float) ($this->request->getPost('brokerage_charges') ?: 0);
        $accruedInterest = (float) ($this->request->getPost('accrued_interest') ?: 0);
        $notes           = trim($this->request->getPost('notes')) ?: null;

        $totalAmount = match ($tx['transaction_type']) {
            'BUY'        => ($quantity * $price) + $charges + $accruedInterest,
            'SELL'       => max(0.0, ($quantity * $price) - $charges),
            'REDEMPTION' => $quantity * $price,
            default      => $quantity * $price,
        };

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->update($id, [
            'transaction_date'  => $date,
            'quantity'          => $quantity,
            'price'             => $price,
            'brokerage_charges' => $charges,
            'accrued_interest'  => $accruedInterest,
            'total_amount'      => $totalAmount,
            'notes'             => $notes,
        ]);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, (int) $tx['bond_id']);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/bonds')->with('error', 'Failed to update transaction.');
            }

            return redirect()->to('/bonds')->with('success', 'Bond transaction updated successfully with FIFO lot rebalancing.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/bonds')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete Bond Transaction with FIFO lot rebalancing.
     */
    public function deleteTransaction(int $id)
    {
        $userId = (int) session()->get('userId');
        $tx = $this->transactionModel->where('user_id', $userId)->find($id);

        if (!$tx) {
            return redirect()->to('/bonds')->with('error', 'Transaction not found.');
        }

        $bondId = (int) $tx['bond_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->delete($id);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, $bondId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/bonds')->with('error', 'Failed to delete transaction.');
            }

            return redirect()->to('/bonds')->with('success', 'Bond transaction deleted successfully with FIFO lot rebalancing.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/bonds')->with('error', $e->getMessage());
        }
    }

    /**
     * Edit Bond Interest Payout record.
     */
    public function updateInterest()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('interest_id');

        $payout = $this->interestModel->where('user_id', $userId)->find($id);
        if (!$payout) {
            return redirect()->to('/bonds')->with('error', 'Interest payout record not found.');
        }

        $rules = [
            'payout_date'    => 'required|valid_date',
            'gross_interest' => 'required|numeric|greater_than[0]',
            'tds_deducted'   => 'permit_empty|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/bonds')->with('errors', $this->validator->getErrors());
        }

        $date        = $this->request->getPost('payout_date');
        $couponRate  = (float) ($this->request->getPost('coupon_rate') ?: $payout['coupon_rate']);
        $gross       = (float) $this->request->getPost('gross_interest');
        $tds         = (float) ($this->request->getPost('tds_deducted') ?: 0);
        $net         = max(0.0, $gross - $tds);
        $periodDesc  = trim($this->request->getPost('period_description')) ?: null;
        $notes       = trim($this->request->getPost('notes')) ?: null;

        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);
        $financialYear = get_financial_year($date, $fyStartMonth);

        $this->interestModel->update($id, [
            'payout_date'        => $date,
            'coupon_rate'        => $couponRate,
            'gross_interest'     => $gross,
            'tds_deducted'       => $tds,
            'net_interest'       => $net,
            'period_description' => $periodDesc,
            'financial_year'     => $financialYear,
            'notes'              => $notes,
        ]);

        return redirect()->to('/bonds')->with('success', 'Bond interest payment record updated successfully.');
    }

    /**
     * Delete Bond Interest Payout record.
     */
    public function deleteInterest(int $id)
    {
        $userId = (int) session()->get('userId');
        $payout = $this->interestModel->where('user_id', $userId)->find($id);

        if (!$payout) {
            return redirect()->to('/bonds')->with('error', 'Interest payout record not found.');
        }

        $this->interestModel->delete($id);

        return redirect()->to('/bonds')->with('success', 'Bond interest payout record deleted successfully.');
    }
}

