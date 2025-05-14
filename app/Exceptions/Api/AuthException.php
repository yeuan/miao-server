<?php

namespace App\Exceptions\Api;

use App\Enums\HttpStatus;

class AuthException extends ApiException
{
    public function __construct(
        protected string $codeKey,
        protected int $httpStatus = HttpStatus::AUTH_ERROR->value
    ) {
        parent::__construct($codeKey, $httpStatus);
    }
}
