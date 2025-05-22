<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'modules';

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
            $table->string('namespace', 50)->default('Content')->comment('控制器命名空間');
            $table->string('code', 50)->unique()->comment('模組代號');
            $table->string('name', 50)->comment('模組名稱');
            $table->string('description', 100)->nullable()->comment('模組說明');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->index(['status', 'sort'], 'status_sort_index');
            $table->index(['name'], 'name_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '系統模組'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
