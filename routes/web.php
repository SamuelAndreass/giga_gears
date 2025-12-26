<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\SocialAuthController;
USE App\Http\Controllers\HomeController;
use App\Http\Controllers\CustController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\SeminarController;


Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    Route::get('login/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('login.google.callback');
    Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

// redirect based on role after login
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isSeller) {
        return redirect()->route('seller.index');
    }
    return redirect()->route('dashboard');
});


// customer (basic user)
Route::middleware(['auth', 'ensure.active'])->group(function(){
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/profile/setting', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/settings/profile', [ProfileController::class, 'updateProfile'])->name('profile.settings.profile.update');
    Route::post('/profile/settings/password', [ProfileController::class, 'updatePassword'])->name('profile.settings.password.update');
    Route::post('/profile/settings/address', [ProfileController::class, 'updateAddress'])->name('profile.settings.address.update');

    Route::view('/checkout', 'customer.checkout')->name('checkout');
    Route::get('/my-order', [CustController::class, 'myOrders'])->name('orders.index');
    Route::get('/orders/{order}', [CustController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [CustController::class, 'cancel'])->name('orders.cancel');
    Route::view('/seller/dashboard', 'seller.dashboard'); 
    Route::view('/profile', 'customer.profile');
    Route::get('/products', [CustController::class, 'showProducts'])->name('products.index');
    Route::get('/products/{id}/detail', [CustController::class, 'showProd'])->name('product.detail');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'showCheckoutPage'])->name('checkout.index');
    Route::post('/process-checkout', [CheckoutController::class, 'checkout'])->name('checkout.process');
    Route::post('/checkout/shipping-fee', [CheckoutController::class, 'getShippingFee'])->name('checkout.shipping_fee');
    Route::post('/buy-now/redirect', [CartController::class, 'buyNowRedirect'])->name('buy_now.redirect');
    Route::post('/buy-now/immediate', [CartController::class, 'buyNowImmediate'])->name('buy_now.immediate');
});
Route::middleware(['auth', 'ensure.active','redirect.seller'])->group(function(){
    Route::view('/become-seller', 'seller.seller-setup-store')->name('become.seller.page');
    Route::post('/become-seller/store', [SellerController::class, 'store'])->name('become.seller');
    Route::post('/become-seller/reactivate', [SellerController::class,'reactivate'])->name('become.seller.reactivate');
});

// seller
Route::middleware(['auth','ensure.seller', 'ensure.active', 'store.active'])->prefix('seller')->group(function(){
    Route::get('/orders/{id}/ship-data', [SellerController::class, 'shipData'])->name('seller.orders.shipData');
    Route::post('/orders/{id}/ship', [SellerController::class, 'ship'])->name('seller.orders.ship');
    Route::get('/dashboard', [SellerController::class, 'viewMainDashboard'])->name('seller.index');
    Route::get('/product', [SellerController::class, 'viewProd'])->name('seller.products');
    Route::get('/analytics', [SellerController::class, 'viewAnalyticsReview'])->name('seller.analytics');
    Route::get('/inbox', [SellerController::class, 'feedback'])->name('seller.inbox');
    Route::delete('/remove/product/{id}', [SellerController::class, 'deleteProd']);
    Route::post('/add/product', [SellerController::class,'addProduct'])->name('seller.add.product');
    Route::get('/add/product', [SellerController::class,'viewAddProductForm'])->name('seller.view.add.product');
    Route::post('/update/product/{id}', [SellerController::class, '']);
    Route::post('/update/status', [SellerController::class, 'updateStatus']);
    Route::get('/recent-order', [SellerController::class,'viewRecentOrder'])->name('seller.orders');
    Route::get('/recent-order/search', [SellerController::class,'viewRecentOrder'])->name('search.seller.orders');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/owner', [SettingsController::class, 'updateOwner'])->name('settings.owner.update');
    Route::put('settings/store', [SettingsController::class, 'updateStore'])->name('settings.store.update');
    Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::get('/seller/orders-over-time/data', [SellerController::class, 'data'])->name('seller.orders-over-time.data');
    Route::delete('settings/store', [SettingsController::class, 'destroyStore'])->name('settings.store.destroy');


});



