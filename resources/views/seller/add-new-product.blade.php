<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Add New Product</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- App CSS -->
  <link rel="stylesheet" href="{{asset('css/styles.css')}}">
</head>
<body>
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1100;">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  
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
          <a class="nav-link active" href="{{ route('seller.products') }}"><i class="bi bi-box"></i>Products</a>
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
        <!-- App Bar -->
        <div class="appbar px-3 px-md-4 py-3 mb-4 d-flex align-items-center justify-content-between">
          <div>
            <div class="small opacity-75 mb-1">Products</div>
            <h1 class="wc-title mb-0">Add New Product</h1>
            <div class="small opacity-75">Fill in the details to list your product on GigaGears marketplace.</div>
          </div>
          <div class="d-flex gap-2">
            <span class="badge-chip d-inline-flex align-items-center gap-2">
               <img src="{{ Auth::check() ? Auth::user()->avatar_url : asset('images/default-avatar.png') }}" alt="Store Logo"  class="rounded-circle bg-white" style="width:28px;height:28px;object-fit:cover;">
              @auth {{Auth::user()->name}} @endauth
            </span>
          </div>
        </div>

        <!-- FORM WRAP -->
        <form id="productForm" method="post" action="{{ route('seller.add.product') }}" enctype="multipart/form-data" class="row g-3">
          @csrf
          <!-- A. Basic Information -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">A. Basic Information</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <input id="pName" class="form-control @error('product_name') is-invalid @enderror" type="text" name='product_name' placeholder="Product Name" required>
                @error('product_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">-- Select category --</option>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}"
                        {{ (int) old('category_id', optional($product ?? null)->category_id) === $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                      </option>
                    @endforeach
                </select>
                @error('category_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <input id="pBrand" name="brand" class="form-control @error('brand') is-invalid @enderror" type="text" placeholder="Brand" required>
                @error('brand')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <input id="pSku" class="form-control @error('PSKU') is-invalid @enderror" type="text" placeholder="SKU / Product Code (optional)">
                @error('PSKU')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-12">
                <textarea id="pDesc" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Product Description"></textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </section>

          <!-- B. Pricing & Stock -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">B. Pricing & Stock</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input id="pPrice" name="price" class="form-control @error('price') is-invalid @enderror" type="number" min="0" step="0.01" placeholder="Price" required>
                  @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <input id="pStock" name="qty" class="form-control @error('qty') is-invalid @enderror" type="number" min="0" step="1" placeholder="Stock Quantity" required>
                @error('qty')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </section>

          <!-- C. Media Upload -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">C. Media Upload</h6>
            <div class="row g-3">
              <div class="col-md-8">
                <input id="pImages" class="form-control" name="image" @error('image') is-invalid @enderror" type="file" accept="image/*" multiple>
                <div class="small text-muted mt-1">Upload Product Images (max 3 MB)</div>
                @error('image')
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
                 <!-- Modal preview -->
                <div id="imgPreview" name="image" class="d-flex gap-2 flex-wrap mt-2"></div>
                  <div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                      <div class="modal-content bg-transparent border-0">
                        <img id="modalImg" class="img-fluid rounded">
                      </div>
                    </div>
                  </div>
                </div>
              <div class="col-md-4">
                <input id="pVideo" name="video" class="form-control @error('image') is-invalid @enderror" type="file" accept="video/*" placeholder="Video URL (e.g. demo product)">
                <div class="small text-muted mt-1">Opsional: Upload video product.</div>
                @error('video')
                  <div class="invalid-feedback">{{ $message }}</div> 
                @enderror
              </div>
            </div>
          </section>

          <!-- D. Variants -->
          <section class="gg-card p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">D. Product Variants (Optional)</h6>
              <button type="button" id="btnAddVariant" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add Variant
              </button>
            </div>
            <div id="variantsWrap" class="vstack gap-2"></div>
            <div class="small text-muted mt-2">Contoh: Warna/Ukuran. Harga & stok bisa override.</div>
          </section>

          <!-- E. Shipping (Physical only) -->
          <section class="gg-card p-3 p-md-4">
            <h6 class="mb-3">E. Other Information (only for physical products)</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <input id="pWeight" name="weight" class="form-control @error('weight') is-invalid @enderror" type="number" min="0" step="1" placeholder="Weight (grams)">
                @error('weight')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-8">
                <input id="pDims" name="diameter" class="form-control @error('diameter') is-invalid @enderror" type="text" placeholder="Dimensions (cm) — e.g. 20 x 12 x 8">
                @error('diameter')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </section>

          <!-- Actions -->
          <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">
              Publish Product
            </button>
          </div>
        </form>

        <p class="text-center mt-4 foot small mb-0">© 2025 GigaGears. All rights reserved.</p>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // image preview
      const imagesInput = document.getElementById('pImages');
      const previewBox = document.getElementById('imgPreview');
      const modalImg = document.getElementById('modalImg');
      const bsModal = new bootstrap.Modal(document.getElementById('imgModal'));

      imagesInput?.addEventListener('change', (e) => {
        previewBox.innerHTML = '';

        const files = Array.from(e.target.files);

        files.forEach(file => {
          const reader = new FileReader();
          reader.onload = (ev) => {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.className = "rounded border";
            img.style = "width:120px;height:120px;object-fit:cover;cursor:pointer";

            // Klik untuk menampilkan modal
            img.addEventListener('click', () => {
              modalImg.src = ev.target.result;
              bsModal.show();
            });

            previewBox.appendChild(img);
          };
          reader.readAsDataURL(file);
        });
      });


      // dynamic variants
      const variantsWrapper = document.getElementById('variantsWrap');
      const addVariantBtn = document.getElementById('btnAddVariant');
      let variantIndex = variantsWrapper.querySelectorAll('.variant-row').length || 0;

      function makeVariantRow(i, data = {}) {
        const div = document.createElement('div');
        div.className = 'variant-row mb-2 p-2 border rounded';
        div.dataset.index = i;
        div.innerHTML = `
          <div class="d-flex gap-2 align-items-start">
            <input type="text" name="variants[${i}][name]" placeholder="Variant name (e.g. Color)"
                  class="form-control" value="${data.name ?? ''}" style="max-width:200px;">
            <input type="text" name="variants[${i}][value]" placeholder="Value (e.g. Red)"
                  class="form-control" value="${data.color ?? ''}">
            <input type="number" step="0.01" name="variants[${i}][price]" placeholder="Extra price"
                  class="form-control" value="${data.price ?? ''}" style="max-width:140px;">
            <input type="number" name="variants[${i}][stock]" placeholder="Stock"
                  class="form-control" value="${data.stock ?? ''}" style="max-width:120px;">
            <button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>
          </div>`;
        return div;
      }

      addVariantBtn?.addEventListener('click', () => {
        variantsWrapper.appendChild(makeVariantRow(variantIndex++));
      });

      // delegate remove
      variantsWrapper.addEventListener('click', (e) => {
        if (e.target.matches('.remove-variant')) {
          e.target.closest('.variant-row')?.remove();
        }
      });
    });
  </script>
</body>
</html>
