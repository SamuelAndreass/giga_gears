<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStoreIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || ! $user->sellerStore || $user->sellerStore->status !== 'active') {
            return redirect()->route('dashboard')->with('error', 'Store Anda tidak aktif.');
        }

        return $next($request);
    }
}
