<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'tags';

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
            $table->string('name', 50)->default('')->comment('標籤名稱');
            $table->string('module_code', 50)->comment('對應 modules.code');
            $table->string('owner_type', 20)->default('platform')->comment('資料歸屬類型：platform/tenant');
            $table->unsignedBigInteger('owner_id')->default(0)->comment('資料歸屬 ID');
            $table->string('color', 20)->nullable()->comment('顏色');
            $table->unsignedBigInteger('used_count')->default(0)->comment('引用次數');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->unique(['owner_type', 'owner_id', 'module_code', 'name'], 'uniq_owner_module_tag_name');
            $table->index(['owner_type', 'owner_id', 'module_code'], 'owner_module_index');
            $table->index(['owner_type', 'owner_id', 'module_code', 'status'], 'owner_module_status_index');
            $table->index(['owner_type', 'owner_id', 'sort'], 'owner_sort_index');
            $table->index(['owner_type', 'owner_id', 'module_code', 'used_count'], 'owner_module_count_index');
            $table->index(['owner_type', 'owner_id', 'module_code', 'status', 'sort'], 'owner_module_status_sort_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '標籤'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
