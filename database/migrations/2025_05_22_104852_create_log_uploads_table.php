<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $schema;

    private $db;

    private $tableName = 'log_uploads';

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
            $table->string('related_table', 100)->nullable()->comment('對應資料表');
            $table->string('related_field', 50)->nullable()->comment('對應欄位');
            $table->unsignedBigInteger('related_id')->nullable()->comment('對應資料表id');
            $table->string('disk', 150)->default('public')->comment('存放目錄類型');
            $table->string('file_path')->comment('檔案路徑');
            $table->string('file_name')->comment('原始檔名');
            $table->string('mime_type', 50)->comment('副檔名');
            $table->string('thumbnail_path')->nullable()->comment('縮圖路徑');
            $table->unsignedBigInteger('size')->comment('檔案大小');
            $table->unsignedTinyInteger('status')->default(0)->comment('狀態 0:pending 1:active 2:deleted');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->index(['related_table', 'related_field', 'related_id'], 'related_full_index');
            $table->index(['related_table', 'related_id', 'status'], 'related_status_index');
            $table->index(['status', 'created_at'], 'status_created_at_index');
        });

        $this->db->statement("ALTER TABLE `$this->tableName` comment '上傳紀錄log'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->dropIfExists($this->tableName);
    }
};
