<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Transactions</title>

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

    /* --- Style Khusus Transaction --- */
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

    .btn-refund {
        color: #0EA5E9; border: 1px solid #0EA5E9;
        font-size: 0.75rem; font-weight: 600; padding: 4px 12px; border-radius: 6px;
        background: #F0F9FF;
    }
    .btn-refund:hover { background: #0EA5E9; color: white; }

    /* Invoice Modal Style */
    .invoice-box { border: 1px solid #eee; padding: 30px; border-radius: 16px; background: #fff; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
    .invoice-header { border-bottom: 2px dashed #eee; padding-bottom: 20px; margin-bottom: 20px; }
    .invoice-total { background: #F8FAFC; padding: 15px; border-radius: 8px; }
    .line-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
    .line-item.total { font-weight: 700; font-size: 1.1rem; color: var(--gg-primary); margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd; }
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
          <a class="nav-link active" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
          <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
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
                <div class="small opacity-75 mb-1">Monitor all recent transactions on GigaGears</div>
                <h1 class="h3 mb-0">Latest Transactions</h1>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24">
              Admin
            </span>
          </div>
        </div>
        <section class="gg-card p-0">
          <div class="table-responsive">
            <table class="table table-blue-head table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Seller</th>
                  <th>Product</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
                </thead>
                <tbody id="tbodyTrans">
                  @forelse ($items as $item)
                    @php
                        $order = $item->order;
                        $product = $item->product;
                        $seller = $product->sellerStore->name;
                        $customer = $order->user->name;
                        $price = $item->unit_price ?? $product->price ?? 0;
                        $amount = $item->subtotal;

                        $status = $order->status;
                    @endphp
                   <tr>
                      <td class="text-muted fw-semibold small">ORD-{{ $order->id }}</td>
                      <td class="fw-bold small">{{ $order->order_date}}</td>
                      <td>{{$customer}}</td>
                      <td>{{ $seller }}</td>
                      <td class="fw-bold text-primary">{{ \Illuminate\Support\Str::limit($product->name, 20) }}</td>
                      <td class="fw-bold"> Rp. {{number_format($amount, 2, '.', ',') }}</td>
                      <td><span class="fw-bold small
                          @if($status === 'active' || $status === 'completed') text-success
                          @elseif($status === 'refunded') text-danger
                          @else text-warning
                          @endif">{{ ucfirst($status) }}</span></td>
                    </tr>
                   @empty
                    <tr>
                      <td colspan="7" class="text-center py-4">
                        <div class="text-muted">No transactions found.</div>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-top">
            <div class="small text-muted"><span id="countInfo">{{ $items->total() }}</span> results</div>
            {{ $items->links() }}
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