<?php

namespace App\Http\Middleware;

use App\Repositories\Manager\AdminNavRepository;
use App\Repositories\Manager\AdminRoleRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class Permission
{
    public function __construct(
        protected AdminNavRepository $adminNavRepository,
        protected AdminRoleRepository $adminRoleRepository
    ) {}

    /**
     * 檢查是否有權限
     */
    public function handle(Request $request, Closure $next): Response
    {
        $prefix = getRoutePrefix();
        $route = optional(\Route::current())->getName() ?? '';
        // 取得 token 內的 backstage
        $backstageValue = Auth::payload()->get('backstage');

        // 取得已經經過驗證的使用者
        $user = $request->user();
        $id = $user->id;
        $roleId = $user->role_id;

        // 設定全局參數
        $request->attributes->set(requestOutParamPrefix('backstage'), $backstageValue);
        $request->attributes->set(requestOutParamPrefix('id'), $id);
        $request->attributes->set(requestOutParamPrefix('role_id'), $roleId);
        $request->attributes->set(requestOutParamPrefix('username'), $user->username);

        // 取得導航列表
        $allNav = $this->adminNavRepository->allNav($backstageValue);

        // 解析出所有路由與允許的路由
        $role = $this->adminRoleRepository->rowArray($roleId);
        $allowNavIds = $role['allow_nav'] ?? [0];

        $allRoutes = $allowedRoutes = $allNavIds = [];

        foreach ($allNav as $nav) {
            $allNavIds[] = $nav['id'];

            if ($nav['route'] !== '') {
                $routes = explode(',', $nav['route']);
                $allRoutes = array_merge($allRoutes, $routes);

                if (in_array($nav['id'], $allowNavIds)) {
                    $allowedRoutes = array_merge($allowedRoutes, $routes);
                }
            }
        }

        // 合併免權限檢查的路由
        $allRoutes = array_merge($allRoutes, config('custom.permission.exempt_routes', []));
        $allowedRoutes = array_merge($allowedRoutes, config('custom.permission.exempt_routes', []));

        // 檢查 route 是否存在於導航中
        if (! in_array($route, $allRoutes)) {
            throw new ServiceUnavailableHttpException('permission', __('message.menu_disable'));
        }

        // 檢查是否有權限訪問（非超管時才檢查）
        if ($roleId != 1 && ! in_array($route, $allowedRoutes)) {
            throw new ServiceUnavailableHttpException('permission', __('message.no_permission_operation'));
        }

        // 設定允許的導航 ID 及權限 (提供 controller 使用)
        $request->attributes->set(requestOutParamPrefix('allow_nav_ids'), $roleId == 1 ? $allNavIds : $allowNavIds);
        $request->attributes->set(requestOutParamPrefix('permission'), $roleId == 1 ? $allRoutes : $allowedRoutes);

        return $next($request);
    }
}
