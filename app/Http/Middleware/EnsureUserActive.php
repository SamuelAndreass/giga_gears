<?php


namespace App\Http\Middleware;

use Closure;

class EnsureUserActive
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        // If user is banned → logout & redirect
        if ($user && $user->status === 'banned') {
            auth()->logout();

            return redirect()
                ->route('account.banned')
                ->with('error', 'Your account has been banned.');
        }

        return $next($request);
    }
}
