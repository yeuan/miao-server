<?php

if (! function_exists('getVerificationField')) {

    /**
     * 取得當前使用方法對應的驗證欄位
     */
    function getVerificationField(): ?string
    {
        $method = config('custom.setting.verification.method', null);

        return match ($method) {
            'turnstile' => 'cf_token',
            default => null,
        };
    }
}
