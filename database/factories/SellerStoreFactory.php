<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SellerStore;
use App\Models\User;

class SellerStoreFactory extends Factory
{
    protected $model = SellerStore::class;

    public function definition()
    {
        return [
            'user_id' => User::where('role','seller')->inRandomOrder()->first()->id ?? null,
            'store_name' => $this->faker->company(),
            'store_logo' => $this->faker->imageUrl(200,200,'business'),
            'store_phone' => $this->faker->phoneNumber(),
            'status' => 'active',
            'created_at' => now()->subDays($this->faker->numberBetween(0, 300)),
        ];
    }
}
