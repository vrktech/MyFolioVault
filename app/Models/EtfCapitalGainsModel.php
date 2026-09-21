<?php

namespace App\Models;

use CodeIgniter\Model;

class EtfCapitalGainsModel extends Model
{
    protected $table            = 'etf_capital_gains';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'etf_id',
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
     * Get all FIFO capital gains records with joined ETF details.
     */
    public function getCapitalGainsWithEtf(int $userId, ?int $etfId = null): array
    {
        $builder = $this->select('etf_capital_gains.*, etfs.symbol, etfs.etf_name, etfs.category, etfs.exchange')
                        ->join('etfs', 'etfs.id = etf_capital_gains.etf_id')
                        ->where('etf_capital_gains.user_id', $userId);

        if ($etfId !== null) {
            $builder->where('etf_capital_gains.etf_id', $etfId);
        }

        return $builder->orderBy('etf_capital_gains.sell_date', 'DESC')
                       ->orderBy('etf_capital_gains.id', 'DESC')
                       ->findAll();
    }
}

