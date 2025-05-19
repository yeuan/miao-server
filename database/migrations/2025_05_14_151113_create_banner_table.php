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
            $table->unsignedTinyInteger('type')->default(1)->comment('類型 1:首頁輪播');
            $table->unsignedInteger('page_tab_id')->default(0)->comment('頁籤ID');
            $table->string('image')->default('')->comment('圖片路徑');
            $table->string('image_app')->default('')->comment('手機版圖片');
            $table->string('url')->default('')->comment('超連結');
            $table->unsignedTinyInteger('link_type')->default(0)->comment('連結類型');
            $table->unsignedInteger('object_id')->default(0)->comment('物件ID');
            $table->unsignedBigInteger('start_time')->default(0)->comment('開始時間（UNIX timestamp）');
            $table->unsignedBigInteger('end_time')->default(5000000000)->comment('結束時間（UNIX timestamp）');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引設計
            $table->index(['type', 'status'], 'type_status_index');
            $table->index(['type', 'status', 'start_time', 'end_time'], 'type_status_time_index');
            $table->index(['type', 'page_tab_id', 'status'], 'type_page_tab_status_index');
            $table->index(['type', 'page_tab_id', 'status', 'start_time', 'end_time'], 'type_page_tab_status_time_index');
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
