<?php

namespace Database\Seeders;

use App\Models\Seminar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SeminarSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->take(10)->get();
        
        if ($users->isEmpty()) {
            $users = User::take(10)->get();
        }

        if ($users->isEmpty()) {
            $this->command->info('No users found. Creating sample user...');
            $user = User::create([
                'name' => 'Tech Enthusiast',
                'email' => 'tech@gigagears.com',
                'password' => bcrypt('password123'),
                'role' => 'customer',
            ]);
            $users = collect([$user]);
        }

        $seminars = [
            [
                'title' => 'Memaksimalkan Performa Gaming PC',
                'description' => 'Workshop mendalam tentang cara membangun dan mengoptimalkan gaming PC dengan performa maksimal. Kami akan membahas komponen terbaik, cooling system, dan tips overclocking yang aman. Cocok untuk pemula hingga advanced gamers yang ingin mencapai FPS tertinggi.',
                'start_date' => Carbon::now()->addDays(7)->setTime(14, 0),
                'end_date' => Carbon::now()->addDays(7)->setTime(16, 0),
                'location' => 'Tech Hub Jakarta, Gedung A Lantai 3',
                'image_url' => 'images/seminars/gaming_pc.jpg',
                'capacity' => 50,
                'instructor' => 'Ahmad Rizki - Gaming Enthusiast & PC Builder',
                'requirements' => 'Pemahaman dasar tentang komponen PC. Laptop untuk live demo direkomendasikan.',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Smartphone Terbaru 2025: Fitur & Trik',
                'description' => 'Seminar eksklusif mengulas flagship smartphones terbaru dengan teknologi terdepan. Peserta akan mendapatkan hands-on experience dengan berbagai device, membandingkan kamera, processor, dan fitur AI terbaru. Gratis merchandise GigaGears untuk 20 peserta pertama!',
                'start_date' => Carbon::now()->addDays(5)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(5)->setTime(12, 0),
                'location' => 'GigaGears Experience Center, Mall Cyber Surabaya',
                'image_url' => 'images/seminars/smartphone.jpg',
                'capacity' => 80,
                'instructor' => 'Sarah Widjaja - Mobile Tech Reviewer',
                'requirements' => 'Membawa ponsel Anda sendiri untuk live testing. Tempat terbatas!',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Audiophile 101: Memahami Sound Quality',
                'description' => 'Untuk para pecinta audio dan music producer. Workshop ini mengajarkan bagaimana memilih headphones dan speaker yang tepat sesuai kebutuhan. Pembicara akan share tips equalizer, noise cancellation technology, dan budget-friendly recommendations.',
                'start_date' => Carbon::now()->addDays(14)->setTime(15, 0),
                'end_date' => Carbon::now()->addDays(14)->setTime(17, 30),
                'location' => 'Audio Studio GigaGears, Jln. Teknologi No. 45',
                'image_url' => 'images/seminars/headphones.jpg',
                'capacity' => 35,
                'instructor' => 'Budi Santoso - Audio Engineer & Producer',
                'requirements' => 'Passionate tentang musik dan audio quality. Bawa earbuds favorit Anda untuk demo.',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Laptop untuk Productivity: Pilih Mana?',
                'description' => 'Bingung memilih laptop untuk kerja atau kuliah? Seminar ini akan membandingkan Windows, macOS, dan Linux untuk berbagai use case. Kami juga bahas budget optimization, warranty, dan after-sales service dari berbagai brand.',
                'start_date' => Carbon::now()->addDays(21)->setTime(13, 0),
                'end_date' => Carbon::now()->addDays(21)->setTime(15, 0),
                'location' => 'Training Room GigaGears, Bandung Tech Park',
                'image_url' => 'images/seminars/laptop.jpg',
                'capacity' => 60,
                'instructor' => 'Endra Wijaya - IT Consultant & Reviewer',
                'requirements' => 'Sudah memiliki laptop atau planning untuk membeli. Q&A session terbuka untuk semua.',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Smart Home Setup untuk Rumah Modern',
                'description' => 'Ciptakan rumah pintar Anda dengan ekosistem IoT yang terintegrasi. Workshop ini mengcover smart lighting, security system, smart appliances, dan automation yang hemat energi. Live demo dengan 5 setup berbeda akan menginspirasi Anda.',
                'start_date' => Carbon::now()->addDays(10)->setTime(16, 0),
                'end_date' => Carbon::now()->addDays(10)->setTime(18, 0),
                'location' => 'Smart Home Showroom GigaGears, Jakarta Selatan',
                'image_url' => 'images/seminars/smart_home.jpg',
                'capacity' => 45,
                'instructor' => 'Dino Hartono - IoT Specialist',
                'requirements' => 'Membawa smartphone untuk konfigurasi live. Gratis 1 smart bulb untuk early birds!',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Content Creator Gear: Kamera & Lighting untuk Pemula',
                'description' => 'Khusus untuk YouTuber, TikToker, dan streamer pemula. Pelajari teknik lighting yang tepat, setup kamera entry-level yang professional, dan editing tips untuk hasil maksimal. Bonus: workshop singkat tentang SEO YouTube dan growth strategy.',
                'start_date' => Carbon::now()->addDays(18)->setTime(11, 0),
                'end_date' => Carbon::now()->addDays(18)->setTime(13, 30),
                'location' => 'Studio Produksi GigaGears, Medan Creative Hub',
                'image_url' => 'images/seminars/camera_lighting.jpg',
                'capacity' => 40,
                'instructor' => 'Lisa Munandar - Content Creator & Producer',
                'requirements' => 'Kamera atau smartphone yang akan digunakan untuk recording. Portfolio link opsional untuk feedback personal.',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Wearables & Fitness Tech: Menjaga Kesehatan Digital',
                'description' => 'Pelajari bagaimana smartwatch, fitness tracker, dan health gadgets membantu monitor kesehatan Anda. Seminar ini akan compare akurasi berbagai device, integration dengan health apps, dan tips maksimalkan battery life.',
                'start_date' => Carbon::now()->addDays(28)->setTime(14, 30),
                'end_date' => Carbon::now()->addDays(28)->setTime(16, 30),
                'location' => 'Fitness & Wellness Center GigaGears, Surabaya',
                'image_url' => 'images/seminars/fitness.jpg',
                'capacity' => 50,
                'instructor' => 'Dr. Randi Pratama - Health Tech Specialist',
                'requirements' => 'Bawa smartwatch atau fitness tracker untuk live testing. Konsultasi personal tersedia.',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Gaming Laptop Showdown 2025: Mana yang Terbaik?',
                'description' => 'Perbandingan mendetail antara gaming laptops flagship dari semua brand ternama. Kami akan test real gaming performance, thermals, display quality, dan value for money. Dapatkan exclusive bundle deals untuk pembelian di akhir seminar!',
                'start_date' => Carbon::now()->addDays(3)->setTime(12, 0),
                'end_date' => Carbon::now()->addDays(3)->setTime(14, 0),
                'location' => 'Gaming Arena GigaGears, Jakarta Pusat',
                'image_url' => 'images/seminars/gaming_laptop_showdown.jpg',
                'capacity' => 70,
                'instructor' => 'Yulian Chen - Hardware Reviewer & Tech Journalist',
                'requirements' => 'Gamers yang serious tentang gaming laptop investment. Early registration mendapat potongan harga 20%.',
                'status' => 'ongoing',
            ],
            [
                'title' => 'Photography Masterclass: Dari Smartphone ke DSLR',
                'description' => 'Pelajari fotografi profesional dari basic composition hingga advanced lighting technique. Workshop ini cocok untuk photographer pemula yang ingin upgrade ke DSLR atau mirrorless. Kami juga bahas post-processing workflow dan portfolio building.',
                'start_date' => Carbon::now()->subDays(2)->setTime(9, 0),
                'end_date' => Carbon::now()->subDays(2)->setTime(12, 0),
                'location' => 'Photography Studio GigaGears, Yogyakarta',
                'image_url' => 'images/seminars/photography.jpg',
                'capacity' => 30,
                'instructor' => 'Bambang Sutrisno - Professional Photographer',
                'requirements' => 'Bawa kamera Anda (smartphone atau dedicated camera). Tripod optional tapi direkomendasikan.',
                'status' => 'ongoing',
            ],
            [
                'title' => 'Cybersecurity untuk Personal & Home Network',
                'description' => 'Lindungi data pribadi dan device Anda dari cyber threats. Seminar ini mengajarkan password management, VPN usage, malware prevention, dan secure browsing habits. Perfect untuk semua level technical knowledge.',
                'start_date' => Carbon::now()->subDays(5)->setTime(15, 0),
                'end_date' => Carbon::now()->subDays(5)->setTime(17, 0),
                'location' => 'Cybersecurity Lab GigaGears, Bandung',
                'image_url' => 'images/seminars/cybersecurity.jpg',
                'capacity' => 40,
                'instructor' => 'Prof. Hendra Kusuma - Cybersecurity Expert',
                'requirements' => 'Membawa laptop untuk live demo. Antivirus sudah terinstall.',
                'status' => 'ongoing',
            ],
        ];

        foreach ($seminars as $index => $seminarData) {
            $seminar = Seminar::create([
                'title' => $seminarData['title'],
                'description' => $seminarData['description'],
                'start_date' => $seminarData['start_date'],
                'end_date' => $seminarData['end_date'],
                'location' => $seminarData['location'],
                'image_url' => $seminarData['image_url'] ?? null,
                'capacity' => $seminarData['capacity'],
                'instructor' => $seminarData['instructor'],
                'requirements' => $seminarData['requirements'],
                'status' => $seminarData['status'],
            ]);
            
            // Add registrations for each seminar with seat numbers
            $seatNumber = 1;
            $numRegistrations = rand(5, 15);
            $selectedUsers = $users->shuffle()->take($numRegistrations);
            
            foreach ($selectedUsers as $user) {
                try {
                    \App\Models\SeminarRegistration::create([
                        'seminar_id' => $seminar->id,
                        'user_id' => $user->id,
                        'status' => 'registered',
                        'seat_number' => $seatNumber,
                    ]);
                    $seatNumber++;
                } catch (\Exception $e) {
                    // Skip duplicate registrations
                }
            }
        }

        $this->command->info('Seminar seeder completed successfully!');
        $this->command->info('Created ' . count($seminars) . ' seminars with diverse tech topics and registrations.');
    }
}
