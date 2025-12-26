<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\ShippingOrder;
use Illuminate\Support\Arr;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $customers = User::where('role','customer')->get();
        $shippings = Shipping::all();

        foreach ($customers as $cust) {
            // create random number of orders
            $num = rand(1,4);
            for ($i=0;$i<$num;$i++){
                $order = Order::create([
                    'user_id' => $cust->id,
                    'order_code' => 'ORD-'.strtoupper(\Illuminate\Support\Str::random(8)),
                    'total_amount' => 0,
                    'status' => Arr::random(['pending','processing','completed']),
                    'ordered_at' => now()->subDays(rand(0,60)),
                ]);

                // choose some products (simulate multi-seller)
                $products = Product::inRandomOrder()->take(rand(1,4))->get();

                $subtotal = 0;
                foreach ($products as $p) {
                    $qty = rand(1,2);
                    $unit = $p->original_price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $p->id,
                        'qty' => $qty,
                        'price' => $unit,
                        'subtotal' => $unit * $qty,
                    ]);
                    $subtotal += ($unit * $qty);
                }

                // update order total (you may want to add shipping, tax)
                $order->total_amount = $subtotal;
                $order->save();

                // create shipping_order entry
                $ship = $shippings->random();
                ShippingOrder::create([
                    'order_id' => $order->id,
                    'shipping_id' => $ship->id,
                    'tracking_number' => 'TRK'.strtoupper(\Illuminate\Support\Str::random(10)),
                    'estimated_arrival_date' => now()->addDays(rand($ship->min_delivery_days, $ship->max_delivery_days)),
                    'shipping_date' => rand(0,1) ? now()->subDays(rand(0,3)) : null,
                ]);
            }
        }
    }
}
