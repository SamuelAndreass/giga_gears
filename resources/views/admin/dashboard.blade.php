<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Admin Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

  <style>

    :root {
    --pie-1: #4299e1;
    --pie-2: #48bb78;
    --pie-3: #facc15;
    --pie-4: #7c3aed;
    --pie-5: #ef4444;
  }

    /* 1. Fix Lebar & Layout Sidebar */
    .admin-side {
        width: 280px; 
        height: 100vh; /* Tinggi pas setinggi layar */
        background: #fff;
        border-right: 1px solid #E6ECFB;
        flex-shrink: 0;
        transition: transform 0.3s ease-in-out;
        
        /* --- PERBAIKAN UTAMA DISINI --- */
        display: flex;           /* Aktifkan Flexbox */
        flex-direction: column;  /* Susun vertikal */
        overflow-y: auto;        /* Izinkan SCROLL jika layar pendek */
        position: sticky;        /* Tetap di tempat untuk Desktop */
        top: 0;
    }

    /* Agar Menu Mengisi Ruang Kosong (Logout terdorong ke bawah) */
    .nav-admin {
        flex: 1; 
        margin-bottom: 20px;
    }

    /* 2. Logika Tampilan Mobile (HP) */
    @media (max-width: 991.98px) {
        .admin-side {
            position: fixed; /* Melayang di HP */
            top: 0; left: 0; bottom: 0;
            z-index: 1050; 
            transform: translateX(-100%); /* Sembunyi */
            box-shadow: none;
        }
        .admin-side.show {
            transform: translateX(0); /* Muncul */
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }
    }

    /* 3. Overlay Hitam */
    .sidebar-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1040;
        display: none; opacity: 0; transition: opacity 0.3s;
    }
    .sidebar-overlay.show { display: block; opacity: 1; }
  </style>
