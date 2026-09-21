<?php

namespace App\Database\Seeds;

use App\Models\MutualFundModel;
use App\Models\MutualFundTransactionModel;
use CodeIgniter\Database\Seeder;

class MutualFundSeeder extends Seeder
{
    public function run()
    {
        $fundModel        = new MutualFundModel();
        $transactionModel = new MutualFundTransactionModel();

        // Target default user
        $user = $this->db->table('users')->where('email', 'admin@portfolio.local')->get()->getRowArray();
        if (!$user) {
            return;
        }
        $userId = (int) $user['id'];

        // Clean existing records for fresh seed
        $this->db->table('mutual_fund_capital_gains')->where('user_id', $userId)->delete();
        $this->db->table('mutual_fund_transactions')->where('user_id', $userId)->delete();
        $this->db->table('mutual_funds')->where('user_id', $userId)->delete();

        // 1. Parag Parikh Flexi Cap Fund
        $ppfasId = $fundModel->insert([
            'user_id'      => $userId,
            'amfi_code'    => '122639',
            'scheme_name'  => 'Parag Parikh Flexi Cap Fund - Direct Plan - Growth',
            'folio_number' => '10294857/01',
            'category'     => 'Equity Scheme - Flexi Cap Fund',
            'fund_house'   => 'PPFAS Mutual Fund',
            'current_nav'  => 89.8569,
            'nav_date'     => date('Y-m-d'),
            'isin'         => 'INF879O01027',
        ]);

        $transactionModel->recordBuy($userId, $ppfasId, 'BUY_SIP', '2024-02-15', 100.0000, 68.5000, 6850.00, 0.34, 'Monthly SIP #1');
        $transactionModel->recordBuy($userId, $ppfasId, 'BUY_SIP', '2024-05-15', 100.0000, 72.4000, 7240.00, 0.36, 'Monthly SIP #2');

        // 2. Mirae Asset Large Cap Fund (with a FIFO redemption)
        $miraeId = $fundModel->insert([
            'user_id'      => $userId,
            'amfi_code'    => '118834',
            'scheme_name'  => 'Mirae Asset Large Cap Fund - Direct Plan - Growth',
            'folio_number' => '99482710/12',
            'category'     => 'Equity Scheme - Large Cap Fund',
            'fund_house'   => 'Mirae Asset Mutual Fund',
            'current_nav'  => 126.5400,
            'nav_date'     => date('Y-m-d'),
            'isin'         => 'INF769K01CX4',
        ]);

        $transactionModel->recordBuy($userId, $miraeId, 'BUY_LUMPSUM', '2024-01-10', 250.0000, 102.5000, 25625.00, 1.28, 'Initial lumpsum allocation');
        // Redeem 50 units after > 365 days -> triggers LTCG matching!
        $transactionModel->recordRedeemFIFO($userId, $miraeId, '2025-02-20', 50.0000, 124.8000, 0.00, 'Partial profit booking');

        // 3. HDFC Mid-Cap Opportunities Fund
        $hdfcMfId = $fundModel->insert([
            'user_id'      => $userId,
            'amfi_code'    => '118989',
            'scheme_name'  => 'HDFC Mid-Cap Opportunities Fund - Direct Plan - Growth',
            'folio_number' => '48201948/88',
            'category'     => 'Equity Scheme - Mid Cap Fund',
            'fund_house'   => 'HDFC Mutual Fund',
            'current_nav'  => 188.4200,
            'nav_date'     => date('Y-m-d'),
            'isin'         => 'INF179K01BE2',
        ]);

        $transactionModel->recordBuy($userId, $hdfcMfId, 'BUY_SIP', '2024-08-10', 120.0000, 155.2000, 18624.00, 0.93, 'Midcap allocation SIP');
    }
}

