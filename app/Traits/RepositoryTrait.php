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

    protected function applyPublishAtRange(array &$conditions, array $search): void
    {
        if (! empty($search['publish_at_1'])) {
            $time = strlen($search['publish_at_1']) <= 10
                ? $search['publish_at_1'].' 00:00:00'
                : $search['publish_at_1'];

            $conditions[] = ['start_time', '>=', strtotime($time)];
        }

        if (! empty($search['publish_at_2'])) {
            $time = strlen($search['publish_at_2']) <= 10
                ? $search['publish_at_2'].' 23:59:59'
                : $search['publish_at_2'];

            $conditions[] = ['end_time', '<=', strtotime($time)];
        }
    }

    protected function applyEnableTimeFilter(array &$conditions): void
    {
        $now = time();
        $conditions[] = ['start_time', '<=', $now];
        $conditions[] = ['end_time', '>=', $now];
    }
}
