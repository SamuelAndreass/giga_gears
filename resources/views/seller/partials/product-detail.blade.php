<div class="container-fluid px-3 py-2">
    <div class="row g-4">
        {{-- IMAGE SECTION --}}
        <div class="col-md-5 text-center">
            <div class="border rounded p-3 bg-light">
                <img src="{{ asset('storage/' . ($product->images[0] ?? 'no-image.png')) }}"
                     alt="{{ $product->name }}"
                     class="img-fluid rounded mb-2"
                     style="max-height: 240px; object-fit: contain;">
            </div>
        </div>

        {{-- INFO SECTION --}}
        <div class="col-md-7">
            <h4 class="fw-bold" style="font-family:'Chakra Petch',sans-serif;">{{ $product->name }}</h4>

            @if($product->type === 'bundle')
                <span class="badge bg-warning text-dark mb-2">Bundle</span>
            @endif

            <p class="text-muted mb-2">
                <i class="bi bi-tag"></i> Category:
                <span class="fw-semibold">{{ $product->category->name ?? '—' }}</span>
            </p>

            <p class="text-primary fw-semibold mb-2" style="font-size:1.1rem;">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </p>

            <p class="mb-2 text-muted">
                <i class="bi bi-box"></i> Stock:
                <span class="fw-semibold">{{ $product->stock }}</span>
            </p>

            <p class="mb-2">
                <i class="bi bi-circle-fill me-1 {{ strtolower($product->status) === 'active' ? 'text-success' : 'text-secondary' }}"></i>
                Status: <span class="fw-semibold text-capitalize">{{ $product->status }}</span>
            </p>

            <hr class="my-3">

            {{-- DESCRIPTION --}}
            <div>
                <p class="fw-semibold mb-1">Description:</p>
                <p class="text-muted" style="white-space: pre-line;">
                    {{ $product->description ?? 'No description provided.' }}
                </p>
            </div>

            {{-- BUNDLE CONTENTS --}}
            @if($product->type === 'bundle' && $product->bundleItems->count())
                <hr>
                <p class="fw-semibold mb-2">Included Items:</p>
                <ul class="list-group small">
                    @foreach($product->bundleItems as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $item->product->name ?? '-' }}</span>
                            <span class="badge bg-light text-dark border">
                                × {{ $item->quantity }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- SPECS --}}
            @if(!empty($product->specs))
                <hr>
                <p class="fw-semibold mb-2">Specifications:</p>
                <ul class="list-group small">
                    @foreach($product->specs as $key => $value)
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">{{ $key }}</span>
                            <span class="fw-semibold">{{ $value }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif            
        </div>
    </div>
</div>
