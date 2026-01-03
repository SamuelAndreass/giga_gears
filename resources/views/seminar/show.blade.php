@extends('layouts.main')
@section('title', $seminar->title)

@section('header')
    <style>
        .header-wrapper {
            width: 100%;
            height: 90px;
            padding-top: 20px;
            background: #FFFFFF;
            border-bottom: 1px solid #eee;
        }
        .main-navbar {
            width: 1280px;
            max-width: 90%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>

    <div class="header-wrapper">
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            <div class="d-flex" style="gap: 45px; font-size:22px; align-items: center;">
                <div class="d-flex gap-4">
                    <a href="{{ route('dashboard') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none; white-space: nowrap;">About Us</a>
                    @auth
                    <a href="{{ route('orders.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">My Order</a>
                    @endauth
                    <a href="{{ route('community.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Communities</a>
                    <a href="{{ route('seminar.index') }}" style="color: #067CC2; text-decoration: none; white-space: nowrap;">Seminars</a>
                    @auth
                    <a href="{{ route('cart.index') }}" 
                        class="position-relative text-decoration-none text-dark" style="white-space: nowrap;">
                        <i class="bi bi-cart3"></i>
                        @php
                            $cartCount = \App\Models\Cart::where('user_id', Auth::id())->first()?->items()->sum('qty') ?? 0;
                        @endphp
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-6">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    @endauth
                </div>
            </div>
            @auth
            <a href="{{ route('profile.edit') }}" class="d-flex align-items-center justify-content-center" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <div class="d-flex align-items-center" style="gap: 9px;">
                    <span>Profile</span>
                    <img src="{{ asset(Auth::user()->customerProfile?->avatar_path ?? 'images/logo foto profile.png') }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%;">
                </div>
            </a>
            @else
            <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <span>Login</span>
            </a>
            @endauth
        </div>
    </div>
@endsection

@section('content')
<div class="page-container" style="padding: 40px 0;">
    <a href="{{ route('seminar.index') }}" style="color: #067CC2; text-decoration: none; margin-bottom: 20px; display: inline-block;">
        ← Kembali ke Seminar
    </a>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var seatNumber = "{{ session('seat_number') ?? 0 }}";
                if(seatNumber && seatNumber !== '0') {
                    alert('✅ Registrasi Berhasil!\n\nNomor Kursi Anda: ' + seatNumber + '\n\nSilakan catat nomor kursi ini dan tunjukkan saat seminar dimulai.');
                }
            });
        </script>
        <div class="alert alert-success" style="background: #c8e6c9; color: #2e7d32; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; text-align: center; font-size: 16px;">
            ✅ Berhasil Terdaftar! Nomor Kursi: <span style="font-size: 24px; color: #067CC2;">{{ session('seat_number') ?? 'N/A' }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #ffcdd2; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
        <div>
            <div style="background: linear-gradient(135deg, #067CC2, #4EDAFE); border-radius: 10px; height: 400px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin-bottom: 30px;">
                @if($seminar->image_url)
                    <img src="{{ $seminar->image_url }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                @else
                    📚 {{ $seminar->title }}
                @endif
            </div>

            <h1 style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin-bottom: 15px;">
                {{ $seminar->title }}
            </h1>

            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <p style="color: #666; font-size: 14px; margin: 0;">📅 Tanggal Mulai</p>
                        <p style="color: #1a1a1a; font-size: 16px; font-weight: 600; margin: 5px 0;">
                            {{ $seminar->start_date->format('d M Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p style="color: #666; font-size: 14px; margin: 0;">📅 Tanggal Berakhir</p>
                        <p style="color: #1a1a1a; font-size: 16px; font-weight: 600; margin: 5px 0;">
                            {{ $seminar->end_date->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <p style="color: #666; font-size: 14px; margin: 0;">📍 Lokasi</p>
                        <p style="color: #1a1a1a; font-size: 16px; font-weight: 600; margin: 5px 0;">{{ $seminar->location }}</p>
                    </div>
                    <div>
                        <p style="color: #666; font-size: 14px; margin: 0;">👥 Kapasitas</p>
                        <p style="color: #1a1a1a; font-size: 16px; font-weight: 600; margin: 5px 0;">
                            {{ $seminar->registered_count }} / {{ $seminar->capacity }}
                        </p>
                    </div>
                </div>
            </div>

            @if($seminar->instructor)
            <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 10px;">👨‍🏫 Instruktur</h3>
                <p style="color: #1a1a1a; margin: 0;">{{ $seminar->instructor }}</p>
            </div>
            @endif

            <div style="margin-bottom: 30px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 15px;">📝 Deskripsi</h3>
                <p style="color: #666; line-height: 1.6;">{{ $seminar->description }}</p>
            </div>

            @if($seminar->requirements)
            <div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 15px;">✅ Persyaratan</h3>
                <p style="color: #666; line-height: 1.6;">{{ $seminar->requirements }}</p>
            </div>
            @endif
        </div>

        <div>
            <div style="background: #fff; border: 1px solid #E5E5E5; border-radius: 10px; padding: 20px; position: sticky; top: 20px;">
                <div style="background: linear-gradient(135deg, #067CC2, #4EDAFE); padding: 15px; border-radius: 8px; color: white; margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 14px;">Status: <strong>{{ ucfirst($seminar->status) }}</strong></p>
                </div>

                @if($seminar->registered_count >= $seminar->capacity)
                    <button disabled style="background: #ccc; color: #666; padding: 12px; border: none; border-radius: 6px; width: 100%; font-weight: 600; cursor: not-allowed;">
                        Penuh
                    </button>
                @elseif($isRegistered)
                    <div style="background: linear-gradient(135deg, #c8e6c9, #a5d6a7); color: #2e7d32; padding: 20px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: 600;">
                        ✅ Anda Sudah Terdaftar
                    </div>
                    <div style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); padding: 20px; border-radius: 8px; margin-bottom: 15px; text-align: center; border: 2px solid #067CC2;">
                        <p style="color: #067CC2; font-size: 12px; margin: 0 0 8px; font-weight: 600;">NOMOR KURSI ANDA</p>
                        <p style="color: #067CC2; font-size: 48px; font-weight: 700; margin: 0;">{{ $registration->seat_number ?? 'N/A' }}</p>
                        <p style="color: #555; font-size: 11px; margin: 10px 0 0; font-style: italic;">Simpan dan tunjukkan saat seminar dimulai</p>
                    </div>
                    <form action="{{ route('seminar.unregister', $seminar->id) }}" method="POST" style="margin-bottom: 10px;">
                        @csrf
                        <button type="submit" style="background: #ff6b6b; color: white; padding: 12px; border: none; border-radius: 6px; width: 100%; font-weight: 600; cursor: pointer;">
                            Batalkan Registrasi
                        </button>
                    </form>
                @else
                    @auth
                        <form action="{{ route('seminar.register', $seminar->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: linear-gradient(135deg, #067CC2, #4EDAFE); color: white; padding: 12px; border: none; border-radius: 6px; width: 100%; font-weight: 600; cursor: pointer;">
                                Daftar Sekarang
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" style="display: block; text-decoration: none;">
                            <button style="background: linear-gradient(135deg, #067CC2, #4EDAFE); color: white; padding: 12px; border: none; border-radius: 6px; width: 100%; font-weight: 600; cursor: pointer;">
                                Login untuk Mendaftar
                            </button>
                        </a>
                    @endauth
                @endif

                <div style="border-top: 1px solid #E5E5E5; margin-top: 20px; padding-top: 20px;">
                    <p style="font-size: 12px; color: #999; margin: 0;">
                        {{ $seminar->remaining_capacity }} tempat tersisa
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
