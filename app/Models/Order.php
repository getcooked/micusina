<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'checkout_group_id',
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

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (! $order->user_id && Auth::check()) {
                $order->user_id = Auth::id();
            }

            if (! $order->checkout_group_id && ! app()->runningInConsole() && app()->bound('request')) {
                $request = request();
                $attribute = 'micusina_order_checkout_group_id';

                if (! $request->attributes->has($attribute)) {
                    $request->attributes->set($attribute, (string) Str::uuid());
                }

                $order->checkout_group_id = $request->attributes->get($attribute);
            }
        });
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
