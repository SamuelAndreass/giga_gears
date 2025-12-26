@extends('layouts.main')
@section('title', 'My Orders')

{{-- ================================================= --}}
{{-- 1. HEADER --}}
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
                    <a href="{{ route('products.index') }}" style="color: #000000; text-decoration: none; white-space: nowrap;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; text-decoration: none; white-space: nowrap;">About Us</a>
                    <a href="{{ route('orders.index') }}" style="color: #067CC2; text-decoration: none; white-space: nowrap;">My Order</a>
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
{{-- 2. BODY CONTENT --}}
{{-- ================================================= --}}
@section('content')
    <style>
        /* --- Improved Table UX/UI --- */
        .order-container {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .table thead th {
            border: none;
            background: #F8FAFC;
            color: #000;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 600;
            font-size: 18px;
        }
        .table tbody tr {
            background: #F9FAFB;
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .table tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.08);
        }

        .badge { border-radius: 8px; font-size: 15px; }
        .search-input, .filter-select {
            border: 1px solid #D5D5D5;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 16px;
            transition: all 0.2s;
        }
        .search-input:focus, .filter-select:focus {
            border-color: #067CC2;
            box-shadow: 0 0 0 3px rgba(6, 124, 194, 0.2);
            outline: none;
        }
        .page-link { border-radius: 50px !important; }
        .page-item.active .page-link { background-color: #067CC2; border-color: #067CC2; }
    </style>

    <div class="container mt-5 mb-5">

        {{-- FILTER & SEARCH --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px;">
            <h3 class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-size: 30px;">Order History Details</h3>

            <div class="d-flex flex-wrap gap-2">
                <form method="GET" action="{{ route('orders.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="search-input shadow-sm" placeholder="🔍 Search Order ID or Product">
                    <select name="status" class="filter-select shadow-sm" onchange="this.form.submit()">
                        <option value="">Filter Status (All)</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Shipped" {{ request('status') == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- ORDER TABLE --}}
        <div class="order-container shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="fw-bold text-primary">#{{ $order->id }}</td>
                                <td class="text-secondary">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="text-start">
                                     @php
                                        $firstProduct = $order->products[0]->name ?? 'Unnamed Product';
                                        $productImage = $order->products[0]->image ?? null;
                                        $imagePath = 'no-image.png';
                                        if ($productImage) {
                                            $imagePath = is_array($productImage) ? ($productImage[0] ?? 'no-image.png') : $productImage;
                                        }
                                        $otherCount = count($order->products) - 1;
                                    @endphp
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $imagePath) }}"
                                            alt="{{ $firstProduct }}" 
                                            style="width:55px; height:55px; border-radius:5px; border:1px solid #eee; object-fit:contain;">
                                        <div>
                                        <div class="fw-bold text-dark">{{ $firstProduct }}</div>
                                            @if ($otherCount > 0)
                                                <small class="text-muted">and {{ $otherCount }} more product{{ $otherCount > 1 ? 's' : '' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold text-success">Rp. {{ number_format($order->total_price, 2) }}</td>
                                <td>
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
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm fw-bold text-white" style="background:#067CC2; border-radius:5px;">Detail</a>
                                        @if (in_array($order->status, ['shipped', 'processing', 'pending']))
                                            <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Batal</button>
                                            </form>
                                        @endif
                                        @if ($order->status == 'Completed')
                                            <a href="#" class="btn btn-sm btn-outline-dark">Invoice</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        </div>

    </div>
@endsection



{{-- ================================================= --}}
{{-- 3. FOOTER --}}
{{-- ================================================= --}}
@section('footer')
    <style>
        .main-footer {
            background-image: linear-gradient(87.6deg, #FFFFFF 8.86%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.93) 95.43%);
            padding: 50px 0 20px 0;
            position: relative;
            z-index: 10;
        }
        .footer-title {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
            margin-bottom: 12px;
        }
        .copyright.page-container {
            width: 100%;
            text-align: center;
            padding-top: 30px;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
        }
    </style>
    
    <footer class="main-footer">
        <div class="page-container d-flex justify-content-between" style="gap: 197px;">
            <div class="d-flex flex-column" style="width: 343px; gap: 27px;">
                <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="263" height="32">
                <p style="font-family: 'Chakra Petch', sans-serif; font-style: italic; font-weight: 500; font-size: 22px; line-height: 29px; color: #000000;">
                    Empowering your digital lifestyle with the best tech and software.
                </p>
            </div>

            <div class="d-flex" style="width: 740px; justify-content: space-between; gap: 155px;">
                <div class="d-flex flex-column" style="width: 121px; gap: 12px;">
                    <div class="footer-title">Quick Links</div>
                    <div class="d-flex flex-column">
                        <a href="/">Home</a><a href="/products">Products</a><a href="#">Categories</a><a href="/about-us">About Us</a><a href="#">Contact</a>
                    </div>
                </div>
                <div class="d-flex flex-column" style="width: 220px; gap: 12px;">
                    <div class="footer-title">Customer Support</div>
                    <div class="d-flex flex-column">
                        <a href="#">Help Center</a><a href="#">FAQs</a><a href="#">Shipping & Delivery</a><a href="#">Return & Refund Policy</a>
                    </div>
                </div>
                <div class="d-flex flex-column" style="width: 220px; gap: 12px;">
                    <div class="footer-title">Social Media</div>
                    <div class="d-flex flex-column">
                        <a href="#">Facebook</a><a href="#">Instagram</a><a href="#">X [Twitter]</a><a href="#">LinkedIn</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="copyright page-container">
            © 2025 GigaGears. All Rights Reserved.
        </div>
    </footer>
@endsection
