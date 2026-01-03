<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
            BundleProductSeeder::class,
            SeminarSeeder::class,
        ]);
    }
}
