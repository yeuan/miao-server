<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'taggables';

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
            $table->unsignedBigInteger('tag_id')->index()->comment('標籤ID');
            $table->string('taggable_type', 50)->comment('標籤關聯模型類型');
            $table->unsignedBigInteger('taggable_id')->comment('標籤關聯主體ID');
            $table->unsignedBigInteger('created_at')->default(0)->comment('建立時間');
            $table->unsignedBigInteger('updated_at')->default(0)->comment('更新時間');

            // 索引
            $table->index(['taggable_type', 'taggable_id'], 'taggable_type_id_index');
            $table->index(['tag_id', 'taggable_type', 'taggable_id'], 'tag_and_taggable_index');
        });

        DB::statement("ALTER TABLE `$this->tableName` comment '標籤關聯表'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
