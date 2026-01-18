<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    // Relasi 1:N (Satu produk hanya punya satu kategori)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi N:M (Satu produk bisa punya banyak opsi kustomisasi)
    // 'product_option' adalah nama tabel pivot kita
    public function options()
    {
        return $this->belongsToMany(Option::class, 'product_option');
    }
}