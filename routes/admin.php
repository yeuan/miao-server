<?php

use Illuminate\Support\Facades\Route;

// 全域變數直接在 routes/admin.php 檔案最上方宣告
$provider = ucfirst(config('custom.routes.provider.admin_prefix', 'admin'));

// Route::middleware(['ipLimit', 'lang'])->group(function () use ($provider) {
Route::group(['prefix' => 'v1'], function () use ($provider) {
    // 登入相關
    Route::controllerGroup('auth', $provider, 'Auth\AuthController', function () {
        Route::post('login', 'login')->name('login');
    });

    // 登入驗證
    Route::middleware(['jwt.refresh', 'jwt.auth:'.$provider, 'ensure.backstage:'.$provider,  'permission'])->group(function () use ($provider) {
        // 登入相關
        Route::controllerGroup('auth', $provider, 'Auth\AuthController', function () {
            Route::post('logout', 'logout')->name('logout');
        });

        // 檔案上傳
        Route::controllerGroup('upload', $provider, 'System\UploadController', function () {
            Route::post('image', 'image')->name('upload.image');
        });

        /* -- 系統管理 -- */
        // 管理者角色
        Route::adminGroup('admin-role', 'Manager\AdminRoleController', function () {
            Route::registerCrud('admin_role');
        });
        // 管理者
        Route::adminGroup('admin', 'Manager\AdminController', function () {
            Route::registerCrud('admin');
        });
        // 後台導航
        Route::adminGroup('admin-nav', 'Manager\AdminNavController', function () {
            // 權限列表
            Route::get('permission', 'permission')->name('permission');
            // 取得後台導航
            Route::get('sidebar', 'sidebar')->name('sidebar');
            Route::registerCrud('admin_nav');
        });
        // 模組
        Route::adminGroup('modules', 'Manager\ModulesController', function () {
            Route::registerCrud('modules', ['index', 'update']);
            // 取得所有啟用中的模組
            Route::get('active', 'active')->name('modules.active');
        });

        // 載入模組的路由
        Route::adminModules();

        /* -- 系統設置 -- */
        // 上傳設置
        Route::adminGroup('upload-settings', 'System\UploadSettingsController', function () {
            Route::registerCrud('upload_settings', ['index', 'show', 'update']);
        });

        // 模組啟動驗證（CRUD功能外用）
        Route::middleware(['module.enabled'])->group(function () {
            /* -- 內容管理 -- */

        });

    });

    // 輔助工具
    Route::controllerGroup('tool', $provider, 'System\ToolController', function () {
        Route::get('clean-unused-uploads/{minutes?}', 'cleanUnusedUploads')->name('tool.clean-unused-uploads');
    });
});
// });
