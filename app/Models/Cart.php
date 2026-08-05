<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  // ✅ Correct
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
     protected $fillable = [
        'food_id',
        'title',
        'details',
        'price',
        'image',
        'quantity',
        'userid',
    ];
}
