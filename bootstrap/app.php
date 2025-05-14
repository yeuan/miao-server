<?php

use App\Exceptions\Handlers\ErrorMapper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            // 載入並拆解設定
            $routesConfig = config('custom.routes');
            $useSubdomain = $routesConfig['use_subdomain'] ?? false;

            [
                'api_domain' => $apiDomain,
                'admin_domain' => $adminDomain,
                'api_prefix' => $apiPrefix,
                'admin_prefix' => $adminPrefix,
            ] = array_merge(
                $routesConfig['subdomain'] ?? [],
                $routesConfig['provider'] ?? []
            );

            // 定義共用路由註冊邏輯
            $registerRoutes = fn (string $prefix, string $domain, string $path) => [
                // 註冊基本路由
                Route::middleware($prefix)
                    ->prefix($prefix)
                    ->group(base_path($path)),

                // 如果使用子域名，則註冊子域名路由
                $useSubdomain ? Route::middleware($prefix)
                    ->domain($domain.'.'.getDomain())
                    ->group(base_path($path)) : null,
            ];

            // 註冊 後台 路由
            $registerRoutes($adminPrefix, $adminDomain, 'routes/admin.php');

            // 註冊 API 路由
            $registerRoutes($apiPrefix, $apiDomain, 'routes/api.php');
        },
    )
    // ->withMiddleware(function (Middleware $middleware) {})
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(fn (Throwable $e, Request $request) => ErrorMapper::render($e, $request));
    })->create()

    ->useEnvironmentPath(
        // 設定env檔案路徑
        __DIR__.'/../env'
    )
    ->loadEnvironmentFrom(
        // 載入env檔案
        '.env'
    );
