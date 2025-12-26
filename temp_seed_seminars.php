<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Seminar;

$seminars = [
    [
        'title' => 'Web Development Fundamentals',
        'description' => 'Belajar dasar-dasar pengembangan web dengan HTML, CSS, dan JavaScript. Cocok untuk pemula yang ingin memulai karir di bidang web development.',
        'location' => 'Jakarta Convention Center, Hall A',
        'capacity' => 50,
        'instructor' => 'Budi Hartanto',
        'requirements' => 'Dasar pengetahuan komputer',
        'status' => 'upcoming'
    ],
    [
        'title' => 'Mobile App Development with Flutter',
        'description' => 'Panduan lengkap membuat aplikasi mobile cross-platform dengan Flutter. Pelajari cara build app profesional untuk iOS dan Android.',
        'location' => 'Tech Hub Surabaya',
        'capacity' => 30,
        'instructor' => 'Siti Nurhaliza',
        'requirements' => 'Pemahaman OOP dan programming basics',
        'status' => 'upcoming'
    ],
    [
        'title' => 'Cloud Computing Essentials',
        'description' => 'Mengenal cloud computing dan AWS untuk scalability. Workshop interaktif dengan hands-on project menggunakan AWS services.',
        'location' => 'Bandung IT Center',
        'capacity' => 40,
        'instructor' => 'Ahmad Rifaldi',
        'requirements' => 'Pengetahuan Linux dasar',
        'status' => 'upcoming'
    ]
];

foreach ($seminars as $data) {
    if (!Seminar::where('title', $data['title'])->exists()) {
        Seminar::create(array_merge($data, [
            'start_date' => now()->addDays(5 + rand(0, 10))->setTime(10 + rand(0, 8), 0),
            'end_date' => now()->addDays(5 + rand(0, 10))->setTime(12 + rand(0, 4), 0),
        ]));
    }
}

echo "✓ Seminars seeded!\n";
