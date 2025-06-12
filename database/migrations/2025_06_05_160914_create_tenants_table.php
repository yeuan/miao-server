<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'tenants';

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
            $table->string('name', 100)->comment('名稱');
            $table->string('code', 50)->comment('唯一識別碼')->unique();
            $table->unsignedTinyInteger('type')->default(1)->comment('類型 1:一般廟宇 2:私人宮廟 3:直營廟宇 4:直營私人宮廟');
            $table->unsignedTinyInteger('level')->default(1)->comment('等級 1:一般');
            $table->unsignedSmallInteger('city_id')->default(0)->comment('縣市ID');
            $table->unsignedSmallInteger('district_id')->default(0)->comment('區域/行政區ID');
            $table->string('logo_url', 255)->nullable()->comment('Logo圖片網址');
            $table->string('cover_url', 255)->nullable()->comment('封面圖片網址');
            $table->unsignedInteger('flag')->default(0)->comment('旗標');
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('狀態 0:關閉 1:開啟');
            $table->json('config')->nullable()->comment('動態自訂設定');
            $table->unsignedBigInteger('expire_at')->default(0)->comment('有效期限（合作/授權到期）');
            $table->string('api_key', 100)->nullable()->unique()->comment('串接API用');
            $table->string('webhook_url', 255)->nullable()->comment('事件推播用');
            $table->string('created_by', 50)->default('')->comment('新增者');
            $table->string('updated_by', 50)->default('')->comment('更新者');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->index(['type', 'level', 'status'], 'type_level_status_index');
            $table->index(['type', 'city_id', 'district_id', 'status'], 'type_city_district_status_index');
            $table->index(['status', 'expire_at'], 'status_expire_at_index');
            $table->index(['flag', 'status'], 'flag_status_index');
            $table->index(['city_id', 'status'], 'city_status_index');
            $table->index(['type', 'status', 'sort'], 'type_status_sort_index');
            $table->index(['status', 'sort'], 'status_sort_index');
        });

        // 用原生 SQL 補上 spatial 欄位與索引
        DB::statement("ALTER TABLE `$this->tableName` ADD COLUMN `location` POINT NOT NULL COMMENT '地理座標（經緯度）' AFTER `status`");
        DB::statement("CREATE SPATIAL INDEX `location_spatial_index` ON `$this->tableName` (`location`)");
        DB::statement("ALTER TABLE `$this->tableName` comment '多租客(廟宇)主表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
