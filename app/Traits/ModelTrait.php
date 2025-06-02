<?php

namespace App\Traits;

trait ModelTrait
{
    /**
     * 一次設多個屬性（不會存進資料庫）
     */
    public function setAttributes(array $attributes): static
    {
        collect($attributes)->each(fn ($value, $key) => $this->{$key} = $value);

        return $this;
    }

    /**
     * 取得上傳欄位
     */
    public function getUploadFields(): array
    {
        return property_exists($this, 'uploadFields') ? $this->uploadFields : [];
    }

    /**
     * 動態取得 casts（支援 baseCasts 與 uploadFields 自動補 cast）
     */
    public function getCasts(): array
    {
        // 先取得原有 casts（父層、子層合併）
        $casts = property_exists($this, 'casts') ? $this->casts : [];

        // 取得 baseCasts，如果有的話（選擇性）
        if (property_exists($this, 'baseCasts')) {
            $casts = array_merge($this->baseCasts, $casts);
        }

        // 自動加上 uploadFields
        if (property_exists($this, 'uploadFields') && is_array($this->uploadFields)) {
            foreach ($this->uploadFields as $field) {
                // 子類已經覆寫就不再補
                if (! array_key_exists($field, $casts)) {
                    $casts[$field] = \App\Casts\UploadCast::class;
                }
            }
        }

        return $casts;
    }
}
