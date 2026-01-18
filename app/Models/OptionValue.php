<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    use HasFactory;
    
    // Relasi: Nilai Opsi ini milik satu Opsi (Level Kepedasan)
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}