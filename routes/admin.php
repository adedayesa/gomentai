<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderManagementController;

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Route Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Route Manajemen Pesanan
    Route::get('orders', [OrderManagementController::class, 'index'])->name('admin.orders');
    Route::get('orders/{order}', [OrderManagementController::class, 'show'])->name('admin.orders.show');

    // Route untuk Validasi Pembayaran dan Status
    Route::post('orders/{order}/confirm-payment', [OrderManagementController::class, 'confirmPayment'])->name('admin.orders.confirm_payment');
    Route::post('orders/{order}/update-status', [OrderManagementController::class, 'updateStatus'])->name('admin.orders.update_status');
});