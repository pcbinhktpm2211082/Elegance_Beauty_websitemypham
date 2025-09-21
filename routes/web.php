<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Product;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\PageController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\User\VoucherController as UserVoucherController;
use Illuminate\Http\Request;


// ==================== USER ====================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Brand Story
Route::get('/about/brand-story', [PageController::class, 'brandStory'])->name('brand.story');
Route::redirect('/about', '/about/brand-story', 301);
Route::get('/brand-story', [PageController::class, 'brandStory']);

Route::get('/products', function () {
    $products = Product::all();
    return view('user.products', compact('products'));
})->name('products');

Route::get('/products', [UserProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [UserProductController::class, 'show'])->name('user.products.show');

// ==================== DEBUG ROUTES ====================
Route::get('/debug/session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'has_session' => session()->has('_token'),
        'session_data' => session()->all()
    ]);
})->name('debug.session');

// ==================== CONTACT ROUTES ====================
Route::middleware('web')->group(function () {
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'store'])->middleware(['auth','throttle:10,1'])->name('contact.store');
});

// ==================== LOGOUT ROUTE ====================
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ==================== CART ROUTES ====================
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');
    Route::put('/update/{id}', [CartController::class, 'updateQuantity'])->name('update');
    Route::post('/update/{id}', [CartController::class, 'updateQuantity'])->name('update.post'); // Backup route
    Route::delete('/remove/{id}', [CartController::class, 'removeFromCart'])->name('remove');
    Route::post('/remove/{id}', [CartController::class, 'removeFromCart'])->name('remove.post'); // Backup route
    Route::delete('/clear', [CartController::class, 'clearCart'])->name('clear');
    Route::post('/clear', [CartController::class, 'clearCart'])->name('clear.post'); // Backup route
});

// ==================== API ROUTES ====================
Route::prefix('api')->group(function () {
    Route::get('/products/{product}/check-variants', function ($product) {
        $product = \App\Models\Product::with('variants')->findOrFail($product);
        return response()->json([
            'has_variants' => $product->variants->count() > 0,
            'variants_count' => $product->variants->count()
        ]);
    });
    // Voucher preview (apply without creating order)
    Route::post('/voucher/preview', [UserVoucherController::class, 'preview'])->name('api.voucher.preview');

});

// ==================== AUTHENTICATED USER ====================
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [UserProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::get('/profile/password', [UserProfileController::class, 'changePassword'])->name('profile.password');
    Route::put('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/addresses', [UserProfileController::class, 'addresses'])->name('profile.addresses');
    
    // Orders routes
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    // Vouchers (USER)
    Route::get('/vouchers', [UserVoucherController::class, 'index'])->name('user.vouchers.index');
    Route::post('/orders/{order}/cancel', [UserOrderController::class, 'cancel'])->name('orders.cancel');
    
    // Payment routes
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/online/{order}', [PaymentController::class, 'onlinePayment'])->name('payment.online');
    Route::get('/payment/bank/{order}', [PaymentController::class, 'bankTransfer'])->name('payment.bank');
    Route::get('/payment/success/{order}', [PaymentController::class, 'success'])->name('payment.success');
    Route::post('/payment/verify/{order}', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
    
    // Cart routes (UPDATED)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::put('/cart/update/{productId}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::get('/cart/debug', [CartController::class, 'debugCart'])->name('cart.debug');
    Route::post('/cart/force-clear', [CartController::class, 'forceClearCart'])->name('cart.force-clear');
    Route::post('/cart/clear-user', [CartController::class, 'clearUserCart'])->name('cart.clear-user');
    Route::get('/cart/test-products', [CartController::class, 'testProducts'])->name('cart.test-products');
    Route::post('/cart/update-product-images', [CartController::class, 'updateProductImages'])->name('cart.update-product-images');
    Route::get('/cart/test-image/{imagePath}', [CartController::class, 'testImage'])->name('cart.test-image');
    Route::get('/cart/test-product-image/{productId}', [CartController::class, 'testProductImage'])->name('cart.test-product-image');
    Route::get('/cart/check-database', [CartController::class, 'checkDatabaseProducts'])->name('cart.check-database');
});

// ==================== ADMIN ====================
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Trang dashboard admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý danh mục, sản phẩm, người dùng, đơn hàng
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    Route::resource('vouchers', AdminVoucherController::class)->except(['show']);

    Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Hỗ trợ
    Route::get('supports', [SupportController::class, 'index'])->name('supports.index');
    Route::get('supports/{support}', [SupportController::class, 'show'])->name('supports.show');
    Route::post('supports/{support}/done', [SupportController::class, 'markDone'])->name('supports.done');
    Route::post('supports/{support}/processing', [SupportController::class, 'markProcessing'])->name('supports.processing');
    Route::post('supports/{support}/cancelled', [SupportController::class, 'markCancelled'])->name('supports.cancelled');
});

require __DIR__.'/auth.php';  