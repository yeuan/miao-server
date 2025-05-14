<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum Status: int
{
    use EnumTrait;

    case DISABLE = 0;
    case ENABLE = 1;
}
