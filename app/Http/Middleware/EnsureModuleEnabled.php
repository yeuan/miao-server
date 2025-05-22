<?php

namespace App\Http\Middleware;

use App\Enums\Status;
use App\Repositories\System\ModuleRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class EnsureModuleEnabled
{
    public function __construct(
        protected ModuleRepository $moduleRepository
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 優先從 route name 取得 module code
        $routeName = $request->route()?->getName();
        $moduleCode = $routeName ? explode('.', $routeName)[0] : null;

        // route name 不存在時，用 segments[1]，並 cross-check 是否有效 module code
        if (! $moduleCode) {
            // 取得路由 prefix 當作 module code
            $segments = $request->segments();
            $validCodes = $this->moduleRepository->getAllModuleCodes(); // ['notice','blog',...]
            $moduleCode = collect($segments)->first(fn ($seg) => in_array($seg, $validCodes, true));
        }

        // 有 moduleCode 再查狀態
        if ($moduleCode) {
            // 取得所有啟用的模組列表
            $codes = $this->moduleRepository->getAllModuleCodes(Status::DISABLE->value);
            // 判斷當前的模組是否停用
            if (in_array($moduleCode, $codes, true)) {
                // 如果模組已經停用，則回傳錯誤
                throw new ServiceUnavailableHttpException('permission', __('message.module_disable'));
            }

        }

        return $next($request);
    }
}
