<?php

namespace App\Http\Resources;

use App\Enums\ApiCode;
use App\Enums\HttpStatus;
use App\Exceptions\Api\ApiException;
use App\Traits\ResourceTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    use ResourceTrait;

    public function __construct(string $codeKey, ?\Throwable $exception = null)
    {
        // 優先處理例外型別，如果例外是model(不存在該資料)未找到，則覆寫錯誤碼為 RESOURCE_NOT_FOUND
        if ($exception instanceof ModelNotFoundException) {
            $this->resource = $codeKey = 'RESOURCE_NOT_FOUND';
        } elseif ($exception instanceof ApiException) {
            // 若為 ApiException，自動覆寫為 Exception 的錯誤代碼
            $this->resource = $codeKey = $exception->getCodeKey();
            $this->setStatusCode($exception->getHttpStatus());
        }

        // 如果值為預設的成功 statusCode（代表非 ApiException）
        if ($this->statusCode === HttpStatus::SUCCESS->value) {
            $this->setStatusCode($this->resolveStatusCode($codeKey));
        }

        parent::__construct($codeKey);
        $this->exception = $exception;
    }

    public function toArray(Request $request): array
    {
        return [];
    }

    public static function respond(string $code = 'SYSTEM_FAILED', ?\Throwable $e = null): self
    {
        return new self($code, $e);
    }

    private function resolveStatusCode(string $resource): int
    {
        return match ($resource) {
            ApiCode::VALIDATION_PARAMS_INVALID->name => HttpStatus::PARAMS_ERROR->value,
            ApiCode::RESOURCE_NOT_FOUND->name => HttpStatus::REQUEST_FAILED->value,
            ApiCode::AUTH_TOKEN_ERROR->name => HttpStatus::AUTH_ERROR->value,
            ApiCode::NOT_FOUND->name => HttpStatus::NOT_FOUND->value,
            ApiCode::SERVICE_UNAVAILABLE->name => HttpStatus::SERVICE_UNAVAILABLE->value,
            default => HttpStatus::SERVER_ERROR->value,
        };
    }
}
