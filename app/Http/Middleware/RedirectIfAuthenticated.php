<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{

    public function handle(Request $request, Closure $next, string|null $guard = null)
    {
        if (Auth::guard($guard)->check()) {

            $user = Auth::guard($guard)->user();

            // Redirect berdasarkan role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'seller':
                    return redirect()->route('seller.index');
                case 'customer':
                default:
                    return redirect()->route('home'); 
            }
        }

        return $next($request);
    }
}