// admin
Route::middleware(['auth', 'ensure.admin'])->prefix('admin')->group(function(){
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/transactions', [AdminController::class, 'dataTransaction'])->name('admin.transactions.index');
    Route::get('/shipping', [AdminController::class, 'shippingIndex'])->name('admin.shipping.index');
    Route::post('/add/shipping/json', [AdminController::class, 'addShipping'])->name('admin.shipping.store');
    Route::put('/shipping/{shipping}', [AdminController::class, 'editShipping'])->name('admin.shipping.update');
    Route::get('/products', [AdminController::class, 'productIndex'])->name('admin.products.index');
    Route::patch('/products/{product}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.products.toggle-status');
    Route::get('/products/{product}/json', [AdminController::class, 'productJson'])->name('admin.products.json');
    Route::patch('/customers/{user}/edit', [AdminController::class, 'updateUser'])->name('admin.customers.update');
    Route::get('/customers/{user}/json', [AdminController::class, 'userJson'])->name('admin.customers.json');
    Route::get('/customers', [AdminController::class, 'viewUser'])->name('admin.customers.index');
    Route::patch('/customers/{user}/status', [AdminController::class, 'updateStatus'])->name('admin.customers.update-status');
    Route::get('/sellers', [AdminController::class, 'sellerIndex'])->name('admin.sellers.index');
    Route::get('/sellers/{seller}/json', [AdminController::class, 'sellerJson'])->name('admin.sellers.json');
    Route::get('/admin/sellers/{seller}/products', [AdminController::class, 'sellerProductsJson']);
    Route::patch('/sellers/{seller}/status', [AdminController::class, 'updateStatusSeller'])->name('admin.sellers.update-status');
    Route::post('/shipping/{id}/toggle', [AdminController::class, 'toggle'])->name('admin.shipping.toggle');
    Route::get('/workshops', [AdminController::class, 'workshopIndex'])->name('admin.workshops.index');
    Route::post('/workshops', [AdminController::class, 'workshopStore'])->name('admin.workshops.store');
    Route::delete('/workshops/{workshop}', [AdminController::class, 'workshopDestroy'])->name('admin.workshops.destroy');
    Route::get('/workshops/{workshop}/json', [AdminController::class, 'workshopJson'])->name('admin.workshops.json');
    Route::put('/workshops/{workshop}', [AdminController::class, 'workshopUpdate'])->name('admin.workshops.update');
});


// Community routes (public access for viewing)
Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/community/{id}', [CommunityController::class, 'show'])->name('community.show');

// Seminar routes (public access for viewing)
Route::get('/seminar', [SeminarController::class, 'index'])->name('seminar.index');
Route::get('/seminar/{id}', [SeminarController::class, 'show'])->name('seminar.show');

// Seminar registration (authenticated only)
Route::middleware(['auth', 'ensure.active'])->group(function() {
    Route::post('/seminar/{id}/register', [SeminarController::class, 'register'])->name('seminar.register');
    Route::post('/seminar/{id}/unregister', [SeminarController::class, 'unregister'])->name('seminar.unregister');
    Route::get('/my-seminars', [SeminarController::class, 'myRegistrations'])->name('seminar.my-registrations');
});

// Community API routes
Route::prefix('api/community')->group(function () {
    Route::get('/posts', [CommunityController::class, 'getPosts']);
    Route::get('/posts/{id}', [CommunityController::class, 'getPost']);
    Route::get('/posts/{id}/comments', [CommunityController::class, 'getComments']);
    Route::get('/tags', [CommunityController::class, 'getTags']);
    
    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/posts', [CommunityController::class, 'storePost']);
        Route::delete('/posts/{id}', [CommunityController::class, 'deletePost']);
        Route::post('/posts/{id}/like', [CommunityController::class, 'toggleLike']);
        Route::post('/comments', [CommunityController::class, 'storeComment']);
        Route::delete('/comments/{id}', [CommunityController::class, 'deleteComment']);
    });
});

require __DIR__.'/auth.php';

    //Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])
    //->middleware('auth', 'can:ship,order');