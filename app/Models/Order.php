<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    use HasFactory;
 protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'title',
        'price',
        'quantity',
        'image',
        'delivery_status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'rider_id',
        'confirmed_by',
        'confirmed_at',
    ];

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }
}
