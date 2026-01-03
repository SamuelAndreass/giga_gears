<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order; 
 use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class CustController extends Controller
{
    public function myOrders(Request $request)
    {
       $user = $request->user();
        $search = $request->query('search');
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 12);
        $query = Order::with(['items.product'])
                      ->where('user_id', $user->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
                $q->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }


        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')
                        ->paginate($perPage)
                        ->withQueryString();

        $orders->getCollection()->transform(function ($order) {

            $products = $order->items->map(function ($item) {
                $product = $item->product;
                return (object) [
                    'id' => $product->id ?? null,
                    'name' => $product->name ?? ($item->product_name ?? 'Unnamed Product'),
                    'image' => $product->images ?? null,
                    'qty' => $item->qty ?? 1,
                    'unit_price' => $item->unit_price ?? $item->price ?? null,
                    'subtotal' => $item->subtotal ?? (($item->qty ?? 0) * ($item->unit_price ?? $item->price ?? 0)),
                ];
            });

            $order->products = $products->values()->all();

            if (isset($order->total) && $order->total !== null) {
                $order->total_price = $order->total;
            } elseif (isset($order->total_amount) && $order->total_amount !== null) {
                $order->total_price = $order->total_amount;
            } else {
                $order->total_price = $order->items->sum(function ($i) {
                    return $i->subtotal ?? (($i->qty ?? 0) * ($i->unit_price ?? $i->price ?? 0));
                });
            }

            return $order;
        });

        return view('customer.my_order', compact('orders'));
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $products = $order->items->map(function ($item) {
            $productModel = $item->product;

            $displayProduct = (object) [
                'id' => $productModel->id ?? null,
                'name' => $productModel->name ?? 'Unnamed Product',
                'image' => $productModel->images ?? null,
            ];

            $displayProduct->pivot = (object) [
                'quantity' => $item->qty ?? $item->quantity ?? 1,
                'price' => $item->unit_price ?? $item->price ?? $item->price_per_unit ?? 0,
            ];

            return $displayProduct;
        });


        $order->products = $products;

        if (isset($order->subtotal) && $order->subtotal !== null) {
            $order->subtotal = (float) $order->subtotal;
        } else {

            $order->subtotal = $order->items->sum(function ($i) {
                return $i->subtotal ?? (($i->qty ?? 0) * ($i->unit_price ?? $i->price ?? 0));
            });
        }


        $order->shipping_fee = $order->shipping_fee ?? $order->shipping_cost ?? 0;


        if (isset($order->total) && $order->total !== null) {
            $order->total_price = (float) $order->total;
        } elseif (isset($order->total_amount) && $order->total_amount !== null) {
            $order->total_price = (float) $order->total_amount;
        } else {
            $order->total_price = $order->subtotal + (float) $order->shipping_fee;

        }

        $order->payment_method = $order->payment_method ?? $order->payment_type ?? null;
        $order->shipping_address = $order->shipping_address ?? $order->address ?? null;
        $order->status = $order->status ?? 'Unknown';

        return view('customer.order-detail', compact('order'));
    }

    public function showProducts(Request $request){
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category');
        $perPage = (int) $request->query('per_page', default: 8);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 12;

        $query = Product::with('category')->where('status', 'active')->where( 'stock', '>', 0)->where('type', 'single');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }


        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('created_at', 'desc')
                          ->paginate($perPage)
                          ->withQueryString();


        $categories = Category::orderBy('name')->get();

        return view('customer.products-show', compact('products', 'categories'));
    }
    public function cancel(Request $request, $id)
    {
        $userId = $request->user()->id;

        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->whereIn('status', ['Processing', 'Shipped'])
            ->firstOrFail();

        $order->status = 'Cancelled';
        $order->save();

        return back()->with('message', 'Order cancelled successfully.');
    }

    public function showProd($id)
    {
        $detailProduct = Product::findOrFail($id);


        $raw = $detailProduct->images;
        $images = [];

        if (is_array($raw)) {
            $images = $raw;
        } elseif (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$raw];
        }

        $imageUrls = collect($images)->map(function ($path) {
            if (!$path) return null;
            if (preg_match('#^https?://#i', $path)) return $path;
            if (Storage::disk('public')->exists($path)) return Storage::url($path);
            if (file_exists(public_path($path))) return asset($path);
            if (file_exists(public_path('images/'.$path))) return asset('images/'.$path);
            return null;
        })->filter()->values()->all();

        $prod_img = $imageUrls[0] ?? asset('images/default-product.png');

        $relatedProducts = collect();

        if (!empty($detailProduct->category_id)) {
            $relatedProducts = Product::where('category_id', $detailProduct->category_id)
                ->where('id', '!=', $detailProduct->id)
                ->take(3)
                ->get();
        }

        $relatedProducts = $relatedProducts->map(function ($p) {

            $raw = $p->images;
            $images = [];

            if (is_array($raw)) {
                $images = $raw;
            } elseif (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$raw];
            }

            $first = collect($images)->first();
            $imageUrl = null;

            if ($first) {
                if (preg_match('#^https?://#i', $first)) {
                    $imageUrl = $first;
                } elseif (Storage::disk('public')->exists($first)) {
                    $imageUrl = Storage::url($first);
                } elseif (file_exists(public_path($first))) {
                    $imageUrl = asset($first);
                } elseif (file_exists(public_path('images/'.$first))) {
                    $imageUrl = asset('images/'.$first);
                }
            }


            $p->image_url = $imageUrl ?? asset('images/default-product.png');

            return $p;
        });


        return view('customer.product-detail', compact('detailProduct', 'prod_img', 'imageUrls', 'relatedProducts'));
        }
}