<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Menyimpan koordinat dan nama lokasi pengguna ke Session.
     */
    public function setLocation(Request $request)
    {
        // 1. Validasi
        // Kita tambahkan 'address' ke dalam validasi karena sekarang JS mengirimkannya
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'address' => 'nullable|string', 
        ]);

        // 2. Simpan ke Session
        // Sekarang kita menyimpan 'address' yang dikirim dari OpenStreetMap (JS)
        session()->put('user_location', [
            'lat' => $request->lat,
            'lon' => $request->lon,
            'address' => $request->address ?? 'Lokasi Terpilih',
            'timestamp' => now(),
        ]);

        // 3. Beri respons sukses ke JavaScript
        return response()->json([
            'message' => 'Lokasi berhasil disimpan.',
            'address' => $request->address,
        ]);
    }
    
    // ... Tambahkan fungsi lain (seperti index menu) di sini ...
}