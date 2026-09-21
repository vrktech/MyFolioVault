<?php

namespace App\Models;

use CodeIgniter\Model;

class NpsSchemeUnitModel extends Model
{
    protected $table            = 'nps_scheme_units';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nps_account_id',
        'nps_transaction_id',
        'scheme_type',
        'scheme_name',
        'transaction_type',
        'transaction_date',
        'allocated_amount',
        'nav',
        'units',
        'remaining_units',
        'current_nav',
        'nav_date',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Update current NAVs for each scheme type for an account.
     */
    public function updateCurrentNavs(int $accountId, array $schemeNavs, string $navDate): void
    {
        foreach ($schemeNavs as $schemeType => $newNav) {
            $navVal = (float) $newNav;
            if ($navVal > 0) {
                $this->where('nps_account_id', $accountId)
                     ->where('scheme_type', $schemeType)
                     ->set([
                         'current_nav' => $navVal,
                         'nav_date'    => $navDate,
                     ])
                     ->update();
            }
        }
    }
}

