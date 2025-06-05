<?php

namespace App\Observers\Content;

use App\Enums\Backstage;

class OwnerObserver
{
    // 在建立時，補上owner資料
    public function creating($model): void
    {
        $backstage = requestOutParam('backstage');
        // 判斷是否為主後台，允許owner欄位空值（不補，直接 return）
        if ($backstage === Backstage::ADMIN->value) {
            return;
        }

        // 多租客登入情境，強制補上正確 owner_type/owner_id
        if ($backstage == Backstage::TENANT->value) {
            if (empty($model->owner_type)) {
                $model->owner_type = Backstage::from($backstage)->toOwnerType()?->value;
            }
            if (empty($model->owner_id)) {
                $model->owner_id = requestOutParam('tenant_id', 0);
            }
        }
    }
}
