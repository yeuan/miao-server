<?php

if (! function_exists('filterFields')) {

    /**
     * 過濾不需要的欄位
     */
    function filterFields(array $fields, array $excludeFields): array
    {
        return collect($fields)->except($excludeFields)->all();
    }
}

if (! function_exists('diffAssocArray')) {

    /**
     * 取得傳入參數與原數據的差集
     */
    function diffAssocArray(array $params, array $original): array
    {
        $diffs = [];

        foreach ($params as $key => $currentValue) {
            if ($currentValue === '' || $currentValue === null) {
                continue;
            }

            $originalValue = $original[$key] ?? null;

            // 巢狀陣列 → 當作 JSON 差異處理
            if (is_array($currentValue) && is_array($originalValue)) {
                $diffs += processJsonDiff($key, json_encode($currentValue), json_encode($originalValue));
            }
            // 字串 JSON 差異 → 呼叫 jsonDiffAssoc 處理
            elseif (
                is_string($currentValue) && is_string($originalValue) &&
                jsonValidate($currentValue) && jsonValidate($originalValue)
            ) {
                $diffs += processJsonDiff($key, $currentValue, $originalValue);
            }

            // 一般值 → 直接比對
            elseif ($currentValue != $originalValue) {
                $diffs[$key] = $currentValue;
            }
        }

        return $diffs;
    }
}

if (! function_exists('filterOriginalByKeys')) {

    /**
     * 取得原數據中對應傳入參數的資料
     */
    function filterOriginalByKeys(array $params, array $original): array
    {
        return collect($params)
            ->mapWithKeys(fn ($_, $key) => [$key => Arr::get($original, $key)])
            ->all();
    }
}

if (! function_exists('isSameAsOriginal')) {

    /**
     * 判斷 changed 值是否與 original 完全一致（by dot key 嚴格比對）
     */
    function isSameAsOriginal(array $changed, array $original): bool
    {
        return collect($changed)->every(
            fn ($val, $key) => Arr::has($original, $key) && Arr::get($original, $key) === $val
        );
    }
}

if (! function_exists('isAssocArray')) {

    /**
     * 判斷是否為關聯陣列
     */
    function isAssocArray(array $var): bool
    {
        return array_keys($var) !== range(0, count($var) - 1);
    }
}
