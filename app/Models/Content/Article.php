<?php

namespace App\Models\Content;

use App\Enums\Status;
use App\Models\BaseModel;
use App\Models\Manager\Tag;

class Article extends BaseModel
{
    protected $table = 'articles';

    protected $guarded = ['id'];

    // 只要有上傳欄位
    protected array $uploadFields = ['cover', 'cover_app'];

    public function category()
    {
        return $this->belongsTo('App\Models\Content\ArticleCategory', 'category_id');
    }

    // 與 Tag 多型多對多
    public function tags()
    {
        return $this->morphToMany('App\Models\Manager\Tag', 'taggable')
            ->where('tags.status', Status::ENABLE->value)
            ->orderBy('tags.sort', 'desc')
            ->orderBy('tags.id', 'desc')
            ->using('App\Models\Manager\Taggable')
            ->withTimestamps();
    }
}
