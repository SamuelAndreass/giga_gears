@php
  // $items adalah LengthAwarePaginator berisi item untuk current page.
  // Each item is object with ->type ('product'|'store'), ->model, ->created_at
  $pageItems = collect($items->items());
  $productItems = $pageItems->where('type', 'product')->values();
  $storeItems = $pageItems->where('type', 'store')->values();
@endphp

<!-- Top header with count -->
<section class="gg-card p-0 mb-3">
  <div class="d-flex align-items-center justify-content-between px-3 px-md-4 py-3 border-bottom">
    <h5 class="mb-0">Customer Feedback</h5>
    <small class="text-muted"><span id="tbl-count">{{ $total ?? $items->total() }}</span> items</small>
  </div>

  <div class="table-responsive overflow-hidden">
    <table class="table mb-0 align-middle">
      <thead>
        <tr>
          <th style="width:160px">Customer</th>
          <th style="width:220px">Product</th>
          <th style="width:110px">Rating</th>
          <th>Review</th>
          <th style="width:150px">Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($productItems as $row)
          @php $m = $row->model; @endphp
          <tr>
            <td>{{ optional($m->user)->name ?? '-' }}</td>
            <td>{{ optional($m->product)->name ?? '-' }}</td>
            <td>{{ $m->rating ?? '-' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($m->message ?? $m->comment ?? '-', 200) }}</td>
            <td>{{ optional($m->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">No product reviews on this page.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- pagination for the merged paginator -->
  <div class="p-3">
    {!! $items->links() !!}
  </div>
</section>

<!-- Message List -->
<section class="gg-card p-3 p-md-4">
  <h5 class="mb-3">Inbox Message</h5>

  <div id="msgList" class="d-flex flex-column gap-2">
    @forelse($storeItems as $row)
      @php $m = $row->model; @endphp
      <div class="d-flex align-items-start gap-3 p-2 rounded-3 border">
        <div class="rounded-circle bg-secondary-subtle d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px">
            <i class="bi bi-person"></i>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between">
            <strong>{{ optional($m->user)->name ?? 'Customer' }}</strong>
            <small class="text-muted">{{ optional($m->created_at)->format('Y-m-d H:i') ?? '-' }}</small>
          </div>
          <div class="text-muted small">{{ \Illuminate\Support\Str::limit($m->message ?? $m->comment ?? '-', 500) }}</div>
        </div>
      </div>
    @empty
      <div class="text-center w-100">No inbox messages on this page.</div>
    @endforelse
  </div>
</section>
