<?php

namespace App\Controllers;

use App\Models\EquityCapitalGainsModel;
use App\Models\EquityDividendModel;
use App\Models\EquityModel;
use App\Models\EquitySectorModel;
use App\Models\EquityTransactionModel;
use App\Models\UserModel;
use App\Models\CorporateActionModel;
use Exception;

class Equities extends BaseController
{
    protected EquityModel $equityModel;
    protected EquityTransactionModel $transactionModel;
    protected EquityDividendModel $dividendModel;
    protected EquityCapitalGainsModel $capitalGainsModel;
    protected EquitySectorModel $sectorModel;
    protected UserModel $userModel;
    protected CorporateActionModel $corporateActionModel;

    public function __construct()
    {
        helper(['currency']);
        $this->equityModel           = new EquityModel();
        $this->transactionModel      = new EquityTransactionModel();
        $this->dividendModel         = new EquityDividendModel();
        $this->capitalGainsModel     = new EquityCapitalGainsModel();
        $this->sectorModel           = new EquitySectorModel();
        $this->userModel             = new UserModel();
        $this->corporateActionModel  = new CorporateActionModel();
    }

    /**
     * Equities Dashboard: Portfolio summary, active holdings, and audit logs.
     */
    public function index()
    {
        $userId = (int) session()->get('userId');

        $metricsData      = $this->equityModel->getHoldingsWithMetrics($userId);
        $transactions     = $this->transactionModel->getTransactionsWithStock($userId);
        $capitalGains     = $this->capitalGainsModel->getCapitalGainsWithStock($userId);
        $dividends        = $this->dividendModel->getDividendsWithStock($userId);
        $sectors          = $this->sectorModel->orderBy('name', 'ASC')->findAll();
        $corporateActions = $this->corporateActionModel->getUserCorporateActions($userId, 'EQUITY');

        return view('equities/index', [
            'title'            => 'Equities Portfolio - WealthPulse',
            'holdings'         => $metricsData['holdings'],
            'summary'          => $metricsData['summary'],
            'transactions'     => $transactions,
            'capitalGains'     => $capitalGains,
            'dividends'        => $dividends,
            'sectors'          => $sectors,
            'corporateActions' => $corporateActions,
        ]);
    }

    /**
     * Display New Stock Entry form (used only once when purchasing a stock for the first time).
     */
    public function newStock()
    {
        $sectors = $this->sectorModel->orderBy('name', 'ASC')->findAll();
        return view('equities/new_stock', [
            'title'   => 'New Stock Purchase - Equities',
            'sectors' => $sectors,
        ]);
    }

    /**
     * Process First-Time Stock Acquisition.
     */
    public function createStock()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'symbol'           => 'required|min_length[1]|max_length[20]',
            'company_name'     => 'required|min_length[2]|max_length[150]',
            'exchange'         => 'required|in_list[NSE,BSE]',
            'sector'           => 'permit_empty|max_length[60]',
            'transaction_date' => 'required|valid_date',
            'quantity'         => 'required|is_natural_no_zero',
            'price'            => 'required|decimal',
            'brokerage'        => 'permit_empty|decimal',
            'stt_taxes'        => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $symbol      = strtoupper(trim($this->request->getPost('symbol')));
        $exchange    = strtoupper(trim($this->request->getPost('exchange')));
        $companyName = trim($this->request->getPost('company_name'));
        $sector      = trim($this->request->getPost('sector')) ?: null;
        $date        = $this->request->getPost('transaction_date');
        $quantity    = (int) $this->request->getPost('quantity');
        $price       = (float) $this->request->getPost('price');
        $brokerage   = (float) ($this->request->getPost('brokerage') ?: 0);
        $sttTaxes    = (float) ($this->request->getPost('stt_taxes') ?: 0);
        $notes       = trim($this->request->getPost('notes')) ?: null;

        // Check if user already holds this stock
        $existing = $this->equityModel->findBySymbol($symbol, $exchange, $userId);
        if ($existing) {
            return redirect()->back()->withInput()->with(
                'error',
                "{$symbol} ({$exchange}) is already in your portfolio! Please use the 'Add Transaction' button on the dashboard to buy more shares."
            );
        }

