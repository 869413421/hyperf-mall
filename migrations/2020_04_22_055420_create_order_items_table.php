<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->unsignedInteger('order_id')->index()->comment('总订单号');
            $table->unsignedInteger('product_id')->index()->comment('商品ID');
            $table->unsignedInteger('product_sku_id')->index()->comment('商品skuID');
            $table->unsignedInteger('amount')->comment('商品数量');
            $table->decimal('price', 10, 2)->comment('单价');
            $table->unsignedInteger('rating')->nullable()->comment('用户评分');
            $table->text('review')->nullable()->comment('用户评价');
            $table->timestamp('reviewed_at')->nullable()->comment('评价时间');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
}
