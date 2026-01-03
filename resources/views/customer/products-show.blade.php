@extends('layouts.main')
@section('title', 'Products')

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
                        <i class="bi bi-cart3 "></i>
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-6">
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

@section('content')
<style>
    .products-section {
        width: 100%;
        margin-top: 60px;
        margin-bottom: 100px;
    }
    .section-title {
        font-family: 'Chakra Petch', sans-serif;
        font-weight: 700;
        font-size: 36px;
        color: #000;
        margin-bottom: 40px;
        text-align: center;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 35px;
    }
    .product-card {
        background: #fff;
        border: 1px solid #E5E5E5;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .product-card img {
        width: 100%;
        height: 220px;
        object-fit: contain;
        margin-bottom: 15px;
    }
    .product-name {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 20px;
        color: #1D1D1D;
        margin-bottom: 5px;
    }
    .product-category {
        font-family: 'Montserrat', sans-serif;
        font-size: 16px;
        color: #707070;
        margin-bottom: 10px;
    }
    .product-price {
        font-family: 'Chakra Petch', sans-serif;
        font-weight: 700;
        font-size: 22px;
        color: #067CC2;
        margin-bottom: 15px;
    }
    .btn-view, .btn-cart {
        font-family: 'Chakra Petch', sans-serif;
        font-weight: 600;
        border-radius: 6px;
        padding: 8px 18px;
        transition: all 0.2s ease;
    }
    .btn-view {
        background: #067CC2;
        color: #fff;
        border: 1px solid #067CC2;
    }
    .btn-view:hover {
        background: #045F9B;
    }
    .btn-cart {
        background: none;
        color: #067CC2;
        border: 1px solid #067CC2;
    }
    .btn-cart:hover {
        background: #067CC2;
        color: #fff;
    }
</style>

<div class="container products-section">

    {{-- TITLE --}}
    <h2 class="section-title">All Products</h2>

    {{-- FILTER BAR (optional) --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px;">
        <form method="GET" action="{{ route('products.index') }}" class="d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="🔍 Search products..." 
                   class="form-control shadow-sm" 
                   style="border-radius:8px; min-width:280px;">
            <select name="category" class="form-select shadow-sm" style="border-radius:8px; width:200px;" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- PRODUCT GRID --}}
    <div class="product-grid">
        @forelse ($products as $product)
            <div class="product-card">
                @php
                    $image = $product->images[0] ?? null;
                @endphp
                <img src="{{ asset('storage/' . ($image ?? 'no-image.png')) }}" alt="{{ $product->name }}">

                <div class="product-name">{{ $product->name }}</div>
                <div class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</div>
                <div class="product-price">Rp. {{ number_format($product->price, 2) }}</div>

                <div class="d-flex justify-content-center gap-2 mt-3">
                    <a href="{{ route('product.detail', $product->id) }}" class="btn btn-view">View Details</a>
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form" onsubmit="event.preventDefault(); showWarningModal(this);">
                        @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="qty" value="1">
                            <button type="submit" class="btn btn-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12 text-center mt-5">
                <p class="text-muted fs-5">No products found.</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-5">
        {{ $products->links() }}
    </div>

</div>
    @include('components.popup.warning-modal')

@endsection
