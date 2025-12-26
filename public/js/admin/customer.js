// =====================================================
// admin-customer-edit.js  (ganti file lama dengan ini)
// Pastikan file ini dimuat AFTER DOM (mis. sebelum </body>)
// =====================================================

/* Sidebar toggle (tetap) */
const btnToggle = document.getElementById('btnToggle');
const sidebar = document.getElementById('adminSidebar');
const overlay = document.getElementById('sidebarOverlay');

function toggleSidebar() {
  sidebar.classList.toggle('show');
  overlay.classList.toggle('show');
}
if (btnToggle) btnToggle.addEventListener('click', toggleSidebar);
if (overlay) overlay.addEventListener('click', toggleSidebar);


/* Helper: show/hide field error */
function showFieldError(fieldName, message) {
  const field = document.querySelector(`#editUserFormAjax [name="${fieldName}"], #editUserFormAjax #${fieldName}`);
  if (!field) {
    console.warn('Field not found for error', fieldName, message);
    return;
  }
  field.classList.add('is-invalid');

  let feedback = document.querySelector(`#err-${fieldName}`) || field.parentElement.querySelector('.invalid-feedback');
  if (!feedback) {
    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    field.parentElement.appendChild(feedback);
  }
  feedback.innerText = message;
  feedback.style.display = '';
}

function clearFormErrors() {
  const form = document.getElementById('editUserFormAjax');
  if (!form) return;
  form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  form.querySelectorAll('.invalid-feedback').forEach(el => {
    el.innerText = '';
    el.style.display = 'none';
  });
}


/* Single openCustomerEdit function (no duplicates) */
async function openCustomerEdit(btnOrId) {
  console.log('openCustomerEdit called with:', btnOrId);
  if (!btnOrId) return console.warn('openCustomerEdit: btnOrId missing');

  const el = (typeof btnOrId === 'string') ? document.getElementById(btnOrId) : btnOrId;
  if (!el || !el.dataset) {
    console.warn('openCustomerEdit: element tidak valid', el);
    return alert('Tombol edit tidak valid.');
  }

  const idFromDataset = el.dataset.id;
  const updateRoute = el.dataset.route; // ex: /admin/customers/2/edit  (sesuaikan route mu)
  const jsonUrl = el.dataset.json;

  const modalEl = document.getElementById('editModal');
  const form = modalEl ? modalEl.querySelector('form#editUserFormAjax') : document.getElementById('editUserFormAjax');
  if (!form) {
    console.warn('Edit form tidak ditemukan di DOM.');
    return alert('Form edit tidak tersedia. Coba refresh halaman.');
  }

  if (updateRoute) form.action = updateRoute; // set form action dari data-route

  // bersihkan error
  clearFormErrors();

  // reset visible inputs saja (jangan hapus hidden token / user_id)
  form.querySelectorAll('input, textarea').forEach(i => {
    if (i.type === 'hidden') return; // jangan kosongkan _token atau user_id hidden
    if (i.type === 'checkbox' || i.type === 'radio') {
      i.checked = false;
      return;
    }
    i.value = '';
  });

  // sementara set user_id dari dataset jika ada
  if (idFromDataset) {
    const hid = form.querySelector('#user_id');
    if (hid) hid.value = idFromDataset;
  }

  if (!jsonUrl) {
    new bootstrap.Modal(modalEl).show();
    return;
  }

  try {
    const resp = await fetch(jsonUrl, {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });

    if (!resp.ok) {
      console.error('Failed fetching user JSON:', resp.status, await resp.text());
      return alert('Gagal memuat data user.');
    }
    const data = await resp.json();

    // isi fields
    if (form.querySelector('#user_id')) form.querySelector('#user_id').value = data.id ?? idFromDataset ?? '';
    if (form.querySelector('#name')) form.querySelector('#name').value = data.name ?? '';
    if (form.querySelector('#email')) form.querySelector('#email').value = data.email ?? '';
    if (form.querySelector('#phone')) form.querySelector('#phone').value = data.phone ?? '';
    if (form.querySelector('#address')) form.querySelector('#address').value = data.address ?? '';

    new bootstrap.Modal(modalEl).show();
  } catch (err) {
    console.error('openCustomerEdit fetch error:', err);
    alert('Network error saat mengambil data user.');
  }
}


/* Attach single submit handler (clean, logged) */
(function attachSubmitHandler() {
  const form = document.getElementById('editUserFormAjax');
  if (!form) {
    console.warn('Tidak menemukan form #editUserFormAjax untuk attach submit handler.');
    return;
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    clearFormErrors();

    const id = form.querySelector('#user_id')?.value?.trim();
    if (!id) return alert('User ID missing');

    const name = form.querySelector('#name')?.value.trim() ?? '';
    const email = form.querySelector('#email')?.value.trim() ?? '';
    const phone = form.querySelector('#phone')?.value.trim() ?? '';
    const address = form.querySelector('#address')?.value.trim() ?? '';

    // client-side quick checks
    if (!name || name.length < 3) return showFieldError('name', 'Name atleast 3 characters.');
    if (!email) return showFieldError('email', 'Email is required.');

    const payload = { name, email, phone, address };

    // gunakan form.action (seharusnya di-set oleh openCustomerEdit dari data-route)
    const url = form.action && form.action !== '' ? form.action : `/admin/customers/${id}/edit`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                      (form.querySelector('input[name="_token"]') && form.querySelector('input[name="_token"]').value) || '';

    console.groupCollapsed('[AJAX] submit edit user');
    console.log('URL:', url);
    console.log('Method: PATCH');
    console.log('Payload:', payload);
    console.log('CSRF token present?', !!csrfToken);

    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    try {
      const res = await fetch(url, {
        method: 'PATCH', // gunakan PATCH nyata
        credentials: 'same-origin',
        headers: {
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      console.log('Response status:', res.status, res.statusText);
      const text = await res.text();
      let data = null;
      try { data = text ? JSON.parse(text) : null; } catch (err) { /* not json */ }

      if (res.status === 419) {
        console.error('CSRF/session expired (419):', text);
        alert('Session/CSRF expired. Refresh halaman dan coba lagi.');
        return;
      }

      if (res.status === 422) {
        console.warn('Validation failed (422):', data);
        const errors = data?.errors ?? {};
        Object.keys(errors).forEach(key => {
          const shortKey = key.split('.').pop();
          showFieldError(shortKey, errors[key][0]);
        });
        return;
      }

      if (!res.ok) {
        console.error('Server error:', res.status, text);
        alert('Terjadi kesalahan server. Lihat console untuk detail.');
        return;
      }

      console.log('Success response:', data ?? text);
      bootstrap.Modal.getInstance(document.getElementById('editModal'))?.hide();
      location.reload();
    } catch (err) {
      console.error('Network error saat submit:', err);
      alert('Network error saat mengirim data.');
    } finally {
      if (submitBtn) submitBtn.disabled = false;
      console.groupEnd();
    }
  });
})();
