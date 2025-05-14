<?php

namespace App\Traits;

trait RepositoryTrait
{
    protected function applyTimeRangeFilter(array &$conditions, array $search, string $field): void
    {
        if (! empty($search["{$field}_1"])) {
            $conditions[] = [$field, '>=', strlen($search["{$field}_1"]) <= 10
                ? $search["{$field}_1"].' 00:00:00'
                : $search["{$field}_1"]];
        }
        if (! empty($search["{$field}_2"])) {
            $conditions[] = [$field, '<=', strlen($search["{$field}_2"]) <= 10
                ? $search["{$field}_2"].' 23:59:59'
                : $search["{$field}_2"]];
        }
    }
}
