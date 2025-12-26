<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\ShippingOrder;
use Exception;
class CheckoutController extends Controller
{

    
    public function showCheckoutPage(Request $request)
    {
        $user = $request->user();

        $cart = Cart::with('items.product')
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cart kosong.');
        }

        $subTotal = 0;
        foreach ($cart->items as $ci) {
            $subTotal += $ci->subtotal;
        }
        
        $shippings = Shipping::where('status', 'active')->get();
        $defaultShipping = $shippings->first();
        $shippingFee = $defaultShipping ? $defaultShipping->base_rate : 0;

        $defaultTotal = $subTotal + $shippingFee;

        return view('customer.checkout', [
            'cart' => $cart,
            'sub_total' => $subTotal,
            'shipping_fee' => $shippingFee,
            'total_payment' => $defaultTotal,
            'shippings' => $shippings,
        ]);
    }
    
    public function getShippingFee(Request $request)
    {
        $request->validate([
            'shipping_id' => 'required|integer|exists:shippings,id',
        ]);

        $shipping = Shipping::find($request->shipping_id);

        $fee = (float) ($shipping->base_rate ?? 0);

        return response()->json([
            'shipping_id' => $shipping->id,
            'shipping_fee' => $fee,
        ], 200);
    }


    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:2000',
            'payment_method' => 'required|string',
            'shipping_id' => 'required|integer|exists:shippings,id',
            'idempotency_key' => 'nullable|string',
        ]);

        try {
            $user = $request->user();

            $idempotencyKey = $request->input('idempotency_key') ?? $request->header('X-Idempotency-Key') ?? (string) Str::uuid();


            $existing = Order::where('user_id', $user->id)
                             ->where('idempotency_key', $idempotencyKey)
                             ->first();
            if ($existing) {

                if (! $request->wantsJson()) {
                    return redirect()->route('orders.show', $existing->id)
                                     ->with('info', 'Order already processed.');
                }
                return response()->json(['order' => $existing], 200);
            }

            $cart = Cart::with('items.product')
                        ->where('user_id', $user->id)
                        ->where('status', 'active')
                        ->first();

            if (! $cart || $cart->items->isEmpty()) {
                if (! $request->wantsJson()) {
                    return redirect()->route('cart.index')->with('error', 'Cart is empty.');
                }
                return response()->json(['message' => 'Cart is empty'], 400);
            }

            $subtotal = 0;
            foreach ($cart->items as $ci) {

                if (! $ci->product) {
                    throw new Exception("Cart item product missing (id: {$ci->product_id})");
                }
                $subtotal += ($ci->product->original_price * $ci->qty);
            }


            $shipping = Shipping::find($request->shipping_id);
            $shippingFee = $shipping ? (float) ($shipping->base_rate ?? 0) : 0;

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                'ordered_at' => now(),
                'shipping_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => 0,
                'discount' => 0,
                'total_amount' => ($subtotal + $shippingFee),
                'idempotency_key' => $idempotencyKey,
            ]);


            ShippingOrder::create([
                'order_id' => $order->id,
                'shipping_id' => $shipping->id ?? null,
                'type' => $shipping->service_type,
            ]);

            foreach ($cart->items as $ci) {

                $product = Product::lockForUpdate()->find($ci->product_id);
                if (! $product) {
                    throw new Exception("Product not found (id: {$ci->product_id})");
                }
                if ($product->stock < $ci->qty) {
                    throw new Exception("Insufficient stock for product: {$product->name}");
                }

                $unitPrice = $product->original_price;
                $lineSubtotal = $unitPrice * $ci->qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => $ci->qty,
                    'sku' => $product->sku ?? null,
                    'price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ]);

                $product->decrement('stock', $ci->qty);
            }


            $cart->update(['status' => 'checked_out']);
            $cart->items()->delete();

            DB::commit();

            if (! $request->wantsJson()) {
                return redirect()->route('orders.show', $order->id)
                                 ->with('success', 'Order placed successfully.');
            }

            return response()->json(['order' => $order], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: '.$e->getMessage(), [
                'user_id' => $request->user()?->id,
                'payload' => $request->all(),
            ]);

            if (! $request->wantsJson()) {
                return back()->with('error', 'Checkout failed: ' . $e->getMessage());
            }
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }


}
