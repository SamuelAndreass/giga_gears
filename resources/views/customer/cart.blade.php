@extends('layouts.main')
@section('title', 'My Cart')

@section('content')
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
                <div class="d-flex gap-5">
                    <a href="{{ route('dashboard') }}" style="color: #000000; text-decoration: none;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #000000; text-decoration: none;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none;">About Us</a>
                    <a href="{{ route('orders.index') }}" style="color: #000000; text-decoration: none;">My Order</a>
                    <a href="{{ route('community.index') }}" style="color: #000000; text-decoration: none;">Communities</a>
                    <a href="{{ route('cart.index') }}" 
                        class="position-relative text-decoration-none 
                        {{ request()->routeIs('cart.index') ? 'text-primary fw-bold' : 'text-dark' }}">
                        <i class="bi bi-cart3 fs-4"></i>
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
@foreach (['success', 'error', 'warning', 'info'] as $type)
    @if (session($type))
        <div class="alert alert-{{ $type == 'error' ? 'danger' : $type }}">
            {{ session($type) }}
        </div>
    @endif
@endforeach
<div class="container my-5">
    <h2 class="text-center fw-bold mb-5" style="font-family:'Chakra Petch',sans-serif;font-size:42px;">
        🛒 My Shopping Cart
    </h2>

    @if ($cart && $cart->items->count() > 0)
        <div class="row g-4">
            {{-- CART ITEMS --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        @foreach ($cart->items as $item)
                            {{-- Tambahkan kondisi stok habis --}}
                            @php
                                $out_of_stock = ($item->product->stock ?? 0) <= 0;
                            @endphp

                            <div class="row align-items-center border-bottom py-3 {{ $out_of_stock ? 'opacity-50' : '' }}">
                                {{-- Product Image --}}
                                <div class="col-3 col-md-2">
                                    <img src="{{ asset('storage/' . ($item->product->images[0] ?? 'no-image.png')) }}"
                                        class="img-fluid rounded border" alt="{{ $item->product->name }}">
                                </div>

                                {{-- Product Info --}}
                                <div class="col-md-4">
                                    <div style="display:flex;flex-direction:column;gap:6px;">
                                        <h5 class="fw-bold mb-1" style="font-family:'Chakra Petch',sans-serif;font-size:18px;color:#000;">
                                            {{ $item->product->name }}
                                            @if($item->meta && ($item->meta['type'] ?? null) === 'bundle')
                                                <span class="badge bg-warning text-dark ms-1" style="font-size:12px;padding:5px 10px;border-radius:6px;">Bundle</span>
                                            @endif
                                        </h5>

                                        {{-- Harga --}}
                                        <p class="mb-1" style="color:#0d6efd;font-weight:600;">
                                            Rp{{ number_format($item->price, 0, ',', '.') }}
                                        </p>

                                        {{-- Info stok habis --}}
                                        @if($out_of_stock)
                                            <small class="text-danger d-block mt-1" style="font-weight:500;">
                                                Out of Stock
                                            </small>
                                        @endif

                                        {{-- Isi Bundle --}}
                                        @if($item->meta && ($item->meta['type'] ?? null) === 'bundle')
                                            <div style="background:#fff8e1;border:1px solid #ffe58f;border-radius:8px;padding:10px 12px;margin-top:5px;">
                                                <p class="mb-2" style="font-weight:600;color:#6c757d;font-size:13px;">Included in this bundle:</p>
                                                <ul class="mb-0 ps-3" style="font-size:13px;color:#555;list-style-type:disc;">
                                                    @foreach($item->meta['items'] as $bundleItem)
                                                        <li style="margin-bottom:3px;">
                                                            {{ $bundleItem['name'] }}
                                                            <span style="color:#777;">× {{ $bundleItem['qty'] * $item->qty }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Quantity Update --}}
                                <div class="col-md-3">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="qty" value="{{ $item->qty }}" min="1" class="form-control text-center" style="max-width:80px;">
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </div>

                                {{-- Subtotal --}}
                                <div class="col-md-2 text-end fw-bold text-success">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>

                                {{-- Remove --}}
                                <div class="col-md-1 text-end">
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Remove">
                                            <i class="bi bi-trash-fill fs-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


            {{-- CART SUMMARY --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="text-center fw-bold mb-4" style="font-family:'Chakra Petch',sans-serif;">Order Summary</h4>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Subtotal</span>
                                <span class="fw-semibold">Rp{{ number_format($cart->items->sum('subtotal'), 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-top pt-3">
                                <strong>Total</strong>
                                <strong class="text-primary">
                                    Rp{{ number_format($cart->items->sum('subtotal'),0, ',', '.') }}
                                </strong>
                            </li>
                        </ul>

                        <div class="d-grid mt-4">
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary fw-bold py-2">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5 text-muted fs-5">
            Your cart is empty 😔  
            <div class="mt-3">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Go to Products</a>
            </div>
        </div>
    @endif
</div>
@endsection
