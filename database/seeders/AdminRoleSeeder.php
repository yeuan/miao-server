<?php

namespace Database\Seeders;

use App\Models\Manager\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminRole::updateOrCreate(
            ['id' => 1],
            [
                'name' => '超级管理者',
                'allow_nav' => [],
                'created_by' => 'System',
                'updated_by' => 'System',
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
    }
}
