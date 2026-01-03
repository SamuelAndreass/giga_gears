@extends('layouts.main')
@section('title', 'Home')



{{-- ================= HEADER ================= --}}
@section('header')
<style>
    .header-wrapper {
        width: 100%;
        height: 427px;
        position: relative;
        background-image: linear-gradient(87.6deg, #ffffff -10.06%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.69) 95.43%), 
            url("{{ asset('images/hero-bg.png') }}");
        background-size: cover;
        background-position: center;
        margin-top: 93px;
    }

    .main-navbar {
        width: 1280px;
        max-width: 90%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: -95px;
    }

    .main-navbar a {
        color: #000;
        font-size: 25px;
        text-decoration: none;
    }

    .main-navbar a.active {
        color: #067CC2;
    }

    .hero-content {
        width: 789px;
        max-width: 80%;
        margin: 0 auto;
        text-align: center;
        padding-top: 60px;
    }

    .hero-content h1 {
        font-family: 'Chakra Petch', sans-serif;
        font-weight: 700;
        font-size: 56px;
        color: #fff;
    }

    .hero-content p {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: 22px;
        color: #fff;
    }
</style>

<div class="header-wrapper">
    {{-- NAVBAR --}}
    <div class="main-navbar-wrapper bg-white pt-4 w-100">
        <div class="page-container main-navbar d-flex justify-content-between align-items-center mx-auto">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197">
            <div class="d-flex" style="gap: 45px; font-size:22px; align-items: center;">
                <div class="d-flex gap-4">
                    <a href="{{ route('dashboard') }}" style="color: #067CC2; text-decoration: none; white-space: nowrap;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none; white-space: nowrap;">About Us</a>
                    @auth
                    <a href="{{ route('orders.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">My Order</a>
                    @endauth
                    <a href="{{ route('community.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Communities</a>
                    <a href="{{ route('seminar.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Seminars</a>
                    @auth
                    <a href="{{ route('cart.index') }}" 
                        class="position-relative text-decoration-none text-dark" style="white-space: nowrap;">
                        <i class="bi bi-cart3"></i>
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

    {{-- HERO --}}
    <div class="hero-content">
    <h1>Power Up with GigaGears 🚀</h1>
    <p>Your ultimate destination for gadgets, digital products, and tech innovation.</p>

    <div class="d-flex justify-content-center" style="gap: 20px;">
        <a href="/products" class="btn btn-info text-white">Shop Now</a>
            @php
                $user = Auth::user();
                $store = $user?->sellerStore;
            @endphp

            @if(!$user->is_seller && $store && $store->status !== 'active')
                <form action="{{ route('become.seller.reactivate') }}" method="POST" style="display:inline">
                    @csrf
                    <input type="hidden" name="action" value="reactivate">
                    <button type="submit" class="btn btn-warning text-white">Reactivate Store</button>
                </form>
            @else
                <a href="{{ route('become.seller.page') }}" class="btn btn-primary">Become Seller</a>
            @endif
        </div>
    </div>
</div>
@endsection


{{-- ================= MAIN CONTENT ================= --}}
@section('content')
<div class="container my-5">
    {{-- CATEGORY SECTION --}}
    <div class="d-flex justify-content-between flex-wrap gap-5">
        @foreach ($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->id]) }}"
            class="text-center category-card"
            style="flex: 1 1 150px; text-decoration:none; color:inherit;">
                <div style="height: 174px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                    <img src="{{ asset('images/icon-' . strtolower($category->name) . '.png') }}"
                        alt="{{ $category->name }}"
                        style="max-height: 50%;">
                </div>
                <h4 style="font-family:'Chakra Petch',sans-serif;font-weight:700;font-size:24px;">{{ $category->name }}</h4>
            </a>
        @endforeach 
    </div>


    {{-- PRODUCTS SECTION --}}
    <div class="mt-5 text-center">
        <h2 style="font-family:'Chakra Petch',sans-serif;font-weight:700;font-size:46px;">✨ Latest Products</h2>
    </div>

    <div class="d-flex justify-content-between flex-wrap gap-4 mt-4">
        @foreach ($products as $product)
            <a href="{{ route('product.detail', $product->id) }}" 
            class="card p-3 text-decoration-none text-dark" 
            style="width: 32%; border:none; transition: transform 0.2s;">
                 @php
                    $image = $product->images[0] ?? null;
                @endphp
                <div class="text-center" style="height:300px;">
                    <img src="{{ asset('storage/' . ($image ?? 'no-image.png')) }}" alt="{{ $product->name }}" 
                    style="width:100%; height:300px; object-fit:contain;">
                </div>

                <h4 class="mt-3" style="font-family:'Chakra Petch',sans-serif;font-weight:700;">
                    {{ $product->name }}
                </h4>
                <p style="font-family:'Montserrat',sans-serif;font-size:24px;color:#000000;">
                    Rp. {{ number_format($product->price, 2) }}
                </p>
            </a>
        @endforeach
    </div>


    @if(isset($bundles) && $bundles->count())
        <div class="mt-5 text-center">
            <h2>🔥 Bundle Deals</h2>
            <p>Best-value bundles featuring top product combinations.</p>
        </div>

        <div class="d-flex justify-content-center flex-wrap gap-4 mt-4">
            @foreach ($bundles as $bundle)

            <div class="card p-3 text-decoration-none text-dark" style="width:32%; border: none;">
                <img src="{{ asset('storage/' . ($bundle->images[0] ?? 'no-image.png')) }}" height="300px">

                <h4 class="mt-3">{{ $bundle->name }}</h4>
                <p>{{ $bundle->description }}</p>

                <div class="d-flex justify-content-between">
                    @if($bundle->original_price)
                        <del style="color: red">Rp {{ number_format($bundle->original_price) }}</del>
                    @endif
                    <strong>Rp {{ number_format($bundle->price) }}</strong>
                </div>

                <a href="{{ route('product.detail', $bundle->id) }}" class="btn btn-primary mt-3">
                    View Bundle
                </a>
            </div>

            @endforeach
        </div>
    @endif



    <hr style="border:0;height:3px;background:linear-gradient(to right,#00B4D8,#0077B6,#023E8A);border-radius:10px;margin:80px 0;">

    {{-- ABOUT SECTION --}}
    <div style="background:#F8F9FA; padding:80px 0;">
        <div class="d-flex align-items-start justify-content-center container" id="about-us-section" style="gap:50px;">
            <div style="width:45%;">
                <img src="{{ asset('images/about-image.png') }}" alt="About Us" class="w-100 rounded shadow-sm">
            </div>
            <div style="width:45%; margin-top: 20px;">
                <h2 style="font-family:'Chakra Petch',sans-serif;font-weight:700;font-size:64px;color:#424141;">
                    About
                </h2>
                <p style="font-family:'Montserrat',sans-serif;font-size:22px;color:#555;line-height:1.6;">
                    At GigaGears, we believe technology should be accessible, reliable, and innovative.
                    We provide the latest gadgets, digital products, and software solutions to empower your lifestyle and work.
                </p>
            </div>
        </div>
    </div>
@endsection


{{-- ================= FOOTER ================= --}}
@section('footer')
<footer class="text-center py-4" style="background:linear-gradient(87.6deg,#FFFFFF 8.86%,rgba(78,218,254,0.67) 32.51%,rgba(6,124,194,0.93) 95.43%);">
    <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="220">
    <p class="mt-2" style="font-family:'Chakra Petch',sans-serif;font-style:italic;">Empowering your digital lifestyle with the best tech and software.</p>
    <p style="font-weight:bold;">© 2025 GigaGears. All Rights Reserved.</p>
</footer>
@endsection
