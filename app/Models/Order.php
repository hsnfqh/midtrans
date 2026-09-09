<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * Kolom-kolom yang boleh diisi secara massal (Mass Assignment).
     * Ini penting agar Laravel mengizinkan Order::create([...])
     */
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

    /**
     * Mengubah tipe data kolom tertentu secara otomatis (Casting).
     * Contoh: payment_response otomatis diubah dari JSON menjadi Array PHP.
     */
    protected $casts = [
        'payment_response' => 'array',
        'gross_amount'     => 'decimal:2',
    ];
}
