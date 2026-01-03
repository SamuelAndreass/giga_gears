@extends('layouts.main')
@section('title', 'Seminar Saya')

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
        .registration-card {
            background: #fff;
            border: 1px solid #E5E5E5;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: 1fr 150px;
            gap: 20px;
            align-items: center;
        }
        .registration-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .seat-badge {
            background: linear-gradient(135deg, #067CC2, #4EDAFE);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 700;
        }
        .seat-number {
            font-size: 48px;
            font-weight: 700;
        }
        .seat-label {
            font-size: 12px;
            opacity: 0.9;
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

    <h1 style="font-size: 32px; font-weight: 700; color: #1a1a1a; margin: 20px 0;">Seminar Saya</h1>

    @if($registrations->isEmpty())
        <div style="text-align: center; padding: 60px 20px; background: #f5f5f5; border-radius: 10px;">
            <p style="font-size: 18px; color: #999; margin-bottom: 20px;">Anda belum terdaftar di seminar manapun</p>
            <a href="{{ route('seminar.index') }}" style="display: inline-block; background: linear-gradient(135deg, #067CC2, #4EDAFE); color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                Jelajahi Seminar
            </a>
        </div>
    @else
        <div style="display: grid; gap: 15px;">
            @foreach($registrations as $registration)
                <div class="registration-card">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0 0 10px;">
                            {{ $registration->seminar->title }}
                        </h3>
                        <p style="color: #666; margin: 5px 0; font-size: 14px;">
                            📅 {{ $registration->seminar->start_date->format('d M Y H:i') }}
                        </p>
                        <p style="color: #666; margin: 5px 0; font-size: 14px;">
                            📍 {{ $registration->seminar->location }}
                        </p>
                        <p style="color: #999; margin: 10px 0 0; font-size: 13px;">
                            Status: <strong style="color: #2e7d32;">{{ ucfirst($registration->status) }}</strong>
                        </p>
                    </div>
                    <div class="seat-badge">
                        <div class="seat-label">Nomor Kursi</div>
                        <div class="seat-number">{{ $registration->seat_number ?? 'N/A' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
