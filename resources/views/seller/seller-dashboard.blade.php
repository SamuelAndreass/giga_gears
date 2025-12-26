<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Dashboard</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Font (Chakra Petch) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- App CSS -->
  <link rel="stylesheet" href="{{asset('css/styles.css')}}">
</head>
<body>
  <div class="container-fluid">
    <div class="row g-0">
      <!-- ===== SIDEBAR (KONSISTEN) ===== -->
      <aside class="col-12 col-lg-2 side d-none d-lg-block">
        <div class="mb-5">
            <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
              <img src="{{ asset('images/logo Gigagears.png') }}" alt="GigaGears" class="brand-logo">
            </a>
        </div>
        

        <nav class="nav flex-column nav-gg">
          <a class="nav-link active" href="{{ route('seller.index') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('seller.orders') }}"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link" href="{{ route('seller.products') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="{{ route('seller.analytics') }}"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="{{ route('seller.inbox') }}"><i class="bi bi-inbox"></i>Inbox</a>
          <hr>
          <a class="nav-link" href="{{ route('settings.index')}}"><i class="bi bi-gear"></i>Settings</a>
        </nav>

        <div class="mt-4">
          <a class="btn btn-outline-danger w-100" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-1"></i> Log Out</a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </aside>

      <!-- ===== MAIN ===== -->
      <main class="col-12 col-lg-10 main-wrap">

        <!-- App Bar gradient -->
        <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
          <div>
            <div class="small opacity-75 mb-1">Welcome back 👋</div>
            <h1 class="wc-title mb-0">@auth {{Auth::user()->name}} @endauth</h1>
          </div>
          <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
               <img src="{{ Auth::check() ? Auth::user()->avatar_url : asset('images/default-avatar.png') }}" alt="Store Logo"  class="rounded-circle bg-white" style="width:28px;height:28px;object-fit:cover;">
              @auth {{Auth::user()->name}} @endauth
            </span>
          </div>
        </div>

        <!-- KPI Row -->
        <div class="row g-3 mb-3">
          <div class="col-12 col-md-3 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-bag"></i></span>
              <div><div class="small text-muted-gg">Products Sold</div><div class="fs-4 fw-black">{{ $total_order }}</div></div>
            </div>
          </div>
          <div class="col-12 col-md-3 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-piggy-bank"></i></span>
              <div><div class="small text-muted-gg">Monthly Revenue</div><div class="fs-4 fw-black">{{ number_format($monthlyRevenue) }}</div></div>
            </div>
          </div>
          <div class="col-12 col-md-3 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-box-seam"></i></span>
              <div><div class="small text-muted-gg">Active Products</div><div class="fs-4 fw-black">{{$activeProducts}}</div></div>
            </div>
          </div>
          <div class="col-12 col-md-3 col-xl-3">
            <div class="kpi">
              <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="bi bi-star"></i></span>
              <div><div class="small text-muted-gg">Store Rating</div><div class="fs-4 fw-black">{{ $storeRating }}</div></div>
            </div>
          </div>
        </div>

        <!-- Chart -->
        <div class="row g-3">
          <div class="col-12 col-xl">
            <section class="gg-card p-3 p-md-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Sales Chart</h5>
                <div class="badge rounded-pill bg-light text-primary border border-primary px-3 py-2">This Year</div>
              </div>
              <div class="mt-4" style="height:300px;background:linear-gradient(180deg,#f7faff,#fff);border:1px dashed #e1e7f8;border-radius:12px">
                <canvas id="salesChart"></canvas>
              </div>
            </section>
          </div>
        </div>
        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                label: "Revenue",
                data: @json($data),
                borderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
          
      }
  });
  </script>
</body>
</html>
