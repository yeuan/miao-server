<?php

use App\Enums\Backstage;

if (! function_exists('getJwtTtlInSeconds')) {

    /**
     * 取得目前 JWT 的 TTL 秒數（以秒為單位）
     */
    function getJwtTtlInSeconds(): int
    {
        return auth()->factory()->getTTL() * 60;
    }
}

if (! function_exists('getSceneFromBackstage')) {
    /**
     * 由 backstage 整數值取得對應 scene 名稱（小寫字串）
     */
    function getSceneFromBackstage(null|int|string $value): string
    {
        $backstage = Backstage::tryFrom($value);

        return $backstage ? strtolower($backstage->name) : '';
    }
}
