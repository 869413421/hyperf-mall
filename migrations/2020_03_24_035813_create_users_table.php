<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('wx_user_id')->nullable();
            $table->string('user_name', 50)->nullable(false);
            $table->string('password', 100)->nullable(false);
            $table->string('email', 50)->nullable();
            $table->timestamp('email_verify_date')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('real_name', 50)->nullable(false)->default('');
            $table->timestamp('last_login_at')->nullable();
            $table->tinyInteger('sex')->nullable(false)->default(0);
            $table->string('avatar', 200)->nullable(false)->default('');
            $table->string('remember_token', 200)->nullable(false)->default('');
            $table->tinyInteger('status')->nullable(false)->default(0)->comment('用户状态,0:正常,1:锁定');

            $table->unique('user_name');
            $table->unique('email');
            $table->unique('phone');
            $table->unique('wx_user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
