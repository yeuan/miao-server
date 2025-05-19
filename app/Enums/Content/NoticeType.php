<?php

namespace App\Enums\Content;

use App\Traits\EnumTrait;

enum NoticeType: int
{
    use EnumTrait;

    case BACKEND = 1;
    case AGENT = 2;
    case SYSTEM = 3;
    case SHOP = 4;
    case PAYMENT = 5;
    case GAME = 6;
}
