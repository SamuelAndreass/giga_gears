<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Products</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{asset('css/admin.css')}}">

  <style>
    /* --- Layout Fix (Sidebar & Mobile) --- */
    .admin-side {
        width: 280px; height: 100vh; background: #fff; border-right: 1px solid #E6ECFB;
        flex-shrink: 0; transition: transform 0.3s ease-in-out;
        display: flex; flex-direction: column; position: sticky; top: 0;
    }
    .nav-admin { flex: 1; overflow-y: auto; }

    @media (max-width: 991.98px) {
        .admin-side { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1050; transform: translateX(-100%); box-shadow: none; }
        .admin-side.show { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,0.15); }
    }
    .sidebar-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); 
        z-index: 1040; display: none; opacity: 0; transition: opacity 0.3s;
    }
    .sidebar-overlay.show { display: block; opacity: 1; }

    /* --- Style Khusus Product Page --- */
    .table-blue-head thead th {
      background-color: #0D6EFD !important; color: #fff !important;
      font-weight: 600; border: 0; padding: 14px;
    }
    .table-blue-head thead th:first-child { border-top-left-radius: 8px; }
    .table-blue-head thead th:last-child { border-top-right-radius: 8px; }
    .table-blue-head tbody td {
      padding: 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
      color: var(--gg-body); font-weight: 500; font-size: 0.95rem;
    }
    .btn-orange { background-color: #FF8C00; color: white; border: 1px solid #E07B00; }
    .btn-orange:hover { background-color: #E07B00; color: white; }

    /* Product Specific */
    .product-thumb {
        width: 48px; height: 48px; background: #F1F5F9; border: 1px solid #E2E8F0; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; color: #94A3B8; font-size: 1.2rem;
    }
    .badge-cat {
        font-size: 0.75rem; padding: 4px 8px; border-radius: 6px;
        background: #EEF2FF; color: #4F46E5; font-weight: 600; border: 1px solid #C7D2FE;
    }
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
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="{{asset('images/logo GigaGears.png')}}" alt="GigaGears" class="brand-logo">
        </a>

         <nav class="nav flex-column nav-gg">
          <a class="nav-link" href="{{ route('seller.index') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('seller.orders') }}"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link active" href="{{ route('seller.products') }}"><i class="bi bi-box"></i>Products</a>
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

    <main class="main-wrap flex-grow-1" style="min-width: 0;">

        <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none shadow-sm p-1 px-2" id="btnToggle" style="border-radius: 8px;">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div>
                <div class="small opacity-75 mb-1">Manage and moderate all products listed by sellers</div>
                <h1 class="h3 mb-0">All Products</h1>
            </div>
          </div>
          <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
               <img src="{{ Auth::check() ? Auth::user()->avatar_url : asset('images/default-avatar.png') }}" alt="Store Logo"  class="rounded-circle bg-white" style="width:28px;height:28px;object-fit:cover;">
              @auth {{Auth::user()->name}} @endauth
            </span>
          </div>
        </div>

        <form action="{{ route('seller.products') }}" method="GET">
          <div class="gg-card p-3 p-md-4 mb-3">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-md-5">
                <div class="input-group">
                  <input id="searchInput" type="text" name="search" class="form-control" placeholder="Search products.." value="{{ request('search') }}">
                  <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
              </div>
              <div class="col-12 col-md-auto justify-self-end ms-md-auto">
                  <a href="{{ route('seller.view.add.product') }}" class="btn btn-orange d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Add Product
                  </a>
                </div>
            </div>

          </div>
        </form>

        <section class="gg-card p-0">
          <div class="table-responsive">
            <table class="table table-blue-head table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th style="width:350px">Product Info</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th class="text-center">Stock</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="tbodyProduct">
                @foreach ($product as $p)
                  <tr>
                      {{-- PRODUCT NAME + ID --}}
                      <td>
                          <div class="d-flex align-items-center gap-3">
                              <div class="product-thumb"><i class="bi bi-box-seam"></i></div>
                              <div>
                                  <div class="fw-bold text-dark">
                                      {{ $p->name }}
                                  </div>
                                  <div class="small text-muted">
                                      ID: {{ $p->id }}
                                  </div>
                              </div>
                          </div>
                      </td>

                      {{-- CATEGORY --}}
                      <td>
                          <span class="badge-cat">
                              {{ $p->category->name}}
                          </span>
                      </td>

                      {{-- PRICE --}}
                      <td class="fw-bold">
                          Rp{{ number_format($p->original_price, 0, ',', '.') }}
                      </td>

                      {{-- STOCK --}}
                      <td class="text-center">
                          {{ $p->stock }}
                      </td>

                      {{-- STATUS --}}
                      <td>
                          <span class="badge-status {{ strtolower(str_replace(' ', '-', $p->status)) }}">
                              {{ ucfirst($p->status) }}
                          </span>
                      </td>

                      {{-- ACTION BUTTONS --}}

                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-top">
            <div class="small text-muted"><span id="countInfo">{{ $product->total() }}</span> results</div>
            {{ $product->links() }}
          </div>
        </section>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
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
  </script>

</body>
</html>
