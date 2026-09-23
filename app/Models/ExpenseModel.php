<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table            = 'expenses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'expense_date',
        'amount',
        'expense_type',
        'module',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'expense_date' => 'required|valid_date',
        'amount'       => 'required|numeric|greater_than[0]',
        'expense_type' => 'required|in_list[BROKERAGE,STT_TAXES,PLATFORM_MISC]',
        'module'       => 'required|in_list[equity,etf,bond,reit_invit,mutual_fund]',
        'notes'        => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'expense_date' => [
            'required'   => 'Please select an expense date.',
            'valid_date' => 'Please enter a valid date format (YYYY-MM-DD).',
        ],
        'amount' => [
            'required'     => 'Please specify the expense amount.',
            'numeric'      => 'Expense amount must be a numeric value.',
            'greater_than' => 'Expense amount must be greater than zero.',
        ],
        'expense_type' => [
            'required' => 'Please select an expense type (Brokerage, STT & Taxes, or Platform & Misc).',
            'in_list'  => 'Invalid expense type selected.',
        ],
        'module' => [
            'required' => 'Please select the related investment module.',
            'in_list'  => 'Invalid investment module selected.',
        ],
    ];

    /**
     * Fetch filtered expense transactions for a specific user.
     */
    public function getExpensesByUser(
        int $userId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $module = null,
        ?string $type = null
    ): array {
        $builder = $this->where('user_id', $userId);

        if (!empty($startDate) && !empty($endDate)) {
            $builder->where('expense_date >=', $startDate)
                    ->where('expense_date <=', $endDate);
        }

        if (!empty($module) && $module !== 'ALL') {
            $builder->where('module', strtolower(trim($module)));
        }

        if (!empty($type) && $type !== 'ALL') {
            $builder->where('expense_type', strtoupper(trim($type)));
        }

        return $builder->orderBy('expense_date', 'DESC')
                       ->orderBy('id', 'DESC')
                       ->findAll();
    }

    /**
     * Compute aggregate metrics (Total, Brokerage, STT, Platform & Misc) for a user in a date range.
     */
    public function getExpensesSummary(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->where('user_id', $userId);

        if (!empty($startDate) && !empty($endDate)) {
            $builder->where('expense_date >=', $startDate)
                    ->where('expense_date <=', $endDate);
        }

        $row = $builder->select("
            COUNT(*) as total_count,
            COALESCE(SUM(amount), 0) as total_amount,
            COALESCE(SUM(CASE WHEN expense_type = 'BROKERAGE' THEN amount ELSE 0 END), 0) as total_brokerage,
            COALESCE(SUM(CASE WHEN expense_type = 'STT_TAXES' THEN amount ELSE 0 END), 0) as total_stt,
            COALESCE(SUM(CASE WHEN expense_type = 'PLATFORM_MISC' THEN amount ELSE 0 END), 0) as total_platform
        ")->get()->getRowArray();

        return [
            'total_count'     => (int)($row['total_count'] ?? 0),
            'total_amount'    => (float)($row['total_amount'] ?? 0),
            'total_brokerage' => (float)($row['total_brokerage'] ?? 0),
            'total_stt'       => (float)($row['total_stt'] ?? 0),
            'total_platform'  => (float)($row['total_platform'] ?? 0),
        ];
    }

    /**
     * Retrieve aggregated expenses grouped by module and expense_type for financial reporting.
     * Returns indexed array: ['equity' => ['brokerage' => x, 'stt' => y, 'other' => z, 'total' => sum, 'count' => cnt], ...]
     */
    public function getModuleExpensesGrouped(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->where('user_id', $userId);

        if (!empty($startDate) && !empty($endDate)) {
            $builder->where('expense_date >=', $startDate)
                    ->where('expense_date <=', $endDate);
        }

        $rows = $builder->select("
            module,
            expense_type,
            COALESCE(SUM(amount), 0) as total_amount,
            COUNT(*) as count
        ")->groupBy('module, expense_type')->get()->getResultArray();

        $modules = ['equity', 'etf', 'bond', 'reit_invit', 'mutual_fund'];
        $grouped = [];

        foreach ($modules as $m) {
            $grouped[$m] = [
                'brokerage' => 0.0,
                'stt'       => 0.0,
                'other'     => 0.0,
                'total'     => 0.0,
                'count'     => 0,
            ];
        }

        foreach ($rows as $r) {
            $mod  = $r['module'];
            $type = $r['expense_type'];
            $amt  = (float)$r['total_amount'];
            $cnt  = (int)$r['count'];

            if (!isset($grouped[$mod])) {
                $grouped[$mod] = [
                    'brokerage' => 0.0,
                    'stt'       => 0.0,
                    'other'     => 0.0,
                    'total'     => 0.0,
                    'count'     => 0,
                ];
            }

            if ($type === 'BROKERAGE') {
                $grouped[$mod]['brokerage'] += $amt;
            } elseif ($type === 'STT_TAXES') {
                $grouped[$mod]['stt'] += $amt;
            } elseif ($type === 'PLATFORM_MISC') {
                $grouped[$mod]['other'] += $amt;
            }

            $grouped[$mod]['total'] += $amt;
            $grouped[$mod]['count'] += $cnt;
        }

        return $grouped;
    }
}