</head>
<body>
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1100;">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  <div class="container-fluid p-0 d-flex" style="min-height: 100vh; overflow-x: hidden;">
    
    <aside class="admin-side" id="adminSidebar">
        <a href="{{ route('admin.customers.index') }}" class="brand-link" aria-label="GigaGears">
          <img src="{{asset('images/logo GigaGears.png')}}" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-admin">
          <a class="nav-link active" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
          <a class="nav-link" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
          <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
          <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
          <a class="nav-link" href="{{ route('admin.workshops.index') }}"><i class="bi bi-calendar-event"></i>Workshops & Seminars</a>
        </nav>

        <div class="mt-auto pb-4 px-3">
           <a href="#" class="btn btn-logout w-100" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
              <i class="bi bi-box-arrow-right me-1"></i> Log Out
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
          </form>
        </div>
    </aside>

    <main class="main-wrap flex-grow-1" style="min-width: 0;">

        <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
          
          <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none shadow-sm p-1 px-2" id="btnToggle" style="border-radius: 8px;">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div>
                <div class="small opacity-75 mb-1">Welcome back 👋</div>
                <h1 class="mb-0">Admin</h1>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <span class="rounded-circle bg-white text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px">A</span>
              Admin
            </span>
          </div>
        </div>

        <!-- Notification & Activity Log 
        <div class="gg-card p-3 p-md-4 mb-3">
          <div class="fw-semibold mb-2">Notifikasi & Activity Log</div>
            <div class="row g-2">
              <div class="col-12 col-md-6">
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                  <b>5</b> new products pending verification
                  <button class="btn btn-sm btn-outline-secondary ms-auto">Verif</button>
                </div>
              </div>
            
              <div class="col-12 col-md-6">
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                  <i class="bi bi-exclamation-octagon-fill"></i>
                  <b>2</b> withdraw requests awaiting approval
                  <button class="btn btn-sm btn-outline-secondary ms-auto">Verif</button>
                </div>
              </div>
            </div>
        </div>
        -->
        <div class="row g-3 mb-3">
          <div class="col-12 col-md-6 col-xl-4">
            <div class="kpi">
              <span class="kpi-icon"><i class="bi bi-people"></i></span>
              <div><div class="small text-muted-gg">Total Customer</div><div class="fs-4 fw-black">{{ $t_customer}}</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <div class="kpi">
              <span class="kpi-icon"><i class="bi bi-person-badge"></i></span>
              <div><div class="small text-muted-gg">Total Sellers</div><div class="fs-4 fw-black">{{ $t_seller }}</div></div>
            </div>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <div class="kpi">
              <span class="kpi-icon"><i class="bi bi-cash-coin"></i></span>
              <div><div class="small text-muted-gg">Total Transactions</div><div class="fs-4 fw-black">{{ $t_transaction }}</div></div>
            </div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-12 col-xl-8">
            <section class="gg-card p-3 p-md-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold">Customer Growth</div>
                @if(empty($customerData) || count(array_filter($customerData)) === 0)
                  <div class="text-muted small">No customer data available.</div>
                @else
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary disabled">This Year</button>
                  </div>
                @endif
              </div>
              <div style="height: 240px;">
                  <canvas id="customerChart" height="120"></canvas>
              </div>
            </section>
          </div>
          <div class="col-12 col-xl-4">
            <section class="gg-card p-3 p-md-4">
              <div class="fw-semibold mb-2">Revenue by Category</div>
              @if(empty($labels) || count($labels) === 0)
                <p class="text-center text-muted small mt-3 mb-0">No revenue data available.</p>
              @else
                <canvas id="revenueChart" height="50"></canvas>
                <ul class="mt-3 mb-0 small">
                  @foreach ($revenues as $rvn)
                    <li><span style="display:inline-block;width:10px;height:10px;background:var(--pie-{{ $loop->index + 1 }});border-radius:3px;margin-right:8px"></span>{{ $rvn->category_name }} — {{ $percentages[$loop->index] }}%</li>
                  @endforeach
                </ul>
              @endif
            </section>
          </div>
        </div>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. Admin Panel.</p>
    </main>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  
  <script>
    const btnToggle = document.getElementById('btnToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    if(btnToggle) btnToggle.addEventListener('click', toggleSidebar);
    if(overlay) overlay.addEventListener('click', toggleSidebar);
    
    const ctx = document.getElementById('customerChart').getContext('2d');
    const customerChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Customers',
                data: @json($customerData),
                borderWidth: 2,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.25)',
                borderRadius: 4,
                maxBarThickness: 40,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 100
                    }
                }
            }
        }
    });
  
    const revLabels = @json($labels ?? []);
    const revValues = @json($values ?? []);
    const revPercentages = @json($percentages ?? []);

    // Jika tidak ada data, jangan inisialisasi chart
    const revenueCanvas = document.getElementById('revenueChart');
    const revenueList = document.querySelector('section.gg-card ul.mt-3'); // adjust jika struktur beda

    if (!revenueCanvas) {
        console.warn('revenueChart canvas not found');
    } else if (!revValues.length || revValues.reduce((a,b)=>a+b,0) === 0) {
        // Tampilkan empty-state visual (opsional)
        revenueCanvas.style.display = 'none';
        if (revenueList) {
            revenueList.innerHTML = '<li class="text-muted">No revenue data.</li>';
        }
    } else {
    // Pilih warna: (A) ambil dari CSS var --pie-1..5 jika tersedia, (B) fallback palette
    const fallbackColors = [
        'rgba(66,153,225,0.85)',
        'rgba(72,187,120,0.85)',
        'rgba(250,204,21,0.85)',
        'rgba(124,58,237,0.85)',
        'rgba(239,68,68,0.85)',
        'rgba(99,102,241,0.85)',
        'rgba(16,185,129,0.85)'
    ];

    // Try read CSS vars --pie-1..n
    const cssColors = [];
    for (let i = 1; i <= revLabels.length; i++) {
        const varName = `--pie-${i}`;
        const val = getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
        if (val) cssColors.push(val);
        else break;
    }
    const palette = cssColors.length >= revLabels.length ? cssColors : fallbackColors;

    const rctx = revenueCanvas.getContext('2d');
    const revenueChart = new Chart(rctx, {
        type: 'pie',
        data: {
            labels: revLabels,
            datasets: [{
                data: revValues,
                backgroundColor: palette.slice(0, revLabels.length),
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }, // kita pakai legend HTML custom
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const idx = ctx.dataIndex;
                            const value = ctx.parsed;
                            const percent = revPercentages[idx] ?? ( (revValues.reduce((a,b)=>a+b,0) ? ((value / revValues.reduce((a,b)=>a+b,0)) * 100).toFixed(2) : 0) );
                            const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
                            return `${ctx.label}: ${formatted} — ${percent}%`;
                        }
                    }
                }
            }
        }
    });

    // Sync warna ke legend list (ul > li > span) — cocok dengan markup yang kamu pakai
    if (revenueList) {
        const items = revenueList.querySelectorAll('li');
        items.forEach((li, i) => {
            const dot = li.querySelector('span');
            if (dot) {
                dot.style.background = palette[i % palette.length];
            }
        });
        }
    }

  </script>

</body>
</html>