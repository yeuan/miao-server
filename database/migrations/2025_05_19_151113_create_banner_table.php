<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'banner';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable($this->tableName)) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner_type', 20)->default('platform')->comment('資料歸屬類型：platform/tenant');
            $table->unsignedBigInteger('owner_id')->default(0)->comment('資料歸屬 ID');
            $table->unsignedTinyInteger('type')->default(1)->comment('類型 1:首頁輪播、2:登入頁、3:活動頁、4:商城專區、5:金流專區、6:遊戲公告、7:會員中心');
            $table->string('image')->default('')->comment('圖片路徑');
            $table->string('image_app')->default('')->comment('手機版圖片');
            $table->string('url')->default('')->comment('超連結');
            $table->unsignedTinyInteger('link_type')->default(0)->comment('連結類型');
            $table->unsignedInteger('module_id')->default(0)->comment('模組ID');
            $table->unsignedInteger('object_id')->default(0)->comment('物件ID');
            $table->unsignedBigInteger('start_time')->default(0)->comment('開始時間（UNIX timestamp）');
            $table->unsignedBigInteger('end_time')->default(5000000000)->comment('結束時間（UNIX timestamp）');
            $table->unsignedInteger('flag')->default(0)->comment('旗標');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引設計
            $table->index(['type', 'status'], 'type_status_index');
            $table->index(['type', 'status', 'flag'], 'type_status_flag_index');
            $table->index(['type', 'status', 'start_time', 'end_time'], 'type_status_time_index');
            $table->index(['type', 'status', 'flag', 'start_time', 'end_time'], 'type_status_flag_time_index');
            $table->index(['owner_type', 'owner_id', 'type', 'status'], 'owner_type_status_index');
            $table->index(['owner_type', 'owner_id', 'type', 'status', 'flag'], 'owner_type_status_flag_index');
            $table->index(['owner_type', 'owner_id', 'type', 'status', 'start_time', 'end_time'], 'owner_type_status_time_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '輪播圖'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
