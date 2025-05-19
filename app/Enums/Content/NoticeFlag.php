<?php

namespace App\Enums\Content;

use App\Traits\EnumTrait;

enum NoticeFlag: int
{
    use EnumTrait;

    // case ShowCash = 1 << 0;
    // case ShowCredit = 1 << 1;
}
