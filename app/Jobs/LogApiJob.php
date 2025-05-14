<?php

namespace App\Jobs;

use App\Repositories\Log\LogAdminActionRepository;
use App\Repositories\Log\LogAdminApiRepository;
use App\Repositories\Log\LogAdminLoginRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class LogApiJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Collection $logs
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $db = $this->getLog('db', 'api');

        match ($db) {
            'admin_action' => $this->adminActionLog(),
            'admin_api' => $this->adminApiLog(),
            'admin_login' => $this->adminLoginLog(),
            default => '',
        };
    }

    private function getLog(string $key, $default = '')
    {
        return $this->logs->get($key, $default) ?? '';
    }

    private function adminActionLog()
    {
        app(LogAdminActionRepository::class)->create([
            'backstage' => $this->getLog('backstage'),
            'admin_id' => $this->getLog('admin_id', 0),
            'route' => $this->getLog('route'),
            'info' => $this->getLog('info'),
            'sql' => $this->getLog('sql'),
            'ip' => $this->getLog('ip'),
            'status' => $this->getLog('status', 0),
        ]);
    }

    private function adminApiLog()
    {
        app(LogAdminApiRepository::class)->create([
            'url' => $this->getLog('url'),
            'route' => $this->getLog('route'),
            'params' => $this->getLog('params'),
            'headers' => $this->getLog('headers'),
            'response' => $this->getLog('response'),
            'ip' => $this->getLog('ip'),
            'exception' => $this->getLog('exception'),
            'exec_time' => $this->getLog('exec_time'),
        ]);
    }

    private function adminLoginLog()
    {
        app(LogAdminLoginRepository::class)->create([
            'backstage' => $this->getLog('backstage'),
            'admin_id' => $this->getLog('admin_id', 0),
            'ip' => $this->getLog('ip'),
            'status' => $this->getLog('status', 0),
            'message' => $this->getLog('message', ''),
            'created_by' => $this->getLog('created_by', ''),
        ]);
    }
}
