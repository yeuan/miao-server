<?php

namespace App\Providers;

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
         * 用法：Route::registerCrud('admin')
         */
        Route::macro('registerCrud', function (string $namePrefix) {
            Route::get('/', 'index')->name("{$namePrefix}.index");
            Route::get('/{id}', 'show')->name("{$namePrefix}.show");
            Route::post('/', 'store')->name("{$namePrefix}.store");
            Route::put('/{id}', 'update')->name("{$namePrefix}.update");
            Route::delete('/{id}', 'destroy')->name("{$namePrefix}.destroy");
        });
    }
}
