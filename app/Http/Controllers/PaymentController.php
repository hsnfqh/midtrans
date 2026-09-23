<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

    // Katalog produk resmi toko yang digunakan untuk checkout dan pengetahuan AI Barista
    protected function getProducts()
    {
        return [
            [
                'id'       => 'PROD-01',
                'name'     => 'Kala Flores Bajawa Single Origin 250g',
                'price'    => 85000,
                'quantity' => 1,
                'desc'     => 'Arabika Flores Bajawa dengan notes caramel, chocolate, nutty, dan low acidity (sangat aman untuk lambung).'
            ],
            [
                'id'       => 'PROD-02',
                'name'     => 'Cold Brew Concentrate 500ml',
                'price'    => 65000,
                'quantity' => 1,
                'desc'     => 'House blend extract kopi pekat, rasio 1:2 siap seduh, segar, praktis, dan tahan 2 minggu di kulkas.'
            ]
        ];
    }

    public function index()
    {
        $products = $this->getProducts();

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
        $request->validate([
            'customer_name'        => 'required|string|max:100',
            'customer_email'       => 'required|email',
            'customer_phone'       => 'required|string|max:20',
            'shipping_courier'     => 'required|string|max:100',
            'shipping_service'     => 'required|string|max:100',
            'shipping_cost'        => 'required|numeric|min:0',
            'shipping_address'     => 'required|string|max:500',
            'shipping_city'        => 'required|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10',
            'order_notes'          => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'coupon_code'          => 'nullable|string|max:50',
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

        $shippingCost = (int) round($request->shipping_cost);
        if ($shippingCost > 0) {
            $itemDetails[] = [
                'id'       => 'ONGKIR-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->shipping_courier), 0, 8)),
                'price'    => $shippingCost,
                'quantity' => 1,
                'name'     => 'Ongkir: ' . substr($request->shipping_courier . ' (' . $request->shipping_service . ')', 0, 45),
            ];
        }

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

        $grossAmount = max(1000, (int) round($subtotal + $shippingCost - $discountAmount));

        $order = Order::create([
            'order_id'             => $orderId,
            'customer_name'        => $request->customer_name,
            'customer_email'       => $request->customer_email,
            'customer_phone'       => $request->customer_phone,
            'shipping_courier'     => $request->shipping_courier,
            'shipping_service'     => $request->shipping_service,
            'shipping_cost'        => $shippingCost,
            'shipping_address'     => $request->shipping_address,
            'shipping_city'        => $request->shipping_city,
            'shipping_postal_code' => $request->shipping_postal_code,
            'order_notes'          => $request->order_notes,
            'gross_amount'         => $grossAmount,
            'status'               => 'pending',
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
                'shipping_address' => [
                    'first_name'   => $request->customer_name,
                    'address'      => $request->shipping_address,
                    'city'         => $request->shipping_city,
                    'postal_code'  => $request->shipping_postal_code ?? '',
                    'country_code' => 'IDN',
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

    /**
     * [FITUR AI] Endpoint Barista AI Chatbot Rekomendasi Produk
     * Memanfaatkan Google Gemini API untuk memberikan konsultasi pemilihan kopi & kupon
     */
    public function chatRecommend(Request $request)
    {
        // 1. Validasi input pesan dari user
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = trim($request->input('message'));
        $geminiApiKey = env('GEMINI_API_KEY');

        // 2. Siapkan data produk dan promo toko sebagai Knowledge Base AI
        $products = $this->getProducts();
        $coupons = $this->getCoupons();

        $productKnowledge = "";
        foreach ($products as $p) {
            $formattedPrice = number_format($p['price'], 0, ',', '.');
            $productKnowledge .= "- {$p['name']} (Harga: Rp {$formattedPrice}): {$p['desc']}\n";
        }

        $couponKnowledge = "";
        foreach ($coupons as $code => $c) {
            $couponKnowledge .= "- Kode Kupon: {$code} ({$c['desc']})\n";
        }

        // 3. Susun System Prompt / Instruksi Karakter untuk Barista AI
        $systemPrompt = "Kamu adalah 'Kala Barista AI', asisten virtual cerdas, ramah, dan bersahabat dari kedai kopi artisan 'Kala Coffee Roastery'.
Tugasmu adalah merekomendasikan produk kopi dan promo yang paling cocok untuk pelanggan berdasarkan pertanyaan mereka.

Berikut adalah DAFTAR MENU PRODUK RESMI KAMI:
{$productKnowledge}
DAFTAR KODE KUPON PROMO AKTIF:
{$couponKnowledge}

PANDUAN MENJAWAB:
1. Bersikap ramah, sopan, antusias, dan gunakan sapaan santun (seperti 'Kak', 'Halo Kak!').
2. HANYA rekomendasikan produk kopi dan kupon yang ada pada daftar resmi di atas.
3. Berikan alasan kenapa kopi tersebut cocok sesuai pertanyaan pelanggan (misal: rasa, keasaman/acidity, aroma, atau kepraktisan).
4. Gunakan bahasa Indonesia yang santai, jelas, dan rapi (boleh gunakan formatting markdown bold/bullet points sederhana).
5. Jawaban jangan terlalu panjang, buat to-the-point, informatif, dan menarik.";

        // Jika API Key belum diisi di .env, sediakan jawaban fallback cerdas agar simulasi di kelas tetap berjalan lancar
        if (empty($geminiApiKey)) {
            $lower = strtolower($userMessage);
            if (str_contains($lower, 'asam') || str_contains($lower, 'lambung') || str_contains($lower, 'pemula')) {
                $mockReply = "Halo Kak! 😊 Untuk yang tidak terlalu asam dan ramah di lambung, saya sangat menyarankan **Kala Flores Bajawa Single Origin 250g** (Rp 85.000). Karakter rasanya dominan *caramel, chocolate, & nutty* dengan *low acidity*, jadi sangat nyaman dinikmati!";
            } elseif (str_contains($lower, 'praktis') || str_contains($lower, 'cepat') || str_contains($lower, 'cold') || str_contains($lower, 'es')) {
                $mockReply = "Halo Kak! Kalau cari yang praktis dan menyegarkan, **Cold Brew Concentrate 500ml** (Rp 65.000) adalah pilihan terbaik! Tinggal tuang dengan rasio 1:2 (tambahkan susu/air/es batu), siap dinikmati dan tahan hingga 2 minggu di kulkas.";
            } elseif (str_contains($lower, 'promo') || str_contains($lower, 'diskon') || str_contains($lower, 'kupon') || str_contains($lower, 'hemat')) {
                $mockReply = "Kabar gembira Kak! 🎉 Hari ini ada kupon **DISKON50** (Diskon 50% maks. 75rb), **HEMAT10K** (Potongan Rp 10.000), atau **BELAJARCODING** (Potongan Rp 50.000). Silakan pasang kodenya di form kupon saat checkout ya!";
            } else {
                $mockReply = "Halo Kak! Selamat datang di Kala Coffee. Kami memiliki **Kala Flores Bajawa** (notes caramel & cokelat yang ramah lambung) dan **Cold Brew Concentrate 500ml** yang praktis & segar. Kakak lebih suka sensasi rasa kopi hangat yang manis-gurih atau es kopi siap seduh?";
            }

            return response()->json([
                'status' => 'success',
                'reply'  => $mockReply,
                'source' => 'fallback_mode'
            ]);
        }

        // 4. Panggil Google Gemini API
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(15)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiApiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nPertanyaan Pelanggan:\n" . $userMessage]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 600,
                ]
            ]);

            if ($response->successful()) {
                $aiReply = $response->json('candidates.0.content.parts.0.text');
                return response()->json([
                    'status' => 'success',
                    'reply'  => $aiReply ?? 'Maaf Kak, Barista AI sedang sibuk meracik kopi. Silakan coba tanyakan lagi ya!'
                ]);
            } else {
                $errMsg = $response->json('error.message') ?? 'Gagal terhubung ke Gemini API';
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gemini API Error: ' . $errMsg
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Koneksi AI gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}
