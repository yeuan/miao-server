<?php

namespace App\Enums\Content;

use App\Traits\EnumTrait;

enum NoticeFlag: int
{
    use EnumTrait;

    case TOP = 1 << 0; // 置頂
    case HOMEPAGE = 1 << 1; // 首頁顯示
    case MARQUEE = 1 << 2; // 跑馬燈顯示
    case PUSH = 1 << 3; // 推播
}
