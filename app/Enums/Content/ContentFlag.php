<?php

namespace App\Enums\Content;

use App\Traits\EnumTrait;

enum ContentFlag: int
{
    use EnumTrait;

    case TOP = 1 << 0; // 1  置頂
    case FEATURED = 1 << 1; // 2  精選
    case RECOMMENDED = 1 << 2; // 4  推薦
    case HOT = 1 << 3; // 8  熱門
    case HOMEPAGE = 1 << 4; // 16 首頁顯示
    case MEMBER = 1 << 5; // 32 僅會員可見
    case APP_ONLY = 1 << 6; // 64 APP專用
}
