<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    public function index()
    {
        $orders = Order::with(['payment', 'delivery', 'customer'])->orderBy('id', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'delivery', 'payment', 'customer']);
        $statuses = ['Payment Confirmed', 'Preparing', 'Ready for Pickup', 'On Delivery', 'Delivered', 'Cancelled'];
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function confirmPayment(Order $order)
    {
        $order->payment->update(['validation_status' => 'Confirmed']);
        $order->update(['status' => 'Payment Confirmed']);

        // Memicu buka tab print otomatis
        return back()->with('success', 'Pembayaran divalidasi!')
                     ->with('open_print', route('admin.orders.print', $order->id));
    }

    public function rejectPayment(Order $order)
    {
        $order->payment->update(['validation_status' => 'Rejected']);
        return back()->with('warning', 'Pembayaran ditolak.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Status diperbarui.');
    }

    public function printInvoice($id)
    {
        $order = Order::with(['items.product', 'payment', 'customer', 'delivery'])->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }
}