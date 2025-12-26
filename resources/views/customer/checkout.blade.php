
@extends('layouts.main')
@section('title', 'Checkout & Payment')

@section('content')
<div class="container my-5">
    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf
        {{-- TITLE --}}
        <div class="text-center mb-5">
            <h1 class="fw-bold" style="font-family:'Chakra Petch',sans-serif;">Checkout & Payment</h1>
            <p class="text-muted">Secure Your Gears</p>
        </div>

        <div class="row g-5">
            {{-- LEFT COLUMN - SHIPPING & PAYMENT --}}
            <div class="col-md-7">
                {{-- PRODUCT SUMMARY --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">Your Items</h4>

                        @foreach ($cart->items as $item)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . ($item->product->images ?? 'no-image.png')) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="rounded border me-3" style="width: 70px; height: 70px; object-fit: contain;">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>
                                        <small class="text-muted">Qty: {{ $item->qty }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="fw-bold mb-0 text-primary">
                                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </h6>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- SHIPPING ADDRESS --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">Shipping Address</h4>
                        <textarea name="shipping_address" class="form-control" rows="3" placeholder="Enter full shipping address..." required></textarea>
                    </div>
                </div>

                {{-- DELIVERY METHOD --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">Delivery Method</h4>

                        <div class="list-group mb-4">
                            @foreach ($shippings as $shipping)
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <input class="form-check-input me-2 shipping-radio" type="radio" name="shipping_id" value="{{ $shipping->id }}" 
                                            {{ $loop->first ? 'checked' : '' }}>
                                        <strong>{{ $shipping->name }}</strong> ({{ $shipping->service_type }})
                                        <div class="text-muted small">
                                            Est. {{ $shipping->min_delivery_days }}–{{ $shipping->max_delivery_days }} days,
                                            {{ $shipping->coverage }}
                                        </div>
                                    </div>
                                    <span class="fw-bold text-primary shipping-label" data-rate="{{ $shipping->base_rate }}">
                                        Rp{{ number_format($shipping->base_rate, 0, ',', '.') }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- PAYMENT METHOD --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">Payment Method</h4>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="card" value="credit_card" checked>
                            <label class="form-check-label" for="card">Credit / Debit Card</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank_transfer">
                            <label class="form-check-label" for="bank">Bank Transfer</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="wallet" value="e_wallet">
                            <label class="form-check-label" for="wallet">E-Wallet (OVO, Gopay, Dana)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="crypto" value="crypto">
                            <label class="form-check-label" for="crypto">Crypto (BTC, ETH, USDT)</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN - SUMMARY --}}
            <div class="col-md-5">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4">Order Summary</h4>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="summary-subtotal" class="fw-semibold" data-subtotal="{{ $sub_total }}">
                                Rp{{ number_format($sub_total, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping Fee</span>
                            <span id="summary-shipping" class="fw-semibold" data-shipping="{{ $shipping_fee }}">
                                Rp{{ number_format($shipping_fee, 0, ',', '.') }}
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Total</h5>
                            <h4 id="summary-total" class="fw-bold text-primary mb-0" data-total="{{ $total_payment }}">
                                Rp{{ number_format($total_payment, 0, ',', '.') }}
                            </h4>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            Pay Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('footer-script')
<script>
(function () {

    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = meta?.getAttribute('content') ?? null;

    function formatRupiah(n) {
        return 'Rp' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function safeParseFloat(v) { 
        return parseFloat(v ?? 0) || 0; 
    }

    const elSubtotal = document.getElementById('summary-subtotal');
    const elShipping = document.getElementById('summary-shipping');
    const elTotal = document.getElementById('summary-total');

    const subtotal = safeParseFloat(elSubtotal?.dataset?.subtotal);
    const initialShipping = safeParseFloat(elShipping?.dataset?.shipping);

    function render(shippingFee) {
        const fee = safeParseFloat(shippingFee);
        const total = Math.round(subtotal + fee);

        if (elSubtotal) elSubtotal.textContent = formatRupiah(subtotal);
        if (elShipping) elShipping.textContent = formatRupiah(fee);
        if (elTotal) {
            elTotal.textContent = formatRupiah(total);
            elTotal.dataset.total = total;
        }
    }

    // Initial load
    render(initialShipping);

    // Event delegation for all shipping radios
    document.addEventListener(
        'change',
        function (ev) {
            const target = ev.target;
            if (!target) return;
            if (!target.classList || !target.classList.contains('shipping-radio')) return;

            const shippingId = target.value;
            const label = target.closest('label')?.querySelector('.shipping-label');
            const fallbackRate = label?.dataset?.rate ?? null;

            if (!csrfToken) {
                render(fallbackRate ?? 0);
                return;
            }

            (async () => {
                try {
                    const res = await fetch("{{ route('checkout.shipping_fee') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ shipping_id: shippingId })
                    });

                    if (!res.ok) {
                        render(fallbackRate ?? 0);
                        return;
                    }

                    const data = await res.json();
                    const fee = safeParseFloat(data.shipping_fee ?? data.shipping_fee_raw ?? 0);
                    render(fee);
                } catch (err) {
                    render(fallbackRate ?? 0);
                }
            })();
        },
        true // capture
    );

})();

</script>
@endsection

