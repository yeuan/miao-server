<?php

namespace App\Providers;

use App\Observers\MultiObserver;
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
            if (! class_exists($modelClass)) {
                continue;
            }

            $observerArr = (array) $observer; // 強制轉陣列
            if (count($observerArr) > 1) {
                $modelClass::observe(new MultiObserver($observerArr));
            } else {
                $observerClass = $observerArr[0];
                if (class_exists($observerClass)) {
                    $modelClass::observe(new $observerClass);
                }
            }
        }
    }
}
