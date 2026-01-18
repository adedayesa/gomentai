<?php

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/cek-iot', function () {
    $latestOrder = Order::with(['user', 'items.product'])
                        ->whereIn('status', ['Order Placed (COD)', 'Payment Pending', 'Waiting Confirmation'])
                        ->latest()
                        ->first();

    if ($latestOrder) {
        // MENGAMBIL SEMUA MENU:
        // Kita ambil nama produk dan jumlahnya, contoh: "2x Mie Ayam, 1x Es Teh"
        $daftarMenu = $latestOrder->items->map(function($item) {
            return $item->quantity . "x " . $item->product->name; 
        })->implode(', '); // Digabung dengan koma

        return response()->json([
            'ada_pesanan' => true,
            'nama_pelanggan' => $latestOrder->user->name ?? 'Pelanggan',
            'menu' => $daftarMenu // Sekarang berisi SEMUA menu
        ]);
    }

    return response()->json(['ada_pesanan' => false]);
});

Route::post('/konfirmasi-pesanan', function (Request $request) {
    // Ambil pesanan terbaru yang masih berstatus "baru"
    $order = Order::whereIn('status', [
                            'Order Placed (COD)', 
                            'Payment Pending', 
                            'Waiting Confirmation',
                            'pending'
                        ])
                  ->latest()
                  ->first();

    if ($order) {
        // Ubah status menjadi 'Preparing' agar alarm di ESP32 berhenti
        $order->update(['status' => 'Preparing']); 
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false], 404);
});