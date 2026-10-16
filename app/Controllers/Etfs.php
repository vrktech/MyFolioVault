<?php

namespace App\Controllers;

use App\Models\EtfCapitalGainsModel;
use App\Models\EtfModel;
use App\Models\EtfTransactionModel;
use App\Models\CorporateActionModel;
use Exception;

class Etfs extends BaseController
{
    protected EtfModel $etfModel;
    protected EtfTransactionModel $transactionModel;
    protected EtfCapitalGainsModel $capitalGainsModel;
    protected CorporateActionModel $corporateActionModel;

    public function __construct()
    {
        helper(['currency']);
        $this->etfModel              = new EtfModel();
        $this->transactionModel      = new EtfTransactionModel();
        $this->capitalGainsModel     = new EtfCapitalGainsModel();
        $this->corporateActionModel  = new CorporateActionModel();
    }

    /**
     * ETFs Dashboard: Portfolio summary, holdings with categories, and FIFO tax audit logs.
     */
    public function index()
    {
        $userId = (int) session()->get('userId');

        $metricsData      = $this->etfModel->getHoldingsWithMetrics($userId);
        $transactions     = $this->transactionModel->getTransactionsWithEtf($userId);
        $capitalGains     = $this->capitalGainsModel->getCapitalGainsWithEtf($userId);
        $corporateActions = $this->corporateActionModel->getUserCorporateActions($userId, 'ETF');

        return view('etfs/index', [
            'title'            => 'ETFs Portfolio - MyFolioVault',
            'holdings'         => $metricsData['holdings'],
            'activeHoldings'   => $metricsData['active_holdings'] ?? $metricsData['holdings'],
            'pastHoldings'     => $metricsData['past_holdings'] ?? [],
            'summary'          => $metricsData['summary'],
            'transactions'     => $transactions,
            'capitalGains'     => $capitalGains,
            'corporateActions' => $corporateActions,
        ]);
    }

    /**
     * Display New ETF Entry form (used only once when purchasing an ETF for the first time).
     */
    public function newEtf()
    {
        return view('etfs/new_etf', [
            'title' => 'New ETF Purchase - MyFolioVault',
        ]);
    }

    /**
     * Process First-Time ETF Acquisition.
     */
    public function createEtf()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'symbol'           => 'required|min_length[1]|max_length[20]',
            'etf_name'         => 'required|min_length[2]|max_length[150]',
            'category'         => 'required|in_list[Index,Commodity - Gold,Commodity - Silver,Sectoral,Global,Debt]',
            'amc_name'         => 'permit_empty|max_length[100]',
            'exchange'         => 'required|in_list[NSE,BSE]',
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
        $etfName     = trim($this->request->getPost('etf_name'));
        $category    = trim($this->request->getPost('category'));
        $amcName     = trim($this->request->getPost('amc_name')) ?: null;
        $date        = $this->request->getPost('transaction_date');
        $quantity    = (int) $this->request->getPost('quantity');
        $price       = (float) $this->request->getPost('price');
        $brokerage   = (float) ($this->request->getPost('brokerage') ?: 0);
        $sttTaxes    = (float) ($this->request->getPost('stt_taxes') ?: 0);
        $notes       = trim($this->request->getPost('notes')) ?: null;

        // Check if user already holds this ETF
        $existing = $this->etfModel->findBySymbol($symbol, $exchange, $userId);
        if ($existing) {
            return redirect()->back()->withInput()->with(
                'error',
                "{$symbol} ({$exchange}) is already in your portfolio! Please use the 'Add Transaction' button on the dashboard to buy more units."
            );
        }

