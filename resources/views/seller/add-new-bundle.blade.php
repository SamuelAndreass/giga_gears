<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Add Bundle</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

  <style>
    body { font-family: 'Chakra Petch', sans-serif; }
    .btn-orange { background-color:#FF8C00;color:#fff;border:1px solid #E07B00; }
    .btn-orange:hover { background-color:#E07B00;color:#fff; }
    .gg-card { background:#fff;border:1px solid #E6ECFB;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.05); }
    .form-label { font-weight:600;color:#0d0d0d; }
    .product-select-box { max-height:320px;overflow:auto;border:1px solid #E6ECFB;border-radius:8px;padding:12px; }
    .product-select-item:hover { background:#F8FAFC; }
    /* --- Sidebar toggle for mobile --- */
    @media (max-width: 991.98px) {
    .admin-side {
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 1050;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        box-shadow: none;
    }
    .admin-side.show {
        transform: translateX(0);
        box-shadow: 4px 0 24px rgba(0,0,0,0.15);
    }
    .sidebar-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
        display: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .sidebar-overlay.show {
        display: block;
        opacity: 1;
    }
    }

  </style>
</head>
<body>
  <div class="container-fluid p-0 d-flex" style="min-height:100vh;overflow-x:hidden;">

    {{-- Sidebar --}}
    <div class="sidebar-overlay" id="sidebarOverlay">
        <aside class="admin-side" id="adminSidebar">
            <a href="{{ route('seller.index') }}" class="brand-link" aria-label="GigaGears">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GigaGears" class="brand-logo">
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
            <a class="btn btn-outline-danger w-100" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-1"></i>Log Out</a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </aside>
    </div>
    {{-- Main --}}
    <main class="main-wrap flex-grow-1" style="min-width:0;">
      <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-light d-lg-none shadow-sm p-1 px-2" id="btnToggle" style="border-radius:8px;">
            <i class="bi bi-list fs-3"></i>
          </button>
          <div>
            <div class="small opacity-75 mb-1">Create a new product bundle from existing items</div>
            <h1 class="h3 mb-0">Add New Bundle</h1>
          </div>
        </div>
      </div>

      {{-- FORM --}}
      <form action="{{ route('seller.bundles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="gg-card p-4 mb-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Bundle Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Price</label>
              <input type="number" name="price" min="1" class="form-control" required>
            </div>
            <div class="col-md-3">  
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>Total harga normal:</strong>
                    <span id="normalPrice" class="text-danger">
                        Rp 0
                    </span>
                </div>
            </div>
            

            <div class="col-md-6">
              <label class="form-label">Bundle Image</label>
              <input type="file" name="images" class="form-control">
            </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Select Category --</option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

          <div class="mt-4">
            <label class="form-label">Specifications</label>
            <div id="specWrapper">
                <div class="row g-2 mb-2 spec-row">
                    <div class="col-md-5">
                        <input type="text" name="specs[key][]" class="form-control" placeholder="Spec name (e.g. Processor)">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="specs[value][]" class="form-control" placeholder="Spec value (e.g. Intel i7)">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-spec">&times;</button>
                    </div>
                </div>
            </div>
            <button type="button" id="addSpec" class="btn btn-outline-secondary btn-sm mt-2">
                + Add Spec
            </button>
        </div>

        <div class="col-12 mt-4">
            <label class="form-label">Bundle Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Deskripsikan isi bundle, kegunaan, dan keunggulannya..." required></textarea>
        </div>
        
          <div class="mt-4">
            <label class="form-label">Select Products</label>
            <div class="product-select-box">
                @foreach($products as $p)
                    <div class="d-flex justify-content-between align-items-center product-select-item py-1 border-bottom">
                        <div class="form-check">
                            <input class="form-check-input bundle-product-checkbox" type="checkbox" name="items[{{ $p->id }}][product_id]" value="{{ $p->id }}" data-product-id="{{ $p->id }}" data-price="{{ $p->price }}" id="prod{{ $p->id }}" data-category-id="{{ $p->category_id }}">
                            <label class="form-check-label" for="prod{{ $p->id }}">
                                {{ $p->name }} <small class="text-muted">(Rp {{ number_format($p->price) }})</small>
                            </label>
                        </div>

                        <input type="number" name="items[{{ $p->id }}][qty]" class="form-control form-control-sm text-center bundle-qty" data-product-id="{{ $p->id }}" min="1" value="1" style="width:70px;" disabled>
                    </div>
                @endforeach
                

            </div>
          </div>

          <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('seller.products') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-orange">Create Bundle</button>
          </div>
        </div>
      </form>

      <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const btnToggle=document.getElementById('btnToggle');
    const sidebar=document.getElementById('adminSidebar');
    const overlay=document.getElementById('sidebarOverlay');
    function toggleSidebar(){sidebar.classList.toggle('show');overlay.classList.toggle('show');}
    if(btnToggle)btnToggle.addEventListener('click',toggleSidebar);
    if(overlay)overlay.addEventListener('click',toggleSidebar);

    document.addEventListener('DOMContentLoaded', function () {

        const checkboxes = document.querySelectorAll('.bundle-product-checkbox');
        const priceLabel = document.getElementById('normalPrice');

        function formatRupiah(number) {
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        function recalculateNormalPrice() {
            let total = 0;

            checkboxes.forEach(cb => {
                if (!cb.checked) return;

                const price = parseInt(cb.dataset.price);
                const productId = cb.dataset.productId;

                const qtyInput = document.querySelector(
                    `.bundle-qty[data-product-id="${productId}"]`
                );

                const qty = parseInt(qtyInput.value || 1);
                total += price * qty;
            });

            priceLabel.textContent = formatRupiah(total);
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {

                const productId = this.dataset.productId;
                const qtyInput = document.querySelector(
                    `.bundle-qty[data-product-id="${productId}"]`
                );

                if (this.checked) {
                const categorySelect = document.querySelector('[name="category_id"]');
                if (!categorySelect.value && this.dataset.categoryId) {
                    categorySelect.value = this.dataset.categoryId;
                }
                    qtyInput.disabled = false;
                    qtyInput.value = qtyInput.value || 1;
                } else {
                    qtyInput.disabled = true;
                }

                recalculateNormalPrice();
            });
        });

        document.querySelectorAll('.bundle-qty').forEach(input => {
            input.addEventListener('input', recalculateNormalPrice);
        });

    });

    document.getElementById('addSpec').addEventListener('click', function () {
        const wrapper = document.getElementById('specWrapper');

        const row = document.createElement('div');
        row.classList.add('row', 'g-2', 'mb-2', 'spec-row');

        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="specs[key][]" class="form-control" placeholder="Spec name">
            </div>
            <div class="col-md-6">
                <input type="text" name="specs[value][]" class="form-control" placeholder="Spec value">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-spec">&times;</button>
            </div>
        `;

        wrapper.appendChild(row);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-spec')) {
            e.target.closest('.spec-row').remove();
        }
    });


  </script>
</body>
</html>
