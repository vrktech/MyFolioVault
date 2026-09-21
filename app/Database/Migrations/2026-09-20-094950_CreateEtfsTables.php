<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEtfsTables extends Migration
{
    public function up()
    {
        // 1. ETFs Master Table
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
            'symbol' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'etf_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['Index', 'Commodity - Gold', 'Commodity - Silver', 'Sectoral', 'Global', 'Debt'],
                'default'    => 'Index',
            ],
            'amc_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'exchange' => [
                'type'       => 'ENUM',
                'constraint' => ['NSE', 'BSE'],
                'default'    => 'NSE',
            ],
            'isin' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'current_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'previous_close' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'price_updated_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('symbol');
        $this->forge->addUniqueKey(['user_id', 'symbol', 'exchange']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('etfs', true);

        // 2. ETF Transactions Table (with FIFO remaining_quantity)
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
            'etf_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['BUY', 'SELL'],
            ],
            'transaction_date' => [
                'type' => 'DATE',
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'remaining_quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0, // Tracks unconsumed units for FIFO
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'brokerage' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'stt_taxes' => [
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('etf_id');
        $this->forge->addKey('transaction_date');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('etf_id', 'etfs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('etf_transactions', true);

        // 3. ETF Capital Gains (FIFO Audit & Tax Ledger)
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
            'etf_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sell_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'buy_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantity_matched' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'buy_date' => [
                'type' => 'DATE',
            ],
            'buy_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'sell_date' => [
                'type' => 'DATE',
            ],
            'sell_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
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
        $this->forge->addKey('etf_id');
        $this->forge->addKey('sell_transaction_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('etf_id', 'etfs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sell_transaction_id', 'etf_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('buy_transaction_id', 'etf_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('etf_capital_gains', true);
    }

    public function down()
    {
        $this->forge->dropTable('etf_capital_gains', true);
        $this->forge->dropTable('etf_transactions', true);
        $this->forge->dropTable('etfs', true);
    }
}
