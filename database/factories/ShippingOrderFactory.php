<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ShippingOrder;
use App\Models\Order;
use App\Models\Shipping;

class ShippingOrderFactory extends Factory
{
    protected $model = Shipping::class;

    public function definition()
    {
        $shipping = ShippingOrder::inRandomOrder()->first();
        return [
            'order_id' => Order::inRandomOrder()->first()->id ?? null,
            'shipping_id' => $shipping->id ?? null,
            'tracking_number' => 'TRK' . strtoupper($this->faker->bothify('########')),
            'shipped_at' => $this->faker->optional()->dateTimeBetween('-10 days', 'now'),
            'estimated_arrival_date' => now()->addDays($this->faker->numberBetween(2,7)),
            'created_at' => now(),
        ];
    }
}
