<?php

namespace App\Observers\Manager;

use App\Models\Manager\Tag;
use App\Models\Manager\Taggable;

class TaggableObserver
{
    // 新增時 +1
    public function created(Taggable $taggable)
    {
        Tag::where('id', $taggable->tag_id)->increment('used_count');
    }

    // 刪除時 -1
    public function deleted(Taggable $taggable)
    {
        // 確保 used_count 不會變負數
        Tag::where('id', $taggable->tag_id)->where('used_count', '>', 0)->decrement('used_count');
    }
}
