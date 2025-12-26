<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition()
    {
        // Pastikan ada cart dan product; kalau tidak, factory akan membuatnya
        $cart = Cart::inRandomOrder()->first() ?? Cart::factory();
        $product = Product::inRandomOrder()->first() ?? Product::factory();

        // ambil current product price snapshot
        $unitPrice = $product->original_price ?? $this->faker->randomFloat(2, 10, 500);

        $qty = $this->faker->numberBetween(1, 3);

        return [
            'cart_id' => $cart instanceof Cart ? $cart->id : $cart,
            'product_id' => $product instanceof Product ? $product->id : $product,
            'qty' => $qty,
            'price' => $unitPrice,
            'subtotal' => $unitPrice * $qty,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
