<?php

namespace App\Models\Content;

use App\Enums\Status;
use App\Models\BaseModel;
use App\Models\Manager\Tag;

class Page extends BaseModel
{
    protected $table = 'pages';

    protected $guarded = ['id'];

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
