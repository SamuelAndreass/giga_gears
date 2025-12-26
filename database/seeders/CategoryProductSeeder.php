<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerStore;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CategoryProductSeeder extends Seeder
{
    private function storeSampleImage($relativePath)
    {
        $relativePath = ltrim($relativePath, '/');

        $source = database_path('seeders/assets/' . $relativePath);

        if (!File::exists($source)) {
            throw new \Exception("Seeder image not found: " . $source);
        }

        $extension = File::extension($source);
        $fileName = uniqid('product_') . '.' . $extension;
        $targetPath = 'products/' . $fileName;

        Storage::disk('public')->put(
            $targetPath,
            File::get($source)
        );

        return $targetPath;
    }


    public function run()
    {
        $categories = [
            'Smartphone', 'Laptop', 'Accessories', 'Gaming', 'Mac', 'Software'
        ];

        foreach ($categories as $catName) {
            Category::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName), 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $sellerUser = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Demo Seller',
                'password' => bcrypt('password'),
                'role' => 'seller',
                'is_seller' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );


        $store = SellerStore::firstOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'store_name' => 'Demo Store',
                'status' => 'active',
                'store_logo' => $this->storeSampleImage('images/sample/logo.jpg'),
                'store_banner' => $this->storeSampleImage('images/sample/banner.png'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        

        $samples = [
            [
                'name' => 'Logitech G Pro X Headset',
                'category' => 'Accessories',
                'price_idr' => 1999000, // Rp 1.999.000
                'stock' => 45,
                'image' => '/images/sample/headset img.jpg',
                'specs' => [
                    'type' => 'Wired',
                    'connection' => 'USB & 3.5mm',
                    'microphone' => 'Detachable, ClearCast',
                    'frequency_response' => '20Hz - 20kHz',
                    'color' => 'Black',
                ],
            ],
            [
                'name' => 'MSI Gaming Laptop i7 RTX 4060',
                'category' => 'Laptop',
                'price_idr' => 22999000, // Rp 22.999.000
                'stock' => 12,
                'image' => '/images/sample/product_msi.png',
                'specs' => [
                    'cpu' => 'Intel Core i7 (13th Gen)',
                    'gpu' => 'NVIDIA GeForce RTX 4060',
                    'ram' => '16GB DDR5',
                    'storage' => '512GB NVMe SSD',
                    'screen' => '15.6" FHD 144Hz',
                ],
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 LTE',
                'category' => 'Smartphone',
                'price_idr' => 12999000, // Rp 12.999.000
                'stock' => 20,
                'image' => '/images/sample/samsung.png',
                'specs' => [
                    'display' => '11" LTPS',
                    'ram' => '8GB',
                    'storage' => '128GB',
                    'network' => 'LTE',
                    'battery' => '8000 mAh',
                ],
            ],
            [
                'name' => 'Razer BlackWidow V3 Mechanical Keyboard',
                'category' => 'Gaming',
                'price_idr' => 2299000, // Rp 2.299.000
                'stock' => 12,
                'image' => '/images/sample/keyboard.jpg',
                'specs' => [
                    'switch' => 'Razer Green (Tactile & Clicky)',
                    'layout' => 'Full-size (104 keys)',
                    'lighting' => 'Chroma RGB',
                    'connection' => 'USB',
                ],
            ],
            [
                'name' => 'MacBook Pro 14-inch (M2 Pro)',
                'category' => 'Mac',
                'price_idr' => 33999000, // Rp 33.999.000
                'stock' => 8,
                'image' => '/images/sample/macbook.jpg',
                'specs' => [
                    'chip' => 'Apple M2 Pro',
                    'ram' => '16GB',
                    'storage' => '512GB SSD',
                    'display' => '14.2" Liquid Retina XDR',
                    'battery' => 'Up to 17 hours',
                ],
            ],
            [
                'name' => 'Adobe Photoshop Single-Month License',
                'category' => 'Software',
                'price_idr' => 300000, // Rp 300.000
                'stock' => 9999,
                'image' => '/images/sample/pshop.png',
                'specs' => [
                    'type' => 'Subscription',
                    'platform' => 'Windows / macOS',
                    'duration' => '1 month',
                ],
            ],
            [
                'name' => 'Sony WH-1000XM5 Wireless Headphones',
                'category' => 'Accessories',
                'price_idr' => 5499000,
                'stock' => 18,
                'image' => 'images/sample/sony_xm5.jpg',
                'specs' => [
                    'type' => 'Wireless',
                    'noise_cancelling' => 'Active',
                    'battery' => 'Up to 30 hours',
                    'connection' => 'Bluetooth',
                ],
            ],
            [
                'name' => 'Anker PowerLine USB-C Cable',
                'category' => 'Accessories',
                'price_idr' => 159000,
                'stock' => 120,
                'image' => 'images/sample/anker_cable.jpg',
                'specs' => [
                    'length' => '1.8m',
                    'power' => '60W',
                    'material' => 'Nylon braided',
                ],
            ],
            [
                'name' => 'ASUS ROG Zephyrus G14 Ryzen 9',
                'category' => 'Laptop',
                'price_idr' => 28999000,
                'stock' => 7,
                'image' => 'images/sample/rog_g14.jpg',
                'specs' => [
                    'cpu' => 'Ryzen 9 7940HS',
                    'gpu' => 'RTX 4070',
                    'ram' => '32GB',
                    'storage' => '1TB NVMe SSD',
                    'screen' => '14" QHD 165Hz',
                ],
            ],
            [
                'name' => 'iPhone 15 Pro 256GB',
                'category' => 'Smartphone',
                'price_idr' => 21999000,
                'stock' => 25,
                'image' => 'images/sample/iphone_15_pro.jpg',
                'specs' => [
                    'chip' => 'A17 Pro',
                    'storage' => '256GB',
                    'camera' => '48MP',
                    'display' => '6.1" OLED',
                ],
            ],
            [
                'name' => 'PlayStation 5 Digital Edition',
                'category' => 'Gaming',
                'price_idr' => 7499000,
                'stock' => 14,
                'image' => 'images/sample/ps5_digital.jpg',
                'specs' => [
                    'storage' => '825GB SSD',
                    'resolution' => '4K',
                    'fps' => 'Up to 120fps',
                ],
            ],
            [
                'name' => 'Microsoft Office 365 Personal (1 Year)',
                'category' => 'Software',
                'price_idr' => 899000,
                'stock' => 9999,
                'image' => 'images/sample/office_365.jpg',
                'specs' => [
                    'platform' => 'Windows / macOS',
                    'duration' => '12 months',
                    'license' => '1 user',
                ],
            ],
        ];

        foreach ($samples as $s) {
            $cat = Category::where('name', $s['category'])->first();
            if (! $cat) {

                $cat = Category::create([
                    'name' => $s['category'],
                    'slug' => Str::slug($s['category']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $slug = Str::slug($s['name']);

            $exists = Product::where('name', $s['name'])->where('seller_store_id', $store->id)->first();
            if ($exists) {
                continue;
            }

            $imagePath = $this->storeSampleImage($s['image']);

            Product::create([
                'seller_store_id' => $store->id,
                'category_id' => $cat->id,
                'name' => $s['name'],

                'description' => $s['name'] . ' — produk demo. Stok: ' . $s['stock'],
                'original_price' => $s['price_idr'],
                'discount_price' => 0,
                'discount_percentage' => 0,
                'stock' => $s['stock'],
                'brand' => 'DemoBrand',
                'images' => [$imagePath],
                'rating' => 4.5,
                'review_count' => 10,
                'is_featured' => false,
                'variants' => json_encode([
                    ['name' => 'Default', 'value' => 'Standard', 'price' => $s['price_idr'], 'stock' => $s['stock']]
                ]),
                'SKU' => strtoupper(Str::random(10)),
                'weight' => 1000,
                'diameter' => 0,
                'status' => 'active',
                'specifications' => json_encode($s['specs']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

    }
}
