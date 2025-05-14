<?php

return [
    'Manager\Admin' => \App\Observers\Auth\AuthObserver::class,
    'Log\LogAdminLogin' => \App\Observers\Auth\GrabIpInfoObserver::class,
];
