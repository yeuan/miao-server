<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PurgeCdnCacheJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $urls,
    ) {}

    /**
     * 解除任務唯一鎖的秒數
     */
    public function uniqueFor(): int
    {
        return config('custom.settings.queue.unique_lock_time', 300);
    }

    /**
     * 任務的 unique ID (唯一ID)
     *
     * @return string
     */
    public function uniqueId()
    {
        return 'cdn_purge_'.md5(json_encode($this->urls));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->urls)) {
            return;
        }

        // 取得cdn設定
        $driver = config('custom.settings.cdn.default', 'cloudflare');
        $settings = config("custom.settings.cdn.drivers.$driver", []);

        match ($driver) {
            'cloudflare' => $this->purgeCloudflare($this->urls, $settings),
            default => false,
        };
    }

    private function purgeCloudflare(array $urls, array $settings): bool
    {
        $cfZone = $settings['zone_id'] ?? '';
        $cfKey = $settings['api_key'] ?? '';
        $cfEmail = $settings['email'] ?? '';
        $purgeUrl = rtrim($settings['api_base_url'] ?? '', '/').'/'.$cfZone.'/purge_cache';
        $headers = [
            'X-Auth-Email' => $cfEmail,
            'X-Auth-Key' => $cfKey,
            'Content-Type' => 'application/json',
        ];

        try {
            $timeout = config('custom.settings.network.timeout', 10);
            $response = Http::timeout($timeout)->withHeaders($headers)
                ->post($purgeUrl, ['files' => $urls]);

            $result = $response->successful() && ($response->json('success') ?? false) === true;
            if (! $result) {
                Log::channel('purgeCache')->warning('Cloudflare 清除緩存錯誤! / Cloudflare Purge Error!', [
                    'urls' => $urls,
                    'response' => $response->body(),
                ]);

                // throw new \Exception('Cloudflare purge fail'); // 視情況要不要 throw, 拋出job才會重新執行
            }

            return $result;
        } catch (\Throwable $e) {
            Log::channel('purgeCache')->error('Cloudflare 清除緩存錯誤: / Cloudflare Purge Error: '.$e->getMessage());

            return false;
        }
    }
}
