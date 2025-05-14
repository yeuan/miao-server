<?php

namespace App\Support;

use Illuminate\Cache\RateLimiter;

class RateLimitHandler
{
    private string $prefix;

    private int $maxAttempts;

    private int $decaySeconds;

    private RateLimiter $rateLimiter;

    /**
     * 建構子：初始化限流參數
     *
     * @param  string  $prefix  限流鍵前綴，可用於區分不同功能的限制（例如：login、api）
     * @param  int  $maxAttempts  最大嘗試次數
     * @param  int  $decaySeconds  過期秒數（限制持續時間）
     */
    public function __construct(string $prefix = 'api', int $maxAttempts = 5, int $decaySeconds = 120)
    {
        $this->prefix = $prefix;
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
        $this->rateLimiter = resolve(RateLimiter::class);
    }

    /**
     * 建立限流器（工廠方法，統一建立流程）
     */
    public static function make(string $prefix, array $config, int $defaultAttempts, int $defaultDecay): self
    {
        return new self(
            $prefix,
            $config['max_attempts'] ?? $defaultAttempts,
            $config['decay_seconds'] ?? $defaultDecay
        );
    }

    /**
     * 檢查指定實體是否已被封鎖（超過最大次數）
     *
     * @param  string  $entityKey  識別實體的唯一鍵（例如帳號、IP）
     */
    public function isBlocked(string $entityKey): bool
    {
        return $this->rateLimiter->tooManyAttempts($this->getKey($entityKey), $this->maxAttempts);
    }

    /**
     * 記錄一次操作嘗試（累加次數）
     *
     * @param  string  $entityKey  識別實體的唯一鍵
     */
    public function hit(string $entityKey): void
    {
        $this->rateLimiter->hit($this->getKey($entityKey), $this->decaySeconds);
    }

    /**
     * 清除限制記錄
     *
     * @param  string  $entityKey  識別實體的唯一鍵
     */
    public function clear(string $entityKey): void
    {
        $this->rateLimiter->clear($this->getKey($entityKey));
    }

    /**
     * 取得剩餘可嘗試次數
     *
     * @param  string  $entityKey  識別實體的唯一鍵
     */
    public function remaining(string $entityKey): int
    {
        return $this->rateLimiter->remaining($this->getKey($entityKey), $this->maxAttempts);
    }

    /**
     * 組合限流鍵名稱（加上前綴）
     *
     * @param  string  $entityKey  識別實體的唯一鍵
     */
    private function getKey(string $entityKey): string
    {
        return "{$this->prefix}:{$entityKey}";
    }
}
