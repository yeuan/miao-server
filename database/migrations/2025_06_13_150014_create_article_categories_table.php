<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'article_categories';

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
            $table->string('slug', 100)->comment('網址代稱');
            $table->unsignedBigInteger('pid')->default(0)->comment('上層分類ID');
            $table->string('path', 200)->default('')->comment('階層路徑');
            $table->string('name', 50)->comment('分類名稱');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:啟用');
            $table->unsignedBigInteger('used_count')->default(0)->comment('引用次數');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->unique(['owner_type', 'owner_id', 'slug'], 'owner_slug_unique');
            $table->index(['owner_type', 'owner_id', 'status', 'pid'], 'owner_status_pid_index');
            $table->index(['owner_type', 'owner_id', 'status', 'sort'], 'owner_status_sort_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '文章分類'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
