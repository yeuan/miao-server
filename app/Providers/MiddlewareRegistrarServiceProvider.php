<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareRegistrarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        $groups = config('middleware_map', []);

        $global = $groups['global'] ?? [];
        unset($groups['global'], $groups['aliases']);

        foreach ($groups as $group => $middlewares) {
            $router->middlewareGroup($group, array_merge($global, $middlewares));
        }

        // 註冊 alias
        foreach (config('middleware_map.aliases', []) as $alias => $class) {
            $router->aliasMiddleware($alias, $class);
        }
    }
}
