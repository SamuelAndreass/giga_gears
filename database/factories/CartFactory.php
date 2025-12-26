<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use App\Models\User;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        // ambil user customer, jika tidak ada maka buat
        $user = User::where('role', 'customer')->inRandomOrder()->first()
            ?? User::factory()->create(['role' => 'customer']);

        return [
            'user_id' => $user->id,
            'total_price' => 0, // akan diupdate oleh seeder jika perlu
            'status' => 'active',
            'created_at' => now()->subDays($this->faker->numberBetween(0, 30)),
            'updated_at' => now(),
        ];
    }
}
