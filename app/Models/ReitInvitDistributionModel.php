<?php

namespace App\Models;

use CodeIgniter\Model;

class ReitInvitDistributionModel extends Model
{
    protected $table            = 'reit_invit_distributions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'trust_id',
        'record_date',
        'eligible_units',
        'dpu',
        'payout_date',
        'total_amount',
        'interest_component',
        'dividend_component',
        'return_of_capital',
        'other_income',
        'tds_deducted',
        'net_received',
        'quarter_description',
        'financial_year',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Record a quarterly distribution payment for a REIT or InvIT.
     */
    public function recordDistribution(
        int $userId,
        int $trustId,
        string $payoutDate,
        float $totalAmount,
        float $interest,
        float $dividend,
        float $roc,
        float $other,
        float $tds,
        float $netReceived,
        ?string $quarterDesc,
        string $financialYear,
        ?string $recordDate = null,
        ?string $notes = null,
        float $eligibleUnits = 0.0,
        float $dpu = 0.0
    ): int {
        return $this->insert([
            'user_id'             => $userId,
            'trust_id'            => $trustId,
            'record_date'         => $recordDate,
            'eligible_units'      => $eligibleUnits,
            'dpu'                 => $dpu,
            'payout_date'         => $payoutDate,
            'total_amount'        => $totalAmount,
            'interest_component'  => $interest,
            'dividend_component'  => $dividend,
            'return_of_capital'   => $roc,
            'other_income'        => $other,
            'tds_deducted'        => $tds,
            'net_received'        => $netReceived,
            'quarter_description' => $quarterDesc,
            'financial_year'      => $financialYear,
            'notes'               => $notes,
        ]);
    }

    /**
     * Get distribution history with joined trust metadata.
     */
    public function getDistributionsWithTrust(int $userId, ?int $trustId = null): array
    {
        $builder = $this->select('reit_invit_distributions.*, reits_invits.trust_name, reits_invits.symbol, reits_invits.trust_type, reits_invits.exchange')
                        ->join('reits_invits', 'reits_invits.id = reit_invit_distributions.trust_id')
                        ->where('reit_invit_distributions.user_id', $userId);

        if ($trustId !== null) {
            $builder->where('reit_invit_distributions.trust_id', $trustId);
        }

        return $builder->orderBy('reit_invit_distributions.payout_date', 'DESC')
                       ->orderBy('reit_invit_distributions.id', 'DESC')
                       ->findAll();
    }
}

