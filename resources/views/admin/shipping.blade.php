<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears Admin — Shipping</title>

  <!-- CSRF token for AJAX -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{asset('css/admin.css')}}">

    <style>
    .ship-card {
      background: #fff;
      border: 1px solid #E5E7EB;
      border-radius: 12px;
      padding: 24px;
      margin-bottom: 16px;
      transition: all 0.25s ease;
    }
    .ship-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }
    .ship-card.inactive {
      opacity: 0.75;
      background: #F9FAFB;
    }
    .ship-label {
      font-size: 0.75rem;
      color: #64748B;
      text-transform: uppercase;
      font-weight: 600;
    }
    .ship-value {
      font-size: 0.95rem;
      font-weight: 600;
      color: #1E293B;
    }
    .ship-value.text-success { color: #16A34A !important; }
    .ship-value.text-danger { color: #DC2626 !important; }

    .admin-side {
      width: 280px;
      height: 100vh;
      background: #fff;
      border-right: 1px solid #E6ECFB;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 0;
      transition: transform .3s ease-in-out;
    }

    .nav-admin {
        flex: 1;
        overflow-y: auto;
    }

    /* Mobile Sidebar */
    @media (max-width: 991.98px) {
        .admin-side {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1050;
            transform: translateX(-100%);
            box-shadow: none;
        }

        .admin-side.show {
            transform: translateX(0);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }
    }

    /* Overlay */
    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        display: none;
        opacity: 0;
        transition: opacity .3s;
    }

    .sidebar-overlay.show {
        display: block;
        opacity: 1;
    }
  </style>
</head>
<body>
   @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index:1100;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
          <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link active" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
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
                <div class="small opacity-75 mb-1">Manage available couriers and their delivery services</div>
                <h1 class="h3 mb-0">Shipping Management</h1>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light btn-sm"><i class="bi bi-bell"></i></button>
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24">
              Admin
            </span>
          </div>
        </div>

        <div class="row g-0 mb-2">
              <div class="col-md-3 ms-auto">
                  <button class="btn btn-primary justify-content-end w-100" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Shipping
                  </button>
              </div>
        </div>

        <div id="shippingListContainer">
          @forelse($shippings as $item)
            <div class="ship-card {{ strtolower($item->status) === 'inactive' ? 'inactive' : '' }}" data-id="{{ $item->id }}">
              <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h4 class="h5 fw-bold mb-0">{{ $item->name }}</h4>
                <div class="btn-group">
                  <button class="btn btn-sm btn-outline-warning px-3"
                    data-item='{{ base64_encode(json_encode($item)) }}'
                    onclick="openEditModalFromButton(this)" data-bs-toggle="modal" data-bs-target="#editModal">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </button>

                  @if(strtolower($item->status) === 'active')
                    <button class="btn btn-sm btn-outline-danger px-3 toggle-status-btn"
                            onclick="toggleStatus({{ $item->id }})">
                      <i class="bi bi-toggle-on me-1"></i> Deactivate
                    </button>
                  @else
                    <button class="btn btn-sm btn-success px-3 toggle-status-btn"
                            onclick="toggleStatus({{ $item->id }})">
                      <i class="bi bi-toggle-off me-1"></i> Activate
                    </button>
                  @endif
                </div>
              </div>

              <div class="row g-3">
                <div class="col-6 col-md-2">
                  <div class="ship-label">Courier</div>
                  <div class="ship-value">{{ $item->name }}</div>
                </div>
                <div class="col-6 col-md-2">
                  <div class="ship-label">Service</div>
                  <div class="ship-value">{{ $item->custom_service ?? $item->service_type }}</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="ship-label">Base Rate</div>
                  <div class="ship-value">Rp{{ number_format($item->base_rate, 0, ',', '.') }}
                    <span class="text-muted small fw-normal">({{ 'Rp'.number_format($item->per_kg,0,',','.') }} /kg)</span>
                  </div>
                </div>
                <div class="col-6 col-md-2">
                  <div class="ship-label">Estimated</div>
                  <div class="ship-value">
                    @if($item->min_delivery_days && $item->max_delivery_days)
                      {{ $item->min_delivery_days }}–{{ $item->max_delivery_days }} days
                    @else - @endif
                  </div>
                </div>
                <div class="col-6 col-md-2">
                  <div class="ship-label">Coverage</div>
                  <div class="ship-value">{{ $item->coverage }}</div>
                </div>
                <div class="col-6 col-md-1 text-md-end">
                  <div class="ship-label">Status</div>
                  <div class="ship-value {{ strtolower($item->status)==='active'?'text-success':'text-danger' }}" data-field="status">
                    {{ strtolower($item->status) }}
                  </div>
                </div>
              </div>
            </div>
            @empty
              <div class="alert alert-info">No shipping methods found.</div>
            @endforelse
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $shippings->links() }}
        </div>

        <p class="text-center mt-5 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>

  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Add Shipping</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="addShippingForm">
          <div class="modal-body">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Courier Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. JNE Express" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Service Type</label>
                <select name="service_type" id="" class="form-select" onchange="toggleCustomServiceAdd(this)">
                  <option value="" selected disabled>Select service</option>
                  <option value="REG">REG (Regular)</option>
                  <option value="ECO">Economy</option>
                  <option value="SDS">Same Day Service</option>
                  <option value="Cargo">Cargo</option>
                  <option value="custom">Other (Custom)</option>
                </select>
                <input type="text" name="custom_service" id="customAdd" class="form-control mt-2 d-none" placeholder="Enter custom service...">
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Base Rate</label>
                <div class="input-group  gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="base_rate" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Rate per Kg</label>
                <div class="input-group gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="per_kg" class="form-control" required>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Min Delivery Days</label>
                <input type="number" name="min_delivery_days" class="form-control" placeholder="e.g. 2" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Max Delivery Days</label>
                <input type="number" name="max_delivery_days" class="form-control" placeholder="e.g. 5" required>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Coverage</label>
                <select name="coverage" class="form-select" readonly>
                  <option selected value="Domestic (Indonesia)">Domestic (Indonesia)</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4">Save Shipping</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Edit Shipping</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editShippingForm">
          <div class="modal-body">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Courier Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. JNE Express" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Service Type</label>
                <select name="service_type" class="form-select" onchange="toggleCustomServiceUpdate(this)">
                  <option value="" selected disabled>Select service</option>
                  <option value="REG">REG (Regular)</option>
                  <option value="ECO">Economy</option>
                  <option value="SDS">Same Day Service</option>
                  <option value="Cargo">Cargo</option>
                  <option value="custom">Other (Custom)</option>
                </select>
                <input type="text" name="custom_service" id="customEdit" class="form-control mt-2 d-none" placeholder="Enter custom service...">
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Base Rate</label>
                <div class="input-group  gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="base_rate" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Rate per Kg</label>
                <div class="input-group gap-1">
                  <span class="input-group-text bg-light">Rp.</span>
                  <input type="number" step="0.01" name="per_kg" class="form-control" required>
                </div>
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Min Delivery Days</label>
                <input type="number" name="min_delivery_days1" class="form-control" placeholder="e.g. 2" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Max Delivery Days</label>
                <input type="number" name="max_delivery_days1" class="form-control" placeholder="e.g. 5" required>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Sidebar overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- BOOTSTRAP JS - must load before scripts that call bootstrap.Modal -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Page scripts (AJAX + modal handling) -->
  <script>
    // helper: parse base64 json payload from button
    function safeJsonParse(str){ try { return JSON.parse(str); } catch(e) { return null; } }
    function decodeDataItem(b64){
      try { return safeJsonParse(atob(b64)); } catch(e) { return null; }
    }

    // Open edit modal from button (data-item contains base64 json)
    function openEditModalFromButton(btn){
      if(!btn) return;
      const b64 = btn.getAttribute('data-item') || btn.dataset.item;
      if(!b64) return console.error('data-item missing');
      const data = decodeDataItem(b64);
      if(!data) return console.error('invalid data-item');
      openEditModal(data);
    }

    // toggle custom input (single clean function)
    function toggleCustomServiceUpdate(select){
      const input = document.getElementById('customEdit');
      if(!input) return;
      const isCustom = (select && select.value === 'custom');
      input.classList.toggle('d-none', !isCustom);
      input.required = isCustom;
      if(!isCustom) input.value = '';
    }
    function toggleCustomServiceAdd(select){
      const input = document.getElementById('customAdd');
      if(!input) return;
      const isCustom = (select && select.value === 'custom');
      input.classList.toggle('d-none', !isCustom);
      input.required = isCustom;
      if(!isCustom) input.value = '';
    }

    // Fill edit form and show modal
    function openEditModal(data){
      if(!data) return;
      const form = document.getElementById('editShippingForm');
      if(!form) return;
      form.setAttribute('data-id', data.id);

      const setField = (name, value) => {
        let el = form.elements[name];
        if(!el) el = form.querySelector('[name="'+name+'"]');
        if(!el) return;
        if(el.tagName === 'SELECT'){
          const opt = Array.from(el.options).find(o => o.value === String(value));
          if(opt) el.value = value;
          else if(String(value) === 'custom') el.value = 'custom';
        } else {
          el.value = (value !== undefined && value !== null) ? value : '';
        }
      };

      setField('name', data.name);
      setField('service_type', data.service_type);
      // handle custom service
      const customEl = document.getElementById('customEdit');
      if(String(data.service_type) === 'custom' && customEl){
        customEl.classList.remove('d-none'); customEl.required = true;
        setField('custom_service', data.custom_service ?? '');
      } else if(customEl){
        customEl.classList.add('d-none'); customEl.required = false; setField('custom_service','');
      }

      setField('base_rate', data.base_rate);
      setField('per_kg', data.per_kg);
      setField('min_delivery_days1', data.min_delivery_days);
      setField('max_delivery_days1', data.max_delivery_days);

      const covHidden = form.querySelector('input[type="hidden"][name="coverage"]');
      if(covHidden && data.coverage !== undefined) covHidden.value = data.coverage;

      const modalEl = document.getElementById('editModal');
      const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.show();
    }

    // Add shipping form submit (AJAX) - already present; kept but ensured CSRF included
    (function attachAddForm(){
      const form = document.getElementById('addShippingForm');
      if(!form) return;
      form.addEventListener('submit', async function(e){
        e.preventDefault();
        const formData = new FormData(form);
        try {
          const res = await fetch("{{ route('admin.shipping.store') }}", {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              "Accept": "application/json"
            },
            credentials: 'same-origin',
            body: formData
          });
          if (res.redirected || res.status === 302) {
            const text = await res.text();
            throw new Error(`Unexpected redirect (status ${res.status}): ${text.slice(0,200)}`);
          }
          const ct = res.headers.get('content-type') || '';
          if(!ct.includes('application/json')) {
            const text = await res.text();
            throw new Error('Expected JSON but got: ' + text.slice(0,200));
          }
          const data = await res.json();
          if(data.success){
            const modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
            if(modal) modal.hide();
            form.reset();
            location.reload();
          } else if(data.errors){
            alert('Validation error: ' + JSON.stringify(data.errors));
          } else {
            alert('Unexpected response');
          }
        } catch(err){
          console.error('Add shipping failed', err);
          alert('Request failed: ' + (err.message||err));
        }
      });
    })();

    // Edit shipping form submit (AJAX with method override PUT)
    (function attachEditFormSubmit(){
      const form = document.getElementById('editShippingForm');
      if(!form) return;
      form.addEventListener('submit', async function(e){
        e.preventDefault();
        const id = form.getAttribute('data-id');
        if(!id) return alert('Missing shipping ID');
        try {
          const formData = new FormData(form);
          if (formData.has('min_delivery_days1')) {
            formData.set('min_delivery_days', formData.get('min_delivery_days1'));
            formData.delete('min_delivery_days1');
          }
          if (formData.has('max_delivery_days1')) {
            formData.set('max_delivery_days', formData.get('max_delivery_days1'));
            formData.delete('max_delivery_days1');
          }
          formData.set('_method','PUT');

          const res = await fetch(`/admin/shipping/${id}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: formData
          });

          if (res.redirected || res.status === 302) {
            const text = await res.text();
            throw new Error('Unexpected redirect: ' + text.slice(0,200));
          }
          if (res.status === 422) {
            const err = await res.json();
            const msgs = (err.errors) ? Object.values(err.errors).flat().join('\n') : JSON.stringify(err);
            return alert('Validation failed:\n' + msgs);
          }
          const ct = res.headers.get('content-type') || '';
          if (!ct.includes('application/json')) {
            const text = await res.text();
            throw new Error('Expected JSON but got: ' + text.slice(0,300));
          }
          const data = await res.json();
          if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            if (modal) modal.hide();
            form.reset();
            location.reload();
          } else {
            alert('Error: ' + (data.message || JSON.stringify(data)));
          }
        } catch(err){
          console.error('Edit submit failed', err);
          alert('Request failed: ' + (err.message || err));
        }
      }, { passive: false });
    })();

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    window.toggleStatus = async function (id) {
      if (!confirm('Change status for this courier?')) return;
      const card = document.querySelector(`.ship-card[data-id="${id}"]`);
      const btn = card.querySelector('.toggle-status-btn');
      const original = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

      try {
        const res = await fetch(`/admin/shipping/${id}/toggle`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Failed');

        const status = data.status.toLowerCase();
        const statusEl = card.querySelector('[data-field="status"]');
        if (statusEl) {
          statusEl.textContent = status;
          statusEl.classList.toggle('text-success', status === 'active');
          statusEl.classList.toggle('text-danger', status !== 'active');
        }

        card.classList.toggle('inactive', status !== 'active');
        if (status === 'active') {
          btn.className = 'btn btn-sm btn-outline-danger px-3 toggle-status-btn';
          btn.innerHTML = '<i class="bi bi-toggle-on me-1"></i> Deactivate';
        } else {
          btn.className = 'btn btn-sm btn-success px-3 toggle-status-btn';
          btn.innerHTML = '<i class="bi bi-toggle-off me-1"></i> Activate';
        }

      } catch (err) {
        alert(err.message);
        btn.innerHTML = original;
      } finally {
        btn.disabled = false;
      }
    };

    // Sidebar toggle (mobile)
    (function sidebarInit(){
      const btnToggle = document.getElementById('btnToggle');
      const sidebar = document.getElementById('adminSidebar');
      const overlay = document.getElementById('sidebarOverlay');
      function toggleSidebar(){ sidebar.classList.toggle('show'); overlay.classList.toggle('show'); }
      if(btnToggle) btnToggle.addEventListener('click', toggleSidebar);
      if(overlay) overlay.addEventListener('click', toggleSidebar);
    })();

    // init: ensure edit modal select visibility correct
    document.addEventListener('DOMContentLoaded', () => {
      const sel = document.querySelector('#editShippingForm [name="service_type"]');
      if(sel) toggleCustomServiceUpdate(sel);
    });

  </script>

</body>
</html>
