<?php

namespace App\Jobs;

use App\Repositories\Log\LogAdminLoginRepository;
use App\Repositories\Log\LogUserLoginRepository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GrabIpInfoJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $type,
        protected int $id,
        protected ?int $uniqueFor = null // 預設值為 null
    ) {
        $this->uniqueFor = config('custom.settings.queue.unique_lock_time', 300);
    }

    /**
     * 任務的 unique ID (唯一ID)
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->type.'-'.$this->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 決定使用的 Repository
        $repo = $this->getRepository();

        // 取得資料
        $data = $repo->row($this->id);
        if (! $data) {
            return;  // 不需要返回 false，Laravel 預設會處理錯誤
        }

        // 呼叫 IP 資訊 API
        $url = config('custom.settings.network.ipdata_url').$data['ip'].'?lang=zh-CN';
        $timeout = config('custom.settings.network.timeout', 10);
        try {
            $response = Http::timeout($timeout)->get($url);
            $result = $response->successful();

            if (! $result) {
                Log::channel('grabIpInfo')->warning('取得ip資訊錯誤! / Error retrieving IP information!', [
                    'url' => $url,
                    'response' => $response->body(),
                ]);

                // throw new \Exception('Fail retrieving IP information!'); // 視情況要不要 throw, 拋出job才會重新執行
            }

            $ipInfo = $result ? $response->json() : [];
        } catch (\Throwable $e) {
            Log::channel('grabIpInfo')->error('取得ip資訊錯誤: / Error retrieving IP information: '.$e->getMessage());
            $ipInfo = [];
        }

        $data['ip_info'] = json_decode($ipInfo ?? '[]', true);
        // 儲存資料
        $data->save();
    }

    /**
     * 根據 type 決定使用的 repository
     */
    private function getRepository()
    {
        return match ($this->type) {
            'admin_login' => app(LogAdminLoginRepository::class),
            'user_login' => app(LogUserLoginRepository::class),
            default => throw new \InvalidArgumentException("Invalid type: {$this->type}")
        };
    }
}
