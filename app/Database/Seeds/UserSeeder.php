<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'          => 'Investor Admin',
            'email'         => 'admin@portfolio.local',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Check if user already exists
        $user = $this->db->table('users')->where('email', $data['email'])->get()->getRow();
        if (!$user) {
            $this->db->table('users')->insert($data);
        }
    }
}
