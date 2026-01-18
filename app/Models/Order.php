<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = []; // Mengizinkan mass assignment untuk kemudahan

    // 1. Pesanan punya banyak item (Order Items)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // 2. Pesanan punya satu detail pengiriman (Order Delivery)
    public function delivery()
    {
        return $this->hasOne(OrderDelivery::class);
    }
    
    // 3. Pesanan punya satu detail pembayaran (Order Payment)
    public function payment()
    {
        return $this->hasOne(OrderPayment::class);
    }

    // 4. Pesanan dibuat oleh satu user/customer
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id'); // Anda mungkin perlu membuat Model User jika belum ada
    }

    // 5. Pesanan mungkin punya satu review
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Pastikan 'user_id' adalah nama kolom di tabel orders
    }
}
