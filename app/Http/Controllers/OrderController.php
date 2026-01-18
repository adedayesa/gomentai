<?php

namespace App\Http\Controllers;

use App\Models\Order; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * API UNTUK IOT (ESP32)
     * Mengecek apakah ada pesanan baru dengan status 'pending'.
     */
    public function cekIot()
    {
        // Mencari pesanan terbaru yang statusnya 'pending'
        $pesanan = Order::where('status', 'pending')
                        ->latest()
                        ->first();

        if ($pesanan) {
            return response()->json([
                'ada_pesanan'    => true,
                'nama_pelanggan' => $pesanan->recipient_name
            ]);
        }

        return response()->json([
            'ada_pesanan'    => false,
            'nama_pelanggan' => ''
        ]);
    }

    /**
     * API UNTUK IOT (ESP32)
     * Mengubah status pesanan menjadi 'processing' setelah tombol di ESP32 ditekan.
     */
    public function konfirmasiIot(Request $request)
    {
        // Cari pesanan pending terbaru untuk dikonfirmasi
        $pesanan = Order::where('status', 'pending')->latest()->first();

        if ($pesanan) {
            $pesanan->update(['status' => 'processing']);
            return response()->json(['status' => 'success', 'message' => 'Pesanan sedang diproses']);
        }

        return response()->json(['status' => 'error', 'message' => 'Tidak ada pesanan'], 404);
    }

    /**
     * FUNGSI WEB: Memproses checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'recipient_name'  => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'address_text'    => 'required|string',
            'payment_method'  => 'required|string',
            'notes'           => 'nullable|string|max:500', 
            'delivery_fee'    => 'required|numeric',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        try {
            DB::beginTransaction();

            $order = new Order();
            $order->user_id        = auth()->id();
            $order->tracking_token = Str::random(20); 
            $order->recipient_name = $request->recipient_name;
            $order->phone          = $request->recipient_phone; 
            $order->address        = $request->address_text;
            $order->notes          = $request->notes; 
            $order->payment_method = $request->payment_method;
            $order->total_price    = array_sum(array_column($cart, 'subtotal')) + $request->delivery_fee;
            $order->status         = 'pending'; // Status awal yang akan dicek ESP32
            $order->save();

            foreach ($cart as $id => $item) {
                $order->items()->create([
                    'product_id' => $id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            DB::commit();
            session()->forget('cart');

            if ($request->payment_method == 'Transfer Bank') {
                return redirect()->route('order.confirmation', $order->tracking_token);
            }

            return redirect()->route('order.status', $order->id)->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI WEB: Halaman konfirmasi Transfer Bank.
     */
    public function confirmation($token)
    {
        $order = Order::where('tracking_token', $token)
            ->with(['items.product', 'delivery', 'payment'])
            ->firstOrFail(); 

        $accountDetails = [
            'bank_name' => 'Bank BRI',
            'account_number' => '123-456-7890',
            'account_name' => 'PT Go Mentai',
        ];

        return view('order_confirmation', compact('order', 'accountDetails'));
    }

    /**
     * FUNGSI WEB: Status pesanan.
     */
    public function status(Order $order)
    {
        if (auth()->id() !== $order->user_id) {
             abort(403);
        }
        
        $order->load(['items.product', 'payment', 'delivery']);
        return view('customer.order_status', compact('order'));
    }

    /**
     * FUNGSI WEB: Batalkan pesanan.
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Akses Dilarang');
        }

        $order->update([
            'status' => 'Cancelled',
            'cancel_reason' => $request->cancel_reason 
        ]);

        return redirect('/')->with('success', 'Pesanan #' . $id . ' berhasil dibatalkan.');
    }

    /**
     * FUNGSI WEB: Upload bukti transfer.
     */
    public function uploadPaymentProof(Request $request, $token)
    {
        $order = Order::where('tracking_token', $token)->firstOrFail();

        $request->validate([
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('public/proofs'); 

            if ($order->payment) {
                $order->payment->update([
                    'proof_image_path' => Storage::url($path), 
                    'validation_status' => 'Proof Uploaded', 
                ]);
            }

            $order->update(['status' => 'Waiting Confirmation']);

            return redirect()->route('order.confirmation', $token)->with('success', 'Bukti transfer diunggah!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * FUNGSI WEB: Riwayat pesanan.
     */
    public function history() 
    {
        $orders = Order::where('user_id', auth()->id())
                       ->with(['items', 'delivery']) 
                       ->latest() 
                       ->get();

        return view('customer.order_history', compact('orders'));
    }

    /**
     * FUNGSI WEB: Review.
     */
    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);

        \App\Models\Review::create([
            'order_id' => $order->id,
            'user_id'  => auth()->id(),
            'rating'   => $request->rating,
            'comment'  => $request->comment,
        ]);

        $order->update(['status' => 'Delivered']);

        return redirect('/')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}