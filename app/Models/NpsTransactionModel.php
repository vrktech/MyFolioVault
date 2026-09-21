<?php

namespace App\Models;

use CodeIgniter\Model;

class NpsTransactionModel extends Model
{
    protected $table            = 'nps_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nps_account_id',
        'transaction_type',
        'contribution_type',
        'transaction_date',
        'acknowledgement_no',
        'gross_amount',
        'optional_cash_charges',
        'net_amount',
        'financial_year',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all contribution transactions with child scheme allotments.
     */
    public function getContributions(int $userId, int $accountId): array
    {
        $transactions = $this->where('user_id', $userId)
            ->where('nps_account_id', $accountId)
            ->where('transaction_type', 'CONTRIBUTION')
            ->orderBy('transaction_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $db = \Config\Database::connect();

        foreach ($transactions as &$tx) {
            $txId = (int) $tx['id'];
            $allotments = $db->table('nps_scheme_units')
                ->where('nps_transaction_id', $txId)
                ->get()
                ->getResultArray();
            $tx['allotments'] = $allotments;
        }

        return $transactions;
    }

    /**
     * Get all quarterly unit deduction records.
     */
    public function getQuarterlyUnitDeductions(int $userId, int $accountId): array
    {
        $transactions = $this->where('user_id', $userId)
            ->where('nps_account_id', $accountId)
            ->where('transaction_type', 'UNIT_DEDUCTION')
            ->orderBy('transaction_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $db = \Config\Database::connect();

        foreach ($transactions as &$tx) {
            $txId = (int) $tx['id'];
            $deductions = $db->table('nps_scheme_units')
                ->where('nps_transaction_id', $txId)
                ->get()
                ->getResultArray();
            $tx['deductions'] = $deductions;
        }

        return $transactions;
    }

    /**
     * Validate that no scheme unit balance is negative after transaction modification/deletion.
     */
    public function validateAccountUnits(int $userId, int $accountId): void
    {
        $db = \Config\Database::connect();
        $schemes = ['SCHEME_E', 'SCHEME_C', 'SCHEME_G', 'SCHEME_A'];

        foreach ($schemes as $scheme) {
            $contrib = $db->table('nps_scheme_units')
                ->selectSum('units')
                ->where('nps_account_id', $accountId)
                ->where('scheme_type', $scheme)
                ->where('transaction_type', 'CONTRIBUTION')
                ->get()->getRowArray()['units'] ?? 0;

            $deducted = $db->table('nps_scheme_units')
                ->selectSum('units')
                ->where('nps_account_id', $accountId)
                ->where('scheme_type', $scheme)
                ->where('transaction_type', 'UNIT_DEDUCTION')
                ->get()->getRowArray()['units'] ?? 0;

            $netUnits = (float) $contrib - abs((float) $deducted);
            if ($netUnits < -0.0001) {
                $schemeLabel = match($scheme) {
                    'SCHEME_E' => 'Scheme E (Equity)',
                    'SCHEME_C' => 'Scheme C (Corporate Debt)',
                    'SCHEME_G' => 'Scheme G (Govt Bonds)',
                    'SCHEME_A' => 'Scheme A (Alternative)',
                };
                throw new \Exception("Inventory validation error: Net balance for {$schemeLabel} would become negative (" . number_format($netUnits, 4) . " units). Cannot save this change.");
            }
        }
    }
}

