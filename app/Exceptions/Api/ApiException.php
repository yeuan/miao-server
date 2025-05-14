<?php

namespace App\Exceptions\Api;

use App\Enums\HttpStatus;
use Exception;

class ApiException extends Exception
{
    /**
     * @param  string  $codeKey  Enum 的 name（如 AUTH_CAPTCHA_ERROR）
     * @param  int|null  $httpStatus  可選 HTTP 狀態碼，預設為 422
     */
    public function __construct(
        protected string $codeKey,
        protected int $httpStatus = HttpStatus::PARAMS_ERROR->value
    ) {
        parent::__construct($codeKey, $httpStatus);
    }

    public function getCodeKey(): string
    {
        return $this->codeKey;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }
}
