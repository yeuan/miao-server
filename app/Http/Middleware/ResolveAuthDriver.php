<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveAuthDriver
{
    /**
     * 處理進來的請求，根據路由前綴動態設置認證驅動
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 根據路由前綴設置認證驅動，默認為 'web'
        $prefix = getRoutePrefix() ?? 'web';
        // 設定認證驅動
        auth()->setDefaultDriver("{$prefix}Auth");

        return $next($request);
    }
}
