<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;

class CartSeeder extends Seeder
{
    public function run()
    {
        $customers = User::where('role', 'customer')->take(8)->get();
        if ($customers->isEmpty()) {
            $customers = User::factory(6)->create(['role' => 'customer']);
        }

        // pastikan ada produk
        $products = Product::inRandomOrder()->take(60)->get();
        if ($products->isEmpty()) {
            $products = Product::factory(40)->create();
        }

        foreach ($customers as $cust) {
            $cart = Cart::create([
                'user_id' => $cust->id,
                'total_price' => 0,
                'status' => 'active',
            ]);

            $items = $products->random(rand(1, 6));

            $total = 0;
            foreach ($items as $p) {
                $qty = rand(1, 3);
                $unit = $p->price;
                $subtotal = $unit * $qty;

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $p->id,
                    'qty' => $qty,
                    'price' => $unit,
                    'price_snapshot' => $unit,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $cart->update(['total_price' => $total]);
        }
    }
}
