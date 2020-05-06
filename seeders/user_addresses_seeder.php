<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function createData()
    {
        for ($i = 0; $i < 10; $i++)
        {
            $user = \App\Model\User::query()->inRandomOrder()->first();

            \App\Model\UserAddress::query()->create(
                [
                    'user_id' => $user->id,
                    'province' => \Hyperf\Utils\Str::random(5),
                    'city' => \Hyperf\Utils\Str::random(5),
                    'district' => \Hyperf\Utils\Str::random(5),
                ]
            );
        }
    }
}
