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
        // Menambahkan atribut pengiriman ke fillable model
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'shipping_address',
        'gross_amount',
        'status',
        'snap_token',
        'payment_type',
        'payment_response',
    ];

    protected $casts = [
        'payment_response' => 'array',
        'gross_amount'     => 'decimal:2',
        // Format casting untuk ongkos kirim
        'shipping_cost'    => 'decimal:2',
    ];
}
