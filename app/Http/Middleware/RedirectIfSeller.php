<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // ambil record toko walau statusnya non-active (gunakan without global scope jika ada)
        $store = $user->sellerStore()->withoutGlobalScopes()->first();

        // Redirect ONLY if user is marked seller AND store is active
        if ($user->is_seller && $store && $store->status === 'active') {
            return redirect()->route('seller.index')->with('info', 'Anda sudah menjadi seller aktif.');
        }

        // Otherwise allow — this permits user yang is_seller=false tapi punya store non-active untuk reaktivasi
        return $next($request);
    }
}
