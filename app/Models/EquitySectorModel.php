<?php

namespace App\Models;

use CodeIgniter\Model;

class EquitySectorModel extends Model
{
    protected $table            = 'equity_sectors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[equity_sectors.name,id,{id}]',
    ];

    /**
     * Get all sectors ordered alphabetically with count of associated active equities.
     */
    public function getSectorsWithStockCount(): array
    {
        $db = \Config\Database::connect();
        $sectors = $this->orderBy('name', 'ASC')->findAll();

        $countsRes = $db->table('equities')
            ->select('sector, COUNT(*) as stock_count')
            ->groupBy('sector')
            ->get()
            ->getResultArray();

        $countMap = [];
        foreach ($countsRes as $c) {
            $countMap[$c['sector']] = (int) $c['stock_count'];
        }

        foreach ($sectors as &$s) {
            $s['stock_count'] = $countMap[$s['name']] ?? 0;
        }

        return $sectors;
    }
}

