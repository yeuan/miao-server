<?php

return [
    'Log\LogAdminLogin' => [\App\Observers\Auth\GrabIpInfoObserver::class],
    'Log\LogUpload' => [\App\Observers\System\UploadObserver::class],
    'Manager\Admin' => [\App\Observers\Auth\AuthObserver::class],
    'Content\Notice' => [
        \App\Observers\Content\NoticeObserver::class,
        \App\Observers\Content\OwnerObserver::class,
    ],
    'Content\Banner' => [
        \App\Observers\System\AttachUploadObserver::class,
        \App\Observers\Content\OwnerObserver::class,
    ],
];
