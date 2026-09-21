<?php

namespace App\Models;

use CodeIgniter\Model;

class EquityDividendModel extends Model
{
    protected $table            = 'equity_dividends';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'equity_id',
        'dividend_date',
        'amount_per_share',
        'shares_held',
        'total_amount',
        'tds_deducted',
        'dividend_type',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Record a dividend entry.
     */
    public function recordDividend(
        int $userId,
        int $equityId,
        string $date,
        float $totalAmount,
        ?float $amountPerShare = null,
        ?int $sharesHeld = null,
        float $tdsDeducted = 0.0,
        string $type = 'Interim',
        ?string $notes = null
    ): int {
        return $this->insert([
            'user_id'          => $userId,
            'equity_id'        => $equityId,
            'dividend_date'    => $date,
            'amount_per_share' => $amountPerShare,
            'shares_held'      => $sharesHeld,
            'total_amount'     => $totalAmount,
            'tds_deducted'     => $tdsDeducted,
            'dividend_type'    => in_array($type, ['Interim', 'Final', 'Special']) ? $type : 'Interim',
            'notes'            => $notes,
        ]);
    }

    /**
     * Get all dividends with joined stock info.
     */
    public function getDividendsWithStock(int $userId, ?int $equityId = null): array
    {
        $builder = $this->select('equity_dividends.*, equities.symbol, equities.company_name, equities.exchange')
                        ->join('equities', 'equities.id = equity_dividends.equity_id')
                        ->where('equity_dividends.user_id', $userId);

        if ($equityId !== null) {
            $builder->where('equity_dividends.equity_id', $equityId);
        }

        return $builder->orderBy('equity_dividends.dividend_date', 'DESC')
                       ->orderBy('equity_dividends.id', 'DESC')
                       ->findAll();
    }
}

