<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\Category;
use App\Models\SellerStore;

class BundleProductSeeder extends Seeder
{
    public function run()
    {
        $store = SellerStore::first();
        if (! $store) {
            $this->command->warn('No seller store found. Skipping bundle seeder.');
            return;
        }


        $products = Product::where('seller_store_id', $store->id)
            ->where('type', '!=', 'bundle')
            ->get();

        if ($products->count() < 3) {
            $this->command->warn('Not enough products to create bundles.');
            return;
        }

        $keyboard = $products->firstWhere('name', 'Razer BlackWidow V3 Mechanical Keyboard');
        $headset  = $products->firstWhere('name', 'Logitech G Pro X Headset');
        $mouse    = $products->firstWhere('name', 'Anker PowerLine USB-C Cable'); // dummy mouse

        if ($keyboard && $headset && $mouse) {

            $bundle = Product::create([
                'seller_store_id' => $store->id,
                'name' => 'Gaming Starter Pack',
                'type' => 'bundle',
                'price' => 4299000,
                'original_price' => 4797000, 
                'stock' => 1,
                'brand' => 'Bundle Deal',
                'status' => 'active',
                'is_featured' => true,
                'category_id' => Category::where('name', 'Gaming')->first()->id,
                'SKU' => 'BUNDLE-GAMING-' . strtoupper(Str::random(5)),
                'description' => 'Paket gaming lengkap dengan harga lebih hemat',
                'images' => ['images/bundle gaming.jpg'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            ProductBundle::insert([
                [
                    'bundle_product_id' => $bundle->id,
                    'product_id' => $keyboard->id,
                    'quantity' => 1,
                ],
                [
                    'bundle_product_id' => $bundle->id,
                    'product_id' => $headset->id,
                    'quantity' => 1,
                ],
                [
                    'bundle_product_id' => $bundle->id,
                    'product_id' => $mouse->id,
                    'quantity' => 1,
                ],
            ]);
        }


        $laptop = $products->firstWhere('name', 'MSI Gaming Laptop i7 RTX 4060');
        $office = $products->firstWhere('name', 'Microsoft Office 365 Personal (1 Year)');

        if ($laptop && $office) {

            $bundle = Product::create([
                'seller_store_id' => $store->id,
                'name' => 'Work From Home Pro Pack',
                'type' => 'bundle',

                'price' => 23500000,
                'original_price' => 23898000,
                'stock' => 1,
                'brand' => 'WFH Bundle',
                'status' => 'active',
                'is_featured' => true,
                'category_id' => Category::where('name', 'Laptop')->first()->id,
                'SKU' => 'BUNDLE-WFH-' . strtoupper(Str::random(5)),
                'description' => 'Laptop + software resmi untuk produktivitas maksimal',
                'images' => ['images/sample/laptop bundle.jpg'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            ProductBundle::insert([
                [
                    'bundle_product_id' => $bundle->id,
                    'product_id' => $laptop->id,
                    'quantity' => 1,
                ],
                [
                    'bundle_product_id' => $bundle->id,
                    'product_id' => $office->id,
                    'quantity' => 1,
                ],
            ]);
        }

        $this->command->info('Bundle products seeded successfully.');
    }
}
