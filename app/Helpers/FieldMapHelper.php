<?php

if (! function_exists('getVerificationField')) {

    /**
     * 取得當前使用方法對應的驗證欄位
     */
    function getVerificationField(): ?string
    {
        $method = config('custom.settings.verification.method', null);

        return match ($method) {
            'turnstile' => 'cf_token',
            default => null,
        };
    }
}

if (! function_exists('getOwnerTypeByModuleCode')) {

    /**
     * 依 module_code 取得對應 owner_type（Model::class）
     */
    function getOwnerTypeByModuleCode(string $moduleCode): ?string
    {
        $map = config('module_map');
        $moduleCode = strtolower($moduleCode);

        if (isset($map[$moduleCode])) {
            return $map[$moduleCode];
        }

        return null;
    }
}
