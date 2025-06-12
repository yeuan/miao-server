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

if (! function_exists('getRelatedTableByModuleCode')) {

    /**
     * 依 module_code 取得對應 related_table（Model::class）
     */
    function getRelatedTableByModuleCode(string $moduleCode): ?string
    {
        $map = config('module_map');
        $moduleCode = strtolower($moduleCode);

        if (isset($map[$moduleCode])) {
            return $map[$moduleCode];
        }

        return null;
    }
}

if (! function_exists('getModuleCodeByModel')) {
    /**
     * 由 Model::class 反查 module_code
     */
    function getModuleCodeByModel(string $modelClass): ?string
    {
        $moduleMap = config('module_map');

        return array_search($modelClass, $moduleMap, true) ?: null;
    }
}
