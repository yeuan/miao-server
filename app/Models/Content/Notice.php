<?php

namespace App\Models\Content;

use App\Enums\Status;
use App\Models\BaseModel;
use App\Models\Manager\Tag;
use App\Models\Manager\Taggable;

class Notice extends BaseModel
{
    protected $table = 'notices';

    protected $guarded = ['id'];

    // 與 Tag 多型多對多
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->where('tags.status', Status::ENABLE->value)
            ->orderBy('tags.sort', 'desc')
            ->orderBy('tags.id', 'desc')
            ->using(Taggable::class)
            ->withTimestamps();
    }
}
