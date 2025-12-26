@extends('layouts.main')
@section('title', 'Seminar & Workshop')

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
        .hero-gradient {
            background: linear-gradient(87.6deg, #ffffff -10.06%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.69) 95.43%);
            padding: 60px 0;
            margin-top: 20px;
        }
        .seminar-card {
            background: #fff;
            border: 1px solid #E5E5E5;
            border-radius: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            padding: 20px;
        }
        .seminar-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        .seminar-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #067CC2, #4EDAFE);
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
        .badge-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .badge-upcoming { background: #e3f2fd; color: #067CC2; }
        .badge-ongoing { background: #c8e6c9; color: #2e7d32; }
        .seminar-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 10px 0;
        }
        .seminar-info {
            font-size: 13px;
            color: #666;
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-register {
            background: linear-gradient(135deg, #067CC2, #4EDAFE);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            margin-top: 15px;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #056ba8, #3ec8eb);
        }
        .capacity-info {
            font-size: 12px;
            color: #ff6b6b;
            margin-top: 10px;
        }
    </style>

    <div class="header-wrapper">
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            <div class="d-flex" style="gap: 45px; font-size:22px; align-items: center;">
                <div class="d-flex gap-3">
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

    <div class="hero-gradient">
        <div class="page-container">
            <h1 style="font-size: 48px; color: #000; margin: 0; font-weight: 700;">Workshop & Seminar</h1>
            <p style="font-size: 18px; color: #666; margin: 10px 0 0;">Tingkatkan skill dan pengetahuan Anda dengan mengikuti seminar dan workshop kami</p>
        </div>
    </div>
@endsection

@section('content')
<div class="page-container" style="padding: 40px 0;">
    @if(session('success'))
        <div class="alert alert-success" style="background: #c8e6c9; color: #2e7d32; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger" style="background: #ffcdd2; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        @foreach($seminars as $seminar)
            <div class="seminar-card">
                <div class="seminar-image">
                    @if($seminar->image_url)
                        <img src="{{ $seminar->image_url }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                    @else
                        📚 Workshop
                    @endif
                </div>

                <div class="badge-status badge-{{ $seminar->status }}">
                    {{ ucfirst($seminar->status) }}
                </div>

                <div class="seminar-title">{{ $seminar->title }}</div>

                <div class="seminar-info">
                    📅 {{ $seminar->start_date->format('d M Y') }}
                </div>

                <div class="seminar-info">
                    🕐 {{ $seminar->start_date->format('H:i') }} - {{ $seminar->end_date->format('H:i') }}
                </div>

                <div class="seminar-info">
                    📍 {{ $seminar->location }}
                </div>

                @if($seminar->instructor)
                <div class="seminar-info">
                    👨‍🏫 {{ $seminar->instructor }}
                </div>
                @endif

                <div class="capacity-info">
                    Peserta: {{ $seminar->registered_count }} / {{ $seminar->capacity }}
                </div>

                <a href="{{ route('seminar.show', $seminar->id) }}" style="display: inline-block; width: 100%; text-align: center;">
                    <button class="btn-register">Lihat Detail</button>
                </a>
            </div>
        @endforeach
    </div>

    @if($seminars->isEmpty())
        <div style="text-align: center; padding: 60px 20px;">
            <p style="font-size: 18px; color: #999;">Belum ada seminar yang tersedia</p>
        </div>
    @endif
</div>
@endsection
