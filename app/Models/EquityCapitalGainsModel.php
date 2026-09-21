<?php

namespace App\Models;

use CodeIgniter\Model;

class EquityCapitalGainsModel extends Model
{
    protected $table            = 'equity_capital_gains';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'equity_id',
        'sell_transaction_id',
        'buy_transaction_id',
        'quantity_matched',
        'buy_date',
        'buy_price',
        'sell_date',
        'sell_price',
        'holding_days',
        'gain_type',
        'realized_gain',
    ];

    protected $useTimestamps = false;

    /**
     * Get all FIFO capital gains records with joined stock information.
     */
    public function getCapitalGainsWithStock(int $userId, ?int $equityId = null): array
    {
        $builder = $this->select('equity_capital_gains.*, equities.symbol, equities.company_name, equities.exchange')
                        ->join('equities', 'equities.id = equity_capital_gains.equity_id')
                        ->where('equity_capital_gains.user_id', $userId);

        if ($equityId !== null) {
            $builder->where('equity_capital_gains.equity_id', $equityId);
        }

        return $builder->orderBy('equity_capital_gains.sell_date', 'DESC')
                       ->orderBy('equity_capital_gains.id', 'DESC')
                       ->findAll();
    }
}

