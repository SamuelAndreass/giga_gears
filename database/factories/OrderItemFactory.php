<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        $qty = $this->faker->numberBetween(1,3);
        $unit = $product->price ?? $this->faker->randomFloat(2, 10, 200);
        return [
            'order_id' => Order::factory(), // allow override in seeder
            'product_id' => $product->id,
            'qty' => $qty,
            'unit_price' => $unit,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
