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

        <nav class="nav flex-column nav-admin">
          <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
          <a class="nav-link" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
          <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
          <a class="nav-link active" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link " href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
        </nav>

        <div class="mt-auto pb-4 px-3 pt-3 border-top">
          <a class="btn btn-logout w-100" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-1"></i> Log Out</a>
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
          <div class="d-flex align-items-center gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24">
              Admin
            </span>
          </div>
        </div>

        <form action="{{ route('admin.products.index') }}" method="GET">
          <div class="gg-card p-3 p-md-4 mb-3">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-md-5">
                <div class="input-group">
                  <input id="searchInput" type="text" name="search" class="form-control" placeholder="Search products.." value="{{ request('search') }}">
                  <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
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
                  <th>Seller / Store</th>
                  <th>Price</th>
                  <th class="text-center">Stock</th>
                  <th>Status</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody id="tbodyProduct">
                @foreach ($products as $p)
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

                      {{-- SELLER --}}
                      <td>
                          <a href="#" class="text-decoration-none fw-semibold">
                              {{ $p->sellerStore->name}}
                          </a>
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
                      <td class="text-center">

                          {{-- VIEW BUTTON --}}
                          <button class="btn p-0 border-0 text-primary"
                                  onclick="openView('{{ $p->id }}')"
                                  title="View Detail">
                              <i class="bi bi-eye-fill fs-5"></i>
                          </button>

                          {{-- BAN / UNBAN --}}
                          @if ($p->status === 'banned')
                              <form method="POST" action="{{ route('admin.products.toggle-status', $p->id) }}" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <button class="btn p-0 border-0 text-success ms-2" title="Activate Product" type="submit">
                                      <i class="bi bi-check-circle-fill fs-5"></i>
                                  </button>
                              </form>
                          @else
                              <form method="POST" action="{{ route('admin.products.toggle-status', $p->id) }}" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <button class="btn p-0 border-0 text-danger ms-2" title="Inactive Product" type="submit">
                                      <i class="bi bi-slash-circle-fill fs-5"></i>
                                  </button>
                              </form>
                          @endif

                      </td>
                  </tr>
                  @endforeach
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-top">
            <div class="small text-muted"><span id="countInfo">{{ $products->total() }}</span> results</div>
            {{ $products->links() }}
          </div>
        </section>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>

  <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Product Detail</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <div id="vm-img" class="bg-light rounded d-flex align-items-center justify-content-center"style="height: 250px; border:1px dashed #ccc; background-size:cover; background-position:center;">
                        <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <div class="mt-3 text-center">
                         <div class="fw-bold mb-1">Rating</div>
                         <div class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <span id="vm-rating-number">0.0</span>
                         </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="badge bg-primary mb-2" id="vm-cat">Category</div>
                    <h4 class="fw-bold mb-1" id="vm-name">Product Name</h4>
                    <div class="text-muted small mb-3">SKU: <span id="vm-sku">PRD-000</span></div>
                    <h3 class="fw-bold text-primary mb-3" id="vm-price">$0.00</h3>
                    <div class="row g-3 mb-3">
                        <div class="col-6"><label class="small text-muted fw-bold">SELLER</label><div id="vm-seller" class="fw-semibold">Seller Name</div></div>
                        <div class="col-6"><label class="small text-muted fw-bold">STOCK</label><div id="vm-stock" class="fw-semibold">0 Units</div></div>
                    </div>
                    <div class="bg-light p-3 rounded mb-3">
                        <label class="small text-muted fw-bold mb-1">DESCRIPTION</label>
                        <p id="vm-desc" class="small mb-0 text-secondary">Loading...</p>
                    </div>
                    <div><label class="small text-muted fw-bold mb-1">STATUS</label><br><span id="vm-status" class="badge-status active">Active</span></div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-light">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
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
  <script>
    function openView(id) {
        fetch(`/admin/products/${id}/json`)
            .then(res => res.json())
            .then(p => {

                // IMAGE
                if (p.image) {
                    document.querySelector('#vm-img').style.backgroundImage = `url('${p.image}')`;
                    document.querySelector('#vm-img i').style.display = 'none';
                } else {
                    document.querySelector('#vm-img').style.backgroundImage = 'none';
                    document.querySelector('#vm-img i').style.display = 'block';
                }

                // BASIC FIELDS
                document.querySelector('#vm-name').textContent = p.name;
                document.querySelector('#vm-sku').textContent = p.sku;
                document.querySelector('#vm-cat').textContent = p.category;
                document.querySelector('#vm-price').textContent = "Rp" + Number(p.price).toLocaleString('id-ID');
                document.querySelector('#vm-stock').textContent = p.stock + " Units";
                document.querySelector('#vm-seller').textContent = p.seller;
                document.querySelector('#vm-status').textContent = p.status.slice(0,1).toUpperCase() + p.status.slice(1);
                
                // DESCRIPTION
                document.querySelector('#vm-desc').textContent = p.description ?? '-';

                // STATUS BADGE CLASS
                const badge = document.querySelector('#vm-status');
                badge.className = "badge-status " + p.status.toLowerCase();

                // RATING
                document.querySelector('#vm-rating-number').textContent = p.rating;

                // SHOW MODAL
                let modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
            });
    }
  </script>


</body>
</html>