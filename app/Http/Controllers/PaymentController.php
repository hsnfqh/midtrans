<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    protected function initMidtrans()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    public function index()
    {
        $products = [
            [
                'id'       => 'PROD-01',
                'name'     => 'Kala Flores Bajawa Single Origin 250g',
                'price'    => 85000,
                'quantity' => 1,
                'desc'     => 'Arabika Flores Bajawa, Notes: Caramel, Nutty'
            ],
            [
                'id'       => 'PROD-02',
                'name'     => 'Cold Brew Concentrate 500ml',
                'price'    => 65000,
                'quantity' => 1,
                'desc'     => 'House blend extract, rasio 1:2 siap seduh'
            ]
        ];

        // Daftar opsi pengiriman kurir yang tersedia di toko
        $shippingOptions = [
            [
                'id'          => 'grab_instant',
                'courier'     => 'GrabFood / GrabExpress',
                'service'     => 'Instant Delivery (1-2 Jam)',
                'cost'        => 15000,
                'badge'       => 'Paling Cepat',
                'icon'        => '⚡'
            ],
            [
                'id'          => 'gosend_sameday',
                'courier'     => 'GoSend / Gojek',
                'service'     => 'SameDay Delivery (4-6 Jam)',
                'cost'        => 10000,
                'badge'       => 'Hemat',
                'icon'        => '🛵'
            ],
            [
                'id'          => 'jne_reg',
                'courier'     => 'JNE Express',
                'service'     => 'Reguler Luar Kota (1-2 Hari)',
                'cost'        => 12000,
                'badge'       => 'Ekspedisi',
                'icon'        => '📦'
            ],
            [
                'id'          => 'pickup',
                'courier'     => 'Ambil Sendiri di Outlet',
                'service'     => 'Pick Up langsung di Kala Coffee Roastery',
                'cost'        => 0,
                'badge'       => 'Gratis',
                'icon'        => '☕'
            ]
        ];

        $adminFee = 1000;
        $clientKey = config('midtrans.client_key');
        $isProduction = config('midtrans.is_production');

        // Mengirimkan data produk dan opsi ongkos kirim ke view checkout
        return view('checkout', compact('products', 'shippingOptions', 'adminFee', 'clientKey', 'isProduction'));
    }

    protected function getCoupons()
    {
        return [
            'DISKON50' => [
                'type'         => 'percent',
                'value'        => 50,
                'max_discount' => 75000,
                'min_spend'    => 50000,
                'desc'         => 'Diskon 50% (Maks. Rp 75.000)'
            ],
            'KOPIGRATIS' => [
                'type'         => 'fixed',
                'value'        => 25000,
                'max_discount' => 25000,
                'min_spend'    => 30000,
                'desc'         => 'Potongan Langsung Rp 25.000'
            ],
            'HEMAT10K' => [
                'type'         => 'fixed',
                'value'        => 10000,
                'max_discount' => 10000,
                'min_spend'    => 20000,
                'desc'         => 'Potongan Langsung Rp 10.000'
            ],
            'BELAJARCODING' => [
                'type'         => 'fixed',
                'value'        => 50000,
                'max_discount' => 50000,
                'min_spend'    => 100000,
                'desc'         => 'Spesial Belajar Coding Potongan Rp 50.000'
            ]
        ];
    }

    public function checkCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
            'subtotal'    => 'required|numeric|min:0'
        ]);

        $code = strtoupper(trim($request->coupon_code));
        $subtotal = (int) $request->subtotal;
        $coupons = $this->getCoupons();

        if (!isset($coupons[$code])) {
            return response()->json([
                'status'  => 'error',
                'message' => "Kode kupon '{$code}' tidak ditemukan atau sudah kadaluarsa."
            ], 422);
        }

        $coupon = $coupons[$code];

        if ($subtotal < $coupon['min_spend']) {
            return response()->json([
                'status'  => 'error',
                'message' => "Minimal belanja untuk kupon ini adalah Rp " . number_format($coupon['min_spend'], 0, ',', '.')
            ], 422);
        }

        $discountAmount = 0;
        if ($coupon['type'] === 'percent') {
            $discountAmount = (int) round(($coupon['value'] / 100) * $subtotal);
            if ($discountAmount > $coupon['max_discount']) {
                $discountAmount = $coupon['max_discount'];
            }
        } else {
            $discountAmount = min($coupon['value'], $subtotal);
        }

        return response()->json([
            'status'          => 'success',
            'coupon_code'     => $code,
            'discount_type'   => $coupon['type'],
            'discount_value'  => $coupon['value'],
            'discount_amount' => $discountAmount,
            'description'     => $coupon['desc'],
            'message'         => "Kupon {$code} berhasil dipasang!"
        ]);
    }

    public function createSnapToken(Request $request)
    {
        // Validasi input data pembeli dan pilihan pengiriman
        $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_email'   => 'required|email',
            'customer_phone'   => 'required|string|max:20',
            'shipping_courier' => 'required|string|max:100',
            'shipping_service' => 'required|string|max:100',
            'shipping_cost'    => 'required|numeric|min:0',
            'shipping_address' => 'required|string|max:500',
            'items'            => 'required|array|min:1',
            'coupon_code'      => 'nullable|string|max:50',
        ]);

        $this->initMidtrans();

        $orderId = 'INV-' . time() . '-' . rand(100, 999);
        
        $subtotal = 0;
        $itemDetails = [];
        foreach ($request->items as $item) {
            $itemPrice = (int) round($item['price']);
            $itemQty   = (int) $item['quantity'];
            $subtotal += ($itemPrice * $itemQty);

            $itemDetails[] = [
                'id'       => $item['id'],
                'price'    => $itemPrice,
                'quantity' => $itemQty,
                'name'     => substr($item['name'], 0, 50),
            ];
        }

        // Menambahkan biaya ongkos kirim ke rincian item Midtrans
        $shippingCost = (int) round($request->shipping_cost);
        if ($shippingCost > 0) {
            $itemDetails[] = [
                'id'       => 'ONGKIR-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->shipping_courier), 0, 8)),
                'price'    => $shippingCost,
                'quantity' => 1,
                'name'     => 'Ongkir: ' . substr($request->shipping_courier . ' (' . $request->shipping_service . ')', 0, 45),
            ];
        }

        // Menghitung potongan diskon kupon jika digunakan
        $discountAmount = 0;
        $couponCode = strtoupper(trim($request->coupon_code ?? ''));
        $coupons = $this->getCoupons();

        if ($couponCode && isset($coupons[$couponCode])) {
            $coupon = $coupons[$couponCode];
            if ($subtotal >= $coupon['min_spend']) {
                if ($coupon['type'] === 'percent') {
                    $discountAmount = (int) round(($coupon['value'] / 100) * $subtotal);
                    if ($discountAmount > $coupon['max_discount']) {
                        $discountAmount = $coupon['max_discount'];
                    }
                } else {
                    $discountAmount = min($coupon['value'], $subtotal);
                }

                if ($discountAmount > 0) {
                    $itemDetails[] = [
                        'id'       => 'DISC-' . substr($couponCode, 0, 10),
                        'price'    => -$discountAmount,
                        'quantity' => 1,
                        'name'     => 'Diskon Kupon ' . $couponCode,
                    ];
                }
            }
        }

        // Total akhir yang harus dibayarkan
        $grossAmount = max(1000, (int) round($subtotal + $shippingCost - $discountAmount));

        // Menyimpan data pesanan lengkap beserta info kurir & ongkir ke database
        $order = Order::create([
            'order_id'         => $orderId,
            'customer_name'    => $request->customer_name,
            'customer_email'   => $request->customer_email,
            'customer_phone'   => $request->customer_phone,
            'shipping_courier' => $request->shipping_courier,
            'shipping_service' => $request->shipping_service,
            'shipping_cost'    => $shippingCost,
            'shipping_address' => $request->shipping_address,
            'gross_amount'     => $grossAmount,
            'status'           => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email'      => $request->customer_email,
                'phone'      => $request->customer_phone,
                // Mengirimkan alamat pengiriman ke Midtrans
                'shipping_address' => [
                    'first_name' => $request->customer_name,
                    'address'    => $request->shipping_address,
                ]
            ],
            'enabled_payments' => [
                'credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va',
                'other_va', 'gopay', 'shopeepay', 'qris'
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $order->update([
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'status'           => 'success',
                'snap_token'       => $snapToken,
                'order_id'         => $orderId,
                'amount'           => $grossAmount,
                'shipping_cost'    => $shippingCost,
                'shipping_courier' => $request->shipping_courier,
                'discount_amount'  => $discountAmount,
                'coupon_code'      => $couponCode
            ]);
        } catch (\Exception $e) {
            $order->update(['status' => 'failed']);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat Snap Token: ' . $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = new Notification();

            $transaction = $notif->transaction_status;
            $type        = $notif->payment_type;
            $orderId     = $notif->order_id;
            $fraud       = $notif->fraud_status;

            $order = Order::where('order_id', $orderId)->first();

            if (!$order) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Order tidak ditemukan di database'
                ], 404);
            }

            if ($transaction == 'capture') {
                if ($fraud == 'challenge') {
                    $order->status = 'challenge';
                } else if ($fraud == 'accept') {
                    $order->status = 'paid';
                }
            } else if ($transaction == 'settlement') {
                $order->status = 'paid';
            } else if ($transaction == 'pending') {
                $order->status = 'pending';
            } else if ($transaction == 'deny') {
                $order->status = 'failed';
            } else if ($transaction == 'expire') {
                $order->status = 'expired';
            } else if ($transaction == 'cancel') {
                $order->status = 'failed';
            }

            $order->payment_type = $type;
            $order->payment_response = $notif->getResponse();
            $order->save();

            return response()->json([
                'status'  => 'success',
                'message' => "Order $orderId status berhasil diperbarui menjadi {$order->status}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error callback: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        $this->initMidtrans();

        $orders = Order::latest()->get();

        foreach ($orders as $order) {
            if ($order->status === 'pending') {
                try {
                    $res = Transaction::status($order->order_id);
                    if ($res && isset($res->transaction_status)) {
                        $trx = $res->transaction_status;
                        $fraud = $res->fraud_status ?? '';

                        if ($trx === 'settlement' || ($trx === 'capture' && $fraud === 'accept')) {
                            $order->status = 'paid';
                        } elseif ($trx === 'expire') {
                            $order->status = 'expired';
                        } elseif (in_array($trx, ['cancel', 'deny'])) {
                            $order->status = 'failed';
                        }

                        if (isset($res->payment_type)) {
                            $order->payment_type = $res->payment_type;
                        }
                        $order->save();
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return view('orders_history', compact('orders'));
    }

    public function checkStatus($orderId)
    {
        $this->initMidtrans();

        $order = Order::where('order_id', $orderId)->firstOrFail();

        try {
            $res = Transaction::status($orderId);
            $trx = $res->transaction_status ?? null;
            $fraud = $res->fraud_status ?? '';

            if ($trx === 'settlement' || ($trx === 'capture' && $fraud === 'accept')) {
                $order->status = 'paid';
            } elseif ($trx === 'expire') {
                $order->status = 'expired';
            } elseif (in_array($trx, ['cancel', 'deny'])) {
                $order->status = 'failed';
            }

            if (isset($res->payment_type)) {
                $order->payment_type = $res->payment_type;
            }
            $order->save();

            if (request()->wantsJson()) {
                return response()->json([
                    'status'         => 'success',
                    'order_status'   => $order->status,
                    'payment_type'   => $order->payment_type
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return redirect()->back();
        }
    }

    public function previewEmail($orderId)
    {
        $order = Order::where('order_id', $orderId)->firstOrFail();
        return view('emails.payment_success', compact('order'));
    }
}
