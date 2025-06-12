<?php

namespace App\Enums\Tenant;

use App\Traits\EnumTrait;

enum TenantFlag: int
{
    use EnumTrait;

    case IS_OFFICIAL = 1 << 0;     // 官方認證
    case IS_HOT = 1 << 1; // 熱門
    case IS_FEATURED = 1 << 2;     // 精選推薦
    case FORCE_REVIEW = 1 << 3; // 必須審核
    case IS_HIDDEN = 1 << 4; // 不顯示於列表中
}
