<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait TreeTrait
{
    /**
     * 取得父層 path 字串
     */
    public function buildPath(int $pid, Model $model): string
    {
        if (empty($pid)) {
            // 第一層
            return '';
        }

        // 使用 pluck 提取父節點的 path，減少查詢負擔
        $parentPath = $model->where('id', $pid)->value('path');

        // 設定 path，並確保格式正確
        return $parentPath === null || $parentPath === ''
            ? (string) $pid
            : "{$parentPath}-{$pid}";
    }
}
