// seller.js
(function(){
  'use strict';

  // ID yang dipakai pada modal (harus cocok dengan HTML)
  const IDS = {
    modal: 'viewModal',
    storeTitle: 'vm-store-title',
    name: 'vm-store-name',
    avatar: 'vm-avatar',
    owner: 'vm-owner',
    email: 'vm-email',
    phone: 'vm-phone',
    status: 'vm-status-badge',
    address: 'vm-location',
    createdAt: 'vm-created-at',  
    statProd: 'vm-stat-prod',        
    revenue: 'vm-revenue',
    orders: 'vm-orders',
    pending: 'vm-pending',           
    productsTable: 'vm-products-table',
    headerActions: 'vm-header-actions'
  };

  // safe-get
  function $id(id){ return document.getElementById(id); }

  // escape
  function escapeHtml(str){ return String(str || '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }

  // wait DOM
  function onReady(fn){
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
    else fn();
  }

  onReady(function(){
    const modalEl = $id(IDS.modal);
    const bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;

    function setText(id, value){
      const el = $id(id);
      if (!el) return;
      el.textContent = value;
    }
    function setHTML(id, value){
      const el = $id(id);
      if (!el) return;
      el.innerHTML = value;
    }
    function setAvatar(src){
      const el = $id(IDS.avatar);
      if (!el) return;
      el.src = src;
    }

    // fetch dari route yang kita definisikan di web.php
    function fetchSellerJson(id){
      // ganti path bila route-mu berbeda
      const url = `/admin/sellers/${id}/json`;
      return fetch(url, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
      }).then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      });
    }

    function clearProductsTable(){
      const table = $id(IDS.productsTable);
      if (!table) return null;
      const tbody = table.querySelector('tbody');
      if (!tbody) {
        // jika tidak ada tbody, ciptakan satu
        const newTbody = document.createElement('tbody');
        table.appendChild(newTbody);
        return newTbody;
      }
      tbody.innerHTML = '';
      return tbody;
    }

    function fillProductsTable(products) {
        const table = document.getElementById('vm-products-table');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!products || products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">No products found</td>
                </tr>
            `;
            return;
        }

        products.forEach(p => {
            console.log(p.rating);
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-muted fw-bold">${escapeHtml(p.sku ?? ('PRD-' + p.id))}</td>
                <td class="fw-bold">${escapeHtml(p.name ?? '-')}</td>
                <td class="text-warning fw-bold">Rp.${escapeHtml(String(p.price ?? 0))}</td>
                <td>${escapeHtml(String(p.stock ?? 0))}</td>
                <td>${escapeHtml(String(p.sold ?? 0))}</td>
                <td>
                    <span class="fw-bold small ${p.status === 'Active' ? 'text-success' : 'text-danger'}">
                        ${escapeHtml(p.status ?? '-')}
                    </span>
                </td>
                <td class="text-end fw-bold">
                    <i class="bi bi-star-fill text-warning me-1"></i>${escapeHtml(String(p.rating ?? 0))}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }


    // fungsi utama: membuka modal & isi data
    function openSellerView(id){
      if (!id) return console.warn('openSellerView called without id');

      if (!modalEl) {
        console.error('Modal element not found (id="' + IDS.modal + '")');
        return;
      }

      // placeholders
      setText(IDS.storeTitle, 'Loading...');
      setText(IDS.name, 'Loading...');
      setAvatar('https://picsum.photos/seed/loading/100/100');
      setText(IDS.owner, '-');
      setText(IDS.email, '-');
      setText(IDS.phone, '-');
      setText(IDS.status, '-');
      setText(IDS.statProd, '0');
      setText(IDS.revenue, '$0');
      setText(IDS.orders, '0');
      setText(IDS.address, '-');
      setText(IDS.createdAt, '-');
      // setText(IDS.pending, '$0'); // skip jika tidak dipakai

      const tbody = clearProductsTable();

      fetchSellerJson(id)
        .then(data => {
          // mapping field sesuai response sellerJson()
          setText(IDS.storeTitle, data.store_name ?? ('Store #' + id));
          setText(IDS.name, data.store_name ?? ('Store #' + id));
          if (data.avatar) setAvatar(data.avatar);
          setText(IDS.owner, data.owner_name ?? (data.owner ?? '-') );
          setText(IDS.email, data.email ?? '-');
          setText(IDS.phone, data.phone ?? '-');
          setText(IDS.status, data.status ?? '-');
          setText(IDS.statProd, String(data.product_count ?? 0));
          setText(IDS.revenue, 'Rp.' + (Number(data.total_revenue ?? 0)).toLocaleString());
          setText(IDS.orders, String(data.total_orders ?? 0));
          
        // LOCATION + SINCE YEAR (format UI)
        if (data.location && data.since_year) {
            const locationText = `${data.location} • Since ${data.since_year}`;

        document.getElementById("vm-location").innerHTML =
            `<i class="bi bi-geo-alt-fill me-1"></i> ${locationText}`;
        } else {
        document.getElementById("vm-location").innerHTML =
            `<i class="bi bi-geo-alt-fill me-1"></i> Unknown • Since -`;
        }


        productPageState.items = data.products || [];
        productPageState.perPage = 5; // ubah sesuai kebutuhan
        productPageState.page = 1;
        renderProductPage(1);
        if (bsModal) bsModal.show();
        })
        .catch(err => {
          console.error('Failed to load seller JSON:', err);
          setText(IDS.storeTitle, 'Error loading data');
          // optional: show modal anyway to display error
          // if (bsModal) bsModal.show();
        });
    }
    
    let productPageState = {
    items: [],
    perPage: 5,
    page: 1
    };

    function renderProductPage(page = 1) {
    const items = productPageState.items;
    const perPage = productPageState.perPage;

    if (!items || items.length === 0) {
        fillProductsTable([]);
        document.getElementById('vm-products-pager-container').innerHTML = '';
        return;
    }

    const lastPage = Math.ceil(items.length / perPage);
    page = Math.max(1, Math.min(page, lastPage));  
    productPageState.page = page;

    const start = (page - 1) * perPage;
    const slice = items.slice(start, start + perPage);

    // render table
    fillProductsTable(slice);

    // render pager
    document.getElementById('vm-products-pager-container').innerHTML = `
        <div class="small text-muted">
        Showing ${start + 1}–${start + slice.length} of ${items.length}
        </div>
        <div>
        <button class="btn btn-sm btn-outline-secondary me-1" id="pg-prev" ${page === 1 ? 'disabled' : ''}>Prev</button>
        <span class="mx-1 small">Page ${page} / ${lastPage}</span>
        <button class="btn btn-sm btn-outline-secondary ms-1" id="pg-next" ${page === lastPage ? 'disabled' : ''}>Next</button>
        </div>
    `;

    // attach controls
    document.getElementById('pg-prev')?.addEventListener('click', () => renderProductPage(page - 1));
    document.getElementById('pg-next')?.addEventListener('click', () => renderProductPage(page + 1));
    }

    // expose global untuk mendukung inline onclick
    window.openSellerView = openSellerView;

    // optional: delegation tombol .open-seller[data-id]
    document.addEventListener('click', function(e){
      const btn = e.target.closest('.open-seller');
      if (!btn) return;
      const id = btn.dataset.id || btn.getAttribute('data-id');
      if (id) openSellerView(id);
    });

  }); // end onReady

})();


    function currencyIdr(n){
      return 'Rp' + Number(n || 0).toLocaleString('id-ID', {maximumFractionDigits:0});
    }


