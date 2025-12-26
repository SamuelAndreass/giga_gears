<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') | Seller Panel GigaGears</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  
  {{-- Custom Style untuk Dashboard (Sidebar) --}}
  <style>
    .sidebar {
        width: 250px; /* Lebar Sidebar */
        height: 100vh; /* Tinggi penuh layar */
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        padding-top: 56px; /* Space di bawah Navbar */
    }
    .content-wrapper {
        margin-left: 250px; /* Sesuaikan dengan lebar sidebar */
        padding-top: 56px; /* Sesuaikan dengan tinggi navbar */
    }
    .nav-link {
        padding: 10px 15px;
        color: rgba(255, 255, 255, 0.8);
    }
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
    }
  </style>

</head>
<body>
  
  {{-- 1. NAVBAR (Header) Seller - Warna berbeda dari Customer --}}
  <nav class="navbar navbar-expand-lg navbar-dark bg-secondary fixed-top">
    <div class="container-fluid">
      {{-- GIGAGEARS Logo di Navbar --}}
      <a class="navbar-brand ms-3 fw-bold" href="/seller/dashboard">GIGAGEARS Seller Panel</a>
      
      {{-- Menu Kanan: Profil Seller --}}
      <ul class="navbar-nav ms-auto me-3">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Nama Seller
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="/seller/settings">Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/logout">Log Out</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  {{-- 2. SIDEBAR (Navigasi Kiri) --}}
  <aside class="sidebar bg-dark text-white">
    <ul class="nav flex-column mt-3">
        {{-- Menu sesuai PDF Halaman 14 --}}
        <li class="nav-item">
            <a class="nav-link active" href="/seller/dashboard">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/seller/orders">
                <i class="fas fa-box me-2"></i> Order Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/seller/products">
                <i class="fas fa-cube me-2"></i> Product Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/seller/balance">
                <i class="fas fa-wallet me-2"></i> Balance & Withdraw
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/seller/analytics">
                <i class="fas fa-chart-line me-2"></i> Analytics & Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/seller/inbox">
                <i class="fas fa-inbox me-2"></i> Inbox (Chat)
            </a>
        </li>
    </ul>
  </aside>

  {{-- 3. KONTEN UTAMA --}}
  <div class="content-wrapper p-4">
    {{-- Di sinilah isi Dashboard Seller muncul --}}
    @yield('content') 
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  {{-- Tambahkan Font Awesome untuk ikon (fas fa-...) --}}
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>