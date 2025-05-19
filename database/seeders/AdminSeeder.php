<?php

namespace Database\Seeders;

use App\Models\Manager\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['id' => 1],
            [
                'username' => 'admin',
                'password' => bcrypt('aaaaaa1'),
                'role_id' => 1,
                'created_by' => 'System',
                'updated_by' => 'System',
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
    }
}
