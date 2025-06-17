<?php

namespace App\Observers\Content;

use App\Models\Content\NewsCategory;
use App\Traits\UsedCountTrait;

class NewsObserver
{
    use UsedCountTrait;

    protected string $usedCountModel = NewsCategory::class;

    protected string $usedCountField = 'category_id';
}
