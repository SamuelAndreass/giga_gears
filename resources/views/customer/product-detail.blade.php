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
    </style>
    
    <div class="header-wrapper">
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            <div class="d-flex" style="gap: 45px; font-size:22px; align-items: center;">
                <div class="d-flex gap-3">
                    <a href="{{ route('dashboard') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #067CC2; text-decoration: none; white-space: nowrap;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none; white-space: nowrap;">About Us</a>
                    <a href="{{ route('orders.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">My Order</a>
                    <a href="{{ route('community.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Communities</a>
                    <a href="{{ route('seminar.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Seminars</a>
                    <a href="{{ route('cart.index') }}" 
                        class="position-relative text-decoration-none text-dark" style="white-space: nowrap;">
                        <i class="bi bi-cart3"></i>
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-6">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}" class="d-flex align-items-center justify-content-center" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <div class="d-flex align-items-center" style="gap: 9px;">
                    <span>Profile</span>
                    <img src="{{ asset(Auth::user()->customerProfile?->avatar_path ?? 'images/logo foto profile.png') }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%;">
                </div>
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
                <div class="d-flex align-items-center gap-2">
                    <span style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 24px; color: #787878;">Rp.{{$detailProduct->original_price}}</span>
                </div>
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
                <form id="buyNowForm" action="{{ route('buy_now.redirect') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailProduct->id }}">
                    <input type="hidden" name="qty" value="1">
                    <button type="button" onclick="showWarningModal(document.getElementById('buyNowForm'))" class="btn btn-success" style="width: 208px; height: 55px; background: rgba(6, 124, 194, 0.93); border-radius: 5px; display: flex; justify-content: center; align-items: center; font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 22px; color: #FFFFFF; text-decoration: none;">Buy Now</button>
                </form>

                
                <form id="addToCartForm" action="{{ route('cart.add', $detailProduct->id) }}" method="POST" class="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailProduct->id }}">
                    <input type="hidden" name="qty" value="1">
                    <button type="button" onclick="showWarningModal(document.getElementById('addToCartForm'))" class="btn btn-cart" style="width: 208px; height: 55px; border: 1px solid #067CC2; border-radius: 5px; display: flex; justify-content: center; align-items: center; font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 22px; color: #067CC2; text-decoration: none;">Add to Cart</button>
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
    @foreach ($relatedProducts as $product)
    <h2 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 46px; color: #000000; margin-bottom: 30px;">Related Products</h2>

    {{-- Frame 60: Product Cards DEALS (Related Products) --}}
    <div class="d-flex justify-content-between" style="width: 100%; gap: 20px;">
        
            {{-- Setiap Kartu Produk --}}
            <div class="d-flex flex-column align-items-start" style="flex: 1; gap: 9px;">
                <div style="width: 100%; height: 338px; border-radius: 5px; display: flex; justify-content: center; align-items: center;">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-height: 100%;">
                </div>
                <h3 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 32px; line-height: 42px; color: #000000; margin-top: 10px; margin-bottom: 5px;">
                    {{ $product->name }}
                </h3>
                <div class="d-flex align-items-center gap-2">
                    <span style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 24px; line-height: 29px; text-decoration: line-through; color: #787878;">
                        {{ $product->original_price }}
                    </span>
                </div>
                {{-- Tombol Add to Cart untuk Related Products --}}
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="qty" value="1">
                    <button type="submit" class="btn btn-cart">Add to Cart</button>
                </form>
            </div>
        @endforeach
    </div>

    @include('components.popup.warning-modal')
    
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