<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    // Relasi 1:N (Satu opsi punya banyak nilai opsi)
    public function values()
    {
        return $this->hasMany(OptionValue::class);
    }
}
