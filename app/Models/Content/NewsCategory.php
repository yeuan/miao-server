<?php

namespace App\Models\Content;

use App\Enums\Status;
use App\Models\BaseModel;

class NewsCategory extends BaseModel
{
    protected $table = 'news_categories';

    protected $guarded = ['id'];

    public function news()
    {
        return $this->hasMany('App\Models\Content\News', 'category_id', 'id')
            ->where('news.status', Status::ENABLE->value)
            ->orderBy('news.sort', 'desc')
            ->orderBy('news.id', 'desc');
    }
}
