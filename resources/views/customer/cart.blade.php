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
    </style>
    
    <div class="header-wrapper">
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            <div class="d-flex" style="gap: 45px; font-size:22px; align-items: center;">
                <div class="d-flex gap-3">
                    <a href="{{ route('dashboard') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none; white-space: nowrap;">About Us</a>
                    <a href="{{ route('orders.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">My Order</a>
                    <a href="{{ route('community.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Communities</a>
                    <a href="{{ route('seminar.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Seminars</a>
                    <a href="{{ route('cart.index') }}" 
                        class="position-relative text-decoration-none text-primary fw-bold" style="white-space: nowrap;">
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
                            <div class="row align-items-center border-bottom py-3">
                                {{-- Product Image --}}
                                <div class="col-3 col-md-2">
                                    <img src="{{ asset('storage/' . ($item->product->images ?? 'no-image.png')) }}" 
                                         class="img-fluid rounded border" alt="{{ $item->product->name }}">
                                </div>

                                {{-- Product Info --}}
                                <div class="col-md-4">
                                    <h5 class="mb-1 fw-bold">{{ $item->product->name }}</h5>
                                    <p class="text-primary mb-0">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>

                                {{-- Quantity Update --}}
                                <div class="col-md-3">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="qty" value="{{ $item->qty }}" min="1" class="form-control text-center" style="max-width: 80px;">
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
