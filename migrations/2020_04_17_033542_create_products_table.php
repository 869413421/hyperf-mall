<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('title', 200)->default('');
            $table->text('description')->nullable();
            $table->string('image', 500)->default('');
            $table->tinyInteger('on_sale')->default(1);
            $table->float('rating')->default(5);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->decimal('price', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}
