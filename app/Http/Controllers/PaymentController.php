<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Menangani unggahan bukti pembayaran dari pelanggan.
     * Mendukung upload awal maupun upload ulang jika status sebelumnya 'Rejected'.
     */
    public function uploadProof(Request $request, $token)
    {
        // 1. Validasi input file
        $request->validate([
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        // 2. Cari data order berdasarkan tracking_token
        $order = Order::where('tracking_token', $token)->firstOrFail();

        // 3. Proses penyimpanan file
        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            
            // Simpan ke folder 'proofs' di disk 'public'
            $path = $file->store('proofs', 'public'); 

            // 4. Update data pembayaran di database
            // validation_status diubah ke 'Pending' agar form upload tertutup 
            // dan admin tahu ada bukti baru yang perlu diperiksa.
            $order->payment->update([
                'validation_status' => 'Pending', 
                'proof_url' => $path,
            ]);
        }

        // 5. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Admin akan segera memverifikasi pesanan Anda!');
    }
}