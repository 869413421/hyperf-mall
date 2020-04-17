<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_skus', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('title', 200)->default('');
            $table->text('description')->nullable();
            $table->decimal('price');
            $table->unsignedInteger('stock');
            $table->unsignedInteger('product_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_skus');
    }
}
