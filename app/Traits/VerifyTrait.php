<?php

namespace App\Traits;

trait VerifyTrait
{
    public function runVerification(string $token): bool
    {
        $method = config('custom.setting.verification.method', null);

        return match ($method) {
            'turnstile' => $this->verifyWithTurnstile($token),
            default => false,
        };
    }

    private function verifyWithTurnstile(string $token): bool
    {
        if (app()->environment('develop')) {
            return true;
        }

        $result = getCurl(config('custom.setting.verification.turnstile.url'), [
            'secret' => config('custom.setting.verification.turnstile.key'),
            'response' => $token,
        ]);

        $response = json_decode($result, true);

        return $response['success'] ?? false;
    }
}
