<?php

namespace App\Models;

use CodeIgniter\Model;

class MutualFundCapitalGainsModel extends Model
{
    protected $table            = 'mutual_fund_capital_gains';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'mutual_fund_id',
        'redeem_transaction_id',
        'buy_transaction_id',
        'units_matched',
        'buy_date',
        'buy_nav',
        'redeem_date',
        'redeem_nav',
        'holding_days',
        'gain_type',
        'realized_gain',
    ];

    protected $useTimestamps = false;

    /**
     * Get all FIFO capital gains records with joined scheme details.
     */
    public function getCapitalGainsWithFund(int $userId, ?int $fundId = null): array
    {
        $builder = $this->select('mutual_fund_capital_gains.*, mutual_funds.amfi_code, mutual_funds.scheme_name, mutual_funds.folio_number, mutual_funds.category')
                        ->join('mutual_funds', 'mutual_funds.id = mutual_fund_capital_gains.mutual_fund_id')
                        ->where('mutual_fund_capital_gains.user_id', $userId);

        if ($fundId !== null) {
            $builder->where('mutual_fund_capital_gains.mutual_fund_id', $fundId);
        }

        return $builder->orderBy('mutual_fund_capital_gains.redeem_date', 'DESC')
                       ->orderBy('mutual_fund_capital_gains.id', 'DESC')
                       ->findAll();
    }
}

