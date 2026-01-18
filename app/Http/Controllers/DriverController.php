<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 

class DriverController extends Controller
{
    public function dashboard()
    {
        // Menarik data dengan relasi User dan Delivery
        $orders = Order::with(['user', 'delivery'])->whereIn('status', ['Ready for Pickup', 'On Delivery'])
                       ->orderBy('updated_at', 'desc')
                       ->get();
        
        $completedOrders = Order::with(['user', 'delivery'])->where('status', 'Delivered')
                                ->orderBy('updated_at', 'desc')
                                ->take(5)
                                ->get();
        
        $newOrdersCount = Order::where('status', 'Ready for Pickup')->count();
        $shippingCount = Order::where('status', 'On Delivery')->count();
        $completedCount = Order::where('status', 'Delivered')->count();

        return view('driver.dashboard', compact(
            'orders', 
            'completedOrders', 
            'newOrdersCount', 
            'shippingCount', 
            'completedCount'
        ));
    }

    public function updateDeliveryStatus(Request $request, Order $order)
    {
        if ($order->status === 'Ready for Pickup') {
            $order->update(['status' => 'On Delivery']);
            return back()->with('success', 'Pesanan sedang Anda antar!');
        } 
        
        if ($order->status === 'On Delivery') {
            $order->update(['status' => 'Delivered']);
            return back()->with('success', 'Pesanan telah selesai diantar!');
        }

        return back()->with('error', 'Gagal memperbarui status.');
    }
}