@extends('layouts.main')
@section('title', 'Product Detail')


{{-- ================================================= --}}
{{-- 1. HEADER SECTION (@section('header')) --}}
{{-- ================================================= --}}
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
        .back-icon {
            position: relative;
            width: 43px;
            height: 43px;
            left: 0;
            top: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #717171;
            border-radius: 9999px;
            text-decoration: none;
            z-index: 20;
            transform: translateX(-50%); 
        }
    </style>
    
    <div class="header-wrapper">
        
        {{-- Frame 19: NAVBAR --}}
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            
            {{-- Frame 16: Links --}}
            <div class="d-flex" style="gap: 71px; font-size:25px">
                <div class="d-flex gap-3">
                    <a href="{{ route('dashboard') }}" style="color: #000000; text-decoration: none;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #067CC2; text-decoration: none;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none;">About Us</a>
                    <a href="{{ route('orders.index') }}" style="color: #000000; text-decoration: none;">My Order</a>
                    <a href="{{ route('community.index') }}" style="color: #000000; text-decoration: none;">Communities</a>
                    <a href="{{ route('seminar.index') }}" style="color: #000000; text-decoration: none;">Seminar</a>
                    <a href="{{ route('cart.index') }}" 
                    class="position-relative text-decoration-none 
                    {{ request()->routeIs('cart.index') ? 'text-primary fw-bold' : 'text-dark' }}">
                    <i class="bi bi-cart3 fs-4"></i>
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill fs-6 
                                    {{ request()->routeIs('cart.index') ? 'bg-primary' : 'bg-danger' }}">
                            {{ $cartCount }}
                        </span>
                    @endif
                    </a>
                </div>
            </div>

            {{-- Frame 18: Profil Button --}}
            <a href="{{ route('profile.edit') }}" style="border: 1px solid #000; border-radius: 5px; padding: 10px 15px; color: #000; text-decoration: none;">
                <span>Profile</span>
                <img src="{{ asset(Auth::user()->customerProfile->avatar_path ?? 'images/pp.png') }}" alt="Profile" width="32" height="32" style="border-radius:50%;margin-left:9px;">
            </a>
        </div>
    </div>
@endsection


