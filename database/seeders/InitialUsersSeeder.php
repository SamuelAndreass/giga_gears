<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SellerStore;

class InitialUsersSeeder extends Seeder
{
    public function run()
    {
        // 1) Admin
        $admin = User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $customer = User::create([
            'name' => 'Customer Demo',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $seller = User::create([
            'name' => 'Seller Demo',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'role' => 'seller',
            'is_seller' => true,
            'email_verified_at' => now(),
        ]);

        \App\Models\CustomerProfile::firstOrCreate([
            'user_id' => $seller->id
        ]);

        $store = SellerStore::create([
            'user_id' => $seller->id,
            'store_name' => 'Demo Seller Store',
            'store_address' => 'Jl. Demo No.1, Jakarta',
        ]);

        // Tambah beberapa customers untuk data dummy peserta seminar
        $customerNames = [
            ['Budi Santoso', 'budi.santoso@email.com'],
            ['Ani Wijaya', 'ani.wijaya@email.com'],
            ['Chandra Kusuma', 'chandra.k@email.com'],
            ['Dina Hermawan', 'dina.h@email.com'],
            ['Eko Pratama', 'eko.p@email.com'],
            ['Farah Aziz', 'farah.aziz@email.com'],
            ['Gita Maulida', 'gita.m@email.com'],
            ['Hendra Wijaya', 'hendra.w@email.com'],
            ['Intan Sari', 'intan.sari@email.com'],
            ['Joko Susilo', 'joko.s@email.com'],
            ['Kirana Putri', 'kirana.p@email.com'],
            ['Lina Marlina', 'lina.m@email.com'],
            ['Mitra Buana', 'mitra.b@email.com'],
            ['Nadia Harahap', 'nadia.h@email.com'],
            ['Oscar Wijaya', 'oscar.w@email.com'],
        ];

        foreach ($customerNames as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }
    }
}
