<?php

return [
    // 一定會執行
    'global' => [
        \App\Http\Middleware\ResolveAuthDriver::class,
    ],

    'admin' => [
    ],

    'api' => [
    ],

    // middleware alias（只有在 Route::middleware('xx') 才會執行）
    'aliases' => [
        // 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'jwt.refresh' => \App\Http\Middleware\JwtRefresh::class,
        'jwt.auth' => \App\Http\Middleware\JwtAuthenticate::class,
        'permission' => \App\Http\Middleware\Permission::class,
        'module.enabled' => \App\Http\Middleware\EnsureModuleEnabled::class,
        'ensure.backstage' => \App\Http\Middleware\EnsureBackstageType::class,
    ],
];
