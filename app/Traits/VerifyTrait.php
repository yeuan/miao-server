<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait VerifyTrait
{
    public function runVerification(string $token): bool
    {
        $method = config('custom.settings.verification.method', null);

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

        try {
            $timeout = config('custom.settings.network.timeout', 10);
            $result = Http::timeout($timeout)->asForm()->post(config('custom.settings.verification.turnstile.url'), [
                'secret' => config('custom.settings.verification.turnstile.key'),
                'response' => $token,
            ]);
            $response = $result->json();

            return $response['success'] ?? false;
        } catch (\Throwable $e) {
            Log::channel('verification')->error('Turnstile 驗證服務異常 / Turnstile verification service error: '.$e->getMessage());

            return false;
        }
    }
}
