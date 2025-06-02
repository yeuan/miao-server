<?php

namespace App\Support;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisOperator
{
    protected string $connectionName;

    protected Connection $redis;

    public function __construct(string $connectionName = 'default')
    {
        $this->connectionName = $connectionName;
        $this->redis = Redis::connection($connectionName);

        try {
            $this->redis->ping();
        } catch (\Throwable $e) {
            $this->redis = Redis::connection($connectionName);
            Log::channel('redis')->info(
                now()->toDateTimeString().": 斷線重連 / Redis reconnect triggered ({$connectionName})!"
            );
        }
    }

    /**
     * 取得完整 Redis key（加上 prefix）
     */
    protected function key(string $key): string
    {
        return config('cache.prefix').$key;
    }

    /**
     * 取得值
     */
    public function get(string $key): mixed
    {
        return $this->redis->get($this->key($key));
    }

    /**
     * 設定值
     */
    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        $ttl
            ? $this->redis->setex($this->key($key), $ttl, $value)
            : $this->redis->set($this->key($key), $value);
    }

    /**
     * 刪除 key
     */
    public function del(string ...$keys): void
    {
        $this->redis->del(...array_map(fn ($key) => $this->key($key), $keys));
    }

    /**
     * 取得集合所有成員
     */
    public function smembers(string $key): array
    {
        return $this->redis->smembers($this->key($key));
    }

    /**
     * 新增集合成員
     */
    public function sadd(string $key, string $value): void
    {
        $this->redis->sadd($this->key($key), $value);
    }

    /**
     * 移除集合成員
     */
    public function srem(string $key, string $value): void
    {
        $this->redis->srem($this->key($key), $value);
    }

    /**
     * 檢查是否存在
     */
    public function exists(string $key): bool
    {
        return $this->redis->exists($this->key($key)) > 0;
    }
}
