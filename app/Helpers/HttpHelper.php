<?php

if (! function_exists('getDomain')) {

    /**
     * 取得當前網域
     */
    function getDomain(): string
    {
        $host = parse_url(request()->root(), PHP_URL_HOST);

        return preg_replace(
            "/^([a-zA-Z0-9].*\.)?([a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[a-zA-Z.]{2,})$/",
            '$2',
            $host
        ) ?? '';
    }
}

if (! function_exists('getSubDomain')) {

    /**
     * 取得當前子網域
     */
    function getSubDomain(): string
    {
        $host = request()->server('HTTP_HOST', '');
        $domain = getDomain();

        // 移除主網域部分，並去除右邊的 "."
        return rtrim(Str::remove($domain, $host), '.');
    }
}

if (! function_exists('getRealIp')) {

    /**
     * 取得路由器底層的真實 IP
     */
    function getRealIp(): string
    {
        // 常見的 IP 標頭，依優先順序檢查
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',  // 一般代理伺服器
            'HTTP_X_REAL_IP',        // Nginx 或其他代理伺服器
            'HTTP_CLIENT_IP',        // 部分代理伺服器
        ];

        foreach ($headers as $header) {
            if (! empty($_SERVER[$header])) {
                // 如果有多個 IP，取第一個（用戶的真實 IP）
                $ips = explode(',', $_SERVER[$header]);

                return trim($ips[0]);
            }
        }

        // 如果沒有代理標頭，回傳 REMOTE_ADDR
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

if (! function_exists('getRoutePrefix')) {

    /**
     * 取得當前路由的前綴或子網域對應的前綴
     */
    function getRoutePrefix(): string
    {
        // 從設定檔取得子網域與前綴的對應關係
        $subdomainMap = config('custom.routes.subdomain', []);
        $prefixMap = config('custom.routes.provider', []);

        // 取得當前的子網域與路由前綴
        $subDomain = getSubDomain();
        $routePrefix = Route::current()?->getPrefix() ?? '';

        // 使用 match 判斷子網域或路由前綴是否存在於對應的陣列中
        return match (true) {
            // 子網域匹配並且在 $subdomainMap 中存在
            ! empty($subDomain) && in_array($subDomain, $subdomainMap) => $subDomain,

            // 路由前綴匹配並且在 $prefixMap 中存在
            ! empty($routePrefix) && in_array(explode('/', $routePrefix)[0], $prefixMap, true) => explode('/', $routePrefix)[0],

            default => '',
        };
    }
}

if (! function_exists('filterRequest')) {

    /**
     * 過濾請求的全局參數
     */
    function filterRequest(array $request): array
    {
        return collect($request)
            ->reject(function ($value, $key) {
                // 過濾掉不需要的參數
                return strpos($key, config('custom.settings.filter.out_parameters')) !== false;
            })
            ->map(function ($value) {
                // 將 null 值轉換為空字串
                return $value === null ? '' : $value;
            })
            ->toArray();
    }
}

if (! function_exists('requestOutParam')) {

    /**
     * 取得帶有 out_parameters 前綴的請求參數
     */
    function requestOutParam(string $key, mixed $default = ''): mixed
    {
        $prefix = config('custom.settings.filter.out_parameters');

        return request()->get($prefix.$key, $default);
    }
}

if (! function_exists('requestOutParamPrefix')) {

    /**
     * 取得 out_parameters 前綴加上 key 的字串
     */
    function requestOutParamPrefix(string $key): string
    {
        $prefix = config('custom.settings.filter.out_parameters');

        return $prefix.$key;
    }
}
