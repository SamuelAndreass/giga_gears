<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class EnsureIfSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('Middleware EnsureIfSeller hit', [
            'middleware'=> 'redirect.seller',
            'user_id' => optional($request->user())->id,
            'is_seller' => optional($request->user())->is_seller ?? null,
            'uri' => $request->path(),
        ]);
        if(!Auth::check() || Auth::user()->role != 'seller'){
            return redirect()->route('login')->with('message', 'anda tidak memiliki akses ke halaman ini,');
        }
        return $next($request);
    }
}
