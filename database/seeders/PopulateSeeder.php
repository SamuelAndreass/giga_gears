<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerStore;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PopulateSeeder extends Seeder
{
    public function run()
    {
        // Create Users
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin Demo',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        $seller = User::firstOrCreate(['email' => 'seller@example.com'], [
            'name' => 'Seller Demo',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'is_seller' => true,
            'email_verified_at' => now()
        ]);

        $customer = User::firstOrCreate(['email' => 'customer@example.com'], [
            'name' => 'Customer Demo',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now()
        ]);

        // Create Customer Profiles
        CustomerProfile::firstOrCreate(['user_id' => $customer->id]);
        CustomerProfile::firstOrCreate(['user_id' => $seller->id]);

        // Create Seller Store
        SellerStore::firstOrCreate(['user_id' => $seller->id], [
            'store_name' => 'Tech Store Demo',
            'store_address' => 'Jl. Demo No.1, Jakarta',
            'store_description' => 'Store for technology products'
        ]);

        // Create Categories with slug
        $categories = [
            'Smartphones' => 'Mobile phones and devices',
            'Laptops' => 'Laptops and computers',
            'Accessories' => 'Phone and computer accessories',
            'Audio' => 'Speakers and headphones',
            'Wearables' => 'Smartwatches and fitness bands'
        ];

        foreach ($categories as $name => $desc) {
            Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => $desc
                ]
            );
        }

        // Create Products
        $products = [
            ['name' => 'iPhone 15 Pro', 'category' => 'Smartphones', 'price' => 12999000, 'stock' => 50],
            ['name' => 'Samsung Galaxy S24', 'category' => 'Smartphones', 'price' => 11999000, 'stock' => 40],
            ['name' => 'MacBook Pro M3', 'category' => 'Laptops', 'price' => 24999000, 'stock' => 20],
            ['name' => 'Dell XPS 15', 'category' => 'Laptops', 'price' => 19999000, 'stock' => 15],
            ['name' => 'USB-C Cable', 'category' => 'Accessories', 'price' => 199000, 'stock' => 200],
            ['name' => 'Wireless Charger', 'category' => 'Accessories', 'price' => 499000, 'stock' => 150],
            ['name' => 'Sony WH-1000XM5', 'category' => 'Audio', 'price' => 5999000, 'stock' => 30],
            ['name' => 'Apple Watch Series 9', 'category' => 'Wearables', 'price' => 7999000, 'stock' => 35],
        ];

        foreach ($products as $prod) {
            $category = Category::where('name', $prod['category'])->first();
            if ($category) {
                Product::firstOrCreate(['name' => $prod['name']], [
                    'category_id' => $category->id,
                    'user_id' => $seller->id,
                    'price' => $prod['price'],
                    'stock' => $prod['stock'],
                    'description' => 'High quality ' . $prod['name'],
                    'status' => 'active'
                ]);
            }
        }
    }
}
