<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'admin_navs';

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
            $table->unsignedInteger('pid')->default(0)->comment('上層ID');
            $table->string('path', 150)->default('')->comment('階層路徑');
            $table->string('icon', 50)->default('')->comment('ICON');
            $table->string('name', 50)->default('')->comment('導航名稱');
            $table->string('module_code', 50)->default('')->comment('對應模組 code');
            $table->string('route', 255)->default('')->comment('路由');
            $table->string('url', 255)->default('')->comment('前端網址');
            $table->unsignedInteger('flag')->default(0)->comment('旗標');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->index(['pid', 'status'], 'pid_status_index');
            $table->index(['module_code', 'status'], 'module_code_status_index');
            $table->index(['flag', 'status'], 'flag_status_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '導航列表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
