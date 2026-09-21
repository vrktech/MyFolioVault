<?php

namespace App\Models;

use CodeIgniter\Model;

class ReitInvitCapitalGainsModel extends Model
{
    protected $table            = 'reit_invit_capital_gains';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'trust_id',
        'sell_transaction_id',
        'buy_transaction_id',
        'buy_date',
        'sell_date',
        'holding_days',
        'quantity_matched',
        'buy_price',
        'sell_price',
        'realized_gain',
        'gain_type',
    ];

    protected $useTimestamps = false;

    /**
     * Get all FIFO capital gains records with joined trust details.
     */
    public function getCapitalGainsWithTrust(int $userId, ?int $trustId = null): array
    {
        $builder = $this->select('reit_invit_capital_gains.*, reits_invits.trust_name, reits_invits.symbol, reits_invits.trust_type, reits_invits.exchange')
                        ->join('reits_invits', 'reits_invits.id = reit_invit_capital_gains.trust_id')
                        ->where('reit_invit_capital_gains.user_id', $userId);

        if ($trustId !== null) {
            $builder->where('reit_invit_capital_gains.trust_id', $trustId);
        }

        return $builder->orderBy('reit_invit_capital_gains.sell_date', 'DESC')
                       ->orderBy('reit_invit_capital_gains.id', 'DESC')
                       ->findAll();
    }
}

