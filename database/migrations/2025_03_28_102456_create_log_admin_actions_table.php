<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $schema;

    private $db;

    private $tableName = 'log_admin_actions';

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
            $table->string('route', 150)->default('')->comment('路由');
            $table->json('info')->comment('操作訊息');
            $table->json('sql')->comment('SQL指令');
            $table->string('ip', 39)->default('')->comment('登入IP');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:失敗 1:成功');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');

            // 將 created_at 加入主鍵
            $table->primary(['id', 'created_at']);

            // 索引
            $table->index(['backstage', 'admin_id', 'status'], 'backstage_admin_status_index');
            $table->index('created_at');
        });

        $this->db->statement("ALTER TABLE `$this->tableName` comment '管理帳號操作LOG'");

        // 建立分區
        // $now   = Carbon::now();
        // $start = $now->startOfMonth();

        // $query = "ALTER TABLE {$this->tableName} PARTITION BY RANGE (`created_at`) (";
        // $partitions = [];

        // for ($i = 0; $i <= 3; $i++) {
        //     $partitionName = 'm' . $start->format('Ym');
        //     $end = $start->copy()->addMonth()->startOfMonth()->timestamp;

        //     $partitions[] = "PARTITION $partitionName VALUES LESS THAN ($end) ENGINE = InnoDB";
        //     $start->addMonth();
        // }

        // // 預設分區，處理超過範圍的資料
        // $partitions[] = "PARTITION m0 VALUES LESS THAN MAXVALUE ENGINE = InnoDB";
        // $query .= implode(',', $partitions) . ")";

        // $this->db->statement($query);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->dropIfExists($this->tableName);
    }
};
