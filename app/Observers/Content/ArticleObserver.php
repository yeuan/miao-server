<?php

namespace App\Observers\Content;

use App\Models\Content\ArticleCategory;
use App\Traits\UsedCountTrait;

class ArticleObserver
{
    use UsedCountTrait;

    protected string $usedCountModel = ArticleCategory::class;

    protected string $usedCountField = 'category_id';
}
