<?php

namespace App\Models;

use CodeIgniter\Model;

class CorporateActionModel extends Model
{
    protected $table            = 'corporate_actions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'module',
        'security_id',
        'action_type',
        'record_date',
        'ratio_old',
        'ratio_new',
        'offer_price',
        'cost_apportionment_ratio',
        'target_security_id',
        'shares_before',
        'shares_after',
        'notes',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Compute eligible active shares/units held on a specific historical record date.
     * Evaluates sum(BUY) - sum(SELL) for all transactions on or before $recordDate.
     */
    public function getEligibleSharesOnDate(int $userId, string $module, int $securityId, string $recordDate): float
    {
        $db = \Config\Database::connect();

        if ($module === 'EQUITY') {
            $buyRow = $db->table('equity_transactions')
                ->selectSum('quantity', 'total_buy')
                ->where('user_id', $userId)
                ->where('equity_id', $securityId)
                ->where('transaction_type', 'BUY')
                ->where('transaction_date <=', $recordDate)
                ->get()->getRowArray();

            $sellRow = $db->table('equity_transactions')
                ->selectSum('quantity', 'total_sell')
                ->where('user_id', $userId)
                ->where('equity_id', $securityId)
                ->where('transaction_type', 'SELL')
                ->where('transaction_date <=', $recordDate)
                ->get()->getRowArray();

            $totalBuy = (float) ($buyRow['total_buy'] ?? 0);
            $totalSell = (float) ($sellRow['total_sell'] ?? 0);

            return max(0.0, round($totalBuy - $totalSell, 4));
        }

        if ($module === 'ETF') {
            $buyRow = $db->table('etf_transactions')
                ->selectSum('quantity', 'total_buy')
                ->where('user_id', $userId)
                ->where('etf_id', $securityId)
                ->where('transaction_type', 'BUY')
                ->where('transaction_date <=', $recordDate)
                ->get()->getRowArray();

            $sellRow = $db->table('etf_transactions')
                ->selectSum('quantity', 'total_sell')
                ->where('user_id', $userId)
                ->where('etf_id', $securityId)
                ->where('transaction_type', 'SELL')
                ->where('transaction_date <=', $recordDate)
                ->get()->getRowArray();

            $totalBuy = (float) ($buyRow['total_buy'] ?? 0);
            $totalSell = (float) ($sellRow['total_sell'] ?? 0);

            return max(0.0, round($totalBuy - $totalSell, 4));
        }

        return 0.0;
    }

    /**
     * Get corporate actions history log for a user.
     */
    public function getUserCorporateActions(int $userId, ?string $module = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('corporate_actions ca')
            ->select('ca.*')
            ->where('ca.user_id', $userId);

        if ($module !== null) {
            $builder->where('ca.module', $module);
        }

        $builder->orderBy('ca.record_date', 'DESC')
                ->orderBy('ca.created_at', 'DESC');

        $rows = $builder->get()->getResultArray();

        // Enhance with security names and target security names
        foreach ($rows as &$row) {
            if ($row['module'] === 'EQUITY') {
                $sec = $db->table('equities')->select('symbol, company_name')->where('id', $row['security_id'])->get()->getRowArray();
                $row['security_symbol'] = $sec['symbol'] ?? '—';
                $row['security_name'] = $sec['company_name'] ?? '—';

                if (!empty($row['target_security_id'])) {
                    $targetSec = $db->table('equities')->select('symbol, company_name')->where('id', $row['target_security_id'])->get()->getRowArray();
                    $row['target_security_symbol'] = $targetSec['symbol'] ?? '—';
                    $row['target_security_name'] = $targetSec['company_name'] ?? '—';
                }
            } elseif ($row['module'] === 'ETF') {
                $sec = $db->table('etfs')->select('symbol, etf_name')->where('id', $row['security_id'])->get()->getRowArray();
                $row['security_symbol'] = $sec['symbol'] ?? '—';
                $row['security_name'] = $sec['etf_name'] ?? '—';
            }
        }

        return $rows;
    }
}

