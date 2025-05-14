<?php

return [
    'filter' => [
        'out_parameters' => 'G_', // 要過濾掉的參數前綴（主要用來區別後台類型）
    ],

    // 驗證相關
    'verification' => [
        'use_admin_login_validate' => true,        // 是否使用後台登入驗證
        'method' => 'turnstile', // 驗證方式
        'turnstile' => [
            'url' => env('TURNSTILE_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify'), // 驗證碼網址
            'key' => env('TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA'),                // 驗證碼金鑰
        ],
    ],

    // 相關網路服務設定
    'network' => [
        'ipdata_url' => env('IPDATA_URL', 'http://ip-api.com/json/'), // IP 資訊 API 網址
    ],

    // 緩存設定
    'cache' => [
        'ttl_time' => 86400, // 緩存時間(秒)
    ],

    // 隊列設定
    'queue' => [
        'use_redis' => false, // 是否使用 redis 隊列執行
        'unique_lock_time' => 300, // 解除任務唯一鎖的秒數(秒)
    ],

    // 限流設定
    'rate_limit' => [
        'admin' => [
            'login' => [
                'ip' => [
                    'max_attempts' => 10,  // 每個帳號允許最大嘗試次數
                    'decay_seconds' => 3600, // 限制時間範圍（60 分鐘）
                ],
                'account' => [
                    'max_attempts' => 5,  // 每個帳號允許最大嘗試次數
                    'decay_seconds' => 600, // 限制時間範圍（10 分鐘）
                ],
            ],
        ],
    ],

    // 登入模式設定（single 單一登入 / multi 多裝置登入）
    'login_mode' => [
        'admin' => 'multi',
    ],

    // 登出模式設定（true 全部裝置登出 / false 單一裝置登出）
    'logout_mode' => [
        'admin' => true,
    ],
];
