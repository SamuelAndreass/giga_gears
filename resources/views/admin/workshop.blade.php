<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>GigaGears Admin — Workshop Management</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

  <style>
    .admin-side {
        width: 280px; 
        height: 100vh; 
        background: #fff;
        border-right: 1px solid #E6ECFB;
        flex-shrink: 0;
        transition: transform 0.3s ease-in-out;
        display: flex;           
        flex-direction: column;  
        position: sticky; top: 0;
    }

    .nav-scroll-wrap {
        flex: 1;
        overflow-y: auto;
    }

    @media (max-width: 991.98px) {
        .admin-side {
            position: fixed; 
            top: 0; left: 0; bottom: 0;
            z-index: 1050; 
            transform: translateX(-100%);
            box-shadow: none;
        }
        .admin-side.show {
            transform: translateX(0);
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }
    }

    .sidebar-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1040;
        display: none; opacity: 0; transition: opacity 0.3s;
    }
    .sidebar-overlay.show { display: block; opacity: 1; }

    .status-badge-upcoming { background-color: #DEF7EC; color: #03543F; padding: 5px 12px; border-radius: 50px; font-size: 0.85rem; }
    .status-badge-finished { background-color: #F3F4F6; color: #374151; padding: 5px 12px; border-radius: 50px; font-size: 0.85rem; }
    .status-badge-cancelled { background-color: #FEE2E2; color: #7F1D1D; padding: 5px 12px; border-radius: 50px; font-size: 0.85rem; }
  </style>
</head>
<body>
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1100;">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1100;">
      {{ session('error') }}
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
              <a class="nav-link" href="{{ route('admin.customers.index') }}"><i class="bi bi-people"></i>Data Customer</a>
              <a class="nav-link" href="{{ route('admin.sellers.index') }}"><i class="bi bi-person-badge"></i>Data Seller</a>
              <a class="nav-link" href="{{ route('admin.transactions.index') }}"><i class="bi bi-receipt"></i>Data Transaction</a>
              <a class="nav-link" href="{{ route('admin.products.index') }}"><i class="bi bi-box"></i>Products</a>
              <a class="nav-link" href="{{ route('admin.shipping.index') }}"><i class="bi bi-truck"></i>Shipping Settings</a>
              <a class="nav-link active" href="{{ route('admin.workshops.index') }}"><i class="bi bi-calendar-event"></i>Workshops & Seminars</a>
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
                <div class="small opacity-75 mb-1">Manage Workshops & Seminars</div>
                <h1 class="h3 mb-0">Workshops & Seminars</h1>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
              <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle" width="24" height="24" alt="A">
              Admin
            </span>
          </div>
        </div>

        <div class="gg-card p-3 p-md-4 mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="input-group" style="max-width: 350px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 bg-light" id="searchInput" placeholder="Search workshop name...">
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-plus-lg me-1"></i> Add New Workshop
                </button>
            </div>
        </div>

        <div class="gg-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Workshop Name</th>
                            <th>Date & Time</th>
                            <th>Instructor</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="workshopTable">
                        @forelse($workshops as $workshop)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-book-half text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $workshop->title }}</h6>
                                            <small class="text-muted">{{ $workshop->location ?? 'Online' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $workshop->start_date->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ $workshop->start_date->format('H:i') }} - {{ $workshop->end_date->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($workshop->instructor) }}&background=random" class="rounded-circle" width="24" alt="">
                                        <span class="small">{{ $workshop->instructor }}</span>
                                    </div>
                                </td>
                                <td>{{ $workshop->getRegisteredCountAttribute() }}/{{ $workshop->capacity }}</td>
                                <td>
                                    @if($workshop->status === 'upcoming')
                                        <span class="status-badge-upcoming">Upcoming</span>
                                    @elseif($workshop->status === 'ongoing')
                                        <span class="badge bg-info">Ongoing</span>
                                    @elseif($workshop->status === 'finished')
                                        <span class="status-badge-finished">Finished</span>
                                    @else
                                        <span class="status-badge-cancelled">Cancelled</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border" onclick="editWorkshop({{ $workshop->id }})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.workshops.destroy', $workshop->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada workshop. <a href="#" data-bs-toggle="modal" data-bs-target="#addEventModal">Tambah sekarang</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top d-flex justify-content-between align-items-center small text-muted">
                <span>Total: {{ count($workshops) }} workshops</span>
            </div>
        </div>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. Admin Panel.</p>
    </main>
  </div>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Add/Edit Modal -->
  <div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="modalTitle">Add New Workshop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <form id="workshopForm" method="POST" action="{{ route('admin.workshops.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="methodOverride" name="_method" value="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Workshop Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Advanced Python Programming" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Workshop description..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Time</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Instructor Name</label>
                            <input type="text" class="form-control" id="instructor" name="instructor" placeholder="e.g. John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" placeholder="Maximum participants" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="e.g. Jakarta, Online, etc." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="finished">Finished</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Requirements</label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="2" placeholder="Prerequisites or requirements..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Image URL</label>
                        <input type="url" class="form-control" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-semibold" id="submitBtn">Save Workshop</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/admin/workshop.js') }}"></script>
</body>
</html>
