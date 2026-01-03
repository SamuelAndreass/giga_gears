@extends('layouts.main')
@section('title', 'Profile & Settings')

{{-- ================================================= --}}
{{-- 1. HEADER SECTION --}}
{{-- ================================================= --}}
@section('header')
    {{-- Memastikan gaya header dimuat --}}
    <style>
        .header-wrapper {
            width: 100%; height: 90px; padding-top: 20px; background: #FFFFFF; border-bottom: 1px solid #eee;
        }
        .main-navbar {
            width: 1280px; max-width: 90%; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;
        }
        .profile-list-item {
            padding: 20px 15px; border: 1px solid #D5D5D5; border-radius: 5px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; text-decoration: none;
        }
        .section-title {
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 22px; line-height: 27px; color: #000000; margin-bottom: 11px;
        }
    </style>
    
    <div class="header-wrapper">
        
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            
            <div class="d-flex" style="gap: 71px;">
                <div class="d-flex gap-5">
                    <a href="{{ route('dashboard') }}" style="color: #000000; font-size:25px; text-decoration: none;">Home</a>
                    <a href="{{ route('products.index') }}" style="color: #000000; font-size:25px; text-decoration: none;">Products</a>
                    <a href="/about-us" style="color: #000000; font-size:25px; text-decoration: none;">About Us</a>
                    <a href="{{ route('orders.index') }}" style="color: #000000; font-size:25px; text-decoration: none;">My Order</a>
                    <a href="{{ route('community.index') }}" style="color: #000000; font-size:25px; text-decoration: none;">Communities</a>
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
    {{-- Title & Log Out --}}
    <div class="d-flex justify-content-between align-items-center" style="width: 100%; margin-top: 50px; margin-bottom: 30px;">
        <div>
            <h1 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 55px; line-height: 72px; color: #000000; margin-bottom: 0;">Profile & Settings</h1>
            <p style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 26px; line-height: 34px; color: #717171;">Manage your account details, security, and preferences</p>
        </div>
        
        {{-- Log Out Button --}}
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="d-flex justify-content-center align-items-center"
                style="width: 179px; height: 54px; background: #E33629; border: 1px solid #E33629; border-radius: 5px; color: #FFFFFF; font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px; text-decoration: none; cursor: pointer;">
                
                <svg style="margin-right: 10px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M16 9v-4l8 7-8 7v-4h-8v-6h8zm-2 10v3h-12v-22h12v3h-2v-2h-8v18h8v-2h2z"/>
                </svg>
                Log Out
            </button>
        </form>
    </div>

    {{-- Frame 102: Profile Summary --}}
    <div class="d-flex align-items-center p-4" style="width: 100%; border: 1px solid #D7D7D7; border-radius: 10px; gap: 42px; margin-bottom: 40px;">
        
        {{-- Profile Image --}}
        <img src="{{ $pfp ?? asset('images/pp.png') }}" alt="Profile" style="width: 140px; height: 140px; border-radius: 50%; background: #D9D9D9; flex-shrink: 0;">
        
        {{-- Details --}}
        <div class="d-flex" style="width: 100%; justify-content: space-between; align-items: center; gap: 62px;">
            
            {{-- Username --}}
            <div class="d-flex flex-column">
                <div class="text-muted" style="font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 18px;">Username</div>
                <div class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px;">{{ $user->name ?? '—' }}</div>
            </div>
            
            {{-- Email --}}
            <div class="d-flex flex-column">
                <div class="text-muted" style="font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 18px;">Email</div>
                <div class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px;">{{ $user->email ?? '—' }}</div>
            </div>
            
            {{-- Phone Number --}}
            <div class="d-flex flex-column">
                <div class="text-muted" style="font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 18px;">Phone Number</div>
                <div class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px;">{{ $cprofile->phone ?? '—' }}</div>
            </div>

            {{-- Edit Profile Button --}}
            <button id="openEditProfile" type="button" class="d-flex justify-content-center align-items-center" style="width: 179px; height: 54px; border: 1px solid #E33629; border-radius: 5px; color: #E33629; background: none; font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px; flex-shrink: 0;">
                Edit Profile
            </button>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editProfileForm" method="POST" action="{{ route('profile.settings.profile.update') }}">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input name="name" id="profile_name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required>
                        <div class="invalid-feedback" id="error-name"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" id="profile_email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        <div class="invalid-feedback" id="error-email"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input name="phone" id="profile_phone" type="text" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="{{ $user->phone ?? 'Enter your phone number' }}">
                        <div class="invalid-feedback" id="error-phone"></div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button id="saveProfileBtn" type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        // if you're using Bootstrap 5
        const editModalEl = document.getElementById('editProfileModal');
        const openBtn = document.getElementById('openEditProfile');

        if (openBtn && editModalEl) {
            var modal = new bootstrap.Modal(editModalEl);
            openBtn.addEventListener('click', () => modal.show());
        }

        // AJAX submit
        const form = document.getElementById('editProfileForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            // clear previous errors
            ['name','email','phone'].forEach(k => {
            const el = document.getElementById('error-' + k);
            if (el) el.textContent = '';
            const input = form.querySelector('[name="'+k+'"]');
            if (input) input.classList.remove('is-invalid');
            });

            const btn = document.getElementById('saveProfileBtn');
            btn.disabled = true;

            const data = new FormData(form);

            try {
                    console.log("Submitting to:", form.action, "Method:", form.method);
                    console.log("Form snapshot (masked):", Array.from(data.entries()).map(([k,v]) => [k, k.toLowerCase().includes('password') ? '***' : v]));

                    const res = await fetch(form.action, {
                        method: (form.method || 'POST').toUpperCase(),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: data,
                        credentials: 'same-origin', // <--- critical: send cookies so auth session is included
                        redirect: 'follow'
                    });

                    console.log('Fetch response status:', res.status, 'redirected:', res.redirected, 'url:', res.url);

                    // If fetch followed a redirect to login, detect it
                    if (res.redirected && res.url.includes('/login')) {
                        alert('Session expired / not logged in. Please login again.');
                        window.location = res.url;
                        return;
                    }

                    const text = await res.clone().text();
                    // helpful debug
                    console.log('Response text snippet:', text.slice(0, 800));

                    if (res.status === 422) {
                        const json = await res.json();
                        const errors = json.errors || json;
                        Object.keys(errors).forEach(field => {
                            const errEl = document.getElementById('error-' + field);
                            const input = form.querySelector('[name="'+field+'"]');
                            if (errEl) errEl.textContent = errors[field][0];
                            if (input) input.classList.add('is-invalid');
                        });
                        btn.disabled = false;
                        return;
                    }

                    if (res.ok) {
                        // success: update small UI or reload
                        location.reload();
                    } else {
                        // server error or unexpected; show text for debugging
                        console.error('Update failed. Status:', res.status, 'Response:', text);
                        alert('Update failed — check console and server logs.');
                        btn.disabled = false;
                    }
                } catch (err) {
                    console.error('Fetch error', err);
                    alert('Network error — check console.');
                    btn.disabled = false;
                }
            });
        });
    </script>



    {{-- Default Shipping Address --}}
    <div class="d-flex flex-column" style="gap: 11px;">
        <div class="section-title">Default Shipping Address</div>

        {{-- Item utama --}}
        <div id="address-section" class="profile-list-item flex-column align-items-start">
            <div class="d-flex justify-content-between w-100 align-items-center">
                <span style="font-family:'Montserrat',sans-serif;font-weight:500;font-size:22px;color:#1D1D1D;">
                    {{ $cprofile->address ?? 'No default address set' }}
                </span>
                <button id="toggleAddressForm" type="button" class="btn btn-link p-0"
                    style="color:#067CC2;font-size:22px;text-decoration:none;">
                    Change
                </button>
            </div>

            {{-- Form ubah alamat (tersembunyi default) --}}
            <form id="addressForm" method="POST" action="{{ route('profile.settings.address.update') }}"
                style="{{ $errors->updateAddress->any() ? '' : 'display:none;' }}; width:100%; margin-top:20px;">
                @csrf
                <input type="text" name="address" value="{{ old('address', $user->address) }}"
                    placeholder="Enter new shipping address"
                    style="width:100%;padding:12px;border:1px solid #D5D5D5;border-radius:5px;font-size:18px;">

                @error('address', 'updateAddress')
                    <div class="text-danger mt-1" style="font-size:16px;">{{ $message }}</div>
                @enderror

                <button type="submit"
                        style="width:180px;height:50px;margin-top:15px;background:#E33629;border:1px solid #E33629;
                            border-radius:5px;color:#fff;font-family:'Chakra Petch',sans-serif;
                            font-weight:600;font-size:20px;cursor:pointer;">
                    Save Address
                </button>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('toggleAddressForm').addEventListener('click', () => {
        const form = document.getElementById('addressForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
    </script>


    {{-- Payment Method --}}
    <div class="d-flex flex-column" style="gap: 11px; margin-top: 30px;">
        <div class="section-title">Payment Method</div>
        <a href="{{ route('profile.edit') }}" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>{{ $user->card ?? 'No card saved' }}</span>
            <span style="color: #067CC2;">Change</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>{{ $user->paypal ?? 'No PayPal linked' }}</span>
            <span style="color: #067CC2;">Change</span>
        </a>
    </div>

    {{-- Security Settings --}}
    <div class="d-flex flex-column" style="gap: 11px; margin-top: 30px;">
        <div class="section-title">Security Settings</div>

        {{-- PASSWORD CHANGE TOGGLE --}}
        <div id="password-section" class="profile-list-item flex-column align-items-start">
            <div class="d-flex justify-content-between w-100 align-items-center">
                <span style="font-family:'Montserrat',sans-serif;font-weight:500;font-size:22px;color:#1D1D1D;">
                    Password
                </span>
                <button id="togglePasswordForm" type="button" class="btn btn-link p-0" style="color:#067CC2;font-size:22px;text-decoration:none;">
                    Change
                </button>
            </div>

            {{-- Password Form (hidden by default) --}} 
                <form id="passwordForm" method="POST" action="{{ route('profile.settings.password.update') }}"
                    style="{{ $errors->updatePassword->any() ? '' : 'display:none;' }}; width:100%; margin-top:20px;">
                    @csrf

                    <div class="d-flex flex-column" style="gap: 15px; width: 100%;">
                        {{-- Current Password --}}
                        <div class="d-flex flex-column">
                            <label style="font-family:'Montserrat',sans-serif;font-weight:500;font-size:18px;color:#1D1D1D;">
                                Current Password
                            </label>
                            <input type="password" name="current_password" placeholder="Enter current password"
                                style="border:1px solid #D5D5D5; border-radius:5px; padding:12px; font-size:18px; width:100%;">
                            <span onclick="togglePassword('current_password', this)"
                                style="position:absolute; right:12px; top:12px; cursor:pointer; font-size:20px;display:flex; align-items:center;">
                                👁
                            </span>    
                            @error('current_password', 'updatePassword')
                                <div class="text-danger mt-1" style="font-size:16px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- New Password --}}
                        <div class="d-flex flex-column">
                            <label style="font-family:'Montserrat',sans-serif;font-weight:500;font-size:18px;color:#1D1D1D;">
                                New Password
                            </label>
                            <input type="password" name="password" placeholder="Enter new password"
                                style="border:1px solid #D5D5D5; border-radius:5px; padding:12px; font-size:18px; width:100%;">
                            <span onclick="togglePassword('password', this)"
                                style="position:absolute; right:12px; top:12px; cursor:pointer; font-size:20px;">
                                👁
                            </span>
                            @error('password', 'updatePassword')
                                <div class="text-danger mt-1" style="font-size:16px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="d-flex flex-column">
                            <label style="font-family:'Montserrat',sans-serif;font-weight:500;font-size:18px;color:#1D1D1D;">
                                Confirm New Password
                            </label>
                            <input type="password" name="password_confirmation" placeholder="Re-enter new password"
                                style="border:1px solid #D5D5D5; border-radius:5px; padding:12px; font-size:18px; width:100%;">
                            <span onclick="togglePassword('current_password', this)"
                                style="position:absolute; right:12px; top:12px; cursor:pointer; font-size:20px;">
                                👁
                            </span>    
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                style="width: 220px; height: 54px; background:#E33629; border:1px solid #E33629; border-radius:5px;
                                    color:#FFFFFF; font-family:'Chakra Petch',sans-serif; font-weight:600; font-size:20px;
                                    margin-top:10px; cursor:pointer;">
                            Update Password
                        </button>

                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success mt-3" style="font-size:18px;">
                                Password updated successfully.
                            </div>
                        @endif
                    </div>
                </form>
        </div>


{{-- Script: Toggle Form + Show/Hide Password --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hasError = {{ $errors->updatePassword->any() ? 'true' : 'false' }};
    if (hasError) {
        document.getElementById('passwordForm').style.display = 'block';
    }
});

document.getElementById('togglePasswordForm').addEventListener('click', () => {
    const form = document.getElementById('passwordForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
});

function togglePassword(id, el) {
    const input = document.getElementById(id);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    el.textContent = isHidden ? '🙈' : '👁';
}
</script>


    {{-- Others (tidak berubah kecuali route) --}}
    <div class="d-flex flex-column" style="gap: 11px; margin-top: 30px;">
        <div class="section-title">Others</div>
        <a href="{{ route('profile.edit') }}" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>Things Preference</span>
            <span style="color: #067CC2;">&gt;</span>
        </a>
        <a href="/my-order" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>Order History</span>
            <span style="color: #067CC2;">&gt;</span>
        </a>
    </div>

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
        }
        .footer-title {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
            margin-bottom: 12px;
        }
        .copyright {
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
                <p style="font-family: 'Chakra Petch', sans-serif; font-style: italic; font-weight: 500; font-size: 22px; line-height: 29px; color: #000000;">Empowering your digital lifestyle with the best tech and software.</p>
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