<?php

namespace App\Database\Seeds;

use App\Models\EtfModel;
use App\Models\EtfTransactionModel;
use CodeIgniter\Database\Seeder;

class EtfSeeder extends Seeder
{
    public function run()
    {
        $etfModel         = new EtfModel();
        $transactionModel = new EtfTransactionModel();

        // Target default user
        $user = $this->db->table('users')->where('email', 'admin@portfolio.local')->get()->getRowArray();
        if (!$user) {
            return;
        }
        $userId = (int) $user['id'];

        // Clean existing records for fresh seed
        $this->db->table('etf_capital_gains')->where('user_id', $userId)->delete();
        $this->db->table('etf_transactions')->where('user_id', $userId)->delete();
        $this->db->table('etfs')->where('user_id', $userId)->delete();

        // 1. NIFTYBEES (Nifty 50 Index ETF)
        $niftyId = $etfModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'NIFTYBEES',
            'etf_name'         => 'Nippon India ETF Nifty 50 BeES',
            'category'         => 'Index',
            'amc_name'         => 'Nippon India Mutual Fund',
            'exchange'         => 'NSE',
            'isin'             => 'INF732E01015',
            'current_price'    => 268.40,
            'previous_close'   => 266.80,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $niftyId, '2024-03-10', 200, 242.50, 15.00, 12.12, 'Core broad market index SIP');
        $transactionModel->recordBuy($userId, $niftyId, '2025-01-15', 100, 260.00, 15.00, 6.50, 'Market dip accumulation');

        // 2. GOLDBEES (Gold Commodity ETF)
        $goldId = $etfModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'GOLDBEES',
            'etf_name'         => 'Nippon India ETF Gold BeES',
            'category'         => 'Commodity - Gold',
            'amc_name'         => 'Nippon India Mutual Fund',
            'exchange'         => 'NSE',
            'isin'             => 'INF732E01031',
            'current_price'    => 72.80,
            'previous_close'   => 71.90,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $goldId, '2024-05-18', 150, 58.20, 15.00, 4.36, 'Portfolio gold hedge');

        // 3. MON100 (Nasdaq 100 International Tech ETF)
        $monId = $etfModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'MON100',
            'etf_name'         => 'Motilal Oswal Nasdaq 100 ETF',
            'category'         => 'Global',
            'amc_name'         => 'Motilal Oswal Mutual Fund',
            'exchange'         => 'NSE',
            'isin'             => 'INF247L01AU4',
            'current_price'    => 184.50,
            'previous_close'   => 182.10,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $monId, '2024-06-20', 80, 162.00, 15.00, 6.48, 'US Tech diversification');

        // 4. BANKBEES (Nifty Bank Sectoral ETF with a FIFO sale)
        $bankId = $etfModel->insert([
            'user_id'          => $userId,
            'symbol'           => 'BANKBEES',
            'etf_name'         => 'Nippon India ETF Nifty Bank BeES',
            'category'         => 'Sectoral',
            'amc_name'         => 'Nippon India Mutual Fund',
            'exchange'         => 'NSE',
            'isin'             => 'INF732E01023',
            'current_price'    => 542.00,
            'previous_close'   => 538.50,
            'price_updated_at' => date('Y-m-d H:i:s'),
        ]);

        $transactionModel->recordBuy($userId, $bankId, '2024-02-10', 100, 480.00, 20.00, 24.00, 'Banking sector play');
        // Sell 30 units after > 365 days -> triggers LTCG matching!
        $transactionModel->recordSellFIFO($userId, $bankId, '2025-03-01', 30, 535.00, 20.00, 8.02, 'Partial profit booking in bank index');
    }
}

