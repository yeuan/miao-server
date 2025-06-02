<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum ApiCode: int
{
    use EnumTrait;

    case SUCCESS = 200;
    case NOT_FOUND = 404;
    case SYSTEM_FAILED = 500;
    case SERVICE_UNAVAILABLE = 503;

    // 資料查詢
    case RESOURCE_NOT_FOUND = 40400;
    // 上傳失敗
    case UPLOAD_SAVE_FAILED = 40401;
    // 參數驗證
    case VALIDATION_PARAMS_INVALID = 42201;
    case VALIDATION_CAPTCHA_ERROR = 42202;

    // LOGIN驗證
    case AUTH_NOT_LOGIN = 40100;
    case AUTH_PARAMS_ERROR = 40101;
    case AUTH_TOKEN_ERROR = 40102;
    case AUTH_STATUS_DISABLE = 40103;
    // case AUTH_STATUS_LOCK       = 40104;
    // case AUTH_PASSWORD_ERROR    = 40105;
    case AUTH_SCENE_ERROR = 40106;
    case AUTH_LOGIN_TIMES = 40107;
    // case AUTH_TRY_LOGIN_TIMES = 40108;
    // case AUTH_NOT_AGENT         = 40109;
    case AUTH_IP_LIMIT = 40110;
    case AUTH_JWT_ERROR = 40121;
    // case AUTH_JWT_BLACK = 40122;
    case AUTH_JWT_INVALID = 40123;
    case AUTH_JWT_EXPIRED = 40124;
}
