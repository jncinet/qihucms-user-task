<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTaskTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 任务
        Schema::create('user_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index()->comment('发布人');
            $table->string('title')->comment('任务名称');
            $table->string('thumbnail')->nullable()->comment('缩略图');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->unsignedInteger('stock')->default(0)->comment('任务总数');
            $table->unsignedBigInteger('currency_type_id')->comment('奖励类型');
            $table->unsignedDecimal('amount')->default(0)->comment('奖励数额');
            $table->longText('content')->nullable()->comment('详细介绍');
            $table->string('btn_text')->nullable()->comment('任务链文字');
            $table->string('link')->nullable()->comment('任务链接');
            $table->boolean('pay_status')->default(0)->comment('支付状态');
            $table->boolean('status')->default(0)->comment('状态');
            $table->timestamps();
        });

        // 会员完成记录
        Schema::create('user_task_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index()->comment('会员ID');
            $table->unsignedBigInteger('user_task_id')->index()->comment('任务ID');
            $table->json('files')->nullable()->comment('任务凭证');
            $table->text('remark')->nullable()->comment('备注');
            $table->boolean('status')->default(0)->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tasks');
        Schema::dropIfExists('user_task_orders');
    }
}
