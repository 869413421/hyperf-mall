<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function createData()
    {
        \App\Model\User::query()->create([
            'user_name' => 'admin',
            'email' => '13528685024@163.com',
            'password' => md5('123456'),
            'email_verify_date' => Carbon\Carbon::now(),
            'status' => 0
        ]);
    }
}
