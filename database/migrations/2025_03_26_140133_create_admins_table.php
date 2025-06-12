<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'admins';

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
            $table->unsignedInteger('tenant_id')->nullable()->comment('所屬租客，租客管理員專用');
            $table->unsignedInteger('agent_id')->nullable()->comment('所屬代理商，代理管理員專用');
            $table->string('username')->default('')->comment('用戶名')->unique();
            $table->string('password')->default('')->comment('密碼');
            // $table->string('token', 500)->default('')->comment('Token');
            $table->unsignedInteger('role_id')->default(0)->comment('角色ID');
            $table->string('login_ip', 50)->default('')->comment('登入IP');
            $table->unsignedBigInteger('login_time')->default(0)->comment('登入時間');
            $table->integer('login_count')->default(0)->comment('登入次數');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->index(['backstage', 'role_id', 'status'], 'backstage_role_id_status_index');
            $table->index(['backstage', 'status', 'username'], 'backstage_status_username_index');
            $table->index(['login_ip', 'login_time', 'status'], 'login_index');
            $table->index(['status', 'username', 'updated_by', 'updated_at'], 'activity_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '管理帳號'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
