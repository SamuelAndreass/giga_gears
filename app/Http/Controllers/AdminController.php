<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerStore;
use Illuminate\Support\Facades\DB;
use App\Models\Shipping;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Seminar;
class AdminController extends Controller
{
    //
    public function index(){
        $t_customer = User::where('role', 'customer')->count();
        $t_seller = SellerStore::count();
        $t_transaction = Order::where('status', 'completed')->sum('total_amount');
        $recent_order = OrderItem::all()->take(5);

        // Seminar Data
        $t_seminar = Seminar::count();
        $t_seminar_registration = \App\Models\SeminarRegistration::count();
        $recent_seminars = Seminar::orderBy('created_at', 'desc')->take(5)->get();
        
        $customer_growth = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->where('role', 'customer')
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $customerData = [];
        foreach ($months as $index => $m) {
            $customerData[] = $customer_growth[$index + 1] ?? 0;
        }

        $revenues = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select('categories.id as category_id', 'categories.name as category_name', DB::raw('SUM(order_items.subtotal) as total_revenue'))
        ->groupBy('categories.id', 'categories.name')
        ->orderByDesc('total_revenue')
        ->get();

        // Total revenue keseluruhan (float)
        $total = (float) $revenues->sum('total_revenue');

        // Arrays untuk chart
        $labels = $revenues->pluck('category_name')->toArray();
        $values = $revenues->pluck('total_revenue')->map(fn($v) => (float) $v)->toArray();

        // Persentase tiap kategori (2 desimal)
        $percentages = collect($values)->map(fn($v) => $total > 0 ? round(($v / $total) * 100, 2) : 0)->toArray();


        return view('admin.dashboard', compact('t_customer', 't_seller', 't_transaction', 'customer_growth' ,'recent_order', 'labels', 'values', 'percentages', 'revenues', 'total', 'customerData', 'months'));
    }

    public function workshopIndex(){
        $workshops = Seminar::all();
        return view('admin.workshop', compact('workshops'));
    }

