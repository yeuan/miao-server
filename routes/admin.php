<?php

use Illuminate\Support\Facades\Route;

// Route::middleware(['ipLimit', 'lang'])->group(function () {
Route::group(['prefix' => 'v1'], function () {
    // 登入相關
    Route::adminGroup('auth', 'Auth\AuthController', function () {
        Route::post('login', 'login')->name('login');
    });

    // 登入驗證
    Route::middleware(['jwt.refresh', 'jwt.auth:admin',  'permission'])->group(function () {
        // 登入相關
        Route::adminGroup('auth', 'Auth\AuthController', function () {
            Route::post('logout', 'logout')->name('logout');
        });

        /* -- 系統管理 -- */
        // 管理者角色
        Route::adminGroup('admin_role', 'Manager\AdminRoleController', function () {
            Route::registerCrud('admin_role');
        });
        // 管理者
        Route::adminGroup('admin', 'Manager\AdminController', function () {
            Route::registerCrud('admin');
        });
        // 導航
        Route::adminGroup('admin_nav', 'Manager\AdminNavController', function () {
            Route::registerCrud('admin_nav');
        });

        /* -- 內容管理 -- */
        // 公告
        Route::adminGroup('notice', 'Content\NoticeController', function () {
            Route::registerCrud('notice');
        });
        // 輪播圖
        Route::adminGroup('banner', 'Content\BannerController', function () {
            Route::registerCrud('banner');
        });
    });
});
// });
