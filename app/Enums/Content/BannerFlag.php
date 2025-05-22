<?php

namespace App\Enums\Content;

use App\Traits\EnumTrait;

enum BannerFlag: int
{
    use EnumTrait;

    case TOP = 1 << 0; // 輪播優先
    case APP_ONLY = 1 << 1; // 只在 App 顯示
}
