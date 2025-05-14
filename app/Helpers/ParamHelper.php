<?php

if (! function_exists('paramProcess')) {

    /**
     * 將查詢參數分為分頁、排序和搜尋條件，並返回結構化的結果
     */
    function paramProcess(array $params, string $defaultOrder = 'id'): array
    {
        $page = $params['page'] ?? 1;
        $order = getOrder($params, $defaultOrder);

        // 過濾掉分頁和排序相關的參數
        $searchParams = array_diff_key($params, array_flip(['page', 'per_page', 'sort_by']));
        $search = decodeSearchParams($searchParams);

        return compact('page', 'order', 'search');
    }
}

if (! function_exists('getOrder')) {

    /**
     * 將排序參數解析為結構化的排序條件（欄位名稱和排序條件）
     */
    function getOrder(array $params, string $defaultOrder = 'id'): array
    {
        $orderString = $params['sort_by'] ?? $defaultOrder;

        return collect(explode(',', $orderString))
            ->mapWithKeys(fn ($row) => [
                explode(' ', $row)[0] => explode(' ', $row)[1] ?? 'asc',
            ])
            ->toArray();
    }
}

if (! function_exists('getPage')) {

    /**
     * 取得當前頁碼
     */
    function getPage(array $params): int
    {
        return (int) ($params['page'] ?? 1);
    }
}

if (! function_exists('decodeSearchParams')) {

    /**
     * 將查詢參數中的搜尋條件進行解碼
     */
    function decodeSearchParams(array $params): array
    {
        return collect($params)
            ->flatMap(fn ($value, $key) => is_array($value)
                ? [
                    "{$key}1" => $value[0] ?? null,
                    "{$key}2" => $value[1] ?? null,
                ]
                : [$key => urldecode($value)]
            )
            ->toArray();
    }
}
