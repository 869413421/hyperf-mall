<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('user_id')->nullable(false);
            $table->string('province')->nullable(false)->default('');
            $table->string('city')->nullable(false)->default('');
            $table->string('district')->nullable(false)->default('');
            $table->string('address')->nullable(false)->default('');
            $table->bigInteger('zip')->nullable(false)->default(0);
            $table->string('contact_name')->nullable(false)->default('');
            $table->string('contact_phone')->nullable(false)->default('');
            $table->timestamp('last_used_at')->nullable();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
}