{{-- ================================================= --}}
{{-- 2. KONTEN UTAMA (@section('content')) --}}
{{-- ================================================= --}}
@section('content')

    {{-- Ikon Kembali --}}
    <a href="/" class="back-icon" aria-label="Kembali ke Beranda">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="white" d="M10 19l-7-7 7-7 1.41 1.41L5.83 12l5.58 5.59z"/></svg>
    </a>

    {{-- Frame 74: PRODUCT DETAIL HEADER --}}
    <div class="d-flex justify-content-between" style="width: 100%; margin-top: 100px; gap: 50px;">
        
        {{-- Rectangle 8: Gambar Produk --}}
        <div style="width: 40%; max-width: 496px; flex-shrink: 0;">
            <img src="{{ $prod_img }}" alt="{{ $detailProduct['name'] }}" style="width: 100%; height: 463px; object-fit: cover;">
        </div>

        {{-- Frame 65: Deskripsi Singkat, Harga & Tombol --}}
        <div class="d-flex flex-column" style="width: 60%; max-width: 448px; gap: 27px;">
            
            {{-- Product Title --}}
            <h1 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 55px; line-height: 72px; color: #000000; margin-top: 0;">
                {{ $detailProduct->name }}
            </h1>
            
            {{-- Frame 60 & 35: Harga & Save --}}
            <div class="d-flex flex-column" style="gap: 9px;">
                @if($detailProduct->type === 'bundle')
                    <span style="font-size:22px;text-decoration:line-through;color:#888;">
                        Rp {{ number_format($detailProduct->original_price) }}
                    </span>
                    <span style="font-size:36px;font-weight:700;color:#067CC2;">
                        Rp {{ number_format($detailProduct->price) }}
                    </span>
                    <span class="badge bg-success w-fit">
                        Hemat Rp {{ number_format($detailProduct->original_price - $detailProduct->price) }}
                    </span>
                @else
                    <span style="font-size:36px;font-weight:700;color:#067CC2;">
                        Rp {{ number_format($detailProduct->price) }}
                    </span>
                @endif
            </div>

            
            {{-- Frame 64: Color Options --}}
            {{-- <div class="d-flex flex-column" style="gap: 14px;">
                <div style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 22px;">Color</div> --}}
                {{-- Frame 63: Color Swatches --}}
                {{-- <div class="d-flex gap-2">
                    <div style="width: 42px; height: 42px; border: 1px solid #067CC2; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                        <div style="width: 39px; height: 39px; background: #000000; border-radius: 50%;"></div>
                    </div>
                    <div style="width: 39px; height: 39px; background: #FFFFFF; border: 1px solid #000000; border-radius: 50%;"></div>
                    <div style="width: 39px; height: 39px; background: rgba(36, 70, 91, 0.93); border-radius: 50%;"></div>
                </div>
            </div> --}}

            {{-- Frame 73: Tombol Buy Now & Add to Cart --}}
            <div class="d-flex gap-2" style="width: 100%; margin-top: 100px;">
                <form action="{{ route('buy_now.redirect') }}" method="POST" onsubmit="event.preventDefault(); showWarningModal(this);">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailProduct->id }}">
                    <input type="hidden" name="qty" value="1">
                    <button type="submit" class="btn btn-success" style="width: 208px; height: 55px; background: rgba(6, 124, 194, 0.93); border-radius: 5px; display: flex; justify-content: center; align-items: center; font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 22px; color: #FFFFFF; text-decoration: none;">Buy Now</button>
                </form>


                <form action="{{ route('cart.add', $detailProduct->id) }}" method="POST" class="add-to-cart-form" onsubmit="event.preventDefault(); showWarningModal(this);">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailProduct->id }}">
                    <input type="hidden" name="qty" value="1">
                    <button type="submit" class="btn btn-cart" style="width: 208px; height: 55px; border: 1px solid #067CC2; border-radius: 5px; display: flex; justify-content: center; align-items: center; font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 22px; color: #067CC2; text-decoration: none;">Add to Cart</button>
                </form>
            </div>

        </div>
    </div>

    <div style="height: 100px;"></div>

    {{-- Frame 71: DESCRIPTION, SPECS, REVIEW WRAPPER --}}
    <div class="d-flex flex-column" style="gap: 42px;">
        
        {{-- Frame 66: Product Description --}}
        <div class="d-flex flex-column" style="gap: 20px;">
            <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 26px; color: #000000;">Product Description (Detail)</h2>
            <p style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px; line-height: 27px; color: #565656; text-align: justify;">
                {{ $detailProduct->description }}
            </p>
        </div>

        {{-- Frame 70: SPECS & REVIEW --}}
        <div class="d-flex justify-content-between" style="gap: 50px;">
            
            {{-- Frame 67: Specifications --}}
            <div class="d-flex flex-column" style="width: 50%; gap: 20px;">
                <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 26px; color: #000000;">Specifications</h2>

                @php
                    $specs = json_decode($detailProduct->specifications, true);
                @endphp

                @if($specs)
                    <ul style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px; line-height: 27px; color: #565656; list-style: none; padding: 0;">
                        @foreach($specs as $key => $value)
                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="font-size: 20px; color: #777;">No specifications available.</p>
                @endif
            </div>


            {{-- Frame 69: Overall Review --}}
            <div class="d-flex flex-column align-items-center" style="width: 372px; gap: 5px;">
                <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 26px; color: #000000;">Overall Review</h2>
                
                {{-- Frame 68: Stars --}}
                @php
                    $rating = round($detailProduct->rating, 1); // misal: 4.8
                    $fullStars = floor($rating);          // 4
                    $halfStar = ($rating - $fullStars) >= 0.5; // true jika ada setengah bintang
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                @endphp
                <div class="d-flex align-items-center justify-content-center" style="height:72px;gap:3px;font-size:40px;color:#F8BD00;">
                    {{-- Full stars --}}
                    @for ($i = 0; $i < $fullStars; $i++)
                        ★
                    @endfor

                    {{-- Half star --}}
                    @if ($halfStar)
                        <span style="color:#F8BD00;">☆</span>
                    @endif

                    {{-- Empty stars --}}
                    @for ($i = 0; $i < $emptyStars; $i++)
                        <span style="color:#ccc;">★</span>
                    @endfor
                </div>
                
                <h3 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 36px; line-height: 47px; color: #067CC2;">{{ $detailProduct->rating }} / 5</h3>
                {{-- <p style="font-family: 'Montserrat', sans-serif; font-style: italic; font-weight: 500; font-size: 22px; line-height: 27px; text-align: center; color: #000000;">
                    "Crisp sound and super comfortable. Perfect for long gaming sessions." – Kevin T
                </p> --}}
            </div>
        </div>
    </div>

    <div style="height: 100px;"></div>

    {{-- Related Products (Title) --}}
    @if ($relatedProducts->count())
        <section style="margin-top:50px;">
            <h2 style="font-family:'Chakra Petch',sans-serif;font-weight:700;font-size:42px;color:#111;margin-bottom:40px;text-align:center;">Related Products</h2>

            <div style="display:flex;justify-content:center;align-items:center;">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center" style="max-width:1100px;">
                    @foreach ($relatedProducts as $product)
                    <div class="col">
                        <div class="mx-4 card border-0 shadow-sm h-100 text-center" style="border-radius:10px;padding:10px;margin:auto;max-width:100%;">
                            <div class="p-3" style="height:250px;display:flex;justify-content:center;align-items:center;">
                                <img src="{{ asset('storage/' . ($product->images[0] ?? 'no-image.png')) }}" alt="{{ $product->name }}" style="max-height:100%;max-width:100%;object-fit:contain;">
                            </div>

                            <div class="card-body">
                                <h5 class="card-title" style="font-family:'Chakra Petch',sans-serif;font-weight:700;font-size:22px;margin-bottom:10px;">{{ $product->name }}</h5>
                                <p class="card-text mb-3" style="font-size:18px;font-weight:600;color:#222;">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn btn-dark px-4 py-2" style="border-radius:6px;">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @include('components.popup.warning-modal')
    @endif



    