        // Insert new stock into equities master table
        $equityId = $this->equityModel->insert([
            'user_id'          => $userId,
            'symbol'           => $symbol,
            'company_name'     => $companyName,
            'exchange'         => $exchange,
            'sector'           => $sector,
            'current_price'    => $price, // Initial CMP equals purchase price
            'previous_close'   => $price,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Record initial BUY lot in equity_transactions (FIFO remaining_quantity = quantity)
        $this->transactionModel->recordBuy(
            $userId,
            $equityId,
            $date,
            $quantity,
            $price,
            $brokerage,
            $sttTaxes,
            $notes ?: 'Initial stock acquisition'
        );

        return redirect()->to('/equities')->with(
            'success',
            "Stock {$symbol} added to your portfolio with an initial lot of {$quantity} shares @ " . format_inr($price) . '!'
        );
    }

    /**
     * Minimal Popup Modal Handler: Buy More, Sell (FIFO), or Record Dividend.
     */
    public function addTransaction()
    {
        $userId = (int) session()->get('userId');

        $equityId   = (int) $this->request->getPost('equity_id');
        $actionType = strtoupper(trim($this->request->getPost('action_type')));

        $stock = $this->equityModel->where('user_id', $userId)->find($equityId);
        if (!$stock) {
            return redirect()->to('/equities')->with('error', 'Stock not found in your portfolio.');
        }

        if ($actionType === 'BUY') {
            $rules = [
                'transaction_date' => 'required|valid_date',
                'quantity'         => 'required|is_natural_no_zero',
                'price'            => 'required|decimal',
                'brokerage'        => 'permit_empty|decimal',
                'stt_taxes'        => 'permit_empty|decimal',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/equities')->with('errors', $this->validator->getErrors());
            }

            $date      = $this->request->getPost('transaction_date');
            $quantity  = (int) $this->request->getPost('quantity');
            $price     = (float) $this->request->getPost('price');
            $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
            $sttTaxes  = (float) ($this->request->getPost('stt_taxes') ?: 0);
            $notes     = trim($this->request->getPost('notes')) ?: null;

            $this->transactionModel->recordBuy($userId, $equityId, $date, $quantity, $price, $brokerage, $sttTaxes, $notes);

            return redirect()->to('/equities')->with(
                'success',
                "Purchased {$quantity} additional shares of {$stock['symbol']} @ " . format_inr($price) . ' (FIFO lot created).'
            );
        }

        if ($actionType === 'SELL') {
            $rules = [
                'transaction_date' => 'required|valid_date',
                'quantity'         => 'required|is_natural_no_zero',
                'price'            => 'required|decimal',
                'brokerage'        => 'permit_empty|decimal',
                'stt_taxes'        => 'permit_empty|decimal',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/equities')->with('errors', $this->validator->getErrors());
            }

            $date      = $this->request->getPost('transaction_date');
            $quantity  = (int) $this->request->getPost('quantity');
            $price     = (float) $this->request->getPost('price');
            $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
            $sttTaxes  = (float) ($this->request->getPost('stt_taxes') ?: 0);
            $notes     = trim($this->request->getPost('notes')) ?: null;

            try {
                $result = $this->transactionModel->recordSellFIFO(
                    $userId,
                    $equityId,
                    $date,
                    $quantity,
                    $price,
                    $brokerage,
                    $sttTaxes,
                    $notes
                );

                $totalGain = array_sum(array_column($result['matched_lots'], 'realized_gain'));
                $gainSign = $totalGain >= 0 ? '+' : '';
                $msg = "Sold {$quantity} shares of {$stock['symbol']} @ " . format_inr($price) . 
                       ". Realized P&L: {$gainSign}" . format_inr($totalGain) . ' (FIFO tax audit logged).';

                return redirect()->to('/equities')->with('success', $msg);
            } catch (Exception $e) {
                return redirect()->to('/equities')->with('error', $e->getMessage());
            }
        }

        if ($actionType === 'DIVIDEND') {
            $rules = [
                'dividend_date'    => 'required|valid_date',
                'total_amount'     => 'required|decimal',
                'amount_per_share' => 'permit_empty|decimal',
                'shares_held'      => 'permit_empty|is_natural',
                'tds_deducted'     => 'permit_empty|decimal',
                'dividend_type'    => 'required|in_list[Interim,Final,Special]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->to('/equities')->with('errors', $this->validator->getErrors());
            }

            $date           = $this->request->getPost('dividend_date');
            $totalAmount    = (float) $this->request->getPost('total_amount');
            $amountPerShare = $this->request->getPost('amount_per_share') !== '' ? (float) $this->request->getPost('amount_per_share') : null;
            $sharesHeld     = $this->request->getPost('shares_held') !== '' ? (int) $this->request->getPost('shares_held') : null;
            $tdsDeducted    = (float) ($this->request->getPost('tds_deducted') ?: 0);
            $type           = $this->request->getPost('dividend_type');
            $notes          = trim($this->request->getPost('notes')) ?: null;

            $this->dividendModel->recordDividend(
                $userId,
                $equityId,
                $date,
                $totalAmount,
                $amountPerShare,
                $sharesHeld,
                $tdsDeducted,
                $type,
                $notes
            );

            return redirect()->to('/equities')->with(
                'success',
                "Dividend of " . format_inr($totalAmount) . " recorded for {$stock['symbol']}!"
            );
        }

        return redirect()->to('/equities')->with('error', 'Invalid transaction action.');
    }

    /**
     * Refresh live market prices from Yahoo Finance.
     */
    public function refreshPrices()
    {
        $userId = (int) session()->get('userId');
        $stocks = $this->equityModel->where('user_id', $userId)->findAll();

        if (empty($stocks)) {
            return redirect()->to('/equities')->with('info', 'No stocks in portfolio to refresh.');
        }

        $updatedCount = 0;

        foreach ($stocks as $stock) {
            $suffix = $stock['exchange'] === 'BSE' ? '.BO' : '.NS';
            $ticker = urlencode($stock['symbol'] . $suffix);
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}?interval=1d&range=1d";

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
                $json = json_decode($response, true);
                if (isset($json['chart']['result'][0]['meta']['regularMarketPrice'])) {
                    $cmp = (float) $json['chart']['result'][0]['meta']['regularMarketPrice'];
                    $prevClose = (float) ($json['chart']['result'][0]['meta']['chartPreviousClose'] ?? $cmp);

                    $this->equityModel->update($stock['id'], [
                        'current_price'    => $cmp,
                        'previous_close'   => $prevClose,
                        'price_updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $updatedCount++;
                }
            }
        }

        return redirect()->to('/equities')->with(
            'success',
            "Live market prices refreshed for {$updatedCount} of " . count($stocks) . ' stock(s) via Yahoo Finance.'
        );
    }

    /**
     * Delete a stock from portfolio.
     */
    public function deleteStock(int $id)
    {
        $userId = (int) session()->get('userId');
        $stock = $this->equityModel->where('user_id', $userId)->find($id);

        if (!$stock) {
            return redirect()->to('/equities')->with('error', 'Stock not found.');
        }

        $this->equityModel->delete($id);

        return redirect()->to('/equities')->with('success', "Stock {$stock['symbol']} removed from portfolio.");
    }

    /**
     * Update stock metadata.
     */
    public function updateStock()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('equity_id');

        $stock = $this->equityModel->where('user_id', $userId)->find($id);
        if (!$stock) {
            return redirect()->to('/equities')->with('error', 'Stock not found.');
        }

        $rules = [
            'company_name' => 'required|min_length[2]|max_length[150]',
            'symbol'       => 'required|min_length[1]|max_length[30]',
            'isin'         => 'permit_empty|min_length[5]|max_length[20]',
            'sector'       => 'permit_empty|max_length[80]',
            'exchange'     => 'required|in_list[NSE,BSE]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/equities')->with('errors', $this->validator->getErrors());
        }

