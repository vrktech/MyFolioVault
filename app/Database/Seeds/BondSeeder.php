<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BondSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $user = $db->table('users')->where('email', 'admin@portfolio.local')->get()->getRowArray();
        if (!$user) {
            return;
        }

        $userId = (int) $user['id'];

        // Clean previous seed data for idempotency
        $db->table('bonds')->where('user_id', $userId)->delete();

        // 1. Bond 1: SGB 2021-22 Series VIII
        $b1Data = [
            'user_id'              => $userId,
            'bond_name'            => 'SGB 2021-22 Series VIII',
            'isin'                 => 'IN0020210194',
            'bond_symbol'          => 'SGBNOV29',
            'category'             => 'SGB',
            'issuer'               => 'Reserve Bank of India',
            'face_value'           => 4791.00,
            'coupon_rate'          => 2.50,
            'interest_frequency'   => 'SEMI_ANNUAL',
            'issue_date'           => '2021-11-02',
            'maturity_date'        => '2029-11-02',
            'current_market_price' => 7450.00,
            'cmp_date'             => '2026-09-18',
            'created_at'           => '2021-11-02 10:00:00',
            'updated_at'           => '2021-11-02 10:00:00',
        ];
        $db->table('bonds')->insert($b1Data);
        $b1Id = (int) $db->insertID();

        // Initial BUY lot for SGB
        $db->table('bond_transactions')->insert([
            'user_id'            => $userId,
            'bond_id'            => $b1Id,
            'transaction_type'   => 'BUY',
            'transaction_date'   => '2021-11-02',
            'quantity'           => 25.0000,
            'remaining_quantity' => 25.0000,
            'price'              => 4791.00,
            'brokerage_charges'  => 0.00,
            'accrued_interest'   => 0.00,
            'total_amount'       => 119775.00,
            'notes'              => 'Initial primary market SGB tranche application (25 grams)',
            'created_at'         => '2021-11-02 10:00:00',
            'updated_at'         => '2021-11-02 10:00:00',
        ]);

        // Semi-annual interest payouts for SGB (2.5% on 1,19,775 = 2,994.38/yr = 1,497.19 per half-year)
        $db->table('bond_interest_payouts')->insertBatch([
            [
                'user_id'            => $userId,
                'bond_id'            => $b1Id,
                'payout_date'        => '2024-05-02',
                'coupon_rate'        => 2.50,
                'gross_interest'     => 1497.19,
                'tds_deducted'       => 0.00,
                'net_interest'       => 1497.19,
                'period_description' => 'Semi-Annual Coupon (Nov 2023 - May 2024)',
                'financial_year'     => '2024-2025',
                'notes'              => 'Direct credit to bank via RBI ECS',
                'created_at'         => '2024-05-02 14:00:00',
                'updated_at'         => '2024-05-02 14:00:00',
            ],
            [
                'user_id'            => $userId,
                'bond_id'            => $b1Id,
                'payout_date'        => '2024-11-02',
                'coupon_rate'        => 2.50,
                'gross_interest'     => 1497.19,
                'tds_deducted'       => 0.00,
                'net_interest'       => 1497.19,
                'period_description' => 'Semi-Annual Coupon (May 2024 - Nov 2024)',
                'financial_year'     => '2024-2025',
                'notes'              => 'Direct credit to bank via RBI ECS',
                'created_at'         => '2024-11-02 14:00:00',
                'updated_at'         => '2024-11-02 14:00:00',
            ],
        ]);

        // 2. Bond 2: 7.18% GS 2033 (Government Security)
        $b2Data = [
            'user_id'              => $userId,
            'bond_name'            => '7.18% GS 2033',
            'isin'                 => 'IN0020230085',
            'bond_symbol'          => '718GS2033',
            'category'             => 'GOVT_SECURITY',
            'issuer'               => 'Government of India',
            'face_value'           => 100.00,
            'coupon_rate'          => 7.18,
            'interest_frequency'   => 'SEMI_ANNUAL',
            'issue_date'           => '2023-08-14',
            'maturity_date'        => '2033-08-14',
            'current_market_price' => 101.40,
            'cmp_date'             => '2026-09-18',
            'created_at'           => '2023-09-01 11:00:00',
            'updated_at'           => '2023-09-01 11:00:00',
        ];
        $db->table('bonds')->insert($b2Data);
        $b2Id = (int) $db->insertID();

        $db->table('bond_transactions')->insert([
            'user_id'            => $userId,
            'bond_id'            => $b2Id,
            'transaction_type'   => 'BUY',
            'transaction_date'   => '2023-09-01',
            'quantity'           => 500.0000,
            'remaining_quantity' => 500.0000,
            'price'              => 99.80,
            'brokerage_charges'  => 50.00,
            'accrued_interest'   => 15.00,
            'total_amount'       => 49965.00,
            'notes'              => 'Purchased via RBI Retail Direct / NSE',
            'created_at'         => '2023-09-01 11:00:00',
            'updated_at'         => '2023-09-01 11:00:00',
        ]);

        $db->table('bond_interest_payouts')->insert([
            'user_id'            => $userId,
            'bond_id'            => $b2Id,
            'payout_date'        => '2024-08-14',
            'coupon_rate'        => 7.18,
            'gross_interest'     => 1795.00,
            'tds_deducted'       => 0.00,
            'net_interest'       => 1795.00,
            'period_description' => 'Semi-Annual Coupon (Feb 2024 - Aug 2024)',
            'financial_year'     => '2024-2025',
            'notes'              => 'RBI Retail Direct coupon payment',
            'created_at'         => '2024-08-14 12:00:00',
            'updated_at'         => '2024-08-14 12:00:00',
        ]);

        // 3. Bond 3: Tata Capital Ltd NCD 8.50%
        $b3Data = [
            'user_id'              => $userId,
            'bond_name'            => 'Tata Capital Ltd NCD 8.50%',
            'isin'                 => 'INE306N07LA8',
            'bond_symbol'          => 'TATACAPNCD',
            'category'             => 'CORPORATE_NCD',
            'issuer'               => 'Tata Capital Financial Services Ltd',
            'face_value'           => 1000.00,
            'coupon_rate'          => 8.50,
            'interest_frequency'   => 'ANNUAL',
            'issue_date'           => '2023-04-10',
            'maturity_date'        => '2028-04-10',
            'current_market_price' => 1025.00,
            'cmp_date'             => '2026-09-18',
            'created_at'           => '2023-04-10 11:30:00',
            'updated_at'           => '2023-04-10 11:30:00',
        ];
        $db->table('bonds')->insert($b3Data);
        $b3Id = (int) $db->insertID();

        $db->table('bond_transactions')->insert([
            'user_id'            => $userId,
            'bond_id'            => $b3Id,
            'transaction_type'   => 'BUY',
            'transaction_date'   => '2023-04-10',
            'quantity'           => 30.0000,
            'remaining_quantity' => 30.0000,
            'price'              => 1000.00,
            'brokerage_charges'  => 0.00,
            'accrued_interest'   => 0.00,
            'total_amount'       => 30000.00,
            'notes'              => 'Primary NCD IPO allotment (30 units)',
            'created_at'         => '2023-04-10 11:30:00',
            'updated_at'         => '2023-04-10 11:30:00',
        ]);

        $db->table('bond_interest_payouts')->insert([
            'user_id'            => $userId,
            'bond_id'            => $b3Id,
            'payout_date'        => '2024-04-10',
            'coupon_rate'        => 8.50,
            'gross_interest'     => 2550.00,
            'tds_deducted'       => 255.00,
            'net_interest'       => 2295.00,
            'period_description' => 'Annual Coupon FY 2023-2024',
            'financial_year'     => '2024-2025',
            'notes'              => '10% TDS deducted as per Section 193',
            'created_at'         => '2024-04-10 15:00:00',
            'updated_at'         => '2024-04-10 15:00:00',
        ]);
    }
}

