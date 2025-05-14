<?php

if (! function_exists('getCurl')) {

    /**
     * 發送 CURL 請求（可支援 GET / POST、Cookie、Referer、Headers）
     *
     * @param  string  $url  請求網址
     * @param  string|array|bool  $post  POST 參數，false 則為 GET 請求
     * @param  string  $cookie  Cookie 字串
     * @param  string  $referer  來源網址
     * @param  array  $headers  自訂 HTTP 標頭
     * @param  int  $timeout  逾時秒數
     * @return string|false 回應內容或 false 表失敗
     */
    function getCurl($url, $post = true, $cookie = '', $referer = '', $headers = [], $timeout = 10)
    {
        if (! function_exists('curl_init')) {
            return @file_get_contents($url);
        }

        $ch = curl_init($url);

        // 強制使用 IPv4、HTTPS 避免驗證、返回內容
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,                                                                                               // 獲取的信息以文件流的形式返回
            CURLOPT_FOLLOWLOCATION => true,                                                                                               // 使用自動跳轉
            CURLOPT_SSL_VERIFYPEER => false,                                                                                              // 跳過證書檢查
            CURLOPT_SSL_VERIFYHOST => 2,                                                                                                  // 從證書中檢查SSL加密算法是否存在
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,                                                                                  // 強制使用IPV4協議解析域名
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; 4399Box.560; .NET4.0C; .NET4.0E)', // 模擬用戶使用的瀏覽器
            CURLOPT_CONNECTTIMEOUT => $timeout,                                                                                           // 鏈接服務器超時的時間
        ]);

        if (! empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect:']); // 頭部送出 Expect: 可減少詢問過程
        }

        if ($referer !== '') {
            curl_setopt($ch, CURLOPT_REFERER, $referer); // 構造來路
        }

        if ($cookie !== '') {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie); // 發送Cookie
        }

        if ($post !== false) {
            curl_setopt($ch, CURLOPT_POST, true);        // 發送一個常規的Post請求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Post提交的數據包
        }

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($errno === 0 && $httpCode === 200) ? $response : false;
    }
}
