<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum Success: int
{
    use EnumTrait;

    case FAIL = 0;
    case SUCCESS = 1;
}
