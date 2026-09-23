<?php

namespace App\Controllers;

use App\Models\ExpenseModel;
use Exception;

class Expenses extends BaseController
{
    protected ExpenseModel $expenseModel;

    public function __construct()
    {
        helper(['currency', 'financial', 'form', 'url']);
        $this->expenseModel = new ExpenseModel();
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
                'label'      => 'All Time (Full History)',
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
     * Display the Consolidated Brokerage & Expenses ledger page.
     */
    public function index()
    {
        $userId = $this->getUserId();
        $range  = $this->getActiveDateRange();

        $selectedModule = trim((string)$this->request->getGet('module')) ?: 'ALL';
        $selectedType   = trim((string)$this->request->getGet('type')) ?: 'ALL';

        $expenses = $this->expenseModel->getExpensesByUser(
            $userId,
            $range['startDate'],
            $range['endDate'],
            $selectedModule,
            $selectedType
        );

        $summary = $this->expenseModel->getExpensesSummary(
            $userId,
            $range['startDate'],
            $range['endDate']
        );

        $moduleLabels = [
            'equity'      => ['label' => 'Equities', 'icon' => 'bi bi-graph-up-arrow', 'color' => 'primary'],
            'etf'         => ['label' => 'Exchange Traded Funds', 'icon' => 'bi bi-pie-chart', 'color' => 'success'],
            'bond'        => ['label' => 'Bonds & Fixed Income', 'icon' => 'bi bi-receipt', 'color' => 'danger'],
            'reit_invit'  => ['label' => 'InvITs & REITs', 'icon' => 'bi bi-buildings', 'color' => 'info'],
            'mutual_fund' => ['label' => 'Mutual Funds', 'icon' => 'bi bi-briefcase', 'color' => 'warning'],
        ];

        $typeLabels = [
            'BROKERAGE'     => ['label' => 'Brokerage Charges', 'badge' => 'primary', 'icon' => 'bi bi-cash-coin'],
            'STT_TAXES'     => ['label' => 'STT & Statutory Taxes', 'badge' => 'info', 'icon' => 'bi bi-file-earmark-ruled'],
            'PLATFORM_MISC' => ['label' => 'Platform & Misc Fees', 'badge' => 'secondary', 'icon' => 'bi bi-gear'],
        ];

        return view('expenses/index', [
            'title'          => 'Consolidated Brokerage & Expenses - MyFolioVault',
            'activeNav'      => 'expenses',
            'range'          => $range,
            'selectedModule' => $selectedModule,
            'selectedType'   => $selectedType,
            'expenses'       => $expenses,
            'summary'        => $summary,
            'moduleLabels'   => $moduleLabels,
            'typeLabels'     => $typeLabels,
        ]);
    }

    /**
     * Record a new consolidated expense entry.
     */
    public function add()
    {
        $userId = $this->getUserId();

        $rules = [
            'expense_date' => 'required|valid_date',
            'amount'       => 'required|numeric|greater_than[0]',
            'expense_type' => 'required|in_list[BROKERAGE,STT_TAXES,PLATFORM_MISC]',
            'module'       => 'required|in_list[equity,etf,bond,reit_invit,mutual_fund]',
            'notes'        => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'user_id'      => $userId,
            'expense_date' => $this->request->getPost('expense_date'),
            'amount'       => (float)$this->request->getPost('amount'),
            'expense_type' => strtoupper(trim((string)$this->request->getPost('expense_type'))),
            'module'       => strtolower(trim((string)$this->request->getPost('module'))),
            'notes'        => trim((string)$this->request->getPost('notes')) ?: null,
        ];

        try {
            $this->expenseModel->insert($data);
            return redirect()->to('/expenses')->with('success', 'Expense transaction recorded successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error recording expense: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing expense entry.
     */
    public function update()
    {
        $userId = $this->getUserId();
        $id     = (int)$this->request->getPost('expense_id');

        $expense = $this->expenseModel->where('user_id', $userId)->find($id);
        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Expense record not found.');
        }

        $rules = [
            'expense_date' => 'required|valid_date',
            'amount'       => 'required|numeric|greater_than[0]',
            'expense_type' => 'required|in_list[BROKERAGE,STT_TAXES,PLATFORM_MISC]',
            'module'       => 'required|in_list[equity,etf,bond,reit_invit,mutual_fund]',
            'notes'        => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'expense_date' => $this->request->getPost('expense_date'),
            'amount'       => (float)$this->request->getPost('amount'),
            'expense_type' => strtoupper(trim((string)$this->request->getPost('expense_type'))),
            'module'       => strtolower(trim((string)$this->request->getPost('module'))),
            'notes'        => trim((string)$this->request->getPost('notes')) ?: null,
        ];

        try {
            $this->expenseModel->update($id, $updateData);
            return redirect()->to('/expenses')->with('success', 'Expense transaction updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error updating expense: ' . $e->getMessage());
        }
    }

    /**
     * Delete an expense entry.
     */
    public function delete(int $id)
    {
        $userId  = $this->getUserId();
        $expense = $this->expenseModel->where('user_id', $userId)->find($id);

        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Expense record not found.');
        }

        try {
            $this->expenseModel->delete($id);
            return redirect()->to('/expenses')->with('success', 'Expense record deleted successfully.');
        } catch (Exception $e) {
            return redirect()->to('/expenses')->with('error', 'Error deleting expense: ' . $e->getMessage());
        }
    }
}