        // Insert new ETF into etfs master table
        $etfId = $this->etfModel->insert([
            'user_id'          => $userId,
            'symbol'           => $symbol,
            'etf_name'         => $etfName,
            'category'         => $category,
            'amc_name'         => $amcName,
            'exchange'         => $exchange,
            'current_price'    => $price, // Initial CMP equals purchase price
            'previous_close'   => $price,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Record initial BUY lot in etf_transactions (FIFO remaining_quantity = quantity)
        $this->transactionModel->recordBuy(
            $userId,
            $etfId,
            $date,
            $quantity,
            $price,
            $brokerage,
            $sttTaxes,
            $notes ?: 'Initial ETF position'
        );

        return redirect()->to('/etfs')->with(
            'success',
            "ETF {$symbol} added to your portfolio with an initial lot of {$quantity} units @ " . format_inr($price) . '!'
        );
    }

    /**
     * Minimal Popup Modal Handler: Buy More or Sell (FIFO).
     */
    public function addTransaction()
    {
        $userId = (int) session()->get('userId');

        $etfId      = (int) $this->request->getPost('etf_id');
        $actionType = strtoupper(trim($this->request->getPost('action_type')));

        $etf = $this->etfModel->where('user_id', $userId)->find($etfId);
        if (!$etf) {
            return redirect()->to('/etfs')->with('error', 'ETF not found in your portfolio.');
        }

        $rules = [
            'transaction_date' => 'required|valid_date',
            'quantity'         => 'required|is_natural_no_zero',
            'price'            => 'required|decimal',
            'brokerage'        => 'permit_empty|decimal',
            'stt_taxes'        => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/etfs')->with('errors', $this->validator->getErrors());
        }

        $date      = $this->request->getPost('transaction_date');
        $quantity  = (int) $this->request->getPost('quantity');
        $price     = (float) $this->request->getPost('price');
        $brokerage = (float) ($this->request->getPost('brokerage') ?: 0);
        $sttTaxes  = (float) ($this->request->getPost('stt_taxes') ?: 0);
        $notes     = trim($this->request->getPost('notes')) ?: null;

        if ($actionType === 'BUY') {
            $this->transactionModel->recordBuy($userId, $etfId, $date, $quantity, $price, $brokerage, $sttTaxes, $notes);

            return redirect()->to('/etfs')->with(
                'success',
                "Purchased {$quantity} additional units of {$etf['symbol']} @ " . format_inr($price) . ' (FIFO lot created).'
            );
        }

        if ($actionType === 'SELL') {
            try {
                $result = $this->transactionModel->recordSellFIFO(
                    $userId,
                    $etfId,
                    $date,
                    $quantity,
                    $price,
                    $brokerage,
                    $sttTaxes,
                    $notes
                );

                $totalGain = array_sum(array_column($result['matched_lots'], 'realized_gain'));
                $gainSign = $totalGain >= 0 ? '+' : '';
                $msg = "Sold {$quantity} units of {$etf['symbol']} @ " . format_inr($price) . 
                       ". Realized P&L: {$gainSign}" . format_inr($totalGain) . ' (FIFO tax audit logged).';

                return redirect()->to('/etfs')->with('success', $msg);
            } catch (Exception $e) {
                return redirect()->to('/etfs')->with('error', $e->getMessage());
            }
        }

        return redirect()->to('/etfs')->with('error', 'Invalid transaction action.');
    }

    /**
     * Refresh live market prices from Yahoo Finance.
     */
    public function refreshPrices()
    {
        $userId = (int) session()->get('userId');
        $etfs   = $this->etfModel->where('user_id', $userId)->findAll();

        if (empty($etfs)) {
            return redirect()->to('/etfs')->with('info', 'No ETFs in portfolio to refresh.');
        }

        $updatedCount = 0;

        foreach ($etfs as $etf) {
            $quote = fetch_yahoo_price($etf['symbol'], $etf['exchange'] ?? 'NSE');
            if ($quote !== null) {
                $this->etfModel->update($etf['id'], [
                    'current_price'    => $quote['price'],
                    'previous_close'   => $quote['previous_close'],
                    'price_updated_at' => date('Y-m-d H:i:s'),
                ]);
                $updatedCount++;
            }
        }

        return redirect()->to('/etfs')->with(
            'success',
            "Live market prices refreshed for {$updatedCount} of " . count($etfs) . ' ETF(s) via Yahoo Finance.'
        );
    }

    /**
     * Delete an ETF from portfolio.
     */
    public function deleteEtf(int $id)
    {
        $userId = (int) session()->get('userId');
        $etf    = $this->etfModel->where('user_id', $userId)->find($id);

        if (!$etf) {
            return redirect()->to('/etfs')->with('error', 'ETF not found.');
        }

        $this->etfModel->delete($id);

        return redirect()->to('/etfs')->with('success', "ETF {$etf['symbol']} removed from portfolio.");
    }

    /**
     * Update ETF metadata.
     */
    public function updateEtf()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('etf_id');

        $etf = $this->etfModel->where('user_id', $userId)->find($id);
        if (!$etf) {
            return redirect()->to('/etfs')->with('error', 'ETF not found.');
        }

        $rules = [
            'symbol'   => 'required|min_length[1]|max_length[20]',
            'etf_name' => 'required|min_length[2]|max_length[150]',
            'category' => 'required|in_list[Index,Commodity - Gold,Commodity - Silver,Sectoral,Global,Debt]',
            'amc_name' => 'permit_empty|max_length[100]',
            'exchange' => 'required|in_list[NSE,BSE]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/etfs')->with('errors', $this->validator->getErrors());
        }

