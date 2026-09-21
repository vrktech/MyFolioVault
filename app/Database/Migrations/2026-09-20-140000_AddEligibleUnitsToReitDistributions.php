<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEligibleUnitsToReitDistributions extends Migration
{
    public function up()
    {
        $fields = [
            'eligible_units' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,4',
                'default'    => 0.0000,
                'after'      => 'record_date',
            ],
            'dpu' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,4',
                'default'    => 0.0000,
                'after'      => 'eligible_units',
            ],
        ];

        $this->forge->addColumn('reit_invit_distributions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('reit_invit_distributions', ['eligible_units', 'dpu']);
    }
}

