<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'news';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable($this->tableName)) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('owner_type', 20)->default('platform')->comment('資料歸屬類型：platform/tenant');
            $table->unsignedBigInteger('owner_id')->default(0)->comment('資料歸屬 ID');
            $table->string('slug', 100)->comment('網址代稱');
            $table->unsignedBigInteger('category_id')->default(0)->comment('分類 ID');
            $table->string('title', 50)->comment('標題');
            $table->string('cover')->default('')->comment('封面路徑');
            $table->string('cover_app')->default('')->comment('手機版封面');
            $table->string('summary', 255)->nullable()->comment('摘要');
            $table->text('content')->comment('內容');
            $table->unsignedBigInteger('start_time')->default(0)->comment('開始時間（UNIX timestamp）');
            $table->unsignedBigInteger('end_time')->default(5000000000)->comment('結束時間（UNIX timestamp）');
            $table->unsignedInteger('flag')->default(0)->comment('旗標');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->unsignedBigInteger('views')->default(0)->comment('瀏覽數');
            $table->unsignedBigInteger('likes')->default(0)->comment('按贊數');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->unique(['owner_type', 'owner_id', 'slug'], 'owner_slug_unique');
            $table->index(['owner_type', 'owner_id', 'status'], 'owner_status_index');
            $table->index(['owner_type', 'owner_id', 'status', 'start_time', 'end_time'], 'owner_status_time_index');
            $table->index(['category_id', 'status', 'start_time', 'end_time'], 'cat_status_time_index');
            $table->index(['owner_type', 'owner_id', 'status', 'sort', 'start_time', 'end_time'], 'owner_status_sort_time_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '最新消息/新聞'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
