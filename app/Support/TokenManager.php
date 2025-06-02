<?php

namespace App\Support;

class TokenManager
{
    protected ?RedisOperator $redisOperator = null;

    public function __construct(
        protected string $scene,
        protected ?string $deviceId = null,
        protected string $redisConnectionName = 'token_cache'
    ) {}

    public static function make(string $scene, ?string $deviceId = null): static
    {
        return new static($scene, $deviceId);
    }

    protected function redis(): RedisOperator
    {
        return $this->redisOperator ??= new RedisOperator($this->redisConnectionName);
    }

    /**
     * 組合完整 Redis Key（包含主鍵與類型）
     */
    protected function buildKey(int|string $userId, string $type): string
    {
        $mode = config("custom.settings.login_mode.{$this->scene}", 'single');

        if ($mode === 'multi') {
            if (empty($this->deviceId)) {
                throw new \LogicException('TokenManager: deviceId is required in multi-login mode.');
            }

            return "token:{$this->scene}:{$userId}:{$this->deviceId}:{$type}";
        }

        return "token:{$this->scene}:{$userId}:{$type}";
    }

    /**
     * 取得該表對應快取 key 集合名稱（追蹤用）
     */
    protected function getTrackedCacheSetKey(int|string $userId): string
    {
        return "token_keys:{$this->scene}:{$userId}";
    }

    /**
     * 寫入 token 並追蹤 key
     */
    protected function set(int|string $userId, string $type, string $token, int $ttl): void
    {
        $key = $this->buildKey($userId, $type);
        $this->redis()->set($key, $token, $ttl);
        $this->redis()->sadd($this->getTrackedCacheSetKey($userId), $key);
    }

    /**
     * 取得特定 token
     */
    protected function get(int|string $userId, string $type): ?string
    {
        return $this->redis()->get($this->buildKey($userId, $type));
    }

    /**
     * 刪除所有 token（可選是否全裝置）
     */
    public function remove(int|string $userId, bool $allDevices = false): void
    {
        if ($allDevices) {
            $indexKey = $this->getTrackedCacheSetKey($userId);
            $keys = $this->redis()->smembers($indexKey);

            if (! empty($keys)) {
                $this->redis()->del(...$keys);
            }

            $this->redis()->del($indexKey);
        } else {
            $this->redis()->del(
                $this->buildKey($userId, 'primary'),
                $this->buildKey($userId, 'grace'),
            );
        }
    }

    /**
     * 寫入主要 token
     */
    public function setPrimaryToken(int|string $userId, string $token, int $ttl): void
    {
        $this->set($userId, 'primary', $token, $ttl);
    }

    /**
     * 寫入臨時 grace token
     */
    public function setGraceToken(int|string $userId, string $token, int $ttl = 3): void
    {
        $this->set($userId, 'grace', $token, $ttl);
    }

    /**
     * 刷新 token（寫入主 token 與 grace token）
     */
    public function refreshToken(int|string $userId, string $newToken, string $oldToken, int $ttl, int $graceTtl = 3): void
    {
        $this->setPrimaryToken($userId, $newToken, $ttl);
        $this->setGraceToken($userId, $oldToken, $graceTtl);
    }

    /**
     * 驗證傳入 token 是否與 Redis 中有效 token 相符
     */
    public function validate(int|string $userId, string $token): bool
    {
        return in_array($token, array_filter([
            $this->get($userId, 'primary'),
            $this->get($userId, 'grace'),
        ]), true);
    }
}
