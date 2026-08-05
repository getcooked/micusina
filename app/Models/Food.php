<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Food extends Model
{
    use HasFactory ;


    protected $fillable = [
        'title',
        'details',
        'price',
        'stock',
        'image',
        'low_stock_notified_at',
        'low_stock_email_sent_at',
        'low_stock_sms_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'low_stock_notified_at' => 'datetime',
            'low_stock_email_sent_at' => 'datetime',
            'low_stock_sms_sent_at' => 'datetime',
        ];
    }
}
