<?php

return [
    // 全域
    'max_size' => 5120, // 單位KB
    'base_dir' => 'uploads', // 上傳根目錄
    'use_date_folder' => true, // 是否使用日期資料夾
    'rename_file' => true, // 是否重新命名檔案
    'to_extension' => 'webp', // null 不轉檔 ; (針對圖片轉檔)
    'quality' => 90, // 圖片品質

    // 預設模組設置
    'default' => [
        'image' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
            'thumbnail_enable' => true,
            'thumb_width' => 900,
            'thumb_height' => 800,
            'thumb_mode' => 4, // 1:cover 2:contain 3:stretch 4:fit
        ],
        'file' => [
            'extensions' => ['pdf', 'docx', 'txt'],
            'thumbnail_enable' => false,
            'thumb_width' => 0,
            'thumb_height' => 0,
            'thumb_mode' => 0,
        ],
    ],
];
