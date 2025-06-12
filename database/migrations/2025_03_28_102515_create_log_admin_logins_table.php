<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $schema;

    private $db;

    private $tableName = 'log_admin_logins';

    public function __construct()
    {
        $this->schema = Schema::connection('log');
        $this->db = DB::connection('log');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ($this->schema->hasTable($this->tableName)) {
            return;
        }

        $this->schema->create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('backstage')->default(1)->comment('後台類型 1:總後台 2:代理後台 3:多租客後台');
            $table->unsignedInteger('admin_id')->default(0)->comment('管理者ID');
            $table->string('ip', 45)->default('')->comment('登入IP');
            $table->json('ip_info')->comment('IP資訊');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:失敗 1:成功');
            $table->string('message')->default('')->comment('訊息');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');

            // 索引
            $table->index(['backstage', 'admin_id', 'ip', 'created_at'], 'backstage_admin_ip_created_index');
            $table->index('ip', 'ip_index');
            $table->index('created_at', 'created_at_index');
        });

        $this->db->statement("ALTER TABLE `$this->tableName` comment '系統帳號登入LOG'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->dropIfExists($this->tableName);
    }
};
