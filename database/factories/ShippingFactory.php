<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Shipping;

class ShippingFactory extends Factory
{
    protected $model = Shipping::class;

    public function definition()
    {
        $services = ['REG','ECO','SDS','Cargo','custom'];
        $service = $this->faker->randomElement($services);
        return [
            'name' => $this->faker->company . ' Express',
            'service_type' => $service,
            'base_rate' => $this->faker->randomFloat(2, 5000, 30000),
            'per_kg' => $this->faker->randomFloat(2, 3000, 15000),
            'min_delivery_days' => $this->faker->numberBetween(1,3),
            'max_delivery_days' => $this->faker->numberBetween(3,7),
            'coverage' => 'Domestic (Indonesia)',
            'created_at' => now(),
        ];
    }
}
