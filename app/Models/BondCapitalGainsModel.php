<?php

namespace App\Models;

use CodeIgniter\Model;

class BondCapitalGainsModel extends Model
{
    protected $table            = 'bond_capital_gains';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'bond_id',
        'exit_transaction_id',
        'buy_transaction_id',
        'buy_date',
        'exit_date',
        'holding_days',
        'quantity_matched',
        'buy_price',
        'exit_price',
        'realized_gain',
        'gain_type',
    ];

    protected $useTimestamps = false;

    /**
     * Get all FIFO capital gains records with joined bond details.
     */
    public function getCapitalGainsWithBond(int $userId, ?int $bondId = null): array
    {
        $builder = $this->select('bond_capital_gains.*, bonds.bond_name, bonds.isin, bonds.bond_symbol, bonds.category')
                        ->join('bonds', 'bonds.id = bond_capital_gains.bond_id')
                        ->where('bond_capital_gains.user_id', $userId);

        if ($bondId !== null) {
            $builder->where('bond_capital_gains.bond_id', $bondId);
        }

        return $builder->orderBy('bond_capital_gains.exit_date', 'DESC')
                       ->orderBy('bond_capital_gains.id', 'DESC')
                       ->findAll();
    }
}

