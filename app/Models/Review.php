<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['order_id', 'user_id', 'rating', 'comment'];
    
    use HasFactory;
}
