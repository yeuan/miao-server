<?php

namespace App\Exceptions\Handlers;

use App\Enums\ApiCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ErrorMapper
{
    public static function render(\Throwable $e, ?Request $request = null)
    {
        dd($e);

        return match (true) {
            $e instanceof NotFoundHttpException,
            $e instanceof MethodNotAllowedHttpException,
            $e instanceof ModelNotFoundException => respondError(ApiCode::NOT_FOUND->name, $e),

            $e instanceof ServiceUnavailableHttpException => respondError(ApiCode::SERVICE_UNAVAILABLE->name, $e),
            $e instanceof UnauthorizedHttpException => respondError(ApiCode::AUTH_TOKEN_ERROR->name, $e),

            $e instanceof JWTException,
            $e instanceof TokenExpiredException => respondError(ApiCode::AUTH_JWT_EXPIRED->name, $e),

            default => respondError('SYSTEM_FAILED', $e),
        };
    }
}
