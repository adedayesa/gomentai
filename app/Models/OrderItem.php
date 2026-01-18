<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Nama tabel jika tidak mengikuti standar plural Laravel (opsional).
     * Jika nama tabelmu adalah 'order_items', Laravel sudah otomatis mengenalinya.
     */
    protected $table = 'order_items';

    /**
     * Mengizinkan semua kolom diisi secara massal.
     */
    protected $guarded = [];

    /**
     * RELASI: Item ini milik satu pesanan (Order).
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * RELASI: Item ini merujuk ke satu produk (Product).
     * Ini penting agar struk bisa mengambil nama produk dengan: $item->product->name
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}