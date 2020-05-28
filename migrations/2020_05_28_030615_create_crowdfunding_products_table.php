<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCrowdfundingProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crowdfunding_products', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedInteger('product_id')->unique()->comment('商品ID');
            $table->decimal('target_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->unsignedInteger('user_count')->default(0);
            $table->timestamp('end_time')->nullable();
            $table->string('status')->default(\App\Model\CrowdfundingProduct::STATUS_FUNDING);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crowdfunding_products');
    }
}
