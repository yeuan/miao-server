<?php

namespace App\Traits;

use App\Enums\ApiCode;
use App\Enums\HttpStatus;
use App\Jobs\LogApiJob;

trait ResourceTrait
{
    protected ?\Throwable $exception = null;

    protected int $statusCode = HttpStatus::SUCCESS->value;

    /**
     * 格式化回應資料
     */
    public function with($request): array
    {
        $isError = $this->exception !== null;
        $resourceKey = is_string($this->resource) ? $this->resource : null;

        $enum = $resourceKey
        ? (HttpStatus::tryFromName($resourceKey) ?? ApiCode::tryFromName($resourceKey))
        : null;

        return [
            'success' => ! $isError,
            'code' => $isError
            ? ($enum?->value ?? 0)
            : ApiCode::SUCCESS->value,
            'message' => $isError
            ? ($enum && method_exists($enum, 'label') ? $enum->label() : 'Unknown error')
            : ApiCode::SUCCESS->label(),
        ];
    }

    /**
     * 自定義回應處理
     */
    public function toResponse($request)
    {
        $response = parent::toResponse($request);
        $response->setStatusCode($this->statusCode);
        $this->saveLog($response);

        return $response;
    }

    /**
     * 設定 HTTP 狀態碼
     */
    public function setStatusCode(int $code): static
    {
        $this->statusCode = $code;

        return $this;
    }

    protected function saveLog($response): void
    {
        $route = optional(\Route::current())->getName() ?? '';
        $isError = $this->exception !== null;

        // 檢查是否需要記錄日誌
        if ($this->shouldLog($route, $isError)) {
            $logData = $this->prepareLogData($response);
            $this->logAction($logData);
        }
    }

    /**
     * 檢查是否需要記錄日誌
     */
    protected function shouldLog(string $route, bool $isError): bool
    {
        $enabled = $isError
        ? config('custom.log.save_error_log', false)
        : config('custom.log.save_success_log', false);

        // 不為過濾不紀錄的api(路由)且開啟紀錄錯誤log功能
        return $enabled && ! in_array($route, config('custom.log.exclude_route', []));
    }

    /**
     * 準備日誌資料
     */
    protected function prepareLogData($response): array
    {
        $routePrefix = getRoutePrefix();

        $db = match (true) {
            $routePrefix === config('custom.routes.subdomain.admin_domain'),
            $routePrefix === config('custom.routes.provider.admin_prefix') => 'admin_api',
            default => 'api',
        };

        return [
            'db' => $db,
            'url' => request()->url(),
            'route' => optional(\Route::current())->getName() ?? '',
            'date' => now(),
            'params' => request()->all(),
            'headers' => request()->header(),
            'response' => $response->getData(),
            'ip' => getRealIp(),
            'exception' => $this->formatException(),
            'exec_time' => bcsub(microtime(true), LARAVEL_START, 5),
        ];
    }

    /**
     * 格式化例外資訊
     */
    private function formatException(): ?array
    {
        if (! $this->exception) {
            return null;
        }

        return [
            'exception' => get_class($this->exception),
            'file' => $this->exception->getFile(),
            'line' => $this->exception->getLine(),
            'message' => $this->exception->getMessage(),
        ];
    }

    private function logAction(array $log): void
    {
        if (config('custom.settings.queue.use_redis')) {
            dispatch(new LogApiJob(collect($log)))->onQueue('logWorker');
        } else {
            (new LogApiJob(collect($log)))->handle();
        }
    }
}
