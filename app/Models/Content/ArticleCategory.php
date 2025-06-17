<?php

namespace App\Models\Content;

use App\Enums\Status;
use App\Models\BaseModel;

class ArticleCategory extends BaseModel
{
    protected $table = 'article_categories';

    protected $guarded = ['id'];

    public function articles()
    {
        return $this->hasMany('App\Models\Content\Article', 'category_id', 'id')
            ->where('articles.status', Status::ENABLE->value)
            ->orderBy('articles.sort', 'desc')
            ->orderBy('articles.id', 'desc');
    }
}
