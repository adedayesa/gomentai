<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OptionValue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderDelivery;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function add(Request $request, Product $product)
    {
        $inputOptions = $request->input('options', []); 
        $quantity = $request->input('quantity', 1);

        if ($quantity < 1) { return back()->with('error', 'Kuantitas harus minimal 1.'); }
        
        $customizations = [];
        $modifierTotal = 0;

        foreach ($inputOptions as $optionValueId) {
            $value = OptionValue::find($optionValueId);
            if ($value) {
                $customizations[] = [
                    'option_name' => $value->option->name, 
                    'value_name' => $value->name,
                    'price_modifier' => $value->price_modifier,
                ];
                $modifierTotal += $value->price_modifier;
            }
        }
        
        $itemTotal = ($product->base_price + $modifierTotal) * $quantity;

        $cartItem = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'base_price' => $product->base_price,
            'quantity' => $quantity,
            'customizations' => $customizations,
            'subtotal' => $itemTotal,
            'uuid' => Str::uuid()->toString(), 
        ];

        $cart = session()->get('cart', []);
        $cart[$cartItem['uuid']] = $cartItem;
        session()->put('cart', $cart);

        return redirect('/')->with('success', $product->name . ' berhasil ditambahkan ke keranjang!');
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $grandTotal = 0;
        foreach ($cart as $item) {
            $grandTotal += $item['subtotal'];
        }
        return view('cart.index', compact('cart', 'grandTotal'));
    }

    public function checkoutForm()
    {
        if (session()->missing('cart') || empty(session('cart'))) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong, tidak bisa checkout.');
        }

        $cart = session()->get('cart');
        $userLocation = session('user_location', null); 

        // --- LOGIKA PERHITUNGAN JARAK DINAMIS ---
        $distance = 0;
        $deliveryFee = 0;

        if ($userLocation) {
            // 1. Koordinat Toko
            $tokoLat = -6.896905; 
            $tokoLon = 107.62852;

            // 2. Koordinat User dari Session
            $userLat = $userLocation['lat'];
            $userLon = $userLocation['lon'];

            // 3. Rumus Haversine (KM)
            $earthRadius = 6371;
            $dLat = deg2rad($userLat - $tokoLat);
            $dLon = deg2rad($userLon - $tokoLon);
            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($tokoLat)) * cos(deg2rad($userLat)) * sin($dLon/2) * sin($dLon/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            
            $distance = round($earthRadius * $c, 2); // Jarak dalam KM

            // 4. Hitung Ongkir (Contoh: Rp 2.000 per KM)
            $deliveryFee = ceil($distance) * 2000; 
            if ($deliveryFee < 5000) $deliveryFee = 5000; // Minimal ongkir 5rb
        }

        return view('checkout', compact('cart', 'userLocation', 'distance', 'deliveryFee'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'address_text' => 'required|string',
            'payment_method' => 'required|string',
            'delivery_fee' => 'required|integer|min:0',
            'distance' => 'required|numeric|min:0',
            'eta' => 'required|integer|min:0',
            'lat' => 'required',
            'lon' => 'required',
        ]);

        $cart = session()->get('cart');
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong!');
        }
        
        $totalBarang = array_sum(array_column($cart, 'subtotal'));
        $deliveryFee = $request->delivery_fee;
        $grandTotal = $totalBarang + $deliveryFee;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id() ?? null,
                'total_amount' => $grandTotal,
                'status' => 'Payment Pending',
                'tracking_token' => Str::random(10),
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['base_price'] + array_sum(array_column($item['customizations'], 'price_modifier')),
                    'customization_details' => json_encode($item['customizations']),
                ]);
            }

            OrderDelivery::create([
                'order_id' => $order->id,
                'courier_id' => null,
                'address_text' => $request->address_text,
                'lat' => $request->lat,
                'lon' => $request->lon,
                'distance_km' => $request->distance,
                'eta_minutes' => $request->eta,
                'delivery_fee' => $deliveryFee,
            ]);

            OrderPayment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'validation_status' => $request->payment_method === 'Transfer Bank' ? 'Waiting Proof' : 'COD',
            ]);

            session()->forget('cart');
            DB::commit();

            if ($request->payment_method === 'Transfer Bank') {
                return redirect()->route('order.confirmation', ['token' => $order->tracking_token]);
            }

            $order->update(['status' => 'Order Placed (COD)']);
            return redirect()->route('order.status', $order)->with('success', 'Pesanan COD Anda berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())->withInput();
        }
    }

    public function remove($uuid)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$uuid])) {
            unset($cart[$uuid]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus.');
        }
        return redirect()->route('cart.index')->with('error', 'Item tidak ditemukan.');
    }
}