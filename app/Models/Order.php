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
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'shipping_address',
        // [KODE BARU] Kolom baru untuk rincian kota, kode pos, dan catatan pesanan
        'shipping_city',
        'shipping_postal_code',
        'order_notes',
        'gross_amount',
        'status',
        'snap_token',
        'payment_type',
        'payment_response',
    ];

    protected $casts = [
        'payment_response' => 'array',
        'gross_amount'     => 'decimal:2',
        'shipping_cost'    => 'decimal:2',
    ];
}

