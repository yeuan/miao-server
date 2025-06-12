<?php

namespace Database\Seeders;

use App\Models\Manager\Module;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'id' => 1,
                'namespace' => 'Tenant',
                'code' => 'tenant',
                'name' => '廟宇管理（多租客）',
                'description' => '多租客架構，目前用於廟宇資料的新增、查詢與管理。',
                'sort' => 1,
            ],
            [
                'id' => 2,
                'namespace' => 'Content',
                'code' => 'notice',
                'name' => '公告',
                'description' => '方便管理員發布重要消息或系統通知。',
                'sort' => 2,
            ],
            [
                'id' => 3,
                'namespace' => 'Content',
                'code' => 'banner',
                'name' => '輪播圖',
                'description' => '可展示多張圖片，適用於廣告、活動、重要訊息等視覺宣傳。',
                'sort' => 3,
            ],
        ];

        foreach ($modules as $item) {
            Module::updateOrCreate(
                ['id' => $item['id']],
                [
                    'code' => $item['code'],
                    'namespace' => $item['namespace'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'sort' => $item['sort'],
                    'updated_by' => 'System',
                    'updated_at' => now(),
                ]
            );
        }
    }
}
