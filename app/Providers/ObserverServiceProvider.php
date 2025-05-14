<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 從配置文件獲取模型與 Observer 的映射
        $observers = config('observers');

        // 註冊每個模型與對應的 Observer
        foreach ($observers as $model => $observer) {
            $modelClass = "App\\Models\\$model";
            if (class_exists($modelClass) && class_exists($observer)) {
                $modelClass::observe(new $observer);
            }
        }
    }
}
