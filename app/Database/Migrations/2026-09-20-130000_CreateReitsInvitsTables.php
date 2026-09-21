<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReitsInvitsTables extends Migration
{
    public function up()
    {
        // 1. Table: reits_invits (Trust Master Catalog)
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
            'trust_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'symbol' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'isin' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'trust_type' => [
                'type'       => 'ENUM',
                'constraint' => ['REIT', 'INVIT'],
                'default'    => 'REIT',
            ],
            'exchange' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'NSE',
            ],
            'sponsor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'current_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'prev_close' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'last_price_update' => [
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
        $this->forge->addUniqueKey(['user_id', 'symbol', 'exchange']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reits_invits', true);

        // 2. Table: reit_invit_transactions (Buy & Sell Orders)
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
            'trust_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['BUY', 'SELL'],
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
        $this->forge->addKey(['user_id', 'trust_id', 'transaction_date']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('trust_id', 'reits_invits', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reit_invit_transactions', true);

        // 3. Table: reit_invit_distributions (Quarterly Distribution Journal)
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
            'trust_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'record_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'payout_date' => [
                'type' => 'DATE',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'interest_component' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'dividend_component' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'return_of_capital' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'other_income' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'tds_deducted' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'net_received' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'quarter_description' => [
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
        $this->forge->addKey(['user_id', 'trust_id', 'payout_date']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('trust_id', 'reits_invits', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reit_invit_distributions', true);

        // 4. Table: reit_invit_capital_gains (FIFO Tax Audit Ledger)
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
            'trust_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'sell_transaction_id' => [
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
            'sell_date' => [
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
            'sell_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'realized_gain' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'gain_type' => [
                'type'       => 'ENUM',
                'constraint' => ['STCG', 'LTCG'],
                'default'    => 'LTCG',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('trust_id', 'reits_invits', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sell_transaction_id', 'reit_invit_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('buy_transaction_id', 'reit_invit_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reit_invit_capital_gains', true);
    }

    public function down()
    {
        $this->forge->dropTable('reit_invit_capital_gains', true);
        $this->forge->dropTable('reit_invit_distributions', true);
        $this->forge->dropTable('reit_invit_transactions', true);
        $this->forge->dropTable('reits_invits', true);
    }
}

