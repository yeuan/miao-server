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
            [
                'id' => 4,
                'namespace' => 'Content',
                'code' => 'page',
                'name' => '單頁',
                'description' => '用於建立靜態內容頁面，如關於我們、服務條款、隱私政策等。',
                'sort' => 4,
            ],
            [
                'id' => 5,
                'namespace' => 'Content',
                'code' => 'article',
                'name' => '文章',
                'description' => '可發布具圖文排版的長文內容，適合經營品牌、知識資訊、專欄等用途。',
                'sort' => 5,
            ],
            [
                'id' => 6,
                'namespace' => 'Content',
                'code' => 'news',
                'name' => '最新消息',
                'description' => '提供快速發布平台或租客的重要即時資訊，供使用者關注。',
                'sort' => 6,
            ],
            [
                'id' => 7,
                'namespace' => 'Content',
                'code' => 'faq',
                'name' => 'FAQ',
                'description' => '常見問題與解答，可提供使用者快速解惑與協助。',
                'sort' => 7,
            ],
            [
                'id' => 8,
                'namespace' => 'Product',
                'code' => 'product',
                'name' => '商品管理',
                'description' => '統一管理平台與租客的商品資料，可設定分類、規格與庫存。',
                'sort' => 8,
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
