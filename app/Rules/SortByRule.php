<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SortByRule implements Rule
{
    private array $allowedColumns;

    /**
     * Construct a new SortByRule.
     *
     * @param  array  $allowedColumns  允許的排序欄位
     */
    public function __construct(?array $allowedColumns = null)
    {
        $this->allowedColumns = $allowedColumns ?? [];  // 設定為空陣列預設值
    }

    /**
     * 驗證欄位及排序方向是否合法。
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 處理排序條件字串
        $fields = $this->parseSortFields($value);

        foreach ($fields as $field) {
            $column = $field['column'];
            $direction = $field['direction'];

            // 驗證欄位名稱是否在允許的範圍內
            if (! $this->isValidColumn($column)) {
                return false;
            }

            // 驗證排序方向是否有效
            if (! $this->isValidDirection($direction)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 將排序字串解析為欄位與排序方向的組合。
     *
     * @param  string  $value  排序條件字串
     */
    private function parseSortFields(string $value): array
    {
        return array_map(function ($field) {
            $parts = explode(' ', trim($field));

            return [
                'column' => $parts[0],
                'direction' => strtolower($parts[1] ?? config('custom.default.direction', 'asc')), // 預設排序方向為 asc
            ];
        }, explode(',', $value));
    }

    /**
     * 驗證欄位名稱是否在允許的欄位範圍內。
     *
     * @param  string  $column  欄位名稱
     */
    private function isValidColumn(string $column): bool
    {
        return in_array($column, $this->allowedColumns);
    }

    /**
     * 驗證排序方向是否有效。
     *
     * @param  string  $direction  排序方向
     */
    private function isValidDirection(string $direction): bool
    {
        return in_array($direction, ['asc', 'desc']);
    }

    /**
     * 驗證失敗後的錯誤訊息。
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute format is invalid. It should be "field direction", e.g., "status desc, id asc".';
    }
}
