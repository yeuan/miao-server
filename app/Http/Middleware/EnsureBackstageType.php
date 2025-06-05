<?php

namespace App\Http\Middleware;

use App\Enums\ApiCode;
use App\Enums\Backstage;
use App\Exceptions\Api\AuthException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBackstageType
{
    /**
     * 驗證管理帳號對應的後台
     */
    public function handle(Request $request, Closure $next, string $backstageType): Response
    {
        // 全轉成大寫
        $backstageType = strtoupper($backstageType);
        // 取得已經經過驗證的使用者
        $user = $request->user();

        // 嚴謹檢查
        if (! $user) {
            throw new AuthException(ApiCode::AUTH_NOT_LOGIN->name);
        }

        // 比對後台型別
        $expected = Backstage::{$backstageType}->value;
        if ($user->backstage !== $expected) {
            throw new AuthException(ApiCode::AUTH_BACKSTAGE_ERROR->name);
        }

        return $next($request);
    }
}