        $this->equityModel->update($id, [
            'company_name' => trim($this->request->getPost('company_name')),
            'symbol'       => strtoupper(trim($this->request->getPost('symbol'))),
            'isin'         => strtoupper(trim($this->request->getPost('isin'))),
            'sector'       => trim($this->request->getPost('sector')) ?: null,
            'exchange'     => strtoupper(trim($this->request->getPost('exchange'))),
        ]);

        return redirect()->to('/equities')->with('success', "Stock details for '{$stock['symbol']}' updated successfully.");
    }

    /**
     * Edit an existing stock transaction (Buy or Sell) with automated FIFO recalculation.
     */
    public function updateTransaction()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('transaction_id');

        $tx = $this->transactionModel->where('user_id', $userId)->find($id);
        if (!$tx) {
            return redirect()->to('/equities')->with('error', 'Transaction not found.');
        }

        $rules = [
            'transaction_date' => 'required|valid_date',
            'quantity'         => 'required|is_natural_no_zero',
            'price'            => 'required|decimal',
            'brokerage'        => 'permit_empty|decimal',
            'stt_taxes'        => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/equities')->with('errors', $this->validator->getErrors());
        }

        $date      = $this->request->getPost('transaction_date');
        $quantity  = (int) $this->request->getPost('quantity');
        $price     = (float) $this->request->getPost('price');
        $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
        $sttTaxes  = (float) ($this->request->getPost('stt_taxes') ?: 0);
        $notes     = trim($this->request->getPost('notes')) ?: null;

        $totalAmount = ($tx['transaction_type'] === 'BUY')
            ? ($quantity * $price) + $brokerage + $sttTaxes
            : ($quantity * $price) - $brokerage - $sttTaxes;

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
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, (int) $tx['equity_id']);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/equities')->with('error', 'Failed to update transaction.');
            }

            return redirect()->to('/equities')->with('success', "Transaction updated successfully with FIFO lot rebalancing.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/equities')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an existing stock transaction with automated FIFO recalculation.
     */
    public function deleteTransaction(int $id)
    {
        $userId = (int) session()->get('userId');
        $tx = $this->transactionModel->where('user_id', $userId)->find($id);

        if (!$tx) {
            return redirect()->to('/equities')->with('error', 'Transaction not found.');
        }

        $equityId = (int) $tx['equity_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->delete($id);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, $equityId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/equities')->with('error', 'Failed to delete transaction.');
            }

            return redirect()->to('/equities')->with('success', "Transaction removed successfully with FIFO lot rebalancing.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/equities')->with('error', $e->getMessage());
        }
    }

    /**
     * Edit an existing dividend record.
     */
    public function updateDividend()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('dividend_id');

        $div = $this->dividendModel->where('user_id', $userId)->find($id);
        if (!$div) {
            return redirect()->to('/equities')->with('error', 'Dividend record not found.');
        }

        $rules = [
            'dividend_date'    => 'required|valid_date',
            'total_amount'     => 'required|decimal',
            'amount_per_share' => 'permit_empty|decimal',
            'shares_held'      => 'permit_empty|is_natural',
            'tds_deducted'     => 'permit_empty|decimal',
            'dividend_type'    => 'required|in_list[Interim,Final,Special]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/equities')->with('errors', $this->validator->getErrors());
        }

        $date           = $this->request->getPost('dividend_date');
        $totalAmount    = (float) $this->request->getPost('total_amount');
        $amountPerShare = $this->request->getPost('amount_per_share') !== '' ? (float) $this->request->getPost('amount_per_share') : null;
        $sharesHeld     = $this->request->getPost('shares_held') !== '' ? (int) $this->request->getPost('shares_held') : null;
        $tdsDeducted    = (float) ($this->request->getPost('tds_deducted') ?: 0);
        $type           = $this->request->getPost('dividend_type');
        $notes          = trim($this->request->getPost('notes')) ?: null;

        $user = $this->userModel->find($userId);
        $fyStartMonth = (int) ($user['fy_start_month'] ?? 4);
        $fy = get_financial_year($date, $fyStartMonth);

        $this->dividendModel->update($id, [
            'dividend_date'    => $date,
            'total_amount'     => $totalAmount,
            'amount_per_share' => $amountPerShare,
            'shares_held'      => $sharesHeld,
            'tds_deducted'     => $tdsDeducted,
            'net_amount'       => $totalAmount - $tdsDeducted,
            'dividend_type'    => $type,
            'financial_year'   => $fy,
            'notes'            => $notes,
        ]);

        return redirect()->to('/equities')->with('success', "Dividend record updated successfully.");
    }

    /**
     * Delete an existing dividend record.
     */
    public function deleteDividend(int $id)
    {
        $userId = (int) session()->get('userId');
        $div = $this->dividendModel->where('user_id', $userId)->find($id);

        if (!$div) {
            return redirect()->to('/equities')->with('error', 'Dividend record not found.');
        }

        $this->dividendModel->delete($id);

        return redirect()->to('/equities')->with('success', "Dividend record deleted successfully.");
    }

    /**
     * AJAX endpoint: Get eligible shares for corporate action on a specific record date.
     */
    public function checkCorporateActionEligibility()
    {
        $userId = (int) session()->get('userId');
        $equityId = (int) $this->request->getGet('equity_id');
        $recordDate = $this->request->getGet('record_date');

        if (!$equityId || !$recordDate) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Stock and record date are required.',
            ]);
        }

        $eligibleShares = $this->corporateActionModel->getEligibleSharesOnDate($userId, 'EQUITY', $equityId, $recordDate);

        return $this->response->setJSON([
            'status'          => 'success',
            'eligible_shares' => $eligibleShares,
            'record_date'     => $recordDate,
        ]);
    }

    /**
     * Record a corporate action (SPLIT, BONUS, RIGHTS, MERGER, DEMERGER) for an Equity stock.
     */
    public function recordCorporateAction()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'equity_id'   => 'required|is_natural_no_zero',
            'action_type' => 'required|in_list[SPLIT,BONUS,RIGHTS,MERGER,DEMERGER]',
            'record_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/equities')->withInput()->with('errors', $this->validator->getErrors());
        }

        $equityId   = (int) $this->request->getPost('equity_id');
        $actionType = $this->request->getPost('action_type');
        $recordDate = $this->request->getPost('record_date');
        $notes      = trim($this->request->getPost('notes') ?? '');

        // Verify stock belongs to user
        $equity = $this->equityModel->where('user_id', $userId)->find($equityId);
        if (!$equity) {
            return redirect()->to('/equities')->with('error', 'Stock not found in your portfolio.');
        }

        // Calculate eligible shares as of record date
        $eligibleShares = $this->corporateActionModel->getEligibleSharesOnDate($userId, 'EQUITY', $equityId, $recordDate);
        if ($eligibleShares <= 0 && $actionType !== 'RIGHTS') {
            return redirect()->to('/equities')->with('error', "No eligible shares held of {$equity['symbol']} on or before the record date ({$recordDate}).");
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($actionType === 'SPLIT') {
                $ratioOld = (float) ($this->request->getPost('ratio_old') ?: 1);
                $ratioNew = (float) ($this->request->getPost('ratio_new') ?: 1);
                if ($ratioOld <= 0 || $ratioNew <= 0) {
                    throw new Exception('Invalid split ratio.');
                }

                $factor = $ratioNew / $ratioOld;

                // Adjust all BUY transactions for this stock on or before record_date
                $lots = $this->transactionModel->where('user_id', $userId)
                    ->where('equity_id', $equityId)
                    ->where('transaction_type', 'BUY')
                    ->where('transaction_date <=', $recordDate)
                    ->findAll();

                foreach ($lots as $lot) {
                    $newQty = round((float) $lot['quantity'] * $factor, 4);
                    $newRemaining = round((float) $lot['remaining_quantity'] * $factor, 4);
                    $newPrice = round((float) $lot['price'] / $factor, 4);

                    $this->transactionModel->update($lot['id'], [
                        'quantity'           => $newQty,
                        'remaining_quantity' => $newRemaining,
                        'price'              => $newPrice,
                        'notes'              => trim(($lot['notes'] ?? '') . " [Split {$ratioOld}:{$ratioNew} on {$recordDate}]"),
                    ]);
                }

                $sharesAfter = round($eligibleShares * $factor, 4);

                $this->corporateActionModel->insert([
                    'user_id'        => $userId,
                    'module'         => 'EQUITY',
                    'security_id'    => $equityId,
                    'action_type'    => 'SPLIT',
                    'record_date'    => $recordDate,
                    'ratio_old'      => $ratioOld,
                    'ratio_new'      => $ratioNew,
                    'shares_before'  => $eligibleShares,
                    'shares_after'   => $sharesAfter,
                    'notes'          => $notes ?: "Stock Split {$ratioOld}:{$ratioNew}",
                ]);

                $msg = "Stock split ({$ratioOld}:{$ratioNew}) recorded for {$equity['symbol']}. Eligible shares adjusted from {$eligibleShares} to {$sharesAfter}.";

            } elseif ($actionType === 'BONUS') {
                $ratioOld = (float) ($this->request->getPost('ratio_old') ?: 1);
                $ratioNew = (float) ($this->request->getPost('ratio_new') ?: 1);
                if ($ratioOld <= 0 || $ratioNew <= 0) {
                    throw new Exception('Invalid bonus ratio.');
                }

                $bonusShares = round($eligibleShares * ($ratioNew / $ratioOld), 4);
                if ($bonusShares <= 0) {
                    throw new Exception('Bonus issue resulted in zero additional shares.');
                }

                // Add bonus allotment BUY lot with price 0.00 per IT Act Sec 55(2)(aa)
                $this->transactionModel->insert([
                    'user_id'            => $userId,
                    'equity_id'          => $equityId,
                    'transaction_type'   => 'BUY',
                    'transaction_date'   => $recordDate,
                    'quantity'           => $bonusShares,
                    'remaining_quantity' => $bonusShares,
                    'price'              => 0.00,
                    'brokerage'          => 0.00,
                    'stt_taxes'          => 0.00,
                    'total_amount'       => 0.00,
                    'notes'              => "Bonus Issue Allotment ({$ratioNew}:{$ratioOld}) on {$recordDate}",
                ]);

                $sharesAfter = round($eligibleShares + $bonusShares, 4);

                $this->corporateActionModel->insert([
                    'user_id'        => $userId,
                    'module'         => 'EQUITY',
                    'security_id'    => $equityId,
                    'action_type'    => 'BONUS',
                    'record_date'    => $recordDate,
                    'ratio_old'      => $ratioOld,
                    'ratio_new'      => $ratioNew,
                    'shares_before'  => $eligibleShares,
                    'shares_after'   => $sharesAfter,
                    'notes'          => $notes ?: "Bonus Issue {$ratioNew}:{$ratioOld}",
                ]);

                $msg = "Bonus issue ({$ratioNew}:{$ratioOld}) recorded for {$equity['symbol']}. {$bonusShares} bonus shares allotted at ₹0.00 cost basis.";

            } elseif ($actionType === 'RIGHTS') {
                $subscribedQty = (float) $this->request->getPost('subscribed_shares');
                $offerPrice    = (float) $this->request->getPost('offer_price');

                if ($subscribedQty <= 0 || $offerPrice <= 0) {
                    throw new Exception('Subscribed quantity and offer price must be greater than zero.');
                }

                $totalAmount = round($subscribedQty * $offerPrice, 2);

                $this->transactionModel->insert([
                    'user_id'            => $userId,
                    'equity_id'          => $equityId,
                    'transaction_type'   => 'BUY',
                    'transaction_date'   => $recordDate,
                    'quantity'           => $subscribedQty,
                    'remaining_quantity' => $subscribedQty,
                    'price'              => $offerPrice,
                    'brokerage'          => 0.00,
                    'stt_taxes'          => 0.00,
                    'total_amount'       => $totalAmount,
                    'notes'              => "Rights Issue Subscribed @ ₹{$offerPrice} on {$recordDate}",
                ]);

                $sharesAfter = round($eligibleShares + $subscribedQty, 4);

                $this->corporateActionModel->insert([
                    'user_id'        => $userId,
                    'module'         => 'EQUITY',
                    'security_id'    => $equityId,
                    'action_type'    => 'RIGHTS',
                    'record_date'    => $recordDate,
                    'ratio_old'      => 1,
                    'ratio_new'      => 1,
                    'offer_price'    => $offerPrice,
                    'shares_before'  => $eligibleShares,
                    'shares_after'   => $sharesAfter,
                    'notes'          => $notes ?: "Rights issue subscription of {$subscribedQty} shares @ ₹{$offerPrice}",
                ]);

                $msg = "Rights issue subscription recorded for {$equity['symbol']}: {$subscribedQty} shares @ ₹{$offerPrice}.";

            } elseif ($actionType === 'MERGER') {
                $targetEquityId = (int) $this->request->getPost('target_security_id');
                $ratioOld       = (float) ($this->request->getPost('ratio_old') ?: 1);
                $ratioNew       = (float) ($this->request->getPost('ratio_new') ?: 1);

                if (!$targetEquityId || $targetEquityId === $equityId) {
                    throw new Exception('Please select a valid destination/target stock for the merger.');
                }
                if ($ratioOld <= 0 || $ratioNew <= 0) {
                    throw new Exception('Invalid merger swap ratio.');
                }

                $targetEquity = $this->equityModel->where('user_id', $userId)->find($targetEquityId);
                if (!$targetEquity) {
                    throw new Exception('Target company stock not found in your portfolio.');
                }

                $swapFactor = $ratioNew / $ratioOld;

                // Extinguish active shares of source stock and create target lots preserving dates & cost per Sec 49(2)
                $lots = $this->transactionModel->where('user_id', $userId)
                    ->where('equity_id', $equityId)
                    ->where('transaction_type', 'BUY')
                    ->where('remaining_quantity >', 0)
                    ->where('transaction_date <=', $recordDate)
                    ->findAll();

                $totalTargetCreated = 0;
                foreach ($lots as $lot) {
                    $activeQty = (float) $lot['remaining_quantity'];
                    $targetQty = round($activeQty * $swapFactor, 4);
                    $sourceCost = round($activeQty * (float) $lot['price'], 2);
                    $targetUnitPrice = ($targetQty > 0) ? round($sourceCost / $targetQty, 4) : 0;

                    // Extinguish remaining quantity of source lot
                    $this->transactionModel->update($lot['id'], [
                        'remaining_quantity' => 0,
                        'notes'              => trim(($lot['notes'] ?? '') . " [Extinguished: Merged into {$targetEquity['symbol']} on {$recordDate}]"),
                    ]);

                    // Insert target lot inheriting original purchase date
                    $this->transactionModel->insert([
                        'user_id'            => $userId,
                        'equity_id'          => $targetEquityId,
                        'transaction_type'   => 'BUY',
                        'transaction_date'   => $lot['transaction_date'],
                        'quantity'           => $targetQty,
                        'remaining_quantity' => $targetQty,
                        'price'              => $targetUnitPrice,
                        'brokerage'          => 0.00,
                        'stt_taxes'          => 0.00,
                        'total_amount'       => $sourceCost,
                        'notes'              => "Merger swap from {$equity['symbol']} Lot #{$lot['id']} ({$ratioNew}:{$ratioOld}) on {$recordDate}",
                    ]);

                    $totalTargetCreated += $targetQty;
                }

                $this->corporateActionModel->insert([
                    'user_id'            => $userId,
                    'module'             => 'EQUITY',
                    'security_id'        => $equityId,
                    'action_type'        => 'MERGER',
                    'record_date'        => $recordDate,
                    'ratio_old'          => $ratioOld,
                    'ratio_new'          => $ratioNew,
                    'target_security_id' => $targetEquityId,
                    'shares_before'      => $eligibleShares,
                    'shares_after'       => $totalTargetCreated,
                    'notes'              => $notes ?: "Merged into {$targetEquity['symbol']} ({$ratioNew}:{$ratioOld})",
                ]);

                $msg = "Merger recorded: {$eligibleShares} shares of {$equity['symbol']} swapped into {$totalTargetCreated} shares of {$targetEquity['symbol']}. Original cost and dates preserved.";

            } elseif ($actionType === 'DEMERGER') {
                $targetEquityId = (int) $this->request->getPost('target_security_id');
                $ratioOld       = (float) ($this->request->getPost('ratio_old') ?: 1);
                $ratioNew       = (float) ($this->request->getPost('ratio_new') ?: 1);
                $costPct        = (float) ($this->request->getPost('cost_apportionment_ratio') ?: 0);

                if (!$targetEquityId || $targetEquityId === $equityId) {
                    throw new Exception('Please select the spun-off / resulting company stock.');
                }
                if ($costPct <= 0 || $costPct >= 100) {
                    throw new Exception('Cost apportionment ratio must be between 0.01% and 99.99%.');
                }

                $targetEquity = $this->equityModel->where('user_id', $userId)->find($targetEquityId);
                if (!$targetEquity) {
                    throw new Exception('Spun-off entity stock not found in your portfolio.');
                }

                $apportionmentRatio = $costPct / 100.0;
                $retainedRatio = 1.0 - $apportionmentRatio;
                $shareRatio = $ratioNew / $ratioOld;

                $lots = $this->transactionModel->where('user_id', $userId)
                    ->where('equity_id', $equityId)
                    ->where('transaction_type', 'BUY')
                    ->where('remaining_quantity >', 0)
                    ->where('transaction_date <=', $recordDate)
                    ->findAll();

                $totalSpunOff = 0;
                foreach ($lots as $lot) {
                    $parentActiveQty = (float) $lot['remaining_quantity'];
                    $spunOffQty = round($parentActiveQty * $shareRatio, 4);
                    $originalPrice = (float) $lot['price'];

                    $newParentPrice = round($originalPrice * $retainedRatio, 4);
                    $spunOffUnitPrice = ($spunOffQty > 0) ? round(($parentActiveQty * $originalPrice * $apportionmentRatio) / $spunOffQty, 4) : 0;
                    $spunOffTotalAmount = round($spunOffQty * $spunOffUnitPrice, 2);

                    // Update parent lot price
                    $this->transactionModel->update($lot['id'], [
                        'price' => $newParentPrice,
                        'notes' => trim(($lot['notes'] ?? '') . " [Demerged: cost reduced by {$costPct}% on {$recordDate}]"),
                    ]);

                    // Insert spun-off entity lot inheriting parent's acquisition date per Sec 47(vid)
                    $this->transactionModel->insert([
                        'user_id'            => $userId,
                        'equity_id'          => $targetEquityId,
                        'transaction_type'   => 'BUY',
                        'transaction_date'   => $lot['transaction_date'],
                        'quantity'           => $spunOffQty,
                        'remaining_quantity' => $spunOffQty,
                        'price'              => $spunOffUnitPrice,
                        'brokerage'          => 0.00,
                        'stt_taxes'          => 0.00,
                        'total_amount'       => $spunOffTotalAmount,
                        'notes'              => "Demerged from {$equity['symbol']} Lot #{$lot['id']} ({$costPct}% cost basis) on {$recordDate}",
                    ]);

                    $totalSpunOff += $spunOffQty;
                }

                $this->corporateActionModel->insert([
                    'user_id'                  => $userId,
                    'module'                   => 'EQUITY',
                    'security_id'              => $equityId,
                    'action_type'              => 'DEMERGER',
                    'record_date'              => $recordDate,
                    'ratio_old'                => $ratioOld,
                    'ratio_new'                => $ratioNew,
                    'cost_apportionment_ratio' => $costPct,
                    'target_security_id'       => $targetEquityId,
                    'shares_before'            => $eligibleShares,
                    'shares_after'             => $totalSpunOff,
                    'notes'                    => $notes ?: "Demerger: {$costPct}% cost apportioned to {$targetEquity['symbol']}",
                ]);

                $msg = "Demerger recorded for {$equity['symbol']}: {$costPct}% cost basis apportioned to create {$totalSpunOff} shares of {$targetEquity['symbol']}.";
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/equities')->with('error', 'Failed to process corporate action transaction.');
            }

            return redirect()->to('/equities')->with('success', $msg);

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/equities')->with('error', 'Corporate Action failed: ' . $e->getMessage());
        }
    }
}


