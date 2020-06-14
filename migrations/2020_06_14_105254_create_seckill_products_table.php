<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateSeckillProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seckill_products', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedSmallInteger('product_id')->index();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seckill_products');
    }
}
