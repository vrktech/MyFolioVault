<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNpsTables extends Migration
{
    public function up()
    {
        // 1. Table: nps_accounts (Tier 1 PRAN Account Master)
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
            'pran' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'subscriber_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'pfm_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'investment_choice' => [
                'type'       => 'ENUM',
                'constraint' => ['ACTIVE', 'AUTO'],
                'default'    => 'ACTIVE',
            ],
            'alloc_equity' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 50.00, // Scheme E %
            ],
            'alloc_corporate_debt' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 30.00, // Scheme C %
            ],
            'alloc_govt_bonds' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 20.00, // Scheme G %
            ],
            'alloc_alternative' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,  // Scheme A %
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['ACTIVE', 'CLOSED'],
                'default'    => 'ACTIVE',
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
        $this->forge->addUniqueKey(['user_id', 'pran']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nps_accounts', true);

        // 2. Table: nps_transactions (Contributions and Quarterly Fee Deductions Header)
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
            'nps_account_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['CONTRIBUTION', 'UNIT_DEDUCTION', 'WITHDRAWAL'],
                'default'    => 'CONTRIBUTION',
            ],
            'contribution_type' => [
                'type'       => 'ENUM',
                'constraint' => ['VOLUNTARY', 'EMPLOYEE', 'EMPLOYER'],
                'default'    => 'VOLUNTARY',
            ],
            'transaction_date' => [
                'type' => 'DATE',
            ],
            'acknowledgement_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
            ],
            'gross_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'optional_cash_charges' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'net_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
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
        $this->forge->addKey(['user_id', 'transaction_date']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nps_account_id', 'nps_accounts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nps_transactions', true);

        // 3. Table: nps_scheme_units (Scheme Units Ledger & Current NAVs)
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
            'nps_account_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'nps_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'scheme_type' => [
                'type'       => 'ENUM',
                'constraint' => ['SCHEME_E', 'SCHEME_C', 'SCHEME_G', 'SCHEME_A'],
            ],
            'scheme_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['CONTRIBUTION', 'UNIT_DEDUCTION', 'WITHDRAWAL'],
                'default'    => 'CONTRIBUTION',
            ],
            'transaction_date' => [
                'type' => 'DATE',
            ],
            'allocated_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'nav' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
            ],
            'units' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,4',
            ],
            'remaining_units' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,4',
                'default'    => 0.0000,
            ],
            'current_nav' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
                'default'    => 10.0000,
            ],
            'nav_date' => [
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
        $this->forge->addKey(['nps_account_id', 'scheme_type']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nps_account_id', 'nps_accounts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nps_transaction_id', 'nps_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nps_scheme_units', true);
    }

    public function down()
    {
        $this->forge->dropTable('nps_scheme_units', true);
        $this->forge->dropTable('nps_transactions', true);
        $this->forge->dropTable('nps_accounts', true);
    }
}

