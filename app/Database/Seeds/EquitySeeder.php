<?php

namespace App\Database\Seeds;

use App\Models\EquityDividendModel;
use App\Models\EquityModel;
use App\Models\EquityTransactionModel;
use CodeIgniter\Database\Seeder;

class EquitySeeder extends Seeder
{
    public function run()
    {
        $equityModel      = new EquityModel();
        $transactionModel = new EquityTransactionModel();
        $dividendModel    = new EquityDividendModel();

        // Target default user
        $user = $this->db->table('users')->where('email', 'admin@portfolio.local')->get()->getRowArray();
        if (!$user) {
            return;
        }
        $userId = (int) $user['id'];

        // Clean existing records for fresh seed
        $this->db->table('equity_capital_gains')->where('user_id', $userId)->delete();
        $this->db->table('equity_dividends')->where('user_id', $userId)->delete();
        $this->db->table('equity_transactions')->where('user_id', $userId)->delete();
        $this->db->table('equities')->where('user_id', $userId)->delete();

        // 1. Reliance Industries Ltd
        $relId = $equityModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'RELIANCE',
            'company_name'     => 'Reliance Industries Ltd',
            'exchange'         => 'NSE',
            'isin'             => 'INE002A01018',
            'sector'           => 'Oil & Gas',
            'current_price'    => 1226.40,
            'previous_close'   => 1243.90,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $relId, '2024-04-15', 20, 1180.00, 20.00, 23.60, 'Initial core accumulation');
        $transactionModel->recordBuy($userId, $relId, '2025-06-10', 10, 1220.00, 20.00, 12.20, 'Added on dip');

        // 2. Tata Consultancy Services Ltd
        $tcsId = $equityModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'TCS',
            'company_name'     => 'Tata Consultancy Services Ltd',
            'exchange'         => 'NSE',
            'isin'             => 'INE467B01029',
            'sector'           => 'IT Services',
            'current_price'    => 4180.00,
            'previous_close'   => 4150.00,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $tcsId, '2024-08-20', 15, 3950.00, 20.00, 59.25, 'Portfolio bluechip IT holding');
        $dividendModel->recordDividend($userId, $tcsId, '2025-01-18', 420.00, 28.00, 15, 0.00, 'Interim', 'Q3 FY25 Interim Dividend');

        // 3. HDFC Bank Ltd (with FIFO Sell and LTCG lot matching demonstration)
        $hdfcId = $equityModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'HDFCBANK',
            'company_name'     => 'HDFC Bank Ltd',
            'exchange'         => 'NSE',
            'isin'             => 'INE040A01034',
            'sector'           => 'Banking',
            'current_price'    => 1645.00,
            'previous_close'   => 1638.00,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $hdfcId, '2024-01-10', 50, 1480.00, 20.00, 74.00, 'Long-term banking bet');
        // Record dividend prior to sale
        $dividendModel->recordDividend($userId, $hdfcId, '2024-07-20', 975.00, 19.50, 50, 0.00, 'Final', 'FY24 Final Dividend');
        // Sell 10 shares after > 365 days -> triggers LTCG matching!
        $transactionModel->recordSellFIFO($userId, $hdfcId, '2025-02-15', 10, 1690.00, 20.00, 16.90, 'Partial profit booking for rebalancing');

        // 4. Infosys Ltd
        $infyId = $equityModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'INFY',
            'company_name'     => 'Infosys Ltd',
            'exchange'         => 'NSE',
            'isin'             => 'INE009A01021',
            'sector'           => 'IT Services',
            'current_price'    => 1895.00,
            'previous_close'   => 1880.00,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $infyId, '2025-07-05', 30, 1760.00, 20.00, 52.80, 'Accumulated post earnings');
    }
}

