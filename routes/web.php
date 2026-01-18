<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController; 
use App\Http\Controllers\OrderController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Halaman Utama & Lokasi
Route::get('/', [MenuController::class, 'index'])->name('menu.list');
Route::get('/menu/{product}', [MenuController::class, 'detail'])->name('product.detail');
Route::post('/set-location', [AppController::class, 'setLocation'])->name('set.location');

// Route Konfirmasi Pembayaran (Bisa diakses tanpa login via token)
Route::get('/order/{token}/confirm', [OrderController::class, 'confirmation'])->name('order.confirmation');


/*
|--------------------------------------------------------------------------
| 2. CUSTOMER ROUTES (Role: customer)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:customer'])->group(function () {
    // Dashboard Customer
    Route::get('/customer/dashboard', [MenuController::class, 'index'])->name('customer.dashboard'); 
    
    // Keranjang & Checkout
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add'); 
    Route::post('/cart/remove/{uuid}', [CartController::class, 'remove'])->name('cart.remove'); 
    
    Route::get('/checkout', [CartController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout.process');
    Route::post('/order/{id}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('order.cancel');
    
    // Riwayat & Status Pesanan
    Route::get('/orders', [OrderController::class, 'history'])->name('order.history'); 
    Route::get('/order/{order}', [OrderController::class, 'status'])->name('order.status'); 
    
    // Upload Bukti Transfer (Gunakan Route Model Binding atau Token)
    Route::post('/order/{token}/upload-proof', [OrderController::class, 'uploadPaymentProof'])->name('payment.upload');

    Route::post('/order/review/{id}', [OrderController::class, 'submitReview'])->name('order.review');
});


/*
|--------------------------------------------------------------------------
| 3. DRIVER ROUTES (Role: driver)
|--------------------------------------------------------------------------
*/

Route::prefix('driver')->middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/', [DriverController::class, 'dashboard'])->name('driver.dashboard');
    Route::get('/deliveries', [DriverController::class, 'pendingDeliveries'])->name('driver.deliveries');
    Route::put('/deliveries/{order}/update-status', [DriverController::class, 'updateDeliveryStatus'])->name('driver.update_status');
    Route::get('/history', [DriverController::class, 'deliveryHistory'])->name('driver.history');
});


/*
|--------------------------------------------------------------------------
| 4. ADMIN ROUTES (Role: admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Kelola Menu (CRUD)
    Route::resource('menus', MenuController::class); 

    // Kelola User
    Route::resource('users', UserController::class);

    // Kelola Pesanan & Verifikasi Pembayaran
    Route::get('/orders', [DashboardController::class, 'orderList'])->name('admin.orders'); // Diperbaiki untuk navigasi
    Route::get('/orders/{order}', [DashboardController::class, 'show'])->name('admin.orders.show'); // Detail Pesanan
    Route::post('/orders/{order}/confirm-payment', [DashboardController::class, 'confirmPayment'])->name('admin.orders.confirm_payment');
    Route::post('/orders/{order}/verify', [DashboardController::class, 'verifyPayment'])->name('admin.orders.verify');
    Route::post('/orders/{order}/reject-payment', [DashboardController::class, 'rejectPayment'])->name('admin.orders.reject_payment');
    Route::post('/orders/{order}/update-status', [DashboardController::class, 'updateStatus'])->name('admin.orders.update_status');
    Route::post('/orders/{order}/assign-driver', [DashboardController::class, 'assignDriver'])->name('admin.orders.assign');

    // Cetak Struk
    Route::get('/orders/{id}/print', [OrderManagementController::class, 'printInvoice'])->name('admin.orders.print');

    // Statistik
    Route::get('/reports/statistics', [DashboardController::class, 'showStatistics'])->name('admin.reports.stats');
});


/*
|--------------------------------------------------------------------------
| 5. GLOBAL AUTH & PROFILE
|--------------------------------------------------------------------------
*/

// Pengalihan Dashboard Otomatis berdasarkan Role
Route::get('/dashboard', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'driver') return redirect()->route('driver.dashboard');
        return redirect()->route('customer.dashboard'); 
    }
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile (Semua Role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';