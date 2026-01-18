<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function orderList()
    {
        $orders = Order::all(); // Mengambil semua data pesanan
        return view('admin.orders.index', compact('orders'));
    }
    public function index()
    {
        // Logika untuk Dashboard (misalnya, menampilkan statistik) akan diletakkan di sini
        // Untuk saat ini, kita hanya me-return view:
        return view('admin.dashboard');
    }

    public function confirmOrder($id)
    {
        // Cari pesanan berdasarkan ID
        $order = \App\Models\Order::findOrFail($id);

        // Update status menjadi Confirmed
        $order->update([
            'status' => 'Confirmed'
        ]);

        return redirect()->back()->with('success', 'Pesanan berhasil dikonfirmasi!');
    }

    public function verifyPayment(Request $request, $id)
    {
        // 1. Cari pesanan
        $order = \App\Models\Order::findOrFail($id);

        // 2. Update status order menjadi Confirmed
        // Kita juga bisa update status pembayaran jika ada kolomnya
        $order->update([
            'status' => 'Confirmed' 
        ]);

        // 3. Opsional: Update status di tabel payment jika relasinya ada
        if ($order->payment) {
            $order->payment->update([
                'validation_status' => 'Confirmed'
            ]);
        }

        return redirect()->back()->with('success', 'Pesanan #' . $id . ' berhasil dikonfirmasi!');
    }

    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'rejection_notes' => 'required|string|max:255'
        ]);

        $order = \App\Models\Order::findOrFail($id);

        // Update status dan simpan alasan
        $order->payment->update([
            'validation_status' => 'Rejected',
            'rejection_notes' => $request->rejection_notes,
            'proof_image_path' => null
        ]);

        $order->update(['status' => 'Waiting Payment']);

        return redirect()->back()->with('error', 'Pembayaran ditolak dengan alasan: ' . $request->rejection_notes);
    }
}