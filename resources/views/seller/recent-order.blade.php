  <!doctype html>
  <html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GigaGears — Orders</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

  </head>
  <body>
  
    <div class="container-fluid">
      <div class="row g-0">
        <!-- ===== SIDEBAR (KONSISTEN) ===== -->
        <aside class="col-12 col-lg-2 side d-none d-lg-block">
          <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GigaGears" class="brand-logo">
          </a>

          <nav class="nav flex-column nav-gg">
            <a class="nav-link" href="{{ route('seller.index') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
            <a class="nav-link active" href="{{ route('seller.orders') }}"><i class="bi bi-bag"></i>Order</a>
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
              <div class="small opacity-75 mb-1">Orders</div>
              <h1 class="wc-title mb-0">Your Order</h1>
            </div>
            <div class="d-flex gap-2">
              <span class="badge-chip d-inline-flex align-items-center gap-2">
                <img src="{{ Auth::check() ? Auth::user()->avatar_url : asset('images/default-avatar.png') }}" alt="Store Logo"  class="rounded-circle bg-white" style="width:28px;height:28px;object-fit:cover;">
                @auth {{Auth::user()->name}} @endauth
              </span>
          </div>
          </div>

          <!-- Filter row -->
          <div class="gg-card p-3 p-md-4 mb-3">
            <div class="row g-2 align-items-center">
              
              <div class="col-6 col-md-5 col-lg-10">
                <form action="{{ route('seller.orders') }}" method="GET" class="col-12 col-md-4 col-lg-6">
                    <select id="orderStatus" class="form-select" name="status">
                      <option value="">Aplikasi (all)</option>
                      <option value="pending" @selected(request('status')=='pending')>Pending</option>
                      <option value="processing" @selected(request('status')=='processing')>Processing</option>
                      <option value="completed" @selected(request('status')=='completed')>Completed</option>
                      <option value="cancelled" @selected(request('status')=='cancelled')>Cancelled</option>
                      <option value="refunded" @selected(request('status')=='refunded')>Refunded</option>
                    </select>
              </div>
              <div class="col-6 col-md-3 col-lg-2">
                <button type="submit" id="orderApply" class="btn btn-primary w-100">
                  <i class="bi bi-funnel me-1"></i>Apply
                </button>
                </form>
              </div>
            </div>
          </div>

          <!-- Recent Orders table -->
          <section class="gg-card p-0 orders">
            <div class="d-flex align-items-center justify-content-between px-3 px-md-4 py-3 border-bottom">
              <h5 class="mb-0">Recent Order</h5>
            </div>

            <div class="table-responsive p-3">
              <table id="ordersTable" class="table mb-0">
                <thead>
                  <tr>
                    <th style="width:110px">Order ID</th>
                    <th>Product</th>
                    <th style="width:160px">Customer</th>
                    <th style="width:120px">Status</th>
                    <th class="col-qty">QTY</th>
                    <th style="width:90px">Total</th>
                    <th style="width:140px">Date</th>
                    <th style="width:120px">Actions</th>
                  </tr>
                </thead>
                <tbody id="ordersBody">
                  @forelse ($orders as $item)
                    <tr>
                      <th scope="row">
                        {{-- aman: cek order dulu --}}
                        @if(optional($item->order)->id)
                          ORD-{{ $item->id }}
                        @else
                          -
                        @endif
                      </th>

                      <td>
                        {{ optional($item->product)->name ?? '-' }}
                      </td>

                      <td>
                        {{-- cek order->user --}}
                        {{ optional(optional($item->order)->user)->name ?? (optional($item->order)->shipping_name ?? '-') }}
                      </td>

                      <td>
                        {{ ucfirst(optional($item->order)->status)  ?? '-' }}
                      </td>

                      <td>
                        {{-- total_qty dari order item, fallback ke quantity --}}
                        {{ $item->qty }}
                      </td>

                      <td>
  
                          Rp{{ number_format($item->price, 0, ',', '.') }}

                      </td>

                      <td>
                        {{ optional($item->order_date)->format('Y-m-d H:i') ?? (optional($item->created_at)->format('Y-m-d H:i') ?? '-') }}
                      </td>

                      <td>
                          @if(in_array(optional($item->order)->status, ['pending', 'processing']))
                              <button class="btn btn-primary" onclick="openUpdateModal({{ $item->order->id }})">
                                  Update
                              </button>
                          @endif
                      </td>

                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="text-center text-muted py-4">Belum ada order.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>

              <div class="mt-4">
                  {{ $orders->links() }}
              </div>
            </div>
          </section>

          <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
        </main>

        <div class="modal fade" id="orderUpdateModal" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">              
              <div class="modal-header">
                <h5 class="modal-title">Ship Order</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                <input type="hidden" id="ship-order-id">

                <div class="mb-3">
                  <label class="form-label">Courier</label>
                  <select id="ship-courier" class="form-select"></select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Tracking Number</label>
                  <input type="text" id="ship-tracking" class="form-control">
                </div>

                <div class="mb-3">
                  <label class="form-label">Shipping Cost</label>
                  <input type="text" id="ship-cost" class="form-control" readonly>
                </div>

                <div class="mb-3">
                  <label class="form-label">ETA</label>
                  <input type="text" id="ship-eta" class="form-control" readonly>
                </div>

              </div>

              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="ship-submit" type="button">Ship Now</button>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // url templates must be defined as before
    const shipDataUrlTemplate = @json(route('seller.orders.shipData', ['id' => '__ID__']));
    const shipPostUrlTemplate = @json(route('seller.orders.ship', ['id' => '__ID__']));

    let courierData = [];
    let currentOrderWeight = 0;

    function openUpdateModal(orderId) {
      console.log('openUpdateModal called', orderId);
      const url = shipDataUrlTemplate.replace('__ID__', orderId);

      fetch(url)
        .then(res => {
          if (!res.ok) return res.text().then(t => { throw new Error('HTTP ' + res.status + ' ' + t) });
          return res.json();
        })
        .then(data => {
          // normalize couriers into array
          const raw = data.couriers || [];
          courierData = raw.map(c => ({
            // keep raw fields and normalized numeric fields
            raw: c,
            name: (c.name ?? c.courier_name ?? c.service_type ?? '').toString(),
            base_rate: Number(c.base_rate ?? 0),
            per_kg: Number(c.per_kg ?? 0),
            min_delivery_days: Number(c.min_delivery_days ?? 0),
            max_delivery_days: Number(c.max_delivery_days ?? c.min_delivery_days ?? 0)
          }));

          currentOrderWeight = Number(data.weight ?? 0);

          const sel = document.getElementById('ship-courier');
          sel.innerHTML = '';

          // placeholder
          const placeholder = document.createElement('option');
          placeholder.value = '';
          placeholder.textContent = 'Pilih layanan kurir...';
          sel.appendChild(placeholder);

          // set option value to index (reliable)
          courierData.forEach((c, i) => {
            const opt = document.createElement('option');
            opt.value = String(i);          // use index
            opt.textContent = c.name || ("Courier " + i);
            sel.appendChild(opt);
          });

          // attach/change listener (remove duplicates safely)
          sel.removeEventListener('change', _selChangeHandler);
          sel.addEventListener('change', _selChangeHandler);

          // choose a default: try to use order.courier (if exists) by matching name, fallback to first
          let chosenIndex = -1;
          if (data.order && data.order.courier) {
            chosenIndex = courierData.findIndex(c => (c.name || '').toLowerCase() === String(data.order.courier).toLowerCase());
          }
          if (chosenIndex === -1) chosenIndex = (courierData.length ? 0 : -1);

          if (chosenIndex >= 0) {
            sel.value = String(chosenIndex);
            // call updateCourier with index string
            updateCourier(sel.value);
          } else {
            // clear fields
            safeClearFields();
          }

          document.getElementById('ship-order-id').value = orderId;
          new bootstrap.Modal(document.getElementById('orderUpdateModal')).show();
        })
        .catch(err => {
          console.error(err);
          alert('Gagal mengambil data order: ' + (err.message || 'unknown'));
        });
    }

    function _selChangeHandler(e) {
      updateCourier(e.target.value);
    }

    function updateCourier(indexStr) {
      // defensive
      console.log('updateCourier called with', indexStr, 'courierData length', courierData.length);
      const idx = parseInt(indexStr, 10);
      if (Number.isNaN(idx) || idx < 0 || idx >= courierData.length) {
        console.warn('invalid courier index', indexStr);
        safeClearFields();
        return;
      }

      const c = courierData[idx];
      console.log('selected courier object', c);

      // compute cost
      const weight = Number(currentOrderWeight || 0);
      const cost = (Number(c.base_rate) || 0) + (Number(c.per_kg) || 0) * weight;
      setInputValue('ship-cost', Math.round(cost));

      // compute ETA
      const today = new Date();
      const min = Number(c.min_delivery_days || 0);
      const max = Number(c.max_delivery_days || min);
      const etaStart = new Date(today); etaStart.setDate(today.getDate() + min);
      const etaEnd = new Date(today); etaEnd.setDate(today.getDate() + max);
      const isoStart = etaStart.toISOString().slice(0,10);
      const isoEnd = etaEnd.toISOString().slice(0,10);
      const etaText = (min === max) ? `${min} hari (Tiba ${isoStart})` : `${min}-${max} hari (${isoStart} — ${isoEnd})`;
      setInputValue('ship-eta', etaText);

      // tracking preview: include user id safely
      const userId = @json(auth()->id());
      const prefix = (c.name || 'UNK').replace(/[^A-Za-z0-9]/g, '').substring(0,4).toUpperCase() || 'UNK';
      const tracking = `${prefix}-S${userId}-O${document.getElementById('ship-order-id').value}-${new Date().toISOString().replace(/[-T:.Z]/g,'')}`;
      setInputValue('ship-tracking', tracking);
    }

    function setInputValue(id, value) {
      const el = document.getElementById(id);
      if (!el) {
        console.warn('element not found:', id);
        return;
      }
      el.value = value;
      // if you need to trigger events for other libs, dispatch input
      el.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function safeClearFields() {
      setInputValue('ship-cost', '');
      setInputValue('ship-eta', '');
      setInputValue('ship-tracking', '');
    }

    document.addEventListener('DOMContentLoaded', function(){
      const sel = document.getElementById('ship-courier');
      if (sel) {
        sel.addEventListener('change', _selChangeHandler);
      }
      const btn = document.getElementById('ship-submit');
      if (btn) {
        
        btn.addEventListener('click', function(e){
          e.preventDefault();
          
          const id = document.getElementById('ship-order-id').value;
          const url = shipPostUrlTemplate.replace('__ID__', id);
          // extract eta_end date (the last ISO date)
          const etaText = document.getElementById('ship-eta').value || '';
          let eta_end = '';
          const m = etaText.match(/(\d{4}-\d{2}-\d{2})\s*—\s*(\d{4}-\d{2}-\d{2})/);
          if (m) eta_end = m[2];
          else {
            const m2 = etaText.match(/Tiba\s+(\d{4}-\d{2}-\d{2})/);
            if (m2) eta_end = m2[1];
          }

          const payload = {
            // kirim courier name supaya server punya label yang jelas
            courier: (document.getElementById('ship-courier').value !== '')
                      ? courierData[parseInt(document.getElementById('ship-courier').value,10)].name
                      : null,
            tracking_number: document.getElementById('ship-tracking').value,
            shipping_cost: Number(document.getElementById('ship-cost').value || 0),
            eta_end: eta_end || null
        };
        console.log('ship-submit clicked, sending POST to', url, 'payload', payload);
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type':'application/json',
              'X-CSRF-TOKEN': @json(csrf_token())
            },
            body: JSON.stringify(payload)
          })
          .then(res => {
            if (!res.ok) return res.text().then(t => { throw new Error(res.status + ' ' + t) });
            return res.json();
          })
          .then(json => {
            if (json.success) {
              bootstrap.Modal.getInstance(document.getElementById('orderUpdateModal'))?.hide();
              location.reload();
            } else {
              alert('Update gagal');
            }
          })
          .catch(err => {
            console.error('ship post error', err);
            alert('Gagal mengupdate order: ' + (err.message||''));
          });
        });
      }
    });
    </script>


  </body>
  </html>


