<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $user = User::where('role','customer')->inRandomOrder()->first();
        $paymentMethods = ['Credit Card','Bank Transfer','COD','E-Wallet'];
        $status = $this->faker->randomElement(['pending','paid','processing','completed','refunded','cancelled']);
        return [
            'user_id' => $user->id ?? null,
            'order_number' => 'ORD-' . strtoupper($this->faker->bothify('########')),
            'total_amount' => 0, // will update in seeder after creating items
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'status' => $status,
            'created_at' => now()->subDays($this->faker->numberBetween(0, 120)),
            'updated_at' => now(),
        ];
    }
}
