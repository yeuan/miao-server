<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class UploadCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // 多圖存的是 json，decode；單圖存的是字串，直接回傳
        if (is_string($value) && jsonValidate($value)) {
            return json_decode($value, true) ?: [];
        }

        return $value ?: '';
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // 空值
        if (empty($value)) {
            return '';
        }

        // 單圖：['upload_id'=>123, 'path'=>'uploads/xxx.webp']
        if (isset($value['path'])) {
            return $value['path'];
        }

        // 多圖：[ ['upload_id'=>123, 'path'=>'xxx'], ... ]
        if (isset($value[0]['path'])) {
            return json_encode(array_column($value, 'path'), JSON_UNESCAPED_SLASHES);
        }

        // 理論上不會有其它型態
        return '';
    }
}
