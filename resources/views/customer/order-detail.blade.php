@extends('layouts.main')
@section('title', 'Order Detail')
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
                    <a href="{{ route('orders.index') }}" style="color: #067CC2; text-decoration: none; white-space: nowrap;">My Order</a>
                    <a href="{{ route('community.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Communities</a>
                    <a href="{{ route('seminar.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Seminars</a>
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
@section('content')
<style>
    .order-detail-container {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .order-info, .product-item {
        font-family: 'Montserrat', sans-serif;
    }
    .order-summary {
        border-top: 1px solid #eee;
        margin-top: 20px;
        padding-top: 20px;
    }
    .product-item {
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 0;
    }
    .product-item:last-child {
        border-bottom: none;
    }
    .badge {
        border-radius: 8px;
        font-size: 15px;
    }
</style>

<div class="container mt-5 mb-5">
    <h2 class="fw-bold mb-4" style="font-family:'Chakra Petch', sans-serif;">Order Details</h2>

    <div class="order-detail-container">
        {{-- Header Info --}}
        <div class="d-flex justify-content-between flex-wrap mb-4">
            <div>
                <h4 class="fw-bold">Order #{{ $order->id }}</h4>
                <p class="text-muted mb-1">Placed on {{ $order->created_at->format('d M Y') }}</p>
                <p class="text-muted mb-1">Payment Method: <strong>{{ $order->payment_method ?? '—' }}</strong></p>
                <p class="text-muted mb-1">Shipping To: <strong>{{ $order->shipping_address ?? 'No address available' }}</strong></p>
            </div>
            <div class="text-end">
                <p class="fw-bold fs-4 text-success mb-1">Rp. {{ number_format($order->total_price, 2) }}</p>
                @switch($order->status)
                    @case('completed')
                        <span class="badge bg-success py-2 px-3">Completed</span>
                        @break
                    @case('delivered')
                    @case('shipped')
                        <span class="badge bg-info py-2 px-3">Shipped</span>
                        @break
                    @case('processing')
                        <span class="badge bg-warning text-dark py-2 px-3">Processing</span>
                        @break
                    @case('pending')
                        <span class="badge bg-secondary py-2 px-3">Pending</span>
                        @break
                    @default
                        <span class="badge bg-danger py-2 px-3">Cancelled</span>
                @endswitch
            </div>
        </div>

        {{-- Product List --}}
        <h5 class="fw-bold mb-3" style="font-family:'Chakra Petch', sans-serif;">Ordered Products</h5>
        @foreach ($order->products as $product)
            <div class="d-flex align-items-center justify-content-between product-item">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{asset('storage/' . $product->image)}}" 
                         alt="{{ $product->name }}" 
                         style="width:70px; height:70px; border-radius:5px; border:1px solid #eee; object-fit:contain;">
                    <div>
                        <div class="fw-bold">{{ $product->name }}</div>
                        <div class="text-muted">{{ $product->pivot->quantity }} × Rp. {{ number_format($product->pivot->price, 2) }}</div>
                    </div>
                </div>
                <div class="fw-bold text-success">
                    Rp. {{ number_format($product->pivot->quantity * $product->pivot->price, 2) }}
                </div>
            </div>
        @endforeach

        {{-- Summary --}}
        <div class="order-summary mt-4">
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal</span>
                <span>Rp. {{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Shipping Fee</span>
                <span>Rp. {{ number_format($order->shipping_fee, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-bold fs-5">
                <span>Total</span>
                <span class="text-success">Rp. {{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 d-flex gap-2 flex-wrap">
            @if(in_array($order->status, ['Processing', 'Shipped']))
                <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Cancel Order</button>
                </form>
            @endif
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
        </div>
    </div>
</div>
@endsection
