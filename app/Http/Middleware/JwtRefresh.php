<?php

namespace App\Http\Middleware;

use App\Enums\ApiCode;
use App\Exceptions\Api\AuthException;
use App\Support\TokenManager;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
use PHPOpenSourceSaver\JWTAuth\Token;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwtRefresh extends BaseMiddleware
{
    /**
     * 處理進來的請求，檢查是否需要刷新 token
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 檢查是否有 token，沒有 token 直接放行給 JwtAuthenticate 處理
        if (! $this->auth->parser()->hasToken()) {
            return $next($request);
        }

        try {
            // 嘗試從 payload 中取得資訊（此處會拋出 TokenExpiredException）
            $this->auth->parseToken()->getPayload();
        } catch (TokenExpiredException $exception) {
            try {
                $rawToken = JWTAuth::getToken()->get();
                // 直接從 JWT Provider 解 payload，不驗證過期
                $payload = $this->auth->getJWTProvider()->decode($rawToken);

                $userId = $payload['sub'];
                $username = $payload['username'];
                $deviceId = $payload['deviceId'] ?? null;
                $backstageValue = $payload['backstage'] ?? null;

                // 轉換成 enum
                $scene = getSceneFromBackstage($backstageValue);
                if (! $scene) {
                    throw new UnauthorizedHttpException('jwt-auth', ApiCode::AUTH_JWT_ERROR->name);
                }

                $token = auth()->claims([
                    'deviceId' => $deviceId,
                    'username' => $username,
                    'backstage' => $backstageValue,
                ])->refresh();

                $ttl = getJwtTtlInSeconds();
                $graceTtl = config('jwt.blacklist_grace_period', 3);
                // 使用封裝好的方法：寫入新/舊 token
                TokenManager::make($scene, $deviceId)->refreshToken($userId, $token, $rawToken, $ttl, $graceTtl);

                return $this->setAuthenticationHeader($next($request), $token);
            } catch (JWTException $exception) {
                throw new AuthException(ApiCode::AUTH_JWT_EXPIRED->name);
            }
        } catch (JWTException $exception) {
            // 其他錯誤，例如格式錯誤、黑名單等，讓後續 JwtAuthenticate 處理
            return $next($request);
        }

        return $next($request);
    }
}
