<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Settings</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- App CSS (punyamu) -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
  @if ('session' )
  
  @endif
  <div class="container-fluid">
    <div class="row g-0">

      <!-- ===== SIDEBAR (konsisten) ===== -->
      <aside class="col-12 col-lg-2 side d-none d-lg-block">
        <a href="dashboard.html" class="brand-link" aria-label="GigaGears">
          <img src="{{ asset('images/logo GigaGears.png') }}" alt="GigaGears" class="brand-logo">
        </a>

        <nav class="nav flex-column nav-gg">
          <a class="nav-link" href="{{ route('seller.index') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>
          <a class="nav-link" href="{{ route('seller.orders') }}"><i class="bi bi-bag"></i>Order</a>
          <a class="nav-link" href="{{ route('seller.products') }}"><i class="bi bi-box"></i>Products</a>
          <a class="nav-link" href="{{ route('seller.analytics') }}"><i class="bi bi-bar-chart"></i>Analytics & Report</a>
          <a class="nav-link" href="{{ route('seller.inbox') }}"><i class="bi bi-inbox"></i>Inbox</a>
          <hr>
          <a class="nav-link active" href="{{ route('settings.index')}}"><i class="bi bi-gear"></i>Settings</a>
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
            <div class="small opacity-75 mb-1">Account & Store</div>
            <h1 class="wc-title mb-0">Settings</h1>
          </div>
        </div>

        <!-- Content -->
        <div class="row g-3">
        <form method="POST" action={{ route('settings.owner.update') }} enctype="multipart/form-data">
          <!-- ========= KIRI ========= -->
          <div class="col-12 col-xl-12">
            <!-- Profil owner -->
          
            @csrf
            @method('put')
            <section class="gg-card p-3 p-md-4 mb-3">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Seller Settings</h5>
                <button class="btn btn-sm btn-primary" id="btnSaveProfile" type="submit">
                  <i class="bi bi-save me-1"></i>Save
                </button>
              </div>
              <div class="row g-3 align-items-center">
                <div class="col-auto">
                  <div class="position-relative">
                    <img id="avatarPreviewOwner" src="{{ $cusT->avatar_path ? asset('storage/' . $cusT->avatar_path) : asset ('images/pp.webp') }}" class="rounded-circle border" alt="avatar" style="width:80px;height:80px;object-fit:cover">
                    <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle border" id="btnAvatarPickOwner" title="Change avatar" type="button" aria-label="Change avatar">
                      <i class="bi bi-camera-fill"></i>
                    </button>
                    <input id="avatarInputOwner" name="owner_photo" type="file" accept="image/*" class="d-none">
                    @error('owner_photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                  </div>
                </div>
                <div class="col">
                  <div class="row g-2">
                    <div class="col-12 col-md-6">
                      <label class="form-label mb-1">Owner Name</label>
                      <input id="owner_name" name="owner_name" type="text" class="form-control" value="{{ old('owner_name', $user->name) }}">
                      @error('owner_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label mb-1">Email</label>
                      <input id="owner_email" name="owner_email" type="text" class="form-control" value="{{ old('owner_email', $user->email) }}">
                      @error('owner_email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </form>

          <!-- Preferensi Toko -->
            <form action={{ route ('settings.store.update') }} method="POST" enctype="multipart/form-data">
              @csrf
              @method('put')
              <section class="gg-card p-3 p-md-4 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="mb-0">Store Settings</h5>
                  <button class="btn btn-sm btn-primary" id="btnSavePref" type="submit"><i class="bi bi-save me-1"></i>Save</button>
                </div>
                <div class="row g-3">
                  <div class="col-auto">
                    <div class="position-relative">
                      <!-- Store avatar -->
                        <img id="avatarPreviewStore" src="{{ $store->store_logo ? asset('storage/' . $store->store_logo) : asset ('images/logo foto profile.png') }}" class="rounded-circle border" alt="avatar" style="width:80px;height:80px;object-fit:cover">
                        <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle border" id="btnAvatarPickStore" title="Change avatar" type="button">
                          <i class="bi bi-camera-fill"></i>
                        </button>
                        <input id="avatarInputStore" name="store_logo" type="file" accept="image/*" class="d-none">
                      @error('store_logo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                  </div>
                  
                  <div class="col">
                    <div class="row g-2">
                      <div class="col-12 col-md-6">
                        <label class="form-label mb-1">Store name</label>
                        <input id="storeName" name="store_name" type="text" class="form-control" value={{ old('store_name', $store->store_name) }}>
                        @error('store_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror   
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label mb-1">Contact</label>
                        <input id="phone" type="text" name="store_phone" class="form-control" value="{{ old('store_phone', $store->store_phone) }}">
                        @error('store_phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                      </div>
                        <div class="col-12">
                        <label class="form-label mb-1">Store address</label>
                        <textarea id="address" rows="2" class="form-control" name="store_address">{{ old('store_address', $store->store_address) }}</textarea>
                        </div>
                      </div>
                    </div>
                  </div>                
                  </div>
                </div>
              </section>
            </form>
           
        <div class="row g-3 align-items-start">

          <div class="col-lg-8 col-md-7 col-12">
            <!-- Keamanan (tanpa 2FA) -->
          <form method="POST" action="{{ route('settings.password.update') }}">
            @csrf
            @method('PUT')
            <section class="gg-card p-3 p-md-4 mb-3">
              <h5 class="mb-3">Security</h5>
              <div class="mb-2">
                <label class="form-label mb-1">Change Password</label>
                <div class="row g-2">
                  <div class="col-12">
                    <input id="pwCurrent" type="password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Current password" name="current_password">
                    @error('current_password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-12 col-md-6">
                    <input id="pwNew" type="password" class="form-control  @error('password') is-invalid @enderror" placeholder="New password" name="password">
                  </div>
                  <div class="col-12 col-md-6">
                    <input id="pwConfirm" type="password" class="form-control" placeholder="Confirm new password"  name="password_confirmation">
                  </div>
                  @error('password')
                        <div class="col-12">
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        </div>
                      @enderror
                </div>
                <div class="form-text" id="pwHint">Min. 8 karakter, kombinasi huruf & angka.</div>
                <div class="mt-2">
                  <button class="btn btn-sm btn-primary" id="btnChangePw" type="submit"><i class="bi bi-shield-lock me-1"></i>Update Password</button>
                </div>
              </div>
            </section>
            </div>
          <div class="col-lg-4 col-md-5 col-12">
            <!-- Danger Zone -->
              <section class="gg-card p-3 p-md-4">
                <h5 class="mb-3 text-danger">Danger Zone</h5>
                <div class="d-flex flex-column gap-2">
                  
                  <form action="">

                  </form>
                  <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteStore">
                    <i class="bi bi-exclamation-octagon me-1"></i> Delete store
                  </button>
                </div>
              </section>
          </div>
          </div>
        </div>
        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <!-- Modals Danger -->
  <div class="modal fade" id="modalDeleteStore" tabindex="-1" aria-labelledby="modalDeleteStoreLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('settings.store.destroy') }}">
        @csrf
        @method('DELETE')

        <div class="modal-header">
          <h5 class="modal-title" id="modalDeleteStoreLabel">Delete Store</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <p class="mb-2">
            Tindakan ini akan menghapus store beserta data terkait (produk, dsb). Ini tidak bisa dibatalkan.
          </p>
          <p class="mb-1">
            Ketik <strong>DELETE</strong> untuk konfirmasi.
          </p>
          <input
            type="text"
            name="confirm_delete"
            id="confirmDelete"
            class="form-control @error('confirm_delete') is-invalid @enderror"
          >
          @error('confirm_delete')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="btnDeleteStore" disabled>
            <i class="bi bi-exclamation-octagon me-1"></i> Delete store
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


  @if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
      <div id="saveToast" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
          <div class="toast-body"><i class="bi bi-check2-circle me-2"></i>Tersimpan!</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>
  @endif

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/settings.js')}}"></script>

</body>
</html>
