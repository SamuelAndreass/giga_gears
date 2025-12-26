<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->name();
        $email = $this->faker->unique()->safeEmail();

        return [
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default password: "password"
            'role' => 'customer', // default role, override when needed
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
            'email' => $attributes['email'] ?? 'admin@example.com',
        ]);
    }

    public function seller()
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'seller',
            'is_seller' => true,
        ]);
    }

    public function customer()
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'customer',
        ]);
    }
}
