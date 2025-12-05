<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_name',
        'buyer_phone',
        'seller_id',
        'order_number',
        'items',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
