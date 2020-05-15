<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('name')->default('');
            $table->unsignedInteger('parent_id')->nullable()->index();
            $table->integer('is_directory')->default(0);
            $table->unsignedInteger('level');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}
