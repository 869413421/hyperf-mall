<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('no')->unique()->comment('分期流水号');
            $table->unsignedInteger('user_id')->index()->comment('用户ID');
            $table->unsignedInteger('order_id')->index()->comment('订单ID');
            $table->decimal('total_amount', 10, 2)->comment('分期金额');
            $table->unsignedInteger('count')->comment('还款期数');
            $table->float('fee_rate')->comment('手续费率');
            $table->float('fine_rate')->comment('逾期费率');
            $table->string('status')->default(\App\Model\Installment::STATUS_PENDING)->comment('还款状态');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
}
