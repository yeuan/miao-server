<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatTrait
{
    /**
     * 格式化日期時間
     */
    protected function formatDateTime(mixed $value, ?string $format = null): string
    {
        $format ??= config('custom.default.datetime', 'Y-m-d H:i:s');

        if (empty($value)) {
            return '';
        }

        return match (true) {
            $value instanceof \DateTimeInterface => $value->format($format),
            is_numeric($value) => date($format, (int) $value),
            default => Carbon::parse($value)->format($format),
        };
    }
}
