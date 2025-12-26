<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Buat kategori default / demo
        $categories = [
            ['name'=>'Laptop','slug'=>'laptop','description'=>'Berbagai jenis laptop untuk kerja, belajar, dan gaming'],
            ['name'=>'Smartphone','slug'=>'smartphone','description'=>'Ponsel pintar terbaru dari berbagai merek'],
            ['name'=>'Accessories','slug'=>'accessories','description'=>'Aksesoris pendukung gadget seperti charger, headset, dan casing'],
            ['name'=>'Gaming','slug'=>'gaming','description'=>'Perangkat dan aksesoris gaming untuk PC, konsol, dan mobile'],
            ['name'=>'Mac','slug'=>'mac','description'=>'Produk Apple Mac seperti MacBook, iMac, dan aksesorisnya'],
        ];


        foreach ($categories as $c) {
            Category::firstOrCreate(['slug' => $c['slug']], $c);
        }

        // Tambahan: buat kategori random jika mau
        Category::factory()->count(5)->create();
    }
}
