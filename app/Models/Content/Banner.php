<?php

namespace App\Models\Content;

use App\Enums\Status;
use App\Models\BaseModel;
use App\Models\Manager\Tag;

class Banner extends BaseModel
{
    protected $table = 'banners';

    protected $guarded = ['id'];

    // 只要有上傳欄位
    protected array $uploadFields = ['image', 'image_app'];

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
