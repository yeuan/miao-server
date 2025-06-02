<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'upload_settings';

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
            $table->unsignedTinyInteger('type')->default(1)->comment('類型 1:image、2:file');
            $table->string('module_code', 50)->comment('對應 modules.code');
            $table->json('extensions')->nullable()->comment('允許副檔名');
            $table->unsignedTinyInteger('thumbnail_enable')->default(1)->comment('是否縮圖 0:關閉 1:開啟');
            $table->unsignedSmallInteger('thumb_width')->default(0)->comment('縮圖寬度');
            $table->unsignedSmallInteger('thumb_height')->default(0)->comment('縮圖高度');
            $table->unsignedTinyInteger('thumb_mode')->default(1)->comment('縮圖模式 1:cover滿版 2:contain留白 3:stretch拉伸 4:fit等比縮放');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:停用 1:啟用');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->index(['type', 'module_code', 'status'], 'type_module_status_index');
            $table->index(['status', 'sort'], 'status_sort_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '上傳設置'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
