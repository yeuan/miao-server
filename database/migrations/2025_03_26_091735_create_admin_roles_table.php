<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'admin_roles';

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
            $table->unsignedTinyInteger('backstage')->default(1)->comment('後台類型 1:主後台 2:代理 3:租客');
            $table->string('name', 50)->default('')->comment('角色名稱')->unique();
            $table->json('allow_nav')->comment('導航權限');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->useCurrentOnUpdate()->comment('更新時間');

            // 索引
            $table->index(['backstage', 'status', 'name'], 'backstage_status_name_index');
            $table->index(['status', 'name', 'updated_at'], 'status_name_updated_at_index');
            $table->index(['name', 'updated_by', 'updated_at'], 'name_updated_by_updated_at_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '管理者角色權限'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
