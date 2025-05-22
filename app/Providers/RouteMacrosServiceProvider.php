<?php

namespace App\Providers;

use App\Repositories\System\ModuleRepository;
use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteMacrosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // --------- Macro 定義區 ---------

        /**
         * 通用 controller 群組註冊方法
         *
         * 用法：
         * Route::controllerGroup('admin_role', 'Admin', 'Manager\AdminRoleController', function () { ... });
         */
        Route::macro('controllerGroup', function (
            string $prefix,
            string $namespace,
            string $controller,
            Closure $callback
        ) {
            $fullController = "App\\Http\\Controllers\\{$namespace}\\{$controller}";

            return Route::prefix($prefix)
                ->controller($fullController)
                ->group($callback);
        });

        /**
         * 根據 config/custom.routes.provider 自動註冊 xxxGroup() macros
         *
         * 例如：
         * Route::adminGroup('admin_role', 'Manager\AdminRoleController', fn () => {...})
         * Route::apiGroup('user', 'UserController', fn () => {...})
         */
        $providers = config('custom.routes.provider', []); // e.g. ['admin_prefix' => 'admin', ...]

        foreach ($providers as $prefix) {
            // 取得像 admin、api，轉為 Admin、Api 作為命名空間
            $macroName = "{$prefix}Group";
            // 產出 adminGroup、apiGroup
            $namespace = ucfirst($prefix);

            Route::macro($macroName, function (
                string $routePrefix,
                string $controller,
                Closure $callback
            ) use ($namespace) {
                return Route::controllerGroup($routePrefix, $namespace, $controller, $callback);
            });
        }

        /**
         * 標準 CRUD 路由（index, show, store, update, destroy）
         * 用法：
         *   Route::registerCrud('admin'); // 全部
         *   Route::registerCrud('admin', ['index', 'update']); // 只要部分
         */
        Route::macro('registerCrud', function (string $namePrefix, ?array $only = null) {
            $map = [
                'index' => ['get',    '/',      'index'],
                'show' => ['get',    '/{id}',  'show'],
                'store' => ['post',   '/',      'store'],
                'update' => ['put',    '/{id}',  'update'],
                'destroy' => ['delete', '/{id}',  'destroy'],
            ];

            $use = $only ? array_intersect_key($map, array_flip($only)) : $map;

            foreach ($use as $action => [$method, $uri, $handler]) {
                $route = Route::$method($uri, $handler)->name("$namePrefix.$action");
                if (str_contains($uri, '{id}')) {
                    $route->where('id', '[0-9]+');
                }
            }
        });

        // --------- modules 動態 route 註冊區 ---------
        Route::macro('adminModules', function () {
            // 取得所有啟用中的模組
            foreach (app(ModuleRepository::class)->getActiveModules() as $module) {
                // 若 module 沒自訂 namespace/controller 就 fallback 為 Content
                $namespace = $module['namespace'] ?? 'Content';
                $controller = ucfirst($module['code']).'Controller';
                $controllerPath = "{$namespace}\\{$controller}";

                Route::adminGroup(
                    $module['code'],
                    $controllerPath,
                    function () use ($module) {
                        Route::registerCrud($module['code']);
                    }
                );
            }
        });

        // 避免 migrate/queue 等 console 執行時載入 route
        if ($this->app->runningInConsole()) {
            return;
        }
    }
}
