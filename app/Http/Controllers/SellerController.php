<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerStore;
use App\models\Order;
use App\Models\User;
use App\Models\ProductReview;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shipping;
use App\Models\ShippingOrder;
use Illuminate\Support\Facades\Validator;
use App\Models\StoreReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{
    public function viewMainDashboard(){
        $user = Auth::user()->sellerStore;
        $storeId = $user->id;
        $total_order = DB::table('order_items as oi')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->where('p.seller_store_id', $storeId)
            ->distinct('oi.id')
            ->count('oi.id');
        
        $monthlyRevenue = DB::table('order_items as oi')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('p.seller_store_id', $storeId)
            ->whereRaw('EXTRACT(MONTH FROM o.ordered_at) = ?', [now()->month])
            ->whereRaw('EXTRACT(YEAR FROM o.ordered_at) = ?', [now()->year])
            ->selectRaw('COALESCE(SUM(oi.subtotal), 0) as revenue')
            ->value('revenue');

        $activeProducts = DB::table('products')
            ->where('seller_store_id', $storeId)
            ->where('stock', '>', 0)
            ->count();
        
        $storeRating = DB::table('store_reviews')
            ->where('seller_store_id', $storeId)
            ->avg('rating');

        $storeRating = $storeRating ? number_format($storeRating, 1) : 0;
        $sales = DB::table('order_items as oi')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('p.seller_store_id', $storeId)
            ->selectRaw('
                EXTRACT(MONTH FROM o.ordered_at)::integer as month,
                EXTRACT(YEAR FROM o.ordered_at)::integer as year,
                SUM(oi.subtotal) as revenue
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date("M", strtotime("2024-$i-01"));
            $data[$i] = 0;
        }

        foreach ($sales as $row) {
            $data[$row->month] = $row->revenue;
        }

        $data = array_values($data);
        
        // Add Seminar Data
        $total_seminar = \App\Models\Seminar::count();
        $total_seminar_registration = \App\Models\SeminarRegistration::count();

        return view('seller.seller-dashboard', compact('total_order', 'monthlyRevenue', 'activeProducts', 'storeRating', 'labels', 'data', 'total_seminar', 'total_seminar_registration'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $sellerStore = $user->sellerStore;

        $logoRule = $sellerStore ? 'nullable|image|mimes:jpg,jpeg,png|max:1024' : 'required|image|mimes:jpg,jpeg,png|max:1024';
        $bannerRule = $sellerStore ? 'nullable|image|mimes:jpg,jpeg,png|max:2048' : 'required|image|mimes:jpg,jpeg,png|max:2048';

        $request->validate([
            'store_name' => 'required|string|max:255|unique:seller_stores,store_name' . ($sellerStore ? ",{$sellerStore->id}" : ''),
            'store_phone' => 'required|string|max:20',
            'store_address' => 'required|string|max:255',
            'logo' => $logoRule,
            'banner' => $bannerRule,
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'store_description' => 'nullable|string|max:2000',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
        ]);

        $data = [
            'store_name' => $request->store_name,
            'store_phone' => $request->store_phone,
            'store_address' => $request->store_address,
            'store_description' => $request->store_description,
            'province' => $request->province,
            'city' => $request->city,
            'open_time' => $request->open_time,
            'close_time' => $request->close_time,
            'status' => 'active',
        ];

        $storedFiles = [];
        $oldFilesToDelete = [];

        try {
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('store_logos', 'public');
                $data['store_logo'] = $path;
                $storedFiles[] = $path;

                if ($sellerStore && $sellerStore->store_logo) {
                    $oldFilesToDelete[] = $sellerStore->store_logo;
                }
            }

            if ($request->hasFile('banner')) {
                $path = $request->file('banner')->store('store_banners', 'public');
                $data['store_banner'] = $path;
                $storedFiles[] = $path;

                if ($sellerStore && $sellerStore->store_banner) {
                    $oldFilesToDelete[] = $sellerStore->store_banner;
                }
            }

            Log::info('ABOUT TO START TRANSACTION', ['user_id' => $user->id]);
            DB::beginTransaction();

            if (! $user->is_seller) {
                $user->update(['role' => 'seller', 'is_seller' => true]);
                Log::info('USER UPDATED TO SELLER', ['user_id' => $user->id]);
            }

            if (! $sellerStore) {
                $created = $user->sellerStore()->create($data);
                $sellerStore = $created;
                Log::info('STORE CREATED', ['id' => $created->id]);
            }

            DB::commit();

            foreach ($oldFilesToDelete as $old) {
                try {
                    Storage::disk('public')->delete($old);
                } catch (\Throwable $ex) {
                    Log::warning('Failed deleting old store file', ['file' => $old, 'error' => $ex->getMessage()]);
                }
            }

            return redirect()
                ->route('seller.index')
                ->with('message', 'Toko Anda berhasil diaktifkan / diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($storedFiles as $file) {
                try {
                    Storage::disk('public')->delete($file);
                } catch (\Throwable $deleteEx) {
                    Log::warning('Failed to delete uploaded file after error', ['file' => $file, 'error' => $deleteEx->getMessage()]);
                }
            }

            Log::error('Error creating/updating seller store: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Gagal membuat toko: '.$e->getMessage()])->withInput();
        }
    }

    public function reactivate(Request $request)
    {
        Log::info('RedirectIfSeller middleware hit', ['user_id' => optional(Auth::user())->id, 'is_seller' => optional(Auth::user())->is_seller]);

        $user = $request->user();
        $store = $user->sellerStore()->first();

        if (!$store) {
            return redirect()->route('become.seller.page')->withErrors('Toko tidak ditemukan.');
        }

        DB::transaction(function () use ($store, $user) {
            $store->update(['status' => 'active']);
            $user->update(['is_seller' => true, 'role' => 'seller']);
        });

        return redirect()->route('seller.index')->with('message','Toko berhasil diaktifkan kembali.');
    }



    public function viewRecentOrder(Request $request)
    {
        $seller = auth()->user()->sellerStore;
        $image = $seller->store_logo;

        $validStatuses = ['pending','processing','shipped','delivered','completed','cancelled','refunded'];
        $status = $request->query('status');

        $query = OrderItem::select('order_items.*')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('shipping_orders', 'orders.id', '=', 'shipping_orders.order_id')
        ->where('products.seller_store_id', $seller->id)
        ->with(['product', 'order.shiporder', 'order.user']);


        if (! empty($status) && in_array(strtolower($status), $validStatuses, true)) {
            $lower = strtolower($status);
            $query->whereHas('order', function($q) use ($lower) {
                $q->where('status', $lower);
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
                        ->paginate(20)
                        ->withQueryString(); 

        return view('seller.recent-order', compact('orders', 'image'));
    }

    public function prodJson($id){
         $order = Order::with(['items','shippingCourier'])
            ->findOrFail($id);

        $data = [
            'id' => $order->id,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'currency' => 'IDR',
            'courier' => $order->courier_name,
            'tracking_number' => $order->tracking_number,
            'shipping_cost' => $order->shipping_cost,
            'shipping_date' => $order->shipping_date ? $order->shipping_date->toDateString() : null,
            'eta_text' => $order->eta_text ?? null,
            'available_couriers' => [
                ['name' => 'JNE'],
                ['name' => 'J&T'],
                ['name' => 'SiCepat'],
            ],
            'items' => $order->items->map(function($it){
                return [
                    'sku' => $it->sku,
                    'name' => $it->name,
                    'qty' => $it->quantity,
                    'price' => $it->price,
                ];
            }),
        ];

        return response()->json($data);
    }

    public function update(Request $request, $id){
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => ['nullable','string'],
            'courier' => ['nullable','string'],
            'tracking_number' => ['nullable','string'],
            'shipping_cost' => ['nullable','numeric'],
            'shipping_date' => ['nullable','date'],
            'eta_text' => ['nullable','string'],
        ]);

        $order->status = $validated['status'] ?? $order->status;
        $order->courier_name = $validated['courier'] ?? $order->courier_name;
        $order->tracking_number = $validated['tracking_number'] ?? $order->tracking_number;
        $order->shipping_cost = $validated['shipping_cost'] ?? $order->shipping_cost;
        $order->shipping_date = $validated['shipping_date'] ?? $order->shipping_date;
        $order->eta_text = $validated['eta_text'] ?? $order->eta_text;

        $order->save();

        return response()->json(['success' => true, 'message' => 'Order updated', 'order' => $order]);
    }

    public function generateTracking(Order $order, $courier){
        return strtoupper($courier) . '-' . $order->id . '-' . strtoupper(Str::random(6));
    }

    public function updateStatus(Request $request, $id){
        $request->validate(['status'=> 'required|string']);
        $order = Order::findOrFail($id);
        $order->update(['status'=> $request->status]);
        return back()->with('message', 'Berhasil merubah status');
    }

    public function viewProd(Request $request){
        $seller = auth()->user()->sellerStore;

        $storelogo = $seller->store_logo;
        $query = Product::where('seller_store_id', $seller->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $product = $query->paginate(10);

        return view('seller.product', compact('product', 'storelogo'));
    }

    public function viewAddProductForm(){
        $categories = Category::all();
        $shippings = Shipping::all();
        return view('seller.add-new-product', compact('categories', 'shippings'));
    }

    public function addProduct(Request $request)
    {
        $seller = auth()->user()->sellerStore;

        if (! $seller) {
            abort(403, 'Seller store not found.');
        }

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'brand'        => 'required|string|max:100',
            'description'  => 'required|string|max:5000',
            'price'        => 'required|numeric|min:0',
            'qty'          => 'required|integer|min:0|max:10000',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'color'        => 'nullable|string|max:50',
            'image'        => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video'        => 'nullable|mimes:mp4,mov,avi,wmv|max:200000',
            'weight'       => 'nullable|integer|min:0',
            'diameter'     => 'nullable|numeric|min:0',
            'variants'     => 'nullable|array',
            'variants.*.name'  => 'nullable|string|max:100',
            'variants.*.value' => 'nullable|string|max:100',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'PSKU'         => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        $sku = $data['PSKU'] ?? null;
        if (! $sku) {
            $sku = 'GG-S' . $seller->id . '-' . now()->format('ymdHis') . '-' . strtoupper(Str::random(4));
        } else {
            $base = $sku;
            $i = 1;
            while (Product::where('SKU', $sku)->exists()) {
                $sku = $base . '-' . $i++;
            }
        }

        $variants = [];
        foreach ($data['variants'] ?? [] as $row) {
            if (empty($row['name']) || empty($row['value'])) {
                continue;
            }

            $variants[] = [
                'name'  => trim($row['name']),
                'value' => trim($row['value']),
                'price' => isset($row['price']) ? (float) $row['price'] : null,
                'stock' => isset($row['stock']) ? (int) $row['stock'] : null,
            ];
        }

        DB::beginTransaction();

        try {
            $imagePath = $request->file('image')->store('products', 'public');
            $videoPath = $request->hasFile('video')
                ? $request->file('video')->store('videos', 'public')
                : null;

            Product::create([
                'name' => $data['product_name'],
                'category_id' => $data['category_id'],
                'seller_store_id' => $seller->id,
                'brand' => $data['brand'],
                'description' => $data['description'],
                'original_price' => $data['price'],
                'discount_percentage' => $data['discount'] ?? 0,
                'stock' => $data['qty'],
                'color' => $data['color'] ?? null,
                'images' => [$imagePath],
                'video' => $videoPath,
                'SKU' => $sku,
                'weight' => $data['weight'] ?? null,
                'diameter' => $data['diameter'] ?? null,
                'variants' => !empty($variants) ? $variants : null,
                'status' => 'active',
            ]);

            DB::commit();

            return back()->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            if (isset($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }

            report($e);

            return back()->withErrors('Gagal menambahkan produk. Silakan coba lagi.');
        }
    }


    public function deleteProd(Product $product){
         $seller = auth()->user()->sellerStore;
         abort_unless($product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk mengubah product ini!');
         $product->delete();
         return back()->with('success', 'Berhasil menghapus product.');
    }

    public function updateStock(Request $request, Product $product){
        $seller = auth()->user()->sellerStore;
        abort_unless($product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk mengubah product ini!');
        $validated = $request->validate(['stock' => 'required|integer|min:0']);
        $product->update(['stock'=> $validated['stock']]);
        return back()->with('success', 'Stock berhasil diperbaharui');
    }


    public function viewAnalyticsReview(Request $request)
    {
        $seller = auth()->user()->sellerStore;
        if (!$seller) {
            abort(403, 'Seller store not found');
        }
        $sellerId = $seller->id;


        $total_revenue = \DB::table('order_items')
            ->selectRaw('COALESCE(SUM(price * qty), 0) as total')
            ->whereExists(function ($q) use ($sellerId) {
                $q->select(\DB::raw(1))
                ->from('products')
                ->whereColumn('products.id', 'order_items.product_id')
                ->where('products.seller_store_id', $sellerId);
            })
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                ->from('orders')
                ->whereColumn('orders.id', 'order_items.order_id')
                ->where('orders.status', 'completed');
            })
            ->value('total');


        $total_order = \App\Models\Order::whereExists(function ($q) use ($sellerId) {
            $q->select(\DB::raw(1))
            ->from('order_items')
            ->whereColumn('order_items.order_id', 'orders.id')
            ->whereExists(function ($qq) use ($sellerId) {
                $qq->select(\DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.id', 'order_items.product_id')
                    ->where('products.seller_store_id', $sellerId);
            });
        })->where('status', '!=', 'cancelled')->count();
        $product_sold = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_store_id', $sellerId);
            })
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed');
            })
            ->sum('qty');


        $total_customers = \App\Models\Order::whereExists(function ($q) use ($sellerId) {
            $q->select(\DB::raw(1))
            ->from('order_items')
            ->whereColumn('order_items.order_id', 'orders.id')
            ->whereExists(function ($qq) use ($sellerId) {
                $qq->select(\DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.id', 'order_items.product_id')
                    ->where('products.seller_store_id', $sellerId);
            });
        })
        ->whereIn('status', ['completed'])
        ->distinct()
        ->count('user_id');


        $best_selliing_prod = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_store_id', $sellerId);
            })
            ->select('product_id', DB::raw('SUM(qty) as sold_items'))
            ->groupBy('product_id')
            ->with(['product:id,name'])
            ->orderByDesc('sold_items')
            ->take(5)
            ->get();

        $topCustomers = \DB::table('order_items')
            ->select('orders.user_id', DB::raw('SUM(order_items.price * order_items.qty) as total_spent'), DB::raw('COUNT(DISTINCT orders.id) as total_orders'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_store_id', $sellerId)
            ->where('orders.status', 'completed')
            ->groupBy('orders.user_id')
            ->orderByDesc('total_spent')
            ->take(6)
            ->get()
            ->map(function($r){

                $user = \App\Models\User::select('id','name')->find($r->user_id);
                return (object) [
                    'user_id' => $r->user_id,
                    'total_spent' => $r->total_spent,
                    'total_orders' => $r->total_orders,
                    'user' => $user
                ];
            });

        $months = [];
        $now = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $now->copy()->subMonths($i);
            $value = $date->format('Y-m');
            $label = $date->format('M Y');
            $months[$value] = $label;
        }


        $month = $request->query('month', now()->format('Y-m'));

        return view('seller.seller-analytics', compact(
            'total_revenue',
            'total_order',
            'product_sold',
            'total_customers',
            'best_selliing_prod',
            'topCustomers',
            'month',
            'months'
        ));

    }

    public function data(Request $request)
    {
        $seller = auth()->user()->sellerStore;
        if (!$seller) {
            return response()->json(['error' => 'No seller store'], 403);
        }
        $sellerId = $seller->id;

        $month = $request->query('month', now()->format('Y-m'));
        try {
            $d = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            $d = now()->startOfMonth();
            $month = $d->format('Y-m');
        }

        $start = $d->copy()->startOfMonth();
        $end   = $d->copy()->endOfMonth();

        $daysInMonth = (int)$start->daysInMonth;
        $labels = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        $ordersPerDay = DB::table('orders')
            ->selectRaw('DATE(orders.created_at) as day, COUNT(DISTINCT orders.id) as cnt')
            ->join('order_items','order_items.order_id','orders.id')
            ->join('products','order_items.product_id','products.id')
            ->where('products.seller_store_id', $sellerId)
            ->whereBetween('orders.created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupByRaw('DATE(orders.created_at)')
            ->get()
            ->keyBy(function($r){ return (new \Carbon\Carbon($r->day))->format('d'); });

        $data = [];
        foreach ($labels as $lab) {
            $data[] = isset($ordersPerDay[$lab]) ? (int)$ordersPerDay[$lab]->cnt : 0;
        }

        $totalOrders = array_sum($data);

        $payload = [
            'labels' => $labels,
            'data'   => $data,
            'summary' => [
                'total_orders' => $totalOrders,

            ],
        ];

        return response()->json($payload);
    }

    public function feedback(Request $request){

        $user = $request->user();
        if (! $user || ! $user->sellerStore) {

            return view('seller.seller-inbox', [
                'items' => new LengthAwarePaginator([], 0, 10),
                'total' => 0,
            ]);
        }

        $seller = $user->sellerStore;
        $q = $request->input('q', null);
        $filter = $request->input('filter', 'all');
        $perPage = 10;


        $productQuery = ProductReview::whereHas('product', function ($query) use ($seller) {
            $query->where('seller_store_id', $seller->id);
        })->with(['product', 'user']);

        $storeQuery = StoreReview::where('seller_store_id', $seller->id)->with(['user']);


        if ($q) {
            $like = "%{$q}%";
            $productQuery->where(function ($qq) use ($like) {
                $qq->where('comment', 'like', $like)
                   ->orWhereHas('user', function ($u) use ($like) {
                       $u->where('name', 'like', $like);
                   })
                   ->orWhereHas('product', function ($p) use ($like) {
                       $p->where('name', 'like', $like);
                   });
            });

            $storeQuery->where(function ($qq) use ($like) {
                $qq->where('comment', 'like', $like)
                   ->orWhereHas('user', function ($u) use ($like) {
                       $u->where('name', 'like', $like);
                   });
            });
        }

        if ($filter === 'review') {

            $productQuery->whereNotNull('rating');
            $storeQuery->whereNotNull('rating');
        } elseif ($filter === 'comment') {

            $productQuery->whereNull('rating');
            $storeQuery->whereNull('rating');
        }


        $productItems = $productQuery->get()->map(function ($item) {
            return (object) [
                'type' => 'product',
                'model' => $item,
                'created_at' => $item->created_at,
            ];
        });

        $storeItems = $storeQuery->get()->map(function ($item) {
            return (object) [
                'type' => 'store',
                'model' => $item,
                'created_at' => $item->created_at,
            ];
        });

 
        $merged = $productItems->merge($storeItems)->sortByDesc('created_at')->values();


        $page = Paginator::resolveCurrentPage('page') ?: 1;
        $total = $merged->count();
        $itemsForCurrentPage = $merged->slice(($page - 1) * $perPage, $perPage)->all();

        $paginated = new LengthAwarePaginator(
            $itemsForCurrentPage,
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(), 
            ]
        );


        if ($request->ajax()) {
            return view('seller.partials.inbox-list', ['items' => $paginated])->render();
        }

        return view('seller.seller-inbox', [
            'items' => $paginated,
            'total' => $total,
        ]);
    }
    
    
    public function replyFeedback(Request $request, ProductReview $review){
        $seller = auth()->user()->sellerStore;
        abort_unless($review->product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk membalas feedback ini!');
        $validated = $request->validate(['reply' => 'required|string|max:2000']);
        return back()->with('message', 'Balasan berhasil dikirim.');
    }

    public function shipData($id){
    $order = Order::with(['items.product','user'])->findOrFail($id);
    $couriers = Shipping::active()
        ->select('name','service_type','base_rate','per_kg','min_delivery_days','max_delivery_days')
        ->orderBy('name')
        ->get();

    $weight = $order->items->sum(function($it){
        $w = $it->product->weight_kg ?? 0;
        $qty = $it->qty ?? $it->quantity ?? 0;
        return $w * $qty;
    });

    return response()->json([
        'order' => $order,
        'couriers' => $couriers,
        'weight' => $weight,
    ]);
}

    public function ship(Request $request, $id)
    {

        $data = $request->validate([
            'courier' => 'required|string|max:255', 
            'tracking_number' => 'nullable|string|max:120',
            'shipping_cost' => 'required|numeric|min:0',
            'eta_end' => 'nullable|date',
        ]);

        try {
            DB::transaction(function() use ($id, $data, &$shipping) {
                $order = Order::findOrFail($id);

                $service = Shipping::where('name', $data['courier'])->first();


                $shipping = ShippingOrder::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'shipping_id' => $service->id ?? null,
                        'tracking_number' => $data['tracking_number'] ?? null,
                        'shipping_date' => now()->toDateString(),
                        'estimated_arrival_date' => $data['eta_end'] ?? null,
                        'shipping_cost' => $data['shipping_cost'],
                    ]
                );

                $order->update([
                    'status' => 'shipped',
                    'shipping_cost' => $shipping->shipping_cost,
                ]);
            });


            return response()->json([
                'success' => true,
                'shipping' => $shipping->fresh()->load('shipping')
            ]);
        } catch (\Throwable $e) {
            Log::error('Ship order failed', [
                'order_id' => $id,
                'payload' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate order',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
