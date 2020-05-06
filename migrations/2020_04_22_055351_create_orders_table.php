<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('no')->unique()->comment('流水号');
            $table->unsignedInteger('user_id')->index();
            $table->text('address');
            $table->decimal('total_amount', 10, 2)->comment('订单总金额');
            $table->text('remark')->comment('备注');
            $table->timestamp('paid_at')->nullable()->comment('支付时间');
            $table->string('payment_method')->nullable()->comment('支付方式');
            $table->string('payment_no')->nullable()->unique()->comment('支付平台订单号');
            $table->string('refund_status')->default(\App\Model\Order::REFUND_STATUS_PENDING)->comment('退款状态');
            $table->string('refund_no')->nullable()->comment('退款单号');
            $table->integer('closed')->default(0)->comment('订单是否关闭');
            $table->integer('reviewed')->default(0)->comment('订单是否已经评价');
            $table->string('ship_status')->default(\App\Model\Order::SHIP_STATUS_PENDING)->comment('物流状态');
            $table->text('ship_data')->nullable()->comment('物流数据');
            $table->text('extra')->nullable()->comment('额外数据');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
