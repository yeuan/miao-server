<?php

namespace App\Enums\Common;

use App\Traits\EnumTrait;

enum OwnerType: string
{
    use EnumTrait;

    case PLATFORM = 'platform'; // 主平台
    case TENANT = 'tenant'; // 多租客
}