        $this->etfModel->update($id, [
            'symbol'   => strtoupper(trim($this->request->getPost('symbol'))),
            'etf_name' => trim($this->request->getPost('etf_name')),
            'category' => trim($this->request->getPost('category')),
            'amc_name' => trim($this->request->getPost('amc_name')) ?: null,
            'exchange' => strtoupper(trim($this->request->getPost('exchange'))),
        ]);

        return redirect()->to('/etfs')->with('success', "ETF '{$etf['symbol']}' details updated successfully.");
    }

    /**
     * Edit an existing ETF transaction with automated FIFO recalculation.
     */
    public function updateTransaction()
    {
        $userId = (int) session()->get('userId');
        $id     = (int) $this->request->getPost('transaction_id');

        $tx = $this->transactionModel->where('user_id', $userId)->find($id);
        if (!$tx) {
            return redirect()->to('/etfs')->with('error', 'Transaction not found.');
        }

        $rules = [
            'transaction_date' => 'required|valid_date',
            'quantity'         => 'required|is_natural_no_zero',
            'price'            => 'required|decimal',
            'brokerage'        => 'permit_empty|decimal',
            'stt_taxes'        => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/etfs')->with('errors', $this->validator->getErrors());
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
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, (int) $tx['etf_id']);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/etfs')->with('error', 'Failed to update transaction.');
            }

            return redirect()->to('/etfs')->with('success', "ETF transaction updated successfully with FIFO lot rebalancing.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/etfs')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an existing ETF transaction with automated FIFO recalculation.
     */
    public function deleteTransaction(int $id)
    {
        $userId = (int) session()->get('userId');
        $tx = $this->transactionModel->where('user_id', $userId)->find($id);

        if (!$tx) {
            return redirect()->to('/etfs')->with('error', 'Transaction not found.');
        }

        $etfId = (int) $tx['etf_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->transactionModel->delete($id);

        try {
            $this->transactionModel->rebuildFifoLotsAndCapitalGains($userId, $etfId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/etfs')->with('error', 'Failed to delete transaction.');
            }

            return redirect()->to('/etfs')->with('success', "ETF transaction deleted successfully with FIFO lot rebalancing.");
        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/etfs')->with('error', $e->getMessage());
        }
    }

    /**
     * AJAX endpoint: Get eligible ETF units for split on a specific record date.
     */
    public function checkSplitEligibility()
    {
        $userId = (int) session()->get('userId');
        $etfId = (int) $this->request->getGet('etf_id');
        $recordDate = $this->request->getGet('record_date');

        if (!$etfId || !$recordDate) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'ETF and record date are required.',
            ]);
        }

        $eligibleUnits = $this->corporateActionModel->getEligibleSharesOnDate($userId, 'ETF', $etfId, $recordDate);

        return $this->response->setJSON([
            'status'         => 'success',
            'eligible_units' => $eligibleUnits,
            'record_date'    => $recordDate,
        ]);
    }

    /**
     * Record an ETF unit split.
     */
    public function recordSplit()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'etf_id'      => 'required|is_natural_no_zero',
            'record_date' => 'required|valid_date',
            'ratio_old'   => 'required|numeric|greater_than[0]',
            'ratio_new'   => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/etfs')->withInput()->with('errors', $this->validator->getErrors());
        }

        $etfId      = (int) $this->request->getPost('etf_id');
        $recordDate = $this->request->getPost('record_date');
        $ratioOld   = (float) $this->request->getPost('ratio_old');
        $ratioNew   = (float) $this->request->getPost('ratio_new');
        $notes      = trim($this->request->getPost('notes') ?? '');

        $etf = $this->etfModel->where('user_id', $userId)->find($etfId);
        if (!$etf) {
            return redirect()->to('/etfs')->with('error', 'ETF not found in your portfolio.');
        }

        $eligibleUnits = $this->corporateActionModel->getEligibleSharesOnDate($userId, 'ETF', $etfId, $recordDate);
        if ($eligibleUnits <= 0) {
            return redirect()->to('/etfs')->with('error', "No eligible units held of {$etf['symbol']} on or before the record date ({$recordDate}).");
        }

        $factor = $ratioNew / $ratioOld;

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lots = $this->transactionModel->where('user_id', $userId)
                ->where('etf_id', $etfId)
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

            $unitsAfter = round($eligibleUnits * $factor, 4);

            $this->corporateActionModel->insert([
                'user_id'       => $userId,
                'module'        => 'ETF',
                'security_id'   => $etfId,
                'action_type'   => 'SPLIT',
                'record_date'   => $recordDate,
                'ratio_old'     => $ratioOld,
                'ratio_new'     => $ratioNew,
                'shares_before' => $eligibleUnits,
                'shares_after'  => $unitsAfter,
                'notes'         => $notes ?: "ETF Split {$ratioOld}:{$ratioNew}",
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/etfs')->with('error', 'Failed to process ETF split.');
            }

            return redirect()->to('/etfs')->with('success', "ETF split ({$ratioOld}:{$ratioNew}) recorded for {$etf['symbol']}. Units adjusted from {$eligibleUnits} to {$unitsAfter}.");

        } catch (Exception $e) {
            $db->transRollback();
            return redirect()->to('/etfs')->with('error', 'ETF Split failed: ' . $e->getMessage());
        }
    }

    /**
     * Export active ETFs holdings to CSV.
     */
    public function exportHoldings()
    {
        $userId = (int) session()->get('userId');
        $metricsData = $this->etfModel->getHoldingsWithMetrics($userId);
        $activeHoldings = $metricsData['active_holdings'] ?? [];

        $activeHoldings = array_filter($activeHoldings, function ($h) {
            return ($h['active_quantity'] ?? 0) > 0;
        });

        $headers = [
            'ETF Symbol',
            'ETF Name',
            'Category',
            'Quantity',
            'Avg Buy Price (INR)',
            'Current Market Price (INR)',
            'Invested Value (INR)',
            'Current Value (INR)',
            'Unrealized P&L (INR)',
            'Return (%)'
        ];

        $rows = [];
        $totalInvested = 0.0;
        $totalCurrent = 0.0;
        $totalPnl = 0.0;

        foreach ($activeHoldings as $h) {
            $qty = (int) ($h['active_quantity'] ?? 0);
            $avgBuy = (float) ($h['avg_buy_price'] ?? 0);
            $cmp = (float) ($h['current_price'] ?? 0);
            $invested = (float) ($h['invested_value'] ?? 0);
            $current = (float) ($h['current_value'] ?? 0);
            $pnl = (float) ($h['unrealized_pnl'] ?? 0);
            $pnlPct = (float) ($h['unrealized_pnl_percent'] ?? 0);

            $totalInvested += $invested;
            $totalCurrent += $current;
            $totalPnl += $pnl;

            $rows[] = [
                $h['symbol'] ?? '',
                $h['etf_name'] ?? '',
                $h['category'] ?? '—',
                $qty,
                $avgBuy,
                $cmp,
                $invested,
                $current,
                $pnl,
                $pnlPct
            ];
        }

        $metadata = [
            ['MyFolioVault - ETFs Active Holdings'],
            ['Generated on:', date('Y-m-d H:i:s')],
            ['Total Active Holdings:', count($rows)],
            ['Total Invested Value (INR):', $totalInvested],
            ['Total Current Value (INR):', $totalCurrent],
            ['Total Unrealized P&L (INR):', $totalPnl]
        ];

        $filename = 'etfs_holdings_' . date('Y-m-d') . '.csv';
        csv_download($filename, $headers, $rows, $metadata);
    }
}

