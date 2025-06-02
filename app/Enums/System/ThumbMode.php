<?php

namespace App\Enums\System;

use App\Traits\EnumTrait;

enum ThumbMode: int
{
    use EnumTrait;

    case COVER = 1; // 滿版裁切
    case CONTAIN = 2; // 留白縮放
    case STRETCH = 3; // 強制拉伸
    case FIT = 4; // 等比縮放
}
