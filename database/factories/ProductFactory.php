<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\SellerStore;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $price = $this->faker->randomFloat(2, 10, 2000);

        // Ambil seller dan kategori secara aman
        $store = SellerStore::inRandomOrder()->first();
        $category = Category::inRandomOrder()->first();

        return [
            'seller_store_id' => $store?->id,
            'category_id' => $category?->id,
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'original_price' => $price,
            'discount_price' => 0,
            'discount_percentage' => 0,
            'stock' => $this->faker->numberBetween(1, 200),
            'brand' => $this->faker->company(),
            'images' => json_encode([
                $this->faker->imageUrl(640, 480, 'technics'),
            ]),
            'rating' => $this->faker->randomFloat(1, 3.5, 5),
            'review_count' => $this->faker->numberBetween(0, 2000),
            'is_featured' => false,

            // Kolom yang kamu butuhkan
            'variants' => json_encode([
                [
                    'name'  => 'Blue',
                    'value' => 'Blue Color',
                    'price' => 99.99,
                    'stock' => 25,
                ],
                [
                    'name'  => 'Red',
                    'value' => 'Red Color',
                    'price' => 89.99,
                    'stock' => 12,
                ]
            ]),

            'SKU' => strtoupper(Str::random(10)),
            'weight' => $this->faker->numberBetween(1, 3000),
            'diameter' => $this->faker->randomFloat(2, 1, 99),
            'status' => 'active',

            'created_at' => now()->subDays(rand(0, 180)),
            'updated_at' => now(),
        ];
    }
}
