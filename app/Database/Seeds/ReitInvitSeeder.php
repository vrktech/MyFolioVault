<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ReitInvitSeeder extends Seeder
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
        $db->table('reits_invits')->where('user_id', $userId)->delete();

        // 1. Trust 1: Embassy Office Parks REIT (REIT)
        $t1Data = [
            'user_id'           => $userId,
            'trust_name'        => 'Embassy Office Parks REIT',
            'symbol'            => 'EMBASSY',
            'isin'              => 'INE041025011',
            'trust_type'        => 'REIT',
            'exchange'          => 'NSE',
            'sponsor'           => 'Blackstone / Embassy Group',
            'current_price'     => 378.50,
            'prev_close'        => 376.20,
            'last_price_update' => '2026-09-18 15:30:00',
            'created_at'        => '2023-05-15 10:00:00',
            'updated_at'        => '2023-05-15 10:00:00',
        ];
        $db->table('reits_invits')->insert($t1Data);
        $t1Id = (int) $db->insertID();

        // Initial BUY lot
        $db->table('reit_invit_transactions')->insert([
            'user_id'            => $userId,
            'trust_id'           => $t1Id,
            'transaction_type'   => 'BUY',
            'transaction_date'   => '2023-05-15',
            'quantity'           => 200.0000,
            'remaining_quantity' => 200.0000,
            'price'              => 342.00,
            'brokerage'          => 20.00,
            'stt_taxes'          => 68.40,
            'total_amount'       => 68488.40,
            'notes'              => 'Initial purchase lot on NSE',
            'created_at'         => '2023-05-15 10:00:00',
            'updated_at'         => '2023-05-15 10:00:00',
        ]);

        // Quarterly distribution for Embassy REIT
        $db->table('reit_invit_distributions')->insert([
            'user_id'             => $userId,
            'trust_id'            => $t1Id,
            'record_date'         => '2024-02-05',
            'payout_date'         => '2024-02-14',
            'total_amount'        => 1040.00,
            'interest_component'  => 360.00,
            'dividend_component'  => 280.00,
            'return_of_capital'   => 400.00,
            'other_income'        => 0.00,
            'tds_deducted'        => 36.00,
            'net_received'        => 1004.00,
            'quarter_description' => 'Q3 FY23-24 Distribution (₹5.20/unit)',
            'financial_year'      => '2023-2024',
            'notes'               => 'ECS credit with 10% TDS on interest component',
            'created_at'          => '2024-02-14 14:00:00',
            'updated_at'          => '2024-02-14 14:00:00',
        ]);

        // 2. Trust 2: PowerGrid Infrastructure Investment Trust (InvIT)
        $t2Data = [
            'user_id'           => $userId,
            'trust_name'        => 'PowerGrid Infrastructure Investment Trust',
            'symbol'            => 'PGINVIT',
            'isin'              => 'INE0BD323010',
            'trust_type'        => 'INVIT',
            'exchange'          => 'NSE',
            'sponsor'           => 'Power Grid Corporation of India',
            'current_price'     => 98.60,
            'prev_close'        => 98.20,
            'last_price_update' => '2026-09-18 15:30:00',
            'created_at'        => '2023-08-22 11:00:00',
            'updated_at'        => '2023-08-22 11:00:00',
        ];
        $db->table('reits_invits')->insert($t2Data);
        $t2Id = (int) $db->insertID();

        $db->table('reit_invit_transactions')->insert([
            'user_id'            => $userId,
            'trust_id'           => $t2Id,
            'transaction_type'   => 'BUY',
            'transaction_date'   => '2023-08-22',
            'quantity'           => 500.0000,
            'remaining_quantity' => 500.0000,
            'price'              => 96.50,
            'brokerage'          => 20.00,
            'stt_taxes'          => 48.25,
            'total_amount'       => 48318.25,
            'notes'              => 'Accumulation of high-yield transmission InvIT',
            'created_at'         => '2023-08-22 11:00:00',
            'updated_at'         => '2023-08-22 11:00:00',
        ]);

        $db->table('reit_invit_distributions')->insert([
            'user_id'             => $userId,
            'trust_id'            => $t2Id,
            'record_date'         => '2024-05-15',
            'payout_date'         => '2024-05-25',
            'total_amount'        => 1500.00,
            'interest_component'  => 950.00,
            'dividend_component'  => 200.00,
            'return_of_capital'   => 350.00,
            'other_income'        => 0.00,
            'tds_deducted'        => 95.00,
            'net_received'        => 1405.00,
            'quarter_description' => 'Q4 FY23-24 Distribution (₹3.00/unit)',
            'financial_year'      => '2024-2025',
            'notes'               => 'Quarterly distribution credited to bank',
            'created_at'          => '2024-05-25 12:00:00',
            'updated_at'          => '2024-05-25 12:00:00',
        ]);

        // 3. Trust 3: Nexus Select Trust (REIT - Retail)
        $t3Data = [
            'user_id'           => $userId,
            'trust_name'        => 'Nexus Select Trust',
            'symbol'            => 'NEXUS',
            'isin'              => 'INE002L25015',
            'trust_type'        => 'REIT',
            'exchange'          => 'NSE',
            'sponsor'           => 'Blackstone Group',
            'current_price'     => 141.20,
            'prev_close'        => 140.80,
            'last_price_update' => '2026-09-18 15:30:00',
            'created_at'        => '2024-01-10 11:30:00',
            'updated_at'        => '2024-01-10 11:30:00',
        ];
        $db->table('reits_invits')->insert($t3Data);
        $t3Id = (int) $db->insertID();

        $db->table('reit_invit_transactions')->insert([
            'user_id'            => $userId,
            'trust_id'           => $t3Id,
            'transaction_type'   => 'BUY',
            'transaction_date'   => '2024-01-10',
            'quantity'           => 150.0000,
            'remaining_quantity' => 150.0000,
            'price'              => 132.00,
            'brokerage'          => 20.00,
            'stt_taxes'          => 19.80,
            'total_amount'       => 19839.80,
            'notes'              => 'Retail mall REIT investment',
            'created_at'         => '2024-01-10 11:30:00',
            'updated_at'         => '2024-01-10 11:30:00',
        ]);
    }
}

