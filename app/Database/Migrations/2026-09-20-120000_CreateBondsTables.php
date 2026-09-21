<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBondsTables extends Migration
{
    public function up()
    {
        // 1. Table: bonds (Bond Master Catalog)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'bond_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'isin' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'bond_symbol' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['SGB', 'GOVT_SECURITY', 'CORPORATE_NCD', 'TAX_FREE'],
                'default'    => 'SGB',
            ],
            'issuer' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'face_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 1000.00,
            ],
            'coupon_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'interest_frequency' => [
                'type'       => 'ENUM',
                'constraint' => ['MONTHLY', 'QUARTERLY', 'SEMI_ANNUAL', 'ANNUAL', 'CUMULATIVE'],
                'default'    => 'SEMI_ANNUAL',
            ],
            'issue_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'maturity_date' => [
                'type' => 'DATE',
            ],
            'current_market_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 1000.00,
            ],
            'cmp_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'isin']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bonds', true);

        // 2. Table: bond_transactions (Buy, Sell & Principal Redemption Orders)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'bond_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['BUY', 'SELL', 'REDEMPTION'],
                'default'    => 'BUY',
            ],
            'transaction_date' => [
                'type' => 'DATE',
            ],
            'quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
            ],
            'remaining_quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
                'default'    => 0.0000,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'brokerage_charges' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'accrued_interest' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'bond_id', 'transaction_date']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bond_id', 'bonds', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bond_transactions', true);

        // 3. Table: bond_interest_payouts (Coupon Payment Journal)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'bond_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'payout_date' => [
                'type' => 'DATE',
            ],
            'coupon_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'gross_interest' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'tds_deducted' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'net_interest' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'period_description' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'financial_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'bond_id', 'payout_date']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bond_id', 'bonds', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bond_interest_payouts', true);

        // 4. Table: bond_capital_gains (FIFO Audit & Maturity Tax Ledger)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'bond_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'exit_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'buy_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'buy_date' => [
                'type' => 'DATE',
            ],
            'exit_date' => [
                'type' => 'DATE',
            ],
            'holding_days' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'quantity_matched' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
            ],
            'buy_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'exit_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'realized_gain' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'gain_type' => [
                'type'       => 'ENUM',
                'constraint' => ['STCG', 'LTCG', 'EXEMPT_SGB_MATURITY'],
                'default'    => 'LTCG',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bond_id', 'bonds', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('exit_transaction_id', 'bond_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('buy_transaction_id', 'bond_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bond_capital_gains', true);
    }

    public function down()
    {
        $this->forge->dropTable('bond_capital_gains', true);
        $this->forge->dropTable('bond_interest_payouts', true);
        $this->forge->dropTable('bond_transactions', true);
        $this->forge->dropTable('bonds', true);
    }
}

