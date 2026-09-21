<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NpsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Get user
        $user = $db->table('users')->where('email', 'admin@portfolio.local')->get()->getRowArray();
        if (!$user) {
            return;
        }

        $userId = (int) $user['id'];

        // Clean previous NPS seed data for idempotency
        $db->table('nps_accounts')->where('user_id', $userId)->delete();

        // 2. Create Tier 1 NPS Account
        $accountData = [
            'user_id'              => $userId,
            'pran'                 => '110055228833',
            'subscriber_name'      => $user['name'] ?: 'Karthik',
            'pfm_name'             => 'HDFC Pension Management',
            'investment_choice'    => 'ACTIVE',
            'alloc_equity'         => 50.00,
            'alloc_corporate_debt' => 30.00,
            'alloc_govt_bonds'     => 20.00,
            'alloc_alternative'    => 0.00,
            'status'               => 'ACTIVE',
            'created_at'           => '2024-05-10 10:00:00',
            'updated_at'           => '2024-05-10 10:00:00',
        ];
        $db->table('nps_accounts')->insert($accountData);
        $accountId = (int) $db->insertID();

        // 3. Initial Contribution: ₹50,000 on 10-May-2024 (Voluntary)
        $tx1 = [
            'user_id'               => $userId,
            'nps_account_id'        => $accountId,
            'transaction_type'      => 'CONTRIBUTION',
            'contribution_type'     => 'VOLUNTARY',
            'transaction_date'      => '2024-05-10',
            'acknowledgement_no'    => 'ACK20240510-9988',
            'gross_amount'          => 50000.00,
            'optional_cash_charges' => 0.00,
            'net_amount'            => 50000.00,
            'financial_year'        => '2024-2025',
            'notes'                 => 'Initial Tier 1 voluntary retirement corpus contribution',
            'created_at'            => '2024-05-10 10:05:00',
            'updated_at'            => '2024-05-10 10:05:00',
        ];
        $db->table('nps_transactions')->insert($tx1);
        $tx1Id = (int) $db->insertID();

        // Scheme allotments for Tx1
        $db->table('nps_scheme_units')->insertBatch([
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx1Id,
                'scheme_type'        => 'SCHEME_E',
                'scheme_name'        => 'HDFC Pension Management - Scheme E (Equity)',
                'transaction_type'   => 'CONTRIBUTION',
                'transaction_date'   => '2024-05-10',
                'allocated_amount'   => 25000.00,
                'nav'                => 45.2000,
                'units'              => 553.0973,
                'remaining_units'    => 553.0973,
                'current_nav'        => 58.6500,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-05-10 10:05:00',
                'updated_at'         => '2024-05-10 10:05:00',
            ],
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx1Id,
                'scheme_type'        => 'SCHEME_C',
                'scheme_name'        => 'HDFC Pension Management - Scheme C (Corporate Debt)',
                'transaction_type'   => 'CONTRIBUTION',
                'transaction_date'   => '2024-05-10',
                'allocated_amount'   => 15000.00,
                'nav'                => 28.5000,
                'units'              => 526.3158,
                'remaining_units'    => 526.3158,
                'current_nav'        => 31.4500,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-05-10 10:05:00',
                'updated_at'         => '2024-05-10 10:05:00',
            ],
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx1Id,
                'scheme_type'        => 'SCHEME_G',
                'scheme_name'        => 'HDFC Pension Management - Scheme G (Govt Securities)',
                'transaction_type'   => 'CONTRIBUTION',
                'transaction_date'   => '2024-05-10',
                'allocated_amount'   => 10000.00,
                'nav'                => 22.4000,
                'units'              => 446.4286,
                'remaining_units'    => 446.4286,
                'current_nav'        => 24.8000,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-05-10 10:05:00',
                'updated_at'         => '2024-05-10 10:05:00',
            ],
        ]);

        // 4. Quarterly Brokerage / CRA Unit Deduction: 30-Jun-2024
        $tx2 = [
            'user_id'               => $userId,
            'nps_account_id'        => $accountId,
            'transaction_type'      => 'UNIT_DEDUCTION',
            'contribution_type'     => 'VOLUNTARY',
            'transaction_date'      => '2024-06-30',
            'acknowledgement_no'    => 'CRA-Q1-DEDUCT',
            'gross_amount'          => 0.00,
            'optional_cash_charges' => 0.00,
            'net_amount'            => 0.00,
            'financial_year'        => '2024-2025',
            'notes'                 => 'Q1 FY24-25 CRA & POP quarterly servicing & maintenance unit deduction',
            'created_at'            => '2024-06-30 18:00:00',
            'updated_at'            => '2024-06-30 18:00:00',
        ];
        $db->table('nps_transactions')->insert($tx2);
        $tx2Id = (int) $db->insertID();

        // Scheme units deducted
        $db->table('nps_scheme_units')->insertBatch([
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx2Id,
                'scheme_type'        => 'SCHEME_E',
                'scheme_name'        => 'HDFC Pension Management - Scheme E (Equity)',
                'transaction_type'   => 'UNIT_DEDUCTION',
                'transaction_date'   => '2024-06-30',
                'allocated_amount'   => 0.00,
                'nav'                => 46.1000,
                'units'              => -0.4500,
                'remaining_units'    => 0.0000,
                'current_nav'        => 58.6500,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-06-30 18:00:00',
                'updated_at'         => '2024-06-30 18:00:00',
            ],
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx2Id,
                'scheme_type'        => 'SCHEME_C',
                'scheme_name'        => 'HDFC Pension Management - Scheme C (Corporate Debt)',
                'transaction_type'   => 'UNIT_DEDUCTION',
                'transaction_date'   => '2024-06-30',
                'allocated_amount'   => 0.00,
                'nav'                => 28.8000,
                'units'              => -0.3200,
                'remaining_units'    => 0.0000,
                'current_nav'        => 31.4500,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-06-30 18:00:00',
                'updated_at'         => '2024-06-30 18:00:00',
            ],
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx2Id,
                'scheme_type'        => 'SCHEME_G',
                'scheme_name'        => 'HDFC Pension Management - Scheme G (Govt Securities)',
                'transaction_type'   => 'UNIT_DEDUCTION',
                'transaction_date'   => '2024-06-30',
                'allocated_amount'   => 0.00,
                'nav'                => 22.6000,
                'units'              => -0.2500,
                'remaining_units'    => 0.0000,
                'current_nav'        => 24.8000,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-06-30 18:00:00',
                'updated_at'         => '2024-06-30 18:00:00',
            ],
        ]);

        // 5. Subsequent Voluntary Contribution: ₹20,000 on 15-Nov-2024
        $tx3 = [
            'user_id'               => $userId,
            'nps_account_id'        => $accountId,
            'transaction_type'      => 'CONTRIBUTION',
            'contribution_type'     => 'VOLUNTARY',
            'transaction_date'      => '2024-11-15',
            'acknowledgement_no'    => 'ACK20241115-3344',
            'gross_amount'          => 20000.00,
            'optional_cash_charges' => 0.00,
            'net_amount'            => 20000.00,
            'financial_year'        => '2024-2025',
            'notes'                 => 'Mid-year voluntary top-up contribution',
            'created_at'            => '2024-11-15 11:30:00',
            'updated_at'            => '2024-11-15 11:30:00',
        ];
        $db->table('nps_transactions')->insert($tx3);
        $tx3Id = (int) $db->insertID();

        $db->table('nps_scheme_units')->insertBatch([
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx3Id,
                'scheme_type'        => 'SCHEME_E',
                'scheme_name'        => 'HDFC Pension Management - Scheme E (Equity)',
                'transaction_type'   => 'CONTRIBUTION',
                'transaction_date'   => '2024-11-15',
                'allocated_amount'   => 10000.00,
                'nav'                => 52.1000,
                'units'              => 191.9386,
                'remaining_units'    => 191.9386,
                'current_nav'        => 58.6500,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-11-15 11:30:00',
                'updated_at'         => '2024-11-15 11:30:00',
            ],
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx3Id,
                'scheme_type'        => 'SCHEME_C',
                'scheme_name'        => 'HDFC Pension Management - Scheme C (Corporate Debt)',
                'transaction_type'   => 'CONTRIBUTION',
                'transaction_date'   => '2024-11-15',
                'allocated_amount'   => 6000.00,
                'nav'                => 29.8000,
                'units'              => 201.3423,
                'remaining_units'    => 201.3423,
                'current_nav'        => 31.4500,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-11-15 11:30:00',
                'updated_at'         => '2024-11-15 11:30:00',
            ],
            [
                'user_id'            => $userId,
                'nps_account_id'     => $accountId,
                'nps_transaction_id' => $tx3Id,
                'scheme_type'        => 'SCHEME_G',
                'scheme_name'        => 'HDFC Pension Management - Scheme G (Govt Securities)',
                'transaction_type'   => 'CONTRIBUTION',
                'transaction_date'   => '2024-11-15',
                'allocated_amount'   => 4000.00,
                'nav'                => 23.5000,
                'units'              => 170.2128,
                'remaining_units'    => 170.2128,
                'current_nav'        => 24.8000,
                'nav_date'           => '2026-09-18',
                'created_at'         => '2024-11-15 11:30:00',
                'updated_at'         => '2024-11-15 11:30:00',
            ],
        ]);
    }
}
