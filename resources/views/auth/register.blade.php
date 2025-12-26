<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up Seller — GigaGears</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  

  <!-- Google Font (Chakra Petch) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/styles.css?v=1">

  <style>
    :root{
    --auth-radial: rgba(91,140,255,.18);
    --auth-top:   #eef5ff;
    --auth-mid:   #ffffff;
    --auth-bot:   #eaf3ff;
  }
  body.bg-auth{
    background:
      radial-gradient(1100px 500px at 50% -120px, var(--auth-radial), transparent 60%),
      linear-gradient(180deg, var(--auth-top) 0%, var(--auth-mid) 46%, var(--auth-bot) 100%);
  }
    /* Ganti/override background halaman */
.signup-bg{
  min-height:100vh;
  /* glow biru muda + gradasi biru pucat (bukan putih) */
  background:
    radial-gradient(1100px 520px at 72% 14%, rgba(91,140,255,.18), transparent 60%),
    linear-gradient(180deg, #eaf2ff 0%, #e3efff 48%, #eaf2ff 100%);
  background-attachment: fixed, fixed;
}


.signup-bg{
  background-attachment: fixed, fixed;
}

    :root{
      --bs-body-font-family: "Chakra Petch", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      --gg-dark:#111;           /* warna tombol/teks utama kiri */
      --gg-muted:#6b7280;
      --gg-brand:#0ea5ff;       /* aksen biru */
      --gg-card-radius: 16px;
    }

    /* Latar luar hitam + frame 16:9 */
    body{
      background:#111;
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:8px;
    }
    .frame{
      width:100%;
      max-width:1200px;                 /* lebar maks saat desktop */
      box-shadow:0 14px 40px rgba(0,0,0,.35);
      border-radius:12px;
      overflow:hidden;
      background:#f7f7f7;
    }
    .frame > .ratio-inner{              /* isi sebenarnya */
      display:flex;
      height:100%;
      width:100%;
      background:#fff;
    }

    /* Kolom kiri: form */
    .left-pane{
      flex: 1 1 52%;
      background:#fff;
      padding: clamp(20px, 4vw, 48px);
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .signup-card{
      width:100%;
      max-width: 520px;
    }
    .brand{
      display:flex; align-items:center; gap:.5rem;
      margin-bottom: .25rem;
      color:#111; text-decoration:none;
      font-weight:700;
      letter-spacing:.5px;
    }
    .brand .star{
      display:inline-flex; align-items:center; justify-content:center;
      width:22px; height:22px; border-radius:50%;
      border:2px solid #111; font-size:.8rem; line-height:1; margin-right:.25rem;
    }

    .text-muted-gg{ color: var(--gg-muted) !important; }
    .fw-black{ font-weight: 800 !important; }

    .form-control{
      background:#f3f4f6;
      border:1px solid #e5e7eb;
      border-radius:10px;
      font-weight:600;
    }
    .form-control:focus{
      background:#fff;
      border-color:#9cc9ff;
      box-shadow:0 0 0 .2rem rgba(14,165,255,.15);
    }
    .btn-dark-gg{
      background:#2b2b2b; border-color:#2b2b2b; color:#fff; font-weight:700;
      border-radius:10px;
    }
    .btn-dark-gg:hover{ filter:brightness(.95); color:#fff; }

    /* Kolom kanan: hero */
    .right-pane{
      position:relative;
      flex: 0 0 48%;
      min-width: 0;
      display:flex;
      align-items:stretch;
      background:
        linear-gradient(90deg, rgba(255,255,255,1) 0%, rgba(255,255,255,.0) 18%),
        radial-gradient(1200px 600px at 100% 0%, rgba(14,165,255,.35), rgba(14,165,255,0) 60%),
        #0b5ed7;
      overflow:hidden;
    }
    .hero-bg{
      position:absolute; inset:0;
      background-image:
        url("assets/hero-seller.jpg"),
        url("https://images.unsplash.com/photo-1604881987921-c27bbe3f0f95?q=80&w=1600&auto=format&fit=crop"); /* fallback demo */
      background-size: cover;
      background-position: center;
      filter: saturate(1.05);
      transform: scale(1.02);
    }
    .hero-overlay{
      position:absolute; inset:0;
      background:
        linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(10,54,130,.45) 35%, rgba(3,19,60,.75) 100%),
        radial-gradient(700px 300px at 20% 50%, rgba(255,255,255,.35), rgba(255,255,255,0) 70%);
      mix-blend-mode: normal;
    }
    .hero-content{
      position:relative; z-index:3;
      color:#fff; padding: clamp(20px, 4vw, 48px);
      margin-left:auto; width: min(520px, 100%);
      display:flex; flex-direction:column; justify-content:center;
    }
    .hero-mark{
      display:flex; align-items:center; gap:10px; margin-bottom:.75rem;
    }
    .hero-mark .bar{ height:4px; width:64px; border-radius:3px; background:#fff; opacity:.95; }
    .hero-mark .dot{ width:12px; height:12px; border-radius:50%; background:#fff; opacity:.9; }

    /* Responsif kecil: stack */
    @media (max-width: 992px){
      .frame{ max-width: 100%; }
      .frame.ratio{ aspect-ratio: unset; } /* biar tinggi mengikuti konten saat mobile */
      .ratio-inner{ flex-direction:column; }
      .right-pane{ min-height: 300px; }
    }
    /* Brand logo (kiri, di atas form) */
.brand-img { display:inline-flex; align-items:center; gap:.5rem; margin-bottom:.25rem; text-decoration:none; }
.brand-logo { height: 22px; width:auto; display:block; }
@media (min-width: 768px){ .brand-logo{ height: 24px; } }
@media (min-width: 1200px){ .brand-logo{ height: 26px; } }

  </style>
</head>
<body>
<body class="bg-auth">


  <!-- Frame 16:9 -->
  <div class="frame ratio ratio-16x9">
    <div class="ratio-inner">
      <!-- LEFT: Sign Up form -->
      <div class="left-pane">
        <div class="signup-card">
          <!-- BRAND pakai PNG -->
<a href="#" class="brand-img" aria-label="GigaGears">
  <img
    src="{{ asset('images/logo GigaGears.png') }}"
    alt="GigaGears"
    class="brand-logo"
    srcset="{{ asset('images/logo GigaGears.png') }} 1x, {{ asset('images/logo GigaGears@2x.png') }} 2x">
</a>


          <h1 class="fw-black mb-2" style="letter-spacing:.2px;">Welcome!</h1>
          <p class="text-muted-gg mb-4">Please enter your data below to create new account</p>

          <!-- Alert -->
          <div id="alertBox" class="alert alert-danger d-none" role="alert" aria-live="polite"></div>
            <form id="signupForm" action="{{ route('register') }}" method="POST">
              @csrf

              <div class="mb-3">
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name" value="{{ old('name') }}" required>
                  @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                  <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Username / Email" value="{{ old('email') }}" required autocomplete="username">
                  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                  <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone Number" value="{{ old('phone') }}" required>
                  @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                  <div class="input-group">
                      <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" minlength="8" required autocomplete="new-password">
                      <button class="btn btn-outline-secondary" type="button" id="toggle1"><i class="bi bi-eye"></i></button>
                  </div>
                  @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                  <div class="form-text">Min. 8 characters</div>
              </div>

              <div class="mb-3">
                  <div class="input-group">
                      <input type="password" name="password_confirmation" id="password2" class="form-control" placeholder="Confirm Password" minlength="8" required autocomplete="new-password">
                      <button class="btn btn-outline-secondary" type="button" id="toggle2"><i class="bi bi-eye"></i></button>
                  </div>
              </div>

              <div class="d-grid">
                  <button class="btn btn-dark btn-lg" type="submit">Sign Up</button>
              </div>

              <p class="mt-3 small text-muted-gg mb-0">Already have an account? <a href="/login" class="text-decoration-none">Sign In</a></p>
            </form>

          </div>
      </div>

      <!-- RIGHT: Hero -->
      <div class="right-pane">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="hero-content">
          <div class="hero-mark">
            <span class="bar"></span>
            <span class="dot"></span>
          </div>
          <h2 class="fw-black mb-2" style="font-size: clamp(1.6rem, 3.2vw, 2.2rem);">Start Selling with</h2>
          <h2 class="fw-black mb-3" style="font-size: clamp(1.6rem, 3.2vw, 2.2rem);">GigaGears</h2>
          <p class="mb-0 text-white-50" style="max-width: 36ch;">
            Join our trusted marketplace and grow your tech business with ease. Create your seller account
            and start reaching thousands of customers today.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const p = document.getElementById('password');
    const c = document.getElementById('password2');
    const t1 = document.getElementById('toggle1');
    const t2 = document.getElementById('toggle2');

    function toggle(btn, input){
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.innerHTML = `<i class="bi ${isText ? 'bi-eye' : 'bi-eye-slash'}"></i>`;
    }

    t1?.addEventListener('click', () => toggle(t1, p));
    t2?.addEventListener('click', () => toggle(t2, c));

    document.getElementById('signupForm').addEventListener('submit', function(e) {
        if (p.value !== c.value) {
            e.preventDefault();
            c.classList.add('is-invalid');
            if (!document.getElementById('confirmError')) {
                c.insertAdjacentHTML('afterend', '<div id="confirmError" class="invalid-feedback d-block">Password confirmation does not match.</div>');
            }
        }
    });

    c.addEventListener('input', function() {
        if (p.value === c.value) {
            c.classList.remove('is-invalid');
            if (document.getElementById('confirmError')) document.getElementById('confirmError').remove();
        }
    });

  </script>
</body>
</html>
