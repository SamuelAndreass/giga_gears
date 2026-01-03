<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with('items.product.bundleItems.product')
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        if ($cart) {
            if ($cart) {
                $cart = $this->revalidateCart($cart);
            }
        }

        return view('customer.cart', compact('cart'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $qty = (int) ($request->input('qty', 1));

        $product = Product::with('bundleItems.product')
            ->findOrFail($request->product_id);

        // create or get active cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['total' => 0]
        );

        if ($this->isBundle($product)) {
            $this->validateBundleStock($product, $qty);
        }

        $item = $cart->items()->where('product_id', $product->id)->first();

        $price = $product->price;

        if ($item) {
            $item->update([
                'qty' => $item->qty + $qty,
                'price' => $price,
                'subtotal' => ($item->qty + $qty) * $price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'qty' => $qty,
                'sku' => $product->sku ?? null,
                'price' => $price,
                'subtotal' => $price * $qty,
                'meta' => $this->isBundle($product)
                    ? $this->buildBundleMeta($product)
                    : null,
                'price_snapshot' => $price * $qty,
            ]);
        }

        $cart->refresh();
        $cart->update([
            'total' => $cart->items->sum(fn($i) => ($i->qty ?? 0) * ($i->price ?? 0))
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Added to cart',
                'cart' => $cart->load('items.product')
            ], 200);
        }

        return redirect()->back()->with('message', 'Product added to cart.');
    }

    private function isBundle(Product $product): bool
    {
        return $product->type === 'bundle';
    }

    private function validateBundleStock(Product $bundle, int $qty): void
    {
        foreach ($bundle->bundleItems as $item) {

            if (! $item->product) {
                abort(422, "Produk bundle tidak valid");
            }

            $needed = $item->quantity * $qty;

            if ($item->product->stock < $needed) {
                abort(422, "Stok {$item->product->name} tidak mencukupi");
            }
        }
    }


    private function buildBundleMeta(Product $bundle): array
    {
        return [
            'type' => 'bundle',
            'items' => $bundle->bundleItems->map(fn ($item) => [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'qty' => $item->quantity,
            ])->toArray()
        ];
    }


    public function update(Request $request, $itemId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail($itemId);

        if ($item->product->stock <= 0) {
            $item->delete();

            return back()->with('error', 'Produk sudah habis stok dan dihapus dari cart');
        }

        if ($item->cart->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($item->product->type === 'bundle') {
            $this->validateBundleStock($item->product, $request->qty);
        }

        $item->update([
            'qty' => $request->qty,
            'subtotal' => $request->qty * $item->price,
        ]);

        $cart = $item->cart;
        $cart->update([
            'total' => $cart->items->sum(fn($i) => ($i->qty ?? 0) * ($i->price ?? 0))
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cart updated', 'cart' => $cart->load('items.product')]);
        }

        return back()->with('message', 'Cart updated.');
    }


    public function remove(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);
        if ($item->cart->user_id !== $request->user()->id) abort(403);

        $cart = $item->cart;
        $item->delete();

        $cart->update([
            'total' => $cart->items->sum(fn($i) => ($i->qty ?? 0) * ($i->price ?? 0))
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Item removed', 'cart' => $cart->load('items.product')]);
        }

        return back()->with('message', 'Item removed.');
    }

    public function buyNowRedirect(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $qty = (int) ($request->qty ?? 1);


       $product = Product::with('bundleItems.product')->findOrFail($request->product_id);

        if ($product->type === 'bundle') {
            $this->validateBundleStock($product, $qty);
        }


        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'status' => 'active',
        ]);


        $cart->items()->delete();

        $cart->items()->create([
            'product_id' => $product->id,
            'qty' => $qty,
            'price' => $product->price, 
            'subtotal' => $product->price * $qty,
            'meta' => $product->type === 'bundle' ? $this->buildBundleMeta($product): null,
            'price_snapshot' => $product->price * $qty,
        ]);

        return redirect()->route('checkout.index');
    }

    private function revalidateCart(Cart $cart): Cart
    {
        foreach ($cart->items as $item) {

            $product = $item->product;

            if (! $product) {
                $item->delete();
                continue;
            }

            if ($product->type === 'bundle') {
                $invalid = false;
                foreach ($product->bundleItems as $bi) {
                    if ($bi->product->stock < ($bi->quantity * $item->qty)) {
                        $invalid = true;
                        break;
                    }
                }
                if ($invalid) {
                    $item->delete();
                }
            }
            
            else {
                if ($product->stock < $item->qty) {
                    $item->delete();
                }
            }
        }

        $cart->refresh();

        $cart->update([
            'total_price' => $cart->items->sum(fn ($i) => $i->price * $i->qty)
        ]);

        return $cart;
    }


}
