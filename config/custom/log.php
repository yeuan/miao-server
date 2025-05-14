<?php

return [
    // 過濾不紀錄的api(路由)
    'exclude_route' => [
    ],

    // 過濾排除比對異動的欄位
    'exclude_fields' => [
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ],

    'save_admin_action_log' => true, // 是否開啟操作紀錄Log功能(後台)
    'save_success_log' => true, // 是否開啟紀錄執行成功Log功能
    'save_error_log' => true, // 是否開啟紀錄執行錯誤Log功能
    'save_admin_login_success_log' => true, // 是否開啟紀錄後台登入成功Log功能
    'save_admin_login_error_log' => true, // 是否開啟紀錄後台登入錯誤Log功能（僅寫入錯誤太多次帳號關閉）
];
