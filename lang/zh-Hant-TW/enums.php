<?php

declare(strict_types=1);

use App\Enums\ApiCode;
use App\Enums\Content\BannerFlag;
use App\Enums\Content\BannerLinkType;
use App\Enums\Content\BannerType;
use App\Enums\Content\NoticeFlag;
use App\Enums\Content\NoticeType;
use App\Enums\HttpStatus;
use App\Enums\Manager\AdminNavFlag;
use App\Enums\RecordStatus;
use App\Enums\Status;
use App\Enums\Success;
use App\Enums\System\ThumbMode;
use App\Enums\System\UploadType;

return [
    // 通用
    ApiCode::class => [
        'SUCCESS' => '成功',
        'RESOURCE_NOT_FOUND' => '請求失敗',
        'SYSTEM_FAILED' => '系統錯誤',

        'VALIDATION_PARAMS_INVALID' => 'API參數驗證錯誤',
        'VALIDATION_CAPTCHA_ERROR' => '驗證碼錯誤',

        'AUTH_NOT_LOGIN' => '尚未登入',
        'AUTH_PARAMS_ERROR' => '帳號或密碼錯誤',
        'AUTH_TOKEN_ERROR' => 'Token錯誤',
        'AUTH_STATUS_DISABLE' => '該帳號已停用',
        // 'AUTH_STATUS_LOCK' => '該帳號已鎖定，請稍後在試',
        // 'AUTH_PASSWORD_ERROR' => '密碼輸入錯誤',
        'AUTH_SCENE_ERROR' => '對應場景設定錯誤',
        'AUTH_LOGIN_TIMES' => '帳號已被封鎖！密碼輸入多次錯誤',
        'AUTH_TRY_LOGIN_TIMES' => '頻繁錯誤嘗試，禁止使用登入',
        // 'AUTH_NOT_AGENT' => '該帳號非代理',
        'AUTH_IP_LIMIT' => '此IP不允許使用',
        'AUTH_JWT_ERROR' => '登入Token錯誤',
        'AUTH_JWT_BLACK' => '登入Token無效',
        'AUTH_JWT_INVALID' => '登入Token失效',
        'AUTH_JWT_EXPIRED' => '登入Token過期',
    ],

    HttpStatus::class => [
        'NOT_FOUND' => '頁面不存在',
        'SERVER_ERROR' => '伺服器錯誤',
        'SERVICE_UNAVAILABLE' => '服務不可用',
        'PARAMS_ERROR' => '參數錯誤',
    ],

    // 執行狀態
    Success::class => [
        'FAIL' => '失敗',
        'SUCCESS' => '成功',
    ],

    Status::class => [
        'DISABLE' => '停用',
        'ENABLE' => '啟用',
    ],

    RecordStatus::class => [
        'PENDING' => '待處理',
        'ACTIVE' => '啟用',
        'DISABLED' => '停用',
        'DELETED' => '已刪除',
        'ARCHIVED' => '已封存',
        'REJECTED' => '審核未通過',
        'EXPIRED' => '已過期',
    ],

    // 公告類型
    NoticeType::class => [
        'BACKEND' => '後台公告',
        'AGENT' => '代理公告',
        'SYSTEM' => '系統公告',
        'SHOP' => '商城公告',
        'PAYMENT' => '金流公告',
        'GAME' => '遊戲公告',
    ],

    // banner 類型
    BannerType::class => [
        'HOME' => '首頁輪播',
        'LOGIN' => '登入頁',
        'EVENT' => '活動頁',
        'SHOP' => '商城專區',
        'PAYMENT' => '金流專區',
        'GAME' => '遊戲專區',
        'MEMBER' => '會員中心',
    ],
    // banner 連結類型
    BannerLinkType::class => [
        'NONE' => '無',
        'SAME_PAGE' => '同頁面',
        'NEW_TAB' => '新分頁',
        'MODULE' => '模組',
        'GAME' => '遊戲',
    ],

    // 上傳類型
    UploadType::class => [
        'IMAGE' => '圖片',
        'FILE' => '檔案',
        // 'VIDEO' => '影片',
        // 'AUDIO' => '音訊',
        // 'DOCUMENT' => '文件',
    ],
    // 縮圖類型
    ThumbMode::class => [
        'COVER' => '滿版裁切',
        'CONTAIN' => '留白縮放',
        'STRETCH' => '強制拉伸',
        'FIT' => '等比縮放',
    ],

    /* -- FLAG  -- */
    // 後台導航
    AdminNavFlag::class => [
        'ALLOW_BACKSTAGE' => '允許總後台',
        'ALLOW_AGENT_BACKSTAGE' => '允許代理後台',
        'ALLOW_RESERVE' => '預留',
        'ACTION_RECORD' => '操作記錄',
        'FINAL' => '最後一層',
    ],

    // 公告
    NoticeFlag::class => [
        'TOP' => '置頂',
        'HOMEPAGE' => '首頁顯示',
        'MARQUEE' => '跑馬燈顯示',
        'PUSH' => '推播',
    ],

    // banner
    BannerFlag::class => [
        'TOP' => '輪播優先',
        'APP_ONLY' => '只在 App 顯示',
    ],
];
