<?php

namespace App\Services\Auth;

use App\Enums\ApiCode;
use App\Enums\Backstage;
use App\Enums\Manager\AdminStatus;
use App\Exceptions\Api\ApiException;
use App\Repositories\Manager\AdminRepository;
use App\Support\RateLimitHandler;
use App\Support\TokenManager;
use App\Traits\VerifyTrait;
use Illuminate\Support\Str;

class AuthService
{
    use VerifyTrait;

    protected const LOGIN_CONTEXTS = [
        'admin' => [
            'backstage' => Backstage::ADMIN,
            'rateLimitConfig' => 'custom.settings.rate_limit.admin',
            'disableStatus' => AdminStatus::DISABLE,
            'useValidate' => 'custom.settings.verification.use_admin_login_validate',
            'repository' => 'adminRepository',
        ],
        // 'agent' => [...]
    ];

    public function __construct(
        protected AdminRepository $adminRepository,
    ) {}

    public function login(array $input, string $scene): object
    {
        $sceneConfig = $this->getSceneConfig($scene);

        $backstage = $sceneConfig['backstage']->value;
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $ip = getRealIp();
        $rateLimit = config($sceneConfig['rateLimitConfig'], []);

        // 設定 IP 限流
        $ipLimiter = RateLimitHandler::make("{$scene}:login_ip", $rateLimit['ip'] ?? [], 10, 3600);
        // 檢查 IP 是否被封鎖
        if ($ipLimiter->isBlocked($ip)) {
            throw new ApiException(ApiCode::AUTH_IP_LIMIT->name);
        }
        // 增加 IP 嘗試次數
        $ipLimiter->hit($ip);

        // 驗證碼檢查
        $this->checkVerification($input, $sceneConfig['useValidate']);

        // 設定帳號登入限流
        $userLimiter = RateLimitHandler::make("{$scene}:login_user", $rateLimit['account'] ?? [], 5, 600);
        // 檢查登入是否超過限制
        if ($userLimiter->isBlocked($username)) {
            // 停用帳號
            $this->disableAccountIfNeeded($username, $sceneConfig['disableStatus'], $sceneConfig['repository']);
            throw new ApiException(ApiCode::AUTH_LOGIN_TIMES->name);
        }

        // 判斷登入模式
        $isMultiLogin = config("custom.settings.login_mode.{$scene}") === 'multi';
        $deviceId = $isMultiLogin ? (string) Str::uuid() : null;

        // 嘗試登入
        $token = $this->attemptLogin($username, $password, $backstage, $deviceId);
        if (! $token) {
            $userLimiter->hit($username);
            throw new ApiException(ApiCode::AUTH_PARAMS_ERROR->name);
        }

        $user = auth()->user();

        // 檢查帳號狀態
        if ($user->status === $sceneConfig['disableStatus']->value) {
            throw new ApiException(ApiCode::AUTH_STATUS_DISABLE->name);
        }

        // 檢查後台類型
        if ($user->backstage !== $backstage) {
            throw new ApiException(ApiCode::AUTH_BACKSTAGE_ERROR->name);
        }

        // 記錄Token
        $ttl = getJwtTtlInSeconds();
        TokenManager::make($scene, $deviceId)->setPrimaryToken($user->id, $token, $ttl);

        // 成功後清除限流
        $this->clearRateLimits($ipLimiter, $userLimiter, $ip, $username);
        // 加上額外需要的資料
        $user->setAttributes([
            'token' => $token,
            'backstage' => $backstage,
        ]);

        return $user;
    }

    public function logout(): void
    {
        if (! auth()->check()) {
            return;
        }

        $userId = auth()->id();
        $payload = auth()->payload();

        $backstageValue = $payload->get('backstage') ?? null;
        $deviceId = $payload->get('device_id') ?? null;
        $scene = getSceneFromBackstage($backstageValue);

        // 先讓 token 本身失效（JWT blacklist）
        auth()->invalidate();
        auth()->logout();

        // 依設定檔決定是否要登出所有裝置
        $allDevices = config("custom.settings.logout_mode.{$scene}", false);

        TokenManager::make($scene, $deviceId)->remove($userId, $allDevices);
    }

    /**
     * 驗證碼檢查（如果啟用）
     */
    protected function checkVerification(array $input, bool $useValidate): void
    {
        // 驗證碼檢查（如果啟用）
        if ($useValidate) {
            $field = getVerificationField();
            if ($field && ! $this->runVerification($input[$field] ?? '')) {
                throw new ApiException(ApiCode::VALIDATION_CAPTCHA_ERROR->name);
            }
        }
    }

    /**
     * 登入取token
     */
    protected function attemptLogin(string $username, string $password, string $backstage, ?string $deviceId): ?string
    {
        // 排除 null 的 deviceId
        $payload = array_filter([
            'username' => $username,
            'backstage' => $backstage,
            'deviceId' => $deviceId,
        ]);

        return auth()->claims($payload)->attempt([
            'username' => $username,
            'password' => $password,
        ]);
    }

    /**
     * 取得場景設定（admin、agent...）
     */
    private function getSceneConfig(string $scene): array
    {
        return self::LOGIN_CONTEXTS[$scene] ?? throw new ApiException(ApiCode::AUTH_SCENE_ERROR);
    }

    /**
     * 停用帳號
     */
    private function disableAccountIfNeeded(string $username, object $disableStatus, string $repositoryKey): void
    {
        if (! property_exists($this, $repositoryKey)) {
            throw new ApiException(ApiCode::AUTH_SCENE_ERROR);
        }

        $repository = $this->{$repositoryKey};
        $user = $repository->search(['username' => $username])->resultOne();

        if ($user && $user->status !== $disableStatus->value) {
            $user->status = $disableStatus->value;
            $user->save();
        }
    }

    /**
     * 清除cache
     */
    private function clearRateLimits(RateLimitHandler $ipRateLimiter, RateLimitHandler $userRateLimiter, string $ip, string $username): void
    {
        $ipRateLimiter->clear($ip);
        $userRateLimiter->clear($username);
    }
}