@endsection


{{-- ================================================= --}}
{{-- 3. FOOTER SECTION (@section('footer')) --}}
{{-- ================================================= --}}
@section('footer')
    <style>
        .main-footer {
            background-image: linear-gradient(87.6deg, #FFFFFF 8.86%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.93) 95.43%);
            padding: 50px 0 20px 0;
            position: relative;
            z-index: 10;
            top:25px;
        }
        .footer-title {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
            margin-bottom: 12px;
        }
    </style>
    
    <footer class="main-footer">
        
        <div class="page-container d-flex justify-content-between" style="gap: 197px;">
            {{-- Frame 58: Logo dan Slogan --}}
            <div class="d-flex flex-column" style="width: 343px; gap: 27px;">
                <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="263" height="32">
                <p style="font-family: 'Chakra Petch', sans-serif; font-style: italic; font-weight: 500; font-size: 22px; line-height: 29px; color: #000000;">Empowering your digital lifestyle with the best tech and software.</p>
            </div>

            {{-- Frame 57: Quick Links, Support, Social Media --}}
            <div class="d-flex" style="width: 740px; justify-content: space-between; gap: 155px;">
                {{-- Quick Links --}}
                <div class="d-flex flex-column" style="width: 121px; gap: 12px;">
                    <div class="footer-title">Quick Links</div>
                    <div class="d-flex flex-column">
                        <a href="#about">Home</a><a href="/products">Products</a><a href="#">Categories</a><a href="/about-us">About Us</a><a href="#">Contact</a>
                    </div>
                </div>
                {{-- Customer Support --}}
                <div class="d-flex flex-column" style="width: 220px; gap: 12px;">
                    <div class="footer-title">Customer Support</div>
                    <div class="d-flex flex-column">
                        <a href="#">Help Center</a><a href="#">FAQs</a><a href="#">Shipping & Delivery</a><a href="#">Return & Refund Policy</a>
                    </div>
                </div>
                {{-- Social Media --}}
                <div class="d-flex flex-column" style="width: 220px; gap: 12px;">
                    <div class="footer-title">Social Media</div>
                    <div class="d-flex flex-column">
                        <a href="#">Facebook</a><a href="#">Instagram</a><a href="#">X [Twitter]</a><a href="#">LinkedIn</a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Copyright --}}
        <div class="copyright page-container" style="font-weight: bold; font-size:22px; text-align: center; padding-top: 30px;">
            © 2025 GigaGears. All Rights Reserved.
        </div>
    </footer>
@endsection