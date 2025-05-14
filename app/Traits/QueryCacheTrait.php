<?php

namespace App\Traits;

use App\Support\RedisOperator;

trait QueryCacheTrait
{
    protected ?RedisOperator $redisOperator = null;

    protected function redis(): RedisOperator
    {
        return $this->redisOperator ??= new RedisOperator($this->redisConnectionName);
    }

    /**
     * 取得指定快取鍵的快取資料
     */
    protected function getCache(string $key): mixed
    {
        return $this->redis()->get($key);
    }

    /**
     * 儲存快取資料並追蹤 key
     */
    protected function storeCacheWithTrack(string $group, string $key, mixed $value): void
    {
        if (! $this->redisCache) {
            return;
        }

        $ttl = (int) config('custom.setting.cache.ttl_time');
        $this->redis()->set($key, json_encode($value, 320), $ttl);
        $this->redis()->sadd($this->getTrackedCacheSetKey($group), $key);
    }

    /**
     * 刪除單筆快取資料（通常用於某筆資料更新時，如 row_array 對應的）
     */
    protected function flushCacheById(string $group, int|string $id): void
    {
        if (! $this->redisCache) {
            return;
        }

        $key = $this->entity->getTable().':id:'.$id;

        $this->redis()->del($key);
        $this->redis()->srem($this->getTrackedCacheSetKey($group), $key);
    }

    /**
     * 一鍵刪除該表所有快取資料（查詢、列表等）
     */
    protected function flushTableCache(string $group): void
    {
        if (! $this->redisCache) {
            return;
        }

        $setKey = $this->getTrackedCacheSetKey($group);
        $keys = $this->redis()->smembers($setKey);

        if (! empty($keys)) {
            $this->redis()->del(...$keys);
        }

        $this->redis()->del($setKey);
    }

    /**JSON_UNESCAPED_UNICODE
     * 取得該表對應快取 key 集合名稱（追蹤用）
     */
    private function getTrackedCacheSetKey(string $group): string
    {
        return "cache_keys:$group";
    }
}
