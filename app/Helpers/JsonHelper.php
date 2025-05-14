<?php

if (! function_exists('jsonValidate')) {

    /**
     * 驗證 JSON 格式是否正確
     */
    function jsonValidate(string $json, bool $assoc = true): bool
    {
        // 先檢查是否為單純數字字串，像 "0"、"2" 等，這些不應該被當作有效 JSON
        if (preg_match('/^\d+$/', $json)) {
            return false;
        }

        json_decode($json, $assoc);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (! function_exists('processJsonDiff')) {

    /**
     * 將 JSON 差異結果標註為 dot key
     */
    function processJsonDiff(string $key, string $currentJson, string $originalJson): array
    {
        $diffs = [];

        $jsonDiff = jsonDiffAssoc($currentJson, $originalJson);

        foreach ($jsonDiff as $subKey => $val) {
            $diffs["{$key}.{$subKey}"] = $val;
        }

        return $diffs;
    }
}

if (! function_exists('jsonDiffAssoc')) {

    /**
     * 找出兩個 JSON 的前後差異（支援巢狀結構）
     */
    function jsonDiffAssoc(string $current, string $original): array
    {
        $currentArray = json_decode($current, true) ?? [];
        $originalArray = json_decode($original, true) ?? [];

        // 過濾空值與 null
        $filteredCurrent = array_filter($currentArray, fn ($value) => $value !== '' && $value !== null);

        // 轉為 dot 陣列以支援巢狀比對
        $flatCurrent = Arr::dot($filteredCurrent);
        $flatOriginal = Arr::dot($originalArray);

        // 過濾出有差異的項目
        return collect($flatCurrent)
            ->filter(fn ($value, $key) => ! array_key_exists($key, $flatOriginal) || $flatOriginal[$key] != $value)
            ->all();
    }
}
