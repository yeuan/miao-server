<?php

return [
    'use_subdomain' => true, // 是否使用子網域方式

    'subdomain' => [
        'api_domain' => 'api',   // api 子網域
        'admin_domain' => 'admin', // 後台 子網域
    ],

    'provider' => [
        'api_prefix' => 'api',   // api 前綴
        'admin_prefix' => 'admin', // admin 前綴
    ],
];
