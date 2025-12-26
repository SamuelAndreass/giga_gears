// routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

// Homepage - Tampilkan semua produk


// Cart Routes
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/update/{id}', [CartController::class, 'updateCart'])->name('cart.update');
    Route::delete('/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clearCart'])->name('cart.clear');
});