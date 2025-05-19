<?php

return [
    'Log\LogAdminLogin' => \App\Observers\Auth\GrabIpInfoObserver::class,
    'Manager\Admin' => \App\Observers\Auth\AuthObserver::class,
    'Content\Notice' => \App\Observers\Content\NoticeObserver::class,
];
