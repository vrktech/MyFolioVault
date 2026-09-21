<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMutualFundsTables extends Migration
{
    public function up()
    {
        // 1. Mutual Funds Master Table (Schemes & Folios)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'amfi_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'scheme_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '180',
            ],
            'folio_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '80',
            ],
            'fund_house' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'current_nav' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
                'default'    => 0.0000,
            ],
            'nav_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'isin' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('amfi_code');
        $this->forge->addUniqueKey(['user_id', 'amfi_code', 'folio_number']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mutual_funds', true);

        // 2. Mutual Fund Transactions Table (SIP, Lumpsum, Redemption with FIFO)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'mutual_fund_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['BUY_SIP', 'BUY_LUMPSUM', 'REDEEM'],
            ],
            'transaction_date' => [
                'type' => 'DATE',
            ],
            'units' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,4',
            ],
            'remaining_units' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,4',
                'default'    => 0.0000, // Tracks unredeemed units for FIFO
            ],
            'nav' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
            ],
            'charges' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'net_amount' => [
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('mutual_fund_id');
        $this->forge->addKey('transaction_date');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mutual_fund_id', 'mutual_funds', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mutual_fund_transactions', true);

        // 3. Mutual Fund Capital Gains (FIFO Audit & Tax Ledger)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'mutual_fund_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'redeem_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'buy_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'units_matched' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,4',
            ],
            'buy_date' => [
                'type' => 'DATE',
            ],
            'buy_nav' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
            ],
            'redeem_date' => [
                'type' => 'DATE',
            ],
            'redeem_nav' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
            ],
            'holding_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'gain_type' => [
                'type'       => 'ENUM',
                'constraint' => ['STCG', 'LTCG'],
            ],
            'realized_gain' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('mutual_fund_id');
        $this->forge->addKey('redeem_transaction_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mutual_fund_id', 'mutual_funds', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('redeem_transaction_id', 'mutual_fund_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('buy_transaction_id', 'mutual_fund_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mutual_fund_capital_gains', true);
    }

    public function down()
    {
        $this->forge->dropTable('mutual_fund_capital_gains', true);
        $this->forge->dropTable('mutual_fund_transactions', true);
        $this->forge->dropTable('mutual_funds', true);
    }
}
