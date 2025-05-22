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
        // 後台導航
        Route::adminGroup('admin_nav', 'Manager\AdminNavController', function () {
            // 權限列表
            Route::get('permission', 'permission')->name('permission');
            // 取得後台導航
            Route::get('sidebar', 'sidebar')->name('sidebar');
            Route::registerCrud('admin_nav');
        });
        // 模組
        Route::adminGroup('modules', 'System\ModulesController', function () {
            Route::registerCrud('modules', ['index', 'update']);
            // 取得所有啟用中的模組
            Route::get('active', 'active')->name('modules.active');
        });

        // 載入模組的路由
        Route::adminModules();

        // 模組啟動驗證（CRUD功能外用）
        Route::middleware(['module.enabled'])->group(function () {
            /* -- 內容管理 -- */

        });

    });
});
// });
