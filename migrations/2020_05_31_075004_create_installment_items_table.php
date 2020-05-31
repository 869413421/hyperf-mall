<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateInstallmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installment_items', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedInteger('installment_id')->index()->comment('分期总订单ID');
            $table->unsignedInteger('sequence')->comment('还款顺序编号');
            $table->decimal('base')->comment('本期本金');
            $table->decimal('fee')->comment('本期手续费');
            $table->decimal('fine')->nullable()->comment('本期逾期费用');
            $table->dateTime('due_date')->comment('还款截至日期');
            $table->dateTime('paid_at')->nullable()->comment('付款日期');
            $table->string('payment_method')->nullable()->comment('支付方式');
            $table->string('payment_no')->nullable();
            $table->string('refund_status')->default(\App\Model\InstallmentItem::REFUND_STATUS_PENDING)->comment('退款状态');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_items');
    }
}
