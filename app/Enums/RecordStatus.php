<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum RecordStatus: int
{
    use EnumTrait;

    case PENDING = 0; // 待處理（待審核、草稿、暫存）
    case ACTIVE = 1; // 啟用/有效
    case DISABLED = 2; // 停用
    case DELETED = 3; // 已刪除
    case ARCHIVED = 4; // 已封存
    case REJECTED = 5; // 審核未通過
    case EXPIRED = 6; // 已過期
}
