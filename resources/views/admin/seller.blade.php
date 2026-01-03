<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Seller Data</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{asset('css/admin.css')}}">

  <style>
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

    /* --- Style Modal Seller Detail --- */
    .detail-wrapper { background: #F5F8FF; padding: 24px; border-radius: 0 0 12px 12px; }
    .seller-header-card {
      border-radius: 16px; overflow: visible; position: relative; 
      background: #fff; border: 1px solid #E6ECFB; box-shadow: 0 4px 12px rgba(19,87,255,.05);
      margin-bottom: 24px; margin-top: 30px;
    }
    .seller-banner {
      height: 140px; background: linear-gradient(90deg, #1e293b 0%, #0f172a 100%); 
      width: 100%; border-radius: 16px 16px 0 0;
    }
    .seller-avatar-wrap {
      position: absolute; top: 90px; left: 24px; width: 110px; height: 110px;
      border-radius: 50%; background: #fff; padding: 5px; 
      box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10;
    }
    .seller-avatar-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .seller-info-header { 
      padding-left: 170px !important; padding-top: 16px; padding-bottom: 20px; padding-right: 24px;
      display: flex; justify-content: space-between; align-items: flex-start; min-height: 80px;
    }
    #vm-store-name { margin-top: 4px; }
    .stat-card-box {
      background: #fff; border: 1px solid #E6ECFB; border-radius: 14px; padding: 16px; text-align: center; height: 100%;
      box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .stat-value { font-size: 1.6rem; font-weight: 800; margin-bottom: 0; line-height: 1.2; }
    .stat-label { color: #8290A5; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; }
    .table-blue-head thead th { background-color: #0D6EFD !important; color: #fff !important; font-weight: 600; border: none; padding: 12px 14px; }
    .table-blue-head thead th:first-child { border-top-left-radius: 8px; }
    .table-blue-head thead th:last-child { border-top-right-radius: 8px; }
    .table-blue-head tbody td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; font-size: 0.9rem; }
    .bank-box { background: #F8FAFC; border-left: 4px solid #1357FF; padding: 10px 12px; border-radius: 0 8px 8px 0; margin-top: 8px; }
    .btn-orange { background-color: #FF8C00; color: white; border: 1px solid #E07B00; }
    .btn-orange:hover { background-color: #E07B00; color: white; }
    .btn-action-pill { padding: 4px 12px; font-size: 0.75rem; border-radius: 6px; font-weight: 600; }
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

        <nav class="nav flex-column nav-admin">
          <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
          <a class="nav-link active" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
          <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
          <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
        </nav>

        <div class="mt-auto pb-4 px-3">
          <a class="btn btn-logout w-100" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-1"></i> Log Out</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
                <div class="small opacity-75 mb-1">View and manage all registered sellers</div>
                <h1 class="h3 mb-0">Seller Data</h1>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24" alt="A">
              Admin
            </span>
          </div>
        </div>

        <form action="{{ route('admin.sellers.index') }}" method="GET" >
          <div class="gg-card p-3 p-md-4 mb-3">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-md-6">
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-search"></i></span>
                  <input id="searchInput" name="search" value="{{ request('search') }}" type="text" class="form-control" placeholder="Search by ID, Store Name, Owner...">
                  <div class="d-none">
                      <button class="btn btn-primary" >Search</button>
                  </div>
                  
                </div>
              </div>              
            </div>
          </div> 
        </form>
        
        <section class="gg-card p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th style="width:110px">Seller ID</th>
                  <th>Store Name</th>
                  <th>Owner Name</th>
                  <th>Email</th>
                  <th>Phone Number</th>
                  <th class="text-center" style="width:120px">Total Product</th>
                  <th style="width:100px">Status</th>
                  <th style="width:160px" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody id="tbodySellers">
                @forelse($sellers as $s)
                <tr>
                  <td class="fw-semibold text-muted">STORE-{{ $s->id }}</td>
                  <td class="fw-bold">{{ $s->store_name }}</td>
                  <td>{{ $s->user->name ?? '-' }}</td>
                  <td><span class="text-opacity-75">{{ $s->user->email ?? '-' }}</span></td>
                  <td>{{ $s->store_phone ?? '-' }}</td>
                  <td class="text-center">{{ $s->products_count }}</td>
                  <td><span class="badge-status {{ strtolower($s->status) }}">{{ ucfirst($s->status) }}</span></td>
                  <td class="text-end text-nowrap">
                    <div class="d-inline-flex align-items-center gap-1">
                      <button class="btn btn-icon rounded-circle border-0 text-primary" title="View" onclick="openSellerView({{ $s->id }})" data-id="{{ $s->id }}">
                        <i class="bi bi-eye-fill fs-5"></i>
                      </button>

                      {{-- Toggle status form --}}
                    <form action="{{ route('admin.sellers.update-status', $s->id, $s->user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')

                        @if(strtolower($s->status) === 'active')
                            <input type="hidden" name="status" value="suspended">
                            <button class="btn btn-icon rounded-circle border-0 text-danger" title="Suspend">
                                <i class="bi bi-dash-circle-fill fs-5"></i>
                            </button>
                        @else
                            <input type="hidden" name="status" value="active">
                            <button class="btn btn-icon rounded-circle border-0 text-success" title="Activate">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                            </button>
                        @endif
                    </form>

                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="8" class="text-center text-muted py-4">No sellers found.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-top">
            <div class="small text-muted">{{ $sellers->total() }} results</div>
            <nav>{{ $sellers->links() }}</nav>
          </div>
        </section>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>

  <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content border-0">
        
        <div class="modal-header border-0 pb-0">
          <div>
             <h5 class="modal-title fw-bold">Seller Detail</h5>
             <p class="text-muted small mb-0">View complete information about <span id="vm-store-title" class="fw-semibold">the store</span></p>
          </div>
          <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-0 pt-3">
          <div class="detail-wrapper">
              
              <section class="seller-header-card">
                <div class="seller-banner">
                   <div style="width:100%; height:100%; background: url('https://picsum.photos/seed/store/1000/300?grayscale') center/cover opacity:0.3;"></div>
                </div>
                <div class="seller-avatar-wrap">
                  <img id="vm-avatar" src="" class="seller-avatar-img" alt="Logo">
                </div>
                <div class="seller-info-header px-4">
                  <div>
                    <h2 id="vm-store-name" class="h4 fw-bold mb-1">Store Name</h2>
                    <p class="text-muted small mb-0" id="vm-location">
                      <i class="bi bi-geo-alt-fill me-1"></i> -
                    </p>

                  </div>
                  <div class="d-flex gap-2">
                     <span id="vm-header-actions"></span>
                  </div>
                </div>
              </section>

              <div class="row g-4">
                <div class="col-12 col-lg-4">
                  <div class="gg-card p-4 h-100">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Store Information</h5>
                    <div class="mb-3"><label class="small text-muted d-block">Owner Name</label><span id="vm-owner" class="fw-semibold">-</span></div>
                    <div class="mb-3"><label class="small text-muted d-block">Email Address</label><span id="vm-email" class="fw-semibold text-primary">-</span></div>
                    <div class="mb-3"><label class="small text-muted d-block">Phone Number</label><span id="vm-phone" class="fw-semibold">-</span></div>
                    <div class="mb-3"><label class="small text-muted d-block">Status</label><span id="vm-status-badge" class="badge-status">-</span></div>
                    <div class="mt-4">
                      <label class="small text-muted d-block mb-1">Bank Account</label>
                      <div class="bank-box">
                        <div class="fw-bold">BCA — 1234567890</div>
                        <div id="vm-bank-name" class="small text-muted">a.n Owner</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-lg-8">
                  <h5 class="fw-bold mb-3">Store Performance</h5>
                  <div class="row g-3">
                    
                      <!-- Active Products -->
                    <div class="col-6 md-6">
                        <div class="stat-card-box">
                            <div class="stat-label">Active Products</div>
                            <div class="stat-value text-primary" id="vm-stat-prod">0</div>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="col-6 md-6">
                        <div class="stat-card-box">
                            <div class="stat-label">Total Revenue</div>
                            <div class="stat-value text-success" id="vm-revenue">$0</div>
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div class="col-6 md-6">
                        <div class="stat-card-box">
                            <div class="stat-label">Total Orders</div>
                            <div class="stat-value text-info" id="vm-orders">0</div>
                        </div>
                    </div>
                </div>
            </div>

              <div class="mt-4">
                <h5 class="fw-bold mb-3">Product List</h5>
                <div class="gg-card p-0 overflow-hidden">
                  <div class="table-responsive">
                    <table class="table table-blue-head mb-0" id="vm-products-table">
                      <thead>
                        <tr>
                          <th>Product ID</th>
                          <th>Product Name</th>
                          <th>Price</th>
                          <th>Stock</th>
                          <th>Sold</th>
                          <th>Status</th>
                          <th class="text-end">Rating</th></tr></thead>
                      <tbody>
                      </tbody>
                      
                    </table>
                    <div id="vm-products-pager-container" class="mt-3 d-flex justify-content-between px-2"></div>

                  </div>
                </div>
              </div>

          </div>
        </div>
        <div class="modal-footer bg-light border-top-0">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script src="{{ asset('js/admin/seller.js') }}"></script>


</body>
</html>