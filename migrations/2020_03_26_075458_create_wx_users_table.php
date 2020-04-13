<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWxUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wx_users', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('user_id')->nullable()->comment('user表user_id');
            $table->string('nick_name', 50)->default('');
            $table->string('avatar', 200)->default('')->comment('头像');
            $table->string('open_id', 50);
            $table->string('union_id', 50);
            $table->string('access_token', 200);
            $table->timestamp('access_token_expire_time')->nullable();
            $table->string('refresh_token', 200);
            $table->timestamp('refresh_token_expire_time')->nullable();
            $table->unique('open_id');
            $table->unique('union_id');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wx_users');
    }
}
