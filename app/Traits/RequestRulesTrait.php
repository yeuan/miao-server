<?php

namespace App\Traits;

use App\Rules\SortByRule;
use Illuminate\Validation\Rules\Password;

trait RequestRulesTrait
{
    /**
     * 驗證指定欄位為整數
     */
    protected function intRule(bool $required = false): string
    {
        return $this->requiredRule($required).'|integer';
    }

    /**
     * 驗證指定欄位為整數且有最大值限制
     */
    protected function intWithMaxRule(int $max = 1000, bool $required = false): string
    {
        return $this->intRule($required)."|max:{$max}";
    }

    /**
     * 當整數欄位不等於指定值時，驗證其是否存在於指定資料表欄位中
     *
     * @param  string  $table  資料表名稱
     * @param  string  $column  欄位名稱（預設為 id）
     * @param  bool  $required  是否必填（預設為 false）
     * @param  int|string  $unprotectedless  排除驗證的數值（預設為 0）
     * @param  string  $field  欄位名稱（如與 column 不同時指定）
     * @param  int|null  $ignoreId  若不為 null，則額外加入 not_in:本身的 id 限制
     */
    protected function intExistsUnlessRule(string $table, string $column = 'id', bool $required = false, int|string $unless = 0, string $field = '', ?int $ignoreId = null): string
    {
        $field = $field ?: $column;

        // 若有提供 ignoreId 且傳入值不等於自己，則加上排除自己 id 的條件
        $excludeOwnIdRule = $ignoreId ? "|not_in:$ignoreId" : '';

        return $this->requiredRule($required).'|integer'."|exclude_if:{$field},{$unless}"."|exists:{$table},{$column}".$excludeOwnIdRule;
    }

    /**
     * 驗證Enum欄位
     */
    protected function enumRule(?array $allow = null, bool $required = false): string
    {
        return $this->requiredRule($required).'|integer|in:'.implode(',', $allow);
    }

    /**
     * 驗證Flag欄位
     */
    protected function flagRule(array $allow, bool $required = false): array
    {
        $rules = [
            $this->requiredRule($required),
            'array',
        ];
        $rules['*.'] = 'in:'.implode(',', $allow);

        return $rules;
    }

    /**
     * 驗證一般字串欄位（包含最大長度與是否 required）
     */
    protected function stringRule(int $max = 0, bool $required = false): string
    {
        return $this->requiredRule($required).'|string'.($max > 0 ? "|max:{$max}" : '');
    }

    /**
     * 驗證唯一欄位（可指定忽略某筆 ID）
     */
    protected function uniqueRule(string $table, string $column = 'name', ?string $ignoreId = null): string
    {
        $rule = "unique:{$table},{$column}";
        if ($ignoreId) {
            $rule .= ",{$ignoreId}";
        }

        return $rule;
    }

    /**
     * 驗證alpha_num欄位（包含長度介於之間與是否 required）
     */
    protected function alphaNumBetweenRule(int $min = 6, int $max = 20, bool $required = false): string
    {
        return $this->requiredRule($required).'|alpha_num|between:'.$min.','.$max;
    }

    /**
     * 驗證密碼欄位
     */
    protected function passwordRule(int $min = 6, int $max = 20, bool $required = false, bool $confirmed = false, ?int $uncompromised = 0): array
    {
        $rule = Password::min($min)
                     // ->symbols()  // 至少需要一個符號
            ->letters()  // 至少需要一個字母
            ->numbers(); // 至少需要一個數字

        if ($uncompromised > 0) {
            $rule->uncompromised($uncompromised);
        }

        return array_filter([
            $this->requiredRule($required),
            "max:{$max}",
            $rule,
            $confirmed ? 'confirmed' : null,
        ]);
    }

    /**
     * 驗證 JSON 欄位格式
     */
    protected function jsonRule(bool $required = false): string
    {
        return $this->requiredRule($required).'|json';
    }

    /**
     * 驗證陣列欄位格式
     */
    protected function arrayRule(bool $required = false): string
    {
        return $this->requiredRule($required).'|array';
    }

    /**
     * 驗證 URL 欄位格式
     */
    protected function urlRule(int $max = 255, bool $required = false): string
    {
        return $this->requiredRule($required)."|string|url|max:{$max}";
    }

    /**
     * 驗證日期欄位格式
     */
    protected function dateRule(bool $required = false, ?string $format = null): string
    {
        $format = $format ?? config('custom.default.datetime', 'Y-m-d H:i:s');

        return $this->requiredRule($required).'|date|date_format:"'.$format.'"';
    }

    /**
     * 驗證結束時間（需大於等於開始時間），支援多格式與 required_if 起始時間存在
     */
    protected function endDateRule(string $startField = 'start', ?string $format = null): string
    {
        $format = $format ?? config('custom.default.datetime', 'Y-m-d H:i:s');
        $required = $this->has($startField) && ! is_null($this->input($startField));
        $rule = $required ? "required_if:{$startField},!null|after_or_equal:{$startField}|" : '';

        return $rule."date|date_format:\"{$format}\"";
    }

    /**
     * 驗證排序欄位格式，並使用自訂的 SortByRule 規則。
     */
    protected function sortRule(array $allowedColumns = ['id'], bool $required = false): array
    {
        // 返回規則陣列，將自訂規則物件加入其中
        return [
            $this->requiredRule($required),  // 必填檢查
            'string',                        // 確保是字串格式
            new SortByRule($allowedColumns), // 自訂的排序規則物件
        ];
    }

    /**
     * 驗證是否為必填
     */
    private function requiredRule(bool $required = false): string
    {
        return $required ? 'required' : 'nullable';
    }

    /**
     * 依照指定欄位值條件，判斷本欄位必填
     */
    protected function requiredIfRule(string $field, array $values): string
    {
        return 'required_if:'.$field.','.implode(',', $values);
    }
}
