<?php

namespace App\Enums;

use App\Enums\Common\OwnerType;
use App\Traits\EnumTrait;

enum Backstage: int
{
    use EnumTrait;

    case ADMIN = 1;
    case AGENT = 2;
    case TENANT = 3;

    /**
     * 轉換對應 OwnerType
     */
    public function toOwnerType(): ?OwnerType
    {
        return match ($this) {
            Backstage::ADMIN => OwnerType::PLATFORM,
            Backstage::TENANT => OwnerType::TENANT,
            default => null,
        };
    }
}
