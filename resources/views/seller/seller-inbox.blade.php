<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Inbox</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- App CSS -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
<div class="container-fluid">
  <div class="row g-0">
    <!-- ===== SIDEBAR ===== -->
    <aside class="col-12 col-lg-2 side d-none d-lg-block">
      <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
        <img src="{{ asset('images/logo GigaGears.png') }}" alt="GigaGears" class="brand-logo">
      </a>

      <nav class="nav flex-column nav-gg">
        <a class="nav-link" href="{{ route('seller.index') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
        <a class="nav-link" href="{{ route('seller.orders') }}"><i class="bi bi-bag"></i>Order</a>
        <a class="nav-link" href="{{ route('seller.products') }}"><i class="bi bi-box"></i>Products</a>
        <a class="nav-link" href="{{ route('seller.analytics') }}"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
        <a class="nav-link active" href="{{ route('seller.inbox') }}"><i class="bi bi-inbox"></i>Inbox</a>
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
      <!-- App Bar -->
      <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
        <div>
          <div class="small opacity-75 mb-1">See feedback and messages</div>
          <h1 class="wc-title mb-0">Inbox</h1>
        </div>
        <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
               <img src="{{ Auth::check() ? Auth::user()->avatar_url : asset('images/default-avatar.png') }}" alt="Store Logo"  class="rounded-circle bg-white" style="width:28px;height:28px;object-fit:cover;">
              @auth {{Auth::user()->name}} @endauth
            </span>
        </div>
      </div>

      <!-- Search + Filter -->
      <section class="gg-card p-3 p-md-4 mb-3">
        <div class="row g-2 align-items-center">
          <div class="col-12 col-md">
            <div class="input-group">
              <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
              <input id="inboxSearch" name="q" type="text" class="form-control" placeholder="Search by customer, product, message…" value="{{ request('q') }}">
            </div>
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <select id="inboxFilter" name="filter" class="form-select">
              <option value="all" {{ request('filter')=='all' ? 'selected' : '' }}>All Review & Message</option>
              <option value="review" {{ request('filter')=='review' ? 'selected' : '' }}>Only Review</option>
              <option value="message" {{ request('filter')=='message' ? 'selected' : '' }}>Only Message</option>
            </select>
          </div>
        </div>
      </section>

      <!-- INBOX RESULTS (table + messages) - will be replaced by AJAX partial -->
      <div id="inboxResults" class="mb-3">
        {{-- initial server-side render using controller data --}}
        @includeIf('seller.partials.inbox-list', ['items' => $items ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10), 'total' => $total ?? 0])
      </div>

      <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
    </main>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
  // route untuk controller feedback (harus ada di routes/web.php)
  const route = "{{ route('seller.inbox') }}";
  const searchEl = document.getElementById('inboxSearch');
  const filterEl = document.getElementById('inboxFilter');
  const resultsEl = document.getElementById('inboxResults');

  function debounce(fn, delay = 350) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  function buildUrl(page = 1) {
    const params = new URLSearchParams();
    const q = searchEl ? searchEl.value.trim() : '';
    const filter = filterEl ? filterEl.value : 'all';
    if (q) params.set('q', q);
    if (filter) params.set('filter', filter);
    if (page && page > 1) params.set('page', page);
    return route + (params.toString() ? '?' + params.toString() : '');
  }

  async function fetchResults(url) {
    try {
      const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });
      if (!res.ok) throw new Error('Network error: ' + res.status);
      const html = await res.text();
      // replace results
      resultsEl.innerHTML = html;
      // update URL (so copy/paste preserves search state)
      if (history.replaceState) history.replaceState({}, '', url);
      // scroll to top of results (nice UX)
      resultsEl.scrollIntoView({ behavior: 'smooth' });
    } catch (err) {
      console.error('Inbox fetch error:', err);
    }
  }

  const doSearch = debounce(() => fetchResults(buildUrl(1)), 300);

  if (searchEl) searchEl.addEventListener('input', doSearch);
  if (filterEl) filterEl.addEventListener('change', doSearch);

  // intercept pagination links inside results
  resultsEl.addEventListener('click', function (e) {
    const a = e.target.closest('a');
    if (!a) return;
    const href = a.getAttribute('href');
    if (!href) return;
    // only intercept internal links
    const url = new URL(href, window.location.origin);
    if (url.origin !== window.location.origin) return;
    e.preventDefault();
    const page = url.searchParams.get('page') || 1;
    fetchResults(buildUrl(page));
  });

  // support back/forward
  window.addEventListener('popstate', function () {
    // load current URL if it's our route
    if (window.location.pathname === new URL(route).pathname) {
      fetchResults(window.location.href);
    }
  });

})();
</script>

</body>
</html>
