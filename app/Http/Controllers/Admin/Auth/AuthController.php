<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Resources\Manager\ProfileResource;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    const SCENE = 'admin';

    public function __construct(
        private AuthService $authService,
    ) {}

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated(), self::SCENE);

            return respondSuccess(ProfileResource::make($result));
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }

    public function logout()
    {
        try {
            $this->authService->logout(self::SCENE);

            return respondSuccess();
        } catch (\Throwable $e) {
            return respondError('SYSTEM_FAILED', $e);
        }
    }
}
