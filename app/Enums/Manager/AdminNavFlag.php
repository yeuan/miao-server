<?php

namespace App\Enums\Manager;

use App\Traits\EnumTrait;

enum AdminNavFlag: int
{
    use EnumTrait;

    case ALLOW_BACKSTAGE = 1 << 0;
    case ALLOW_AGENT_BACKSTAGE = 1 << 1;
    case ALLOW_RESERVE = 1 << 2;
    case ACTION_RECORD = 1 << 3;
    case FINAL = 1 << 4;
}
