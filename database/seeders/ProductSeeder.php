<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerStore;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $store = SellerStore::first();
        if (! $store) {
            $this->command->warn('No seller store found. Skipping product seeder.');
            return;
        }

        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        $total = 20;
        for ($i = 0; $i < $total; $i++) {
            $product = Product::factory()->create([
                'seller_store_id' => $store->id,
            ]);

            $attach = $categories->random(rand(1, min(3, $categories->count())))->pluck('id')->toArray();
            $product->categories()->sync($attach);
        }
    }
}
