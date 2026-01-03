<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cart;
use App\Models\CartItem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
        $cartCount = 0;

        if (auth()->check()) {
            $userId = auth()->id();

            $cartCount = \DB::table('cart_items')
                ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
                ->where('carts.user_id', $userId)
                ->where('carts.status', 'active')
                ->selectRaw('COALESCE(SUM(cart_items.qty),0) as total_qty')
                ->value('total_qty') ?: 0;
        }

                $view->with('cartCount', (int) $cartCount);
        });
        Paginator::useBootstrap(); 
    }
}
