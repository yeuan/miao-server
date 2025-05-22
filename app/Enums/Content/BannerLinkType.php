<?php

namespace App\Enums\Content;

use App\Traits\EnumTrait;

enum BannerLinkType: int
{
    use EnumTrait;

    case NONE = 0;
    case SAME_PAGE = 1;
    case NEW_TAB = 2;
    case MODULE = 3;
    case GAME = 4;
}
