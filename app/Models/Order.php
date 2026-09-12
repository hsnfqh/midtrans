<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'gross_amount',
        'status',
        'snap_token',
        'payment_type',
        'payment_response',
    ];

    protected $casts = [
        'payment_response' => 'array',
        'gross_amount'     => 'decimal:2',
    ];
}
