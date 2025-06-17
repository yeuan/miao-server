<?php

namespace App\Observers\Content;

use App\Models\Content\FaqCategory;
use App\Traits\UsedCountTrait;

class FaqObserver
{
    use UsedCountTrait;

    protected string $usedCountModel = FaqCategory::class;

    protected string $usedCountField = 'category_id';
}
