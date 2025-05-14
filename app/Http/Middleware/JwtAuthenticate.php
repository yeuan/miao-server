<?php

namespace App\Http\Middleware;

use App\Enums\ApiCode;
use App\Enums\Backstage;
use App\Exceptions\Api\AuthException;
use App\Support\TokenManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtAuthenticate extends BaseMiddleware
{
    /**
     * 驗證token 以及 backstage 是否符合當前 guard
     */
    public function handle(Request $request, Closure $next, string $guard)
    {
        // 先指定 guard，確保 provider + 驗證是正確的
        Auth::shouldUse("{$guard}Auth");

        // 驗證 token 是否有效（無 token、過期、黑名單、provider 不符都會是 false）
        if (! Auth::check()) {
            throw new AuthException(ApiCode::AUTH_NOT_LOGIN->name);
        }

        // 取得 claim
        $payload = Auth::payload();
        $backstageValue = $payload->get('backstage') ?? null;
        $deviceId = $payload->get('deviceId') ?? null;
        $token = auth()->getToken()?->get();
        $userId = auth()->id();

        // 轉換成 enum
        $scene = getSceneFromBackstage($backstageValue);

        if ($scene !== strtolower($guard)) {
            throw new AuthException(ApiCode::AUTH_JWT_ERROR->name);
        }

        // === 登入檢查 ===
        // 使用validate 方法（會比對主 token + grace token）
        if (! TokenManager::make($scene, $deviceId)->validate($userId, $token)) {
            throw new AuthException(ApiCode::AUTH_JWT_INVALID->name);
        }

        // 設定 userResolver（避免後續重覆查詢資料庫）
        $user = Auth::user();
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