    public function workshopStore(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'instructor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string',
            'status' => 'required|in:upcoming,ongoing,finished,cancelled',
            'requirements' => 'nullable|string',
        ]);

        Seminar::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'instructor' => $request->instructor,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'status' => $request->status,
            'requirements' => $request->requirements,
        ]);

        return redirect()->route('admin.workshops.index')->with('success', 'Workshop berhasil ditambahkan');
    }

    public function workshopDestroy(Seminar $workshop){
        $workshop->delete();
        return redirect()->route('admin.workshops.index')->with('success', 'Workshop berhasil dihapus');
    }

    public function workshopJson(Seminar $workshop){
        return response()->json([
            'id' => $workshop->id,
            'title' => $workshop->title,
            'description' => $workshop->description,
            'start_date' => $workshop->start_date->format('Y-m-d'),
            'start_time' => $workshop->start_date->format('H:i'),
            'end_date' => $workshop->end_date->format('Y-m-d'),
            'end_time' => $workshop->end_date->format('H:i'),
            'instructor' => $workshop->instructor,
            'capacity' => $workshop->capacity,
            'location' => $workshop->location,
            'status' => $workshop->status,
            'requirements' => $workshop->requirements,
            'image_url' => $workshop->image_url,
        ]);
    }

    public function workshopUpdate(Request $request, Seminar $workshop){
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date',
            'end_time' => 'required|date_format:H:i',
            'instructor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string',
            'status' => 'required|in:upcoming,ongoing,finished,cancelled',
            'requirements' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $startDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->start_date . ' ' . $request->start_time);
        $endDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->end_date . ' ' . $request->end_time);

        $workshop->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'instructor' => $request->instructor,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'status' => $request->status,
            'requirements' => $request->requirements,
            'image_url' => $request->image_url,
        ]);

        return redirect()->route('admin.workshops.index')->with('success', 'Workshop berhasil diperbarui');
    }

    public function viewUser(Request $request){
        $query = User::with('customerProfile')
            ->where('role', 'customer')
            ->orderBy('created_at', 'desc');

        // Search form GET (by ID, name, email, phone)
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Optional status filter (dropdown)
        if ($status = $request->status) {
            if ($status !== "all") {
                $query->where('status', $status);
            }
        }

        $customers = $query->paginate(10)->withQueryString();
        return view('admin.customer', compact('customers'));
    }
    public function userJson($id)
    {
        $user =User::with('customerProfile')->find($id);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->customerProfile->phone ?? '',
            'address' => $user->customerProfile->address ?? '',
            'status' => $user->status,
        ]);
    }

    public function updateUser(Request $request, User $user){
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$user->id}",
            'phone'    => 'nullable|string|regex:/^[0-9+\-\s]{8,20}$/',
            'address'  => 'nullable|string',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $profile = $user->customerProfile;
        // update CustomerProfile
        if($request->filled('phone')){
            $profile->phone = $request->phone;
        }

         if($request->filled('address')){
            $profile->address = $request->address;
        }

        $profile->save();
        return response()->json(['status' => 'ok', 'message' => 'User updated successfully.', 'user' => $user]);
    }

    public function updateStatus(Request $request, User $user){
        $request->validate([
            'status' => 'required|in:active,suspended,banned',
        ]);

        $user->status = $request->status;
        $user->save();

        return back()->with('success', "Status updated to {$user->status}");
    }

    public function updateStatusSeller(Request $request, $storeId){
        $request->validate([
            'status' => 'required|in:active,suspended,banned',
        ]);
        

        $seller = SellerStore::findOrFail($storeId);
        $seller->status = $request->status;
        $seller->save();

        return back()->with('success', "Status updated to {$seller->status}");
    }

    public function sellerIndex(Request $request){
        $query = SellerStore::with('user')
            ->withCount('products')
            ->orderBy('created_at','desc');

        if ($q = $request->search) {
            $query->where(function($qq) use ($q) {
                $qq->where('id', 'like', "%{$q}%")
                   ->orWhere('store_name', 'like', "%{$q}%")
                   ->orWhereHas('user', fn($u)=> $u->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%"));
            });
        }

        $sellers = $query->paginate(10)->withQueryString();

        return view('admin.seller', compact('sellers'));
    }

    public function updateSellerStatus(Request $request, SellerStore $seller){
        $request->validate([
            'status' => 'required|in:active,suspended,closed'
        ]);

        $seller->status = $request->status;
        $seller->save();

        return back()->with('success','Seller status updated.');
    }

    public function sellerJson(SellerStore $seller){
        $seller->load(['user', 'products' => function($q){
            $q->select('id','seller_store_id','name','original_price','stock','status', 'rating');
        }]);

        $totalRevenue = DB::table('order_items')
            ->join('products','order_items.product_id','products.id')
            ->where('products.seller_store_id', $seller->id)
            ->selectRaw('COALESCE(SUM(order_items.subtotal),0) as total')
            ->value('total');


        $totalOrders = DB::table('order_items')
            ->join('products','order_items.product_id','products.id')
            ->where('products.seller_store_id', $seller->id)
            ->distinct('order_items.order_id')
            ->count('order_items.order_id');

        if($seller->city && $seller->country){
                $location = $seller->city . ', ' . $seller->country;
        }else{
                $location = $seller->store_address ?? '-';
        }

        return response()->json([
            'id' => $seller->id,
            'store_name' => $seller->store_name,
            'avatar' => $seller->store_logo ?? null,
            'banner' => $seller->store_banner ?? null,
            'location' => $location,
            'owner_name' => $seller->user->name ?? '-',
            'email' => $seller->user->email ?? '-',
            'phone' => $seller->phone ?? '-',
            'status' => $seller->status,
            'since_year' => $seller->created_at->format('Y'),
            'product_count' => $seller->products->count(),
            'total_revenue' => (float) $totalRevenue,
            'total_orders' => (int) $totalOrders,
            
            'products' => $seller->products->map(function($p){
                return [
                    'id' => $p->id,
                    'sku' => 'PRD-'.$p->id,
                    'name' => $p->name,
                    'price' => (float) $p->original_price,
                    'stock' => $p->stock,
                    'sold' => $p->sold_count ?? 0,
                    'status' => $p->status,
                    'rating' => (float) ($p->rating ?? 0),
                ];
            }),
        ]);
    }

    public function sellerProductsJson(SellerStore $seller, Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);

        $products = $seller->products()
            ->select('id','seller_store_id','name','original_price','stock','status','rating')
            ->orderBy('id','desc')
            ->paginate($perPage);

        $products->getCollection()->transform(function($p){
            return [
                'id' => $p->id,
                'sku' => 'PRD-' . $p->id,
                'name' => $p->name,
                'price' => (float) $p->original_price,
                'stock' => $p->stock,
                'sold' => $p->sold_count ?? 0,
                'status' => $p->status,
                'rating' => (float) ($p->rating ?? 0),
            ];
        });

        return response()->json($products);
    }


    public function dataTransaction(){

        $q = OrderItem::query();

        //eager load ke relasi terkait
        $q->with([
            'order:id,user_id,order_code,total_amount,status,ordered_at',
            'order.user:id,name,email',
            'product:id,name,original_price,seller_store_id',
            'product.sellerStore:id,store_name' 
        ]);


        $q->select('id', 'order_id', 'product_id', 'qty', 'price', 'subtotal');
        $items = $q->latest('created_at')->paginate(25)->withQueryString();

        return view('admin.data-transaction', compact('items'));
    }

    public function toggle(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $shipping = Shipping::lockForUpdate()->find($id);

            if (! $shipping) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipping method not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Normalize status values (you may have different convention; adjust accordingly)
            $current = $shipping->status ?? 'inactive';
            $newStatus = ($current === 'active') ? 'inactive' : 'active';

            // update and save
            $old = $shipping->status;
            $shipping->status = $newStatus;
            $shipping->save();

            // optional: write to audit log table or laravel log
            Log::info('Shipping status toggled', [
                'shipping_id' => $shipping->id,
                'old_status' => $old,
                'new_status' => $newStatus,
                'by_user_id' => $request->user()?->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'id' => $shipping->id,
                'status' => $shipping->status,
                'message' => 'Shipping status updated'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to toggle shipping status', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'by_user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping status'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function shippingIndex(){
        $shippings = Shipping::paginate(5);
        return view('admin.shipping', compact('shippings'));
    }

    public function addShipping(Request $request){
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'service_type'       => 'required|string|max:50',
            'custom_service'     => 'nullable|string|max:255',
            'base_rate'          => 'required|numeric|min:0',
            'per_kg'             => 'required|numeric|min:0',
            'min_delivery_days'  => 'required|integer|min:1|max:60',
            'max_delivery_days'  => 'required|integer|gte:min_delivery_days|max:60',
            'coverage'           => 'required|string|max:255',
        ]);

        // Jika service_type = custom, custom_service wajib
        if ($validated['service_type'] === 'custom' && empty($validated['custom_service'])) {
            return back()->withErrors(['custom_service' => 'Custom service name is required.'])->withInput();
        }

        Shipping::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping method added successfully!',
        ]);
    }

    public function editShipping(Request $request, Shipping $shipping){
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'service_type'       => 'required|string|max:50',
            'custom_service'     => 'nullable|string|max:255',
            'base_rate'          => 'required|numeric|min:0',
            'per_kg'             => 'required|numeric|min:0',
            'min_delivery_days'  => 'required|integer|min:1|max:60',
            'max_delivery_days'  => 'required|integer|gte:min_delivery_days|max:60',
        ]);

        // Jika service = custom maka custom_service wajib
        if ($validated['service_type'] === 'custom' && empty($validated['custom_service'])) {
            return response()->json([
                'success' => false,
                'message' => 'Custom service name is required.',
            ], 422);
        }

        $shipping->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping updated successfully.',
        ]);
    }


    public function sellerView(){
        $seller = SellerStore::with('user')->paginate(10);
        return view('admin.seller-view', compact('seller'));
    }

    public function updateSeller(){
    }

    
    public function productIndex(Request $request){
        $query = Product::with(['sellerStore', 'category'])
            ->orderBy('created_at', 'desc');

        // Optional: Search
        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('sellerStore', fn($q) => $q->where('store_name', 'like', "%{$search}%"));
        }

        $products = $query->paginate(6);

        return view('admin.product', compact('products'));
    }

        public function toggleStatus(Product $product)
        {
            $product->status = strtolower($product->status) === 'active' ? 'banned' : 'active';
            $product->save();

            return back()->with('success', 'Product status updated.');
        }


    public function productJson(Product $product){
        $product->load(['sellerStore', 'category']);

        $imageUrl = $product->images; 
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku ?? ('PRD-'.$product->id),
            'price' => $product->original_price,
            'stock' => $product->stock,
            'description' => $product->description,
            'status' => $product->status,
            'image' => $imageUrl,
            'category' => $product->category->name ?? '-',
            'seller' => $product->sellerStore->store_name ?? 'Unknown Seller',
            'rating' => number_format($product->rating ?? 0, 1),
        ]);
    }

    public function approveSeller($id){
        $seller = SellerStore::find($id);
        $seller::approve(auth()->user()->id);
        return back()->with('message', 'Berhasil melakukan approve seller');
    }

    public function SuspendSeller($id){
        $seller = SellerStore::find($id);
        $seller::suspend(auth()->user()->id);
        return back()->with('message', 'Berhasil melakukan approve seller');
    }

    public function totalStore(){}

    public function sellerDetail($id){
        $seller = SellerStore::find($id);
        $product_list = Product::where('seller_store_id', $id)
        ->withCount([
            'orderItems as total_sold' => function($q){
                $q->select(DB::raw('SUM(qty)'));
            }
        ])->paginate(3);

        $order_history = Order::whereHas('items.product', fn($q)=>$q->where('seller_store_id','=', $id))
        ->with(['user:id,name', 'items.product:id,name,price,seller_store_id'])->paginate(3)
        ->map(function($order){
            $amount = $order->items->sum(function($item){
                return $item->price * $item->qty;
            });
            return [
                'order_code' => $order->order_code,
                'date' => $order->created_at->format('M d, y'),
                'customer' => $order->user->name,
                'product' => $order->items->pluck('product.name')->join(', '),
                'amount' => number_format($amount, 2),
                'status' => ucfirst($order->status),
            ];
        });

        return view('admin.seller-detail-view');
    }

}

