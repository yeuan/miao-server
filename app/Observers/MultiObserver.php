<?php

namespace App\Observers;

class MultiObserver
{
    protected array $observers = [];

    public function __construct(array $observers)
    {
        $this->observers = array_map(fn ($class) => new $class, $observers);
    }

    // 動態接收所有 Eloquent 事件
    public function __call($method, $arguments)
    {
        foreach ($this->observers as $observer) {
            if (method_exists($observer, $method)) {
                $observer->{$method}(...$arguments);
            }
        }
    }
}
