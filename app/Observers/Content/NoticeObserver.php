<?php

namespace App\Observers\Content;

use App\Models\Content\Notice;

class NoticeObserver
{
    // 在建立時，預設排序
    public function creating(Notice $model): void
    {
        // 使用條件式 MAX() + CASE WHEN，減少查詢次數
        $type = $model->type;

        $result = Notice::selectRaw(
            'MAX(sort) as max_sort, MAX(CASE WHEN type = ? THEN type_sort ELSE NULL END) as max_type_sort',
            [$type]
        )->first();

        $model->sort = ($result->max_sort ?? 0) + 1;
        $model->type_sort = ($result->max_type_sort ?? 0) + 1;
    }
}
