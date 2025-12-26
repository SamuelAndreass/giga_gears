<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GigaGears — Set Up Your Store</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/setup.css') }}">
</head>
<body>
<body class="bg-auth">

  <div class="sheet">
    <a href="#" class="brand-img" aria-label="GigaGears">
  <img
    src="{{asset ('images/logo GigaGears.png')}}"
    alt="GigaGears"
    class="brand-logo mb-2">
</a>

    <!-- Title -->
    <div class="mb-3">
      <h1 class="mb-1" style="font-weight:800; letter-spacing:.2px;">Set Up Your Store</h1>
      <p class="section-hint mb-0">Complete your store information to start selling on GigaGears.</p>
    </div>

    <!-- Form (memanjang; scroll body) -->
    <form id="storeSetupForm" method="post" action="{{ route('become.seller') }}" enctype="multipart/form-data" class="mt-2">
      @csrf
      <div class="row g-4">
        <!-- A. Store Details -->
        <div class="col-12 col-lg-6">
          <div class="legend">A. Store Details</div>
          <div class="mb-3">
            <label class="form-label">Storename</label>
            <input type="text" name="store_name" value="{{ old('store_name') }}" class="form-control @error('store_name') is-invalid @enderror" placeholder="e.g. TechnoWorld">
             @error('store_name') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Store Description</label>
            <input type="text" name="store_description" class="form-control @error('store_description') is-invalid @enderror" placeholder="Short description about your store">
          </div>

          <div class="row g-2 align-items-center upload-row mb-2">
            <div class="col">
              <label class="form-label mb-1">Store Logo</label>
              <input id="logoInput" name='logo' type="file" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
              @error('logo') <div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">PNG/JPG up to 1MB. Square ratio recommended.</div>
            </div>
            <div class="col-auto">
              <label for="logoInput" class="btn btn-outline-primary">Upload</label>
            </div>
          </div>

          <div class="row g-2 align-items-center upload-row">
            <div class="col">
              <label class="form-label mb-1">Store Banner</label>
              <input id="bannerInput" name='banner' type="file" accept="image/*" class="form-control @error('banner') is-invalid @enderror">
              @error('banner') <div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              <div class="form-text">PNG/JPG up to 2MB. 16:9 ratio recommended.</div>
            </div>
            <div class="col-auto">
              <label for="bannerInput" class="btn btn-outline-primary">Upload</label>
            </div>
          </div>
        </div>

        <!-- B. Store Location -->
        <div class="col-12 col-lg-6">
          <div class="legend">B. Store Location <small>(If seller also has offline store)</small></div>
          <div class="mb-3">
            <label class="form-label">Province</label>
            <select name="province" class="form-select @error('province') is-invalid @enderror">
              <option selected disabled value="" {{ old ('province') ? '': 'selected'}}>Choose province</option>
              <option value="DKI Jakarta" {{ old('province') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
              <option value="Jawa Barat" {{ old('province') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
              <option value="Jawa Tengah" {{ old('province') == 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
              <option value="Jawa Timur" {{ old('province') == 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
              <option value="Banten" {{ old('province') == 'Banten' ? 'selected' : '' }}>Banten</option>
            </select>
            @error('province') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">City/District</label>
            <select name="city" class="form-select @error('city') is-invalid @enderror">
              <option disabled value="">Choose city/district</option>
              <option value="Jakarta" {{ old('city') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
              <option value="Bogor" {{ old('city') == 'Bogor' ? 'selected' : '' }}>Bogor</option>
              <option value="Depok" {{ old('city') == 'Depok' ? 'selected' : '' }}>Depok</option>
              <option value="Bekasi" {{ old('city') == 'Bekasi' ? 'selected' : '' }}>Bekasi</option>
              <option value="Tangerang" {{ old('city') == 'Tangerang' ? 'selected' : '' }}>Tangerang</option>
            </select>
            @error('city') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Full Address</label>
            <input type="text" name="store_address" value="{{ old('store_address') }}" class="form-control @error('store_address') is-invalid @enderror" placeholder="Street, number, area, ZIP">
            @error('store_address') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="tel" name="store_phone" value="{{ old('store_phone') }}" class="form-control @error('store_phone') is-invalid @enderror" placeholder="08xxxxxxxxxx">
            @error('store_phone') <div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <!-- C. Payment Info -->

        <!-- D. Additional Info -->
        <div class="col-12 col-lg-6">
          <div class="legend">D. Additional Info <small>(Optional)</small></div>
          <div class="mb-4">
            <label class="form-label">Operating Hours</label>
              <div class="d-flex gap-2">
                <div>
                    <label class="form-label small">Open</label>
                    <input type="time" name="open_time"
                        value="{{ old('open_time') }}"
                        class="form-control">
                </div>

                <div>
                    <label class="form-label small">Close</label>
                    <input type="time" name="close_time"
                        value="{{ old('close_time') }}"
                        class="form-control">
                </div>
              </div>
        
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-dark-gg btn-lg">Save & Continue</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
