<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipping;

class ShippingSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            ['name'=>'JNE', 'service_type'=>'REG', 'base_rate'=>12000, 'per_kg'=>8000, 'min'=>2, 'max'=>4],
            ['name'=>'J&T', 'service_type'=>'ECO', 'base_rate'=>9000, 'per_kg'=>7000, 'min'=>3, 'max'=>6],
            ['name'=>'SiCepat', 'service_type'=>'SDS', 'base_rate'=>30000, 'per_kg'=>15000, 'min'=>1, 'max'=>1],
            ['name'=>'CargoX', 'service_type'=>'Cargo', 'base_rate'=>50000, 'per_kg'=>20000, 'min'=>5, 'max'=>10],
            ['name'=>'LocalExpress', 'service_type'=>'custom', 'custom_service'=>'Local NextDay', 'base_rate'=>18000, 'per_kg'=>10000, 'min'=>1, 'max'=>2],
        ];

        foreach ($methods as $m) {
            Shipping::create([
                'name' => $m['name'],
                'service_type' => $m['service_type'],
                'custom_service' => $m['custom_service'] ?? null,
                'base_rate' => $m['base_rate'],
                'per_kg' => $m['per_kg'],
                'min_delivery_days' => $m['min'],
                'max_delivery_days' => $m['max'],
                'coverage' => 'Domestic (Indonesia)',
            ]);
        }
    }
}
