<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupon_codes', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->string('name')->default('');
            $table->string('code')->nullable(false)->unique();
            $table->string('type')->default('');
            $table->decimal('value', 10, 2)->default(0.00);
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('used')->default(0);
            $table->decimal('min_amount', 10, 2)->default(0.00);
            $table->timestamp('not_before')->nullable();
            $table->timestamp('not_after')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_codes');
    }
}
