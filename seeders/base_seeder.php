<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserTableSeeder::createData();
        UserAddressesSeeder::createData();
        PermissionTableSeeder::createData();
    }
}
