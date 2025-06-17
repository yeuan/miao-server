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
    'Content\Page' => [\App\Observers\Content\OwnerObserver::class],
    'Content\Article' => [
        \App\Observers\Content\ArticleObserver::class,
        \App\Observers\Content\OwnerObserver::class,
        \App\Observers\System\AttachUploadObserver::class,
    ],
    'Content\News' => [
        \App\Observers\Content\NewsObserver::class,
        \App\Observers\Content\OwnerObserver::class,
        \App\Observers\System\AttachUploadObserver::class,
    ],
    'Content\Faq' => [
        \App\Observers\Content\FaqObserver::class,
        \App\Observers\Content\OwnerObserver::class,
    ],
    'Manager\Taggable' => [\App\Observers\Manager\TaggableObserver::class],
];
