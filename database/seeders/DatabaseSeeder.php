<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            InitialUsersSeeder::class,
            CategoryProductSeeder::class,
            ShippingSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
            ForumSeeder::class,
            SeminarSeeder::class,
            BundleProductSeeder::class,
        ]);
    }
}
