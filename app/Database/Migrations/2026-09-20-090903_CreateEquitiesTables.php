<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquitiesTables extends Migration
{
    public function up()
    {
        // 1. Equities Catalog Table
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
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
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
            'sector' => [
                'type'       => 'VARCHAR',
                'constraint' => '60',
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
        $this->forge->createTable('equities', true);

        // 2. Equity Transactions Table (with FIFO remaining_quantity)
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
            'equity_id' => [
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
                'default'    => 0, // Unsold shares for FIFO matching
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
        $this->forge->addKey('equity_id');
        $this->forge->addKey('transaction_date');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('equity_id', 'equities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('equity_transactions', true);

        // 3. Equity Capital Gains (FIFO Audit & Indian Tax Ledger)
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
            'equity_id' => [
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
                'constraint' => ['STCG', 'LTCG'], // STCG <= 365 days, LTCG > 365 days
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
        $this->forge->addKey('equity_id');
        $this->forge->addKey('sell_transaction_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('equity_id', 'equities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sell_transaction_id', 'equity_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('buy_transaction_id', 'equity_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('equity_capital_gains', true);

        // 4. Equity Dividends Table
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
            'equity_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'dividend_date' => [
                'type' => 'DATE',
            ],
            'amount_per_share' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'shares_held' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'tds_deducted' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'dividend_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Interim', 'Final', 'Special'],
                'default'    => 'Interim',
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
        $this->forge->addKey('equity_id');
        $this->forge->addKey('dividend_date');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('equity_id', 'equities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('equity_dividends', true);
    }

    public function down()
    {
        $this->forge->dropTable('equity_dividends', true);
        $this->forge->dropTable('equity_capital_gains', true);
        $this->forge->dropTable('equity_transactions', true);
        $this->forge->dropTable('equities', true);
    }
}
