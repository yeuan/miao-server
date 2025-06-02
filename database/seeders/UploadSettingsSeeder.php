<?php

namespace Database\Seeders;

use App\Models\System\UploadSetting;
use Illuminate\Database\Seeder;

class UploadSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'id' => 1,
                'type' => 1, // 1:圖片, 2:檔案
                'module_code' => 'banner',
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'thumbnail_enable' => 1,
                'thumb_width' => 900,
                'thumb_height' => 800,
                'thumb_mode' => 4,
                'sort' => 1,
            ],
        ];

        foreach ($modules as $item) {
            UploadSetting::updateOrCreate(
                ['id' => $item['id']],
                [
                    'type' => $item['type'],
                    'module_code' => $item['module_code'],
                    'extensions' => $item['extensions'],
                    'thumbnail_enable' => $item['thumbnail_enable'],
                    'thumb_width' => $item['thumb_width'],
                    'thumb_height' => $item['thumb_height'],
                    'thumb_mode' => $item['thumb_mode'],
                    'sort' => $item['sort'],
                    'created_by' => 'System',
                    'updated_by' => 'System',
                    'created_at' => time(),
                    'updated_at' => time(),
                ]
            );
        }
    }
}
