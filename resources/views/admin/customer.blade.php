<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>GigaGears Admin — Customer Data</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

  <style>
    /* 1. Fix Lebar & Layout Sidebar */
    .admin-side {
        width: 280px; 
        height: 100vh; 
        background: #fff;
        border-right: 1px solid #E6ECFB;
        flex-shrink: 0;
        transition: transform 0.3s ease-in-out;
        
        /* Flexbox untuk susunan vertikal rapi */
        display: flex;           
        flex-direction: column;  
        position: sticky; top: 0;
    }

    /* Agar Menu bisa di-scroll jika terlalu panjang di HP */
    .nav-scroll-wrap {
        flex: 1;
        overflow-y: auto;
    }

    /* 2. Logika Tampilan Mobile (HP) */
    @media (max-width: 991.98px) {
        .admin-side {
            position: fixed; 
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
        <a href="{{ route('admin.dashboard') }}" class="brand-link" aria-label="GigaGears">
          <img src="{{asset('images/logo GigaGears.png')}}" alt="GigaGears" class="brand-logo">
        </a>

        <div class="nav-scroll-wrap">
            <nav class="nav flex-column nav-admin">
              <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
              <a class="nav-link active" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
              <a class="nav-link" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
              <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
              <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
              <a class="nav-link" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
              <a class="nav-link" href="{{ route('admin.workshops.index') }}"><i class="bi bi-calendar-event"></i>Workshops & Seminars</a>
            </nav>
        </div>

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
                <div class="small opacity-75 mb-1">View and manage all registered customers</div>
                <h1 class="h3 mb-0">Customer Data</h1>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24" alt="A">
              Admin
            </span>
          </div>
        </div>

      <form action="{{ route('admin.customers.index') }}" method="GET">
        <div class="gg-card p-3 p-md-4 mb-3">
          <div class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by ID, Name, Email...">
                  <button class="btn btn-primary px-4">Search</button>
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
                  <th style="width:110px">Cust ID</th>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Phone Number</th>
                  <th style="width:150px">Registered</th>
                  <th style="width:100px">Status</th>
                  <th style="width:160px" class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody id="tbodyCustomers">
                @forelse($customers as $c)
                  <tr>
                      <td class="fw-semibold text-muted">
                          CUST-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}
                      </td>
                      <td class="fw-bold">{{ $c->name }}</td>
                      <td>{{ $c->email ?? '-' }}</td>
                      <td>{{ $c->customerProfile?->phone ?? '-' }}</td>
                      <td>{{ $c->created_at->format('d M Y') }}</td>
                      <td>
                          <span class="badge-status {{ strtolower($c->status) }}">
                              {{ ucfirst($c->status) }}
                          </span>
                      </td>
                      <td class="text-end text-nowrap">
                          {{-- EDIT --}}
                          <button class="btn p-0 border-0 text-secondary ms-2" onclick="openCustomerEdit(this)" data-id="{{ $c->id }}" data-json="{{ url('/admin/customers/'.$c->id.'/json') }}" data-route="{{ route('admin.customers.update', $c->id) }}">
                              <i class="bi bi-pencil-square fs-5"></i>
                          </button>
                          {{-- ACTIVATE / SUSPEND --}}
                          @if(strtolower($c->status) === 'active')
                              <form action="{{ route('admin.customers.update-status', $c->id) }}" 
                                    method="POST" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <input type="hidden" name="status" value="suspended">
                                  <button class="btn p-0 border-0 text-danger ms-2">
                                      <i class="bi bi-dash-circle-fill fs-5"></i>
                                  </button>
                              </form>
                          @elseif(strtolower($c->status) === 'suspended')
                              <form action="{{ route('admin.customers.update-status', $c->id) }}" 
                                    method="POST" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <input type="hidden" name="status" value="active">
                                  <button class="btn p-0 border-0 text-success ms-2">
                                      <i class="bi bi-check-circle-fill fs-5"></i>
                                  </button>
                              </form>
                          @endif
                      </td>
                  </tr>
                  @empty
                  <tr>
                      <td colspan="7" class="text-center text-muted py-4">
                          No customers found.
                      </td>
                  </tr>
                  @endforelse
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-top">
            <div class="small text-muted">{{ $customers->total() }} results</div>
            <nav>{{ $customers->links() }}</nav>
          </div>
        </section>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>

    <!-- EDIT MODAL -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="editUserFormAjax" class="needs-validation" novalidate>
          @csrf
          @method('PATCH')
          <input type="hidden" id="user_id" name="user_id" value="">

          <div class="modal-header">
            <h5 class="modal-title">Edit Customer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" id="name" name="name" class="form-control">
              <div class="invalid-feedback" id="err-name"></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-control">
              <div class="invalid-feedback" id="err-email"></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" id="phone" name="phone" class="form-control">
              <div class="invalid-feedback" id="err-phone"></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea id="address" name="address" class="form-control" rows="2"></textarea>
              <div class="invalid-feedback" id="err-address"></div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="saveAjax" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>


 

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/admin/customer.js') }}"></script>


</body>
</html>