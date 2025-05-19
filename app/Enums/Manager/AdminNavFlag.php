<?php

namespace App\Enums\Manager;

use App\Traits\EnumTrait;

enum AdminNavFlag: int
{
    use EnumTrait;

    case ALLOW_BACKSTAGE = 1 << 0; // 允許總後台 -> 1
    case ALLOW_AGENT_BACKSTAGE = 1 << 1; // 允許代理後台 -> 2
    case ALLOW_RESERVE = 1 << 2; // 預留 -> 4
    case ACTION_RECORD = 1 << 3; // 操作記錄 -> 8
    case FINAL = 1 << 4; // 最後一層 -> 16
}
