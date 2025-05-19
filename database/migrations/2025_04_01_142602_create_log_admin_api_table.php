<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $schema;

    private $db;

    private $tableName = 'log_admin_api';

    public function __construct()
    {
        $this->schema = Schema::connection('log');
        $this->db = DB::connection('log');
        $this->dbType = env('DB_CONNECTION', 'mysql');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($this->schema->hasTable($this->tableName)) {
            return;
        }

        $this->schema->create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url', 255)->default('')->comment('API網址');
            $table->string('route', 150)->default('')->comment('路由');
            $table->json('params')->nullable()->comment('請求參數');
            $table->json('headers')->nullable()->comment('標頭');
            $table->json('response')->nullable()->comment('回傳參數');
            if ($this->dbType === 'mysql') {
                $this->db->statement("ALTER TABLE `$this->tableName` comment '輪播圖'");

                $table->boolean('success')->virtualAs("JSON_VALUE(response, '$.success')")->comment('呼叫結果');
                $table->unsignedSmallInteger('code')->virtualAs("JSON_VALUE(response, '$.code')")->comment('回傳code');
            } elseif ($this->dbType === 'mariadb') {
                $table->boolean('success')->storedAs("IF(JSON_UNQUOTE(JSON_EXTRACT(response, '$.success')) = 'true', 1, 0)")->comment('呼叫結果');
                $table->unsignedSmallInteger('code')->storedAs("JSON_UNQUOTE(JSON_EXTRACT(response, '$.code'))")->comment('回傳code');
            }
            $table->json('exception')->comment('例外訊息');
            $table->decimal('exec_time', 10, 4)->default(0.0000)->comment('執行時間');
            $table->string('ip', 45)->default('')->comment('登入IP');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');

            // 索引
            $table->index('created_at', 'created_at_index');
            $table->index('route', 'route_index');
            $table->index('code', 'code_index');
            $table->index(['route', 'created_at'], 'route_created_at_index');
        });

        $this->db->statement("ALTER TABLE `$this->tableName` comment '後台API執行Log'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->schema->dropIfExists($this->tableName);
    }
};
