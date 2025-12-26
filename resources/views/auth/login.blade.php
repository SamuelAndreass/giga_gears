<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | GigaGears</title>
    
    {{-- Memanggil Font dari Figma --}}
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    {{-- Memanggil Bootstrap (Hanya untuk Grid dasar dan utilitas) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ======================================= */
        /* LAYOUT UTAMA & RESET (FIXED TAMPILAN LAYAR) */
        /* ======================================= */
        body {
            background: #FFFFFF;
            margin: 0;
            padding: 0;
            width: 100%; 
            height: auto;
            min-height: 100vh;
            overflow-x: hidden; 
            position: relative;
        }
        
        /* FIX: Menggunakan Flexbox untuk dua kolom 50/50 */
        .login-canvas {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        /* Panel Kiri (Form) - FIXED 50% */
        .left-panel {
            position: relative;
            width: 50%; 
            padding: 60px 5%; 
            display: flex;
            flex-direction: column;
            gap: 25px;
            align-items: center; /* FIX: Pusatkan form di tengah kiri */
            justify-content: center;
        }
        /* Panel Kanan (Slogan/Biru) - FIXED 50% */
        .right-panel-area {
            position: relative;
            width: 50%;
            min-height: 100vh;
            overflow: hidden;
            order: 1; /* Pastikan selalu di kanan (order 1) */
        }
        
/* ======================================= */
/* PANEL KANAN (BACKGROUND & KONTEN) - PERBAIKAN FINAL */
/* ======================================= */
.right-panel-area {
    position: relative;
    width: 50%;
    min-height: 100vh;
    overflow: hidden;
    order: 1;

    /* GAMBAR dan GRADIENT DIJADIKAN SATU PROPERTI: background-image */
    background-image: 
        /* LAPISAN 1: Gradient Transparan Biru (di atas gambar) */
        linear-gradient(87.6deg, rgba(255, 255, 255, 0.01) 8.86%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.93) 95.43%), 
        /* LAPISAN 2: Gambar Asli */
        url("{{ asset('images/gambar mobil login.png') }}"); 
        
    background-blend-mode: multiply; /* Menyatu (Warna biru akan mewarnai gambar) */
    background-size: cover;
    background-position: center;
    
    /* Gunakan Flexbox untuk menata konten (slogan) di dalam panel */
    display: flex;
    flex-direction: column;
    justify-content: flex-end; /* Taruh konten di bagian bawah */
    padding: 0 10% 100px 10%; /* Padding bawah dan samping */
    color: #FFFFFF;
    text-align: left;
}

        /* Konten Panel Kanan (Slogan) */
        .right-panel-content {
            position: relative; /* TIDAK ABSOLUTE LAGI */
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 10;
        }
        .right-panel-title {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 38px; 
            line-height: 1.2;
            color: #FFFFFF;
        }
        .right-panel-desc {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 500;
            font-size: 16px; 
            line-height: 1.4;
            color: #FFFFFF;
        }

        /* ======================================= */
        /* PANEL KIRI (FORM) */
        /* ======================================= */
        .left-panel > * {
            width: 100%;
            max-width: 380px; /* Batasi lebar form di tengah 50% area kiri */
            margin: 0 auto;
        }
        .welcome-title {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 48px; 
            line-height: 1.2;
            color: #000000;
            margin-top: 10px;
        }
        .welcome-subtitle {
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
            font-size: 18px; 
            line-height: 1.2;
            color: #000000;
            margin-bottom: 30px;
        }

        /* Input Field */
        .input-group-figma {
            width: 100%;
            height: 50px; 
            border: 1px solid #D5D5D5;
            border-radius: 5px;
            padding: 0 15px;
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .input-group-figma input {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            background: none;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
        }
        
        /* Tombol dan Link */
        .btn-sign-in, .btn-google {
            width: 100%;
            height: 50px; 
            font-size: 18px;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
        }
        .btn-sign-in {
            background: #424141;
            color: #FFFFFF;
            border: none;
            margin-top: 10px;
        }
        .or-continue {
            margin: 15px 0;
            font-size: 14px;
        }
        .forgot-password {
             width: 100%;
             text-align: right;
             font-size: 14px;
        }
        
        /* ======================================= */
        /* RESPONSIVE KHUSUS LAYAR SANGAT KECIL (MOBILE) */
        /* ======================================= */
        @media (max-width: 768px) {
            .login-canvas {
                flex-direction: column;
            }
            .left-panel, .right-panel-area {
                width: 100%;
            }
            .right-panel-area {
                min-height: 300px; 
                order: -1; 
                padding: 50px 10%;
            }
            .left-panel {
                padding: 40px 5%;
                gap: 15px;
                order: 1; 
            }
        }
    </style>
</head>
<body>

<div class="login-canvas">
    
    {{-- PANEL KIRI (FORM) --}}
    <div class="left-panel">
        
        {{-- Logo GIGAGEARS* --}}
        <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="261" height="32" style="margin-bottom: 10px;">
        
        {{-- Welcome Text --}}
        <div style="display: flex; flex-direction: column; gap: 5px; width: 100%;">
            <div class="welcome-title">Welcome</div>
            <div class="welcome-subtitle">Please enter your data below</div>
        </div>

        {{-- Input Fields --}}
        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
            @if ($errors->any())
                <div class="text-danger mb-3">
                    {{ $errors->first() }}
                </div>
            @endif
            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                <form action="{{ route('login') }}" method="POST" style="width: 100%;">
                    @csrf
                    {{-- Email --}}
                    <div class="input-group-figma">
                        <input type="email" name="email" placeholder="Username/email" value="{{ old('email') }}" required class="@error('email') is-invalid @enderror">
                    </div>

                    {{-- Password --}}
                    <div class="input-group-figma" style="justify-content: space-between; align-items:center;">
                        <input type="password" name="password" id="passwordInput" placeholder="Password" required class="@error('password') is-invalid @enderror" style="flex:1;">
                        <button type="button"class="btn btn-outline-none" id="togglePassword" style="cursor:pointer; margin-left:8px;">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    {{-- Forgot Password --}}
                    <a href="{{ route('password.request') }}" class="forgot-password" style="font-size: 14px; margin-bottom: 25px; display: block;">Forgot Password</a>

                    {{-- Sign In --}}
                    <button type="submit" class="btn-sign-in">Sign In</button>
                </form>

            </div>
        </div>
        
        {{-- Buttons and Links --}}
        <div style="display: flex; flex-direction: column; gap: 15px; width: 100%;">
            
            {{-- Sign Up Link --}}
            <div class="sign-up-link" style="margin-top: 20px;">Don't have an account? <a href="/register">Sign Up</a></div>
        </div>
    </div>
    
    {{-- PANEL KANAN (Slogan/Biru) --}}
    <div class="right-panel-area">
        <div class="right-panel-content">
            {{-- Indikator bar --}}
            <div style="display: flex; gap: 7px; margin-bottom: 10px;">
                <div style="width: 50px; height: 5px; background: white; border-radius: 9999px;"></div>
                <div style="width: 5px; height: 5px; background: white; border-radius: 50%;"></div>
                <div style="width: 25px; height: 5px; background: white; border-radius: 9999px;"></div>
            </div>
            <div class="right-panel-title">Explore your favorite tech products</div>
            <div class="right-panel-desc">Access your account and discover the latest technology and digital products tailored for you.</div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');

        const isVisible = input.type === "text";
        input.type = isVisible ? "password" : "text";

        // ganti icon
        icon = isVisible ? "bi bi-eye" : "bi bi-eye-slash";
    });
</script>


</body>
</html>