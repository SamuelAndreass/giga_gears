<div>
  <!-- Bootstrap modal markup (hidden by default). JS events open/close the modal -->
  <div class="modal fade" id="orderUpdateModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Shipping / Order #{{ $orderId ?? '-' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          @if(!$order)
            <div class="alert alert-warning">Order tidak ditemukan atau sudah dihapus.</div>
          @else
            <div class="mb-3">
              <label class="form-label">Customer</label>
              <div>{{ optional($order->user)->name ?? $order->shipping_name }}</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Courier</label>
              <input wire:model.defer="courier" type="text" class="form-control @error('courier') is-invalid @enderror">
              @error('courier') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Tracking Number</label>
              <input wire:model.defer="tracking_number" type="text" class="form-control @error('tracking_number') is-invalid @enderror">
              @error('tracking_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Status</label>
              <select wire:model.defer="status" class="form-select @error('status') is-invalid @enderror">
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
              </select>
              @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Order Items</label>
              <ul class="list-group">
                @foreach($order->items ?? [] as $it)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ optional($it->product)->name ?? ('Product #'.$it->product_id) }}
                    <span class="badge bg-secondary">{{ $it->qty ?? $it->quantity ?? '-' }}</span>
                  </li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button wire:click="updateShipping" wire:loading.attr="disabled" class="btn btn-primary">
            <span wire:loading.remove>Save changes</span>
            <span wire:loading>Saving...</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
