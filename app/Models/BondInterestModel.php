<?php

namespace App\Models;

use CodeIgniter\Model;

class BondInterestModel extends Model
{
    protected $table            = 'bond_interest_payouts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'bond_id',
        'payout_date',
        'coupon_rate',
        'gross_interest',
        'tds_deducted',
        'net_interest',
        'period_description',
        'financial_year',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Record a bond coupon/interest payout.
     */
    public function recordPayout(
        int $userId,
        int $bondId,
        string $payoutDate,
        float $couponRate,
        float $grossInterest,
        float $tdsDeducted,
        float $netInterest,
        ?string $periodDesc,
        string $financialYear,
        ?string $notes = null
    ): int {
        return $this->insert([
            'user_id'            => $userId,
            'bond_id'            => $bondId,
            'payout_date'        => $payoutDate,
            'coupon_rate'        => $couponRate,
            'gross_interest'     => $grossInterest,
            'tds_deducted'       => $tdsDeducted,
            'net_interest'       => $netInterest,
            'period_description' => $periodDesc,
            'financial_year'     => $financialYear,
            'notes'              => $notes,
        ]);
    }

    /**
     * Get interest payment history with joined bond metadata.
     */
    public function getPayoutsWithBond(int $userId, ?int $bondId = null): array
    {
        $builder = $this->select('bond_interest_payouts.*, bonds.bond_name, bonds.isin, bonds.bond_symbol, bonds.category, bonds.interest_frequency')
                        ->join('bonds', 'bonds.id = bond_interest_payouts.bond_id')
                        ->where('bond_interest_payouts.user_id', $userId);

        if ($bondId !== null) {
            $builder->where('bond_interest_payouts.bond_id', $bondId);
        }

        return $builder->orderBy('bond_interest_payouts.payout_date', 'DESC')
                       ->orderBy('bond_interest_payouts.id', 'DESC')
                       ->findAll();
    }
}

