<?php

namespace App\Http\Controllers;

use App\Models\Order; // Model Order untuk membaca & menyimpan ke tabel 'orders' di MySQL
use Illuminate\Http\Request;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction; // Digunakan untuk mengecek status langsung ke Midtrans API

class PaymentController extends Controller
{
    /**
     * Inisialisasi konfigurasi Midtrans
     */
    protected function initMidtrans()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Menampilkan halaman checkout toko
     */
    public function index()
    {
        // Data produk simulasi toko kopi
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

        $adminFee = 1000;
        $clientKey = config('midtrans.client_key');
        $isProduction = config('midtrans.is_production');

        return view('checkout', compact('products', 'adminFee', 'clientKey', 'isProduction'));
    }

    /**
     * LANGKAH 1 (DATABASE):
     * Simpan data order ke MySQL dengan status 'pending',
     * lalu minta Snap Token ke Midtrans dan simpan token tersebut ke database.
     */
    public function createSnapToken(Request $request)
    {
        // Validasi input data dari form di browser
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'items'          => 'required|array|min:1',
            'gross_amount'   => 'required|numeric|min:1000',
        ]);

        $this->initMidtrans();

        // Buat Order ID unik (contoh: INV-1725261899-432)
        $orderId = 'INV-' . time() . '-' . rand(100, 999);
        $grossAmount = (int) round($request->gross_amount);

        // =========================================================================
        // [TAMBAHAN DATABASE]: Simpan order baru ke tabel 'orders' di MySQL
        // Status awal pesanan adalah 'pending' (menunggu dibayar oleh pembeli)
        // =========================================================================
        $order = Order::create([
            'order_id'       => $orderId,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'gross_amount'   => $grossAmount,
            'status'         => 'pending',
        ]);

        // Siapkan detail item untuk Midtrans
        $itemDetails = [];
        foreach ($request->items as $item) {
            $itemDetails[] = [
                'id'       => $item['id'],
                'price'    => (int) round($item['price']),
                'quantity' => (int) $item['quantity'],
                'name'     => substr($item['name'], 0, 50),
            ];
        }

        // Parameter transaksi untuk dikirim ke API Midtrans
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
            ],
            'enabled_payments' => [
                'credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va',
                'other_va', 'gopay', 'shopeepay', 'qris'
            ]
        ];

        try {
            // Minta Snap Token dari server Midtrans
            $snapToken = Snap::getSnapToken($params);

            // =========================================================================
            // [TAMBAHAN DATABASE]: Simpan snap_token ke database agar tersimpan di order
            // =========================================================================
            $order->update([
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
                'amount'     => $grossAmount
            ]);
        } catch (\Exception $e) {
            // Jika gagal mendapatkan Snap Token, tandai status order jadi 'failed'
            $order->update(['status' => 'failed']);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat Snap Token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * LANGKAH 2 (DATABASE & WEBHOOK):
     * Menerima notifikasi resmi dari Midtrans ketika pembayaran sudah lunas / gagal / kadaluarsa,
     * lalu otomatis meng-update status transaksi di tabel 'orders' MySQL.
     */
    public function callback(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = new Notification();

            $transaction = $notif->transaction_status; // settlement, capture, pending, expire, cancel
            $type        = $notif->payment_type;       // bca_va, qris, gopay, credit_card, dll
            $orderId     = $notif->order_id;
            $fraud       = $notif->fraud_status;

            // Cari data order di tabel 'orders' MySQL berdasarkan order_id
            $order = Order::where('order_id', $orderId)->first();

            if (!$order) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Order tidak ditemukan di database'
                ], 404);
            }

            // =========================================================================
            // [LOGIKA STATUS PEMBAYARAN MIDTRANS]:
            // - 'settlement' / 'capture': Uang sudah masuk -> ubah status jadi 'paid' (Lunas)
            // - 'pending': Menunggu pembeli transfer ke VA / scan QRIS -> status tetap 'pending'
            // - 'expire': Waktu bayar habis -> ubah status jadi 'expired'
            // - 'cancel' / 'deny': Ditolak / Dibatalkan -> ubah status jadi 'failed'
            // =========================================================================
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

            // Simpan jenis metode bayar & respon mentah Midtrans ke database
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

    /**
     * LANGKAH 3 (PEMBELAJARAN):
     * Halaman untuk melihat semua riwayat transaksi yang tersimpan di MySQL.
     * Mengapa ada auto-sync?
     * Karena di localhost (127.0.0.1), webhook Midtrans dari internet tidak bisa
     * menembus masuk ke laptop kita (tanpa Ngrok). Jadi saat halaman ini dibuka,
     * Laravel akan otomatis bertanya ke Midtrans API untuk memperbarui status pesanan pending!
     */
    public function history()
    {
        $this->initMidtrans();

        $orders = Order::latest()->get();

        // Cek dan sinkronkan pesanan yang masih 'pending' langsung ke server Midtrans
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
                    // Abaikan jika order belum pernah diinput di simulator
                }
            }
        }

        return view('orders_history', compact('orders'));
    }

    /**
     * Endpoint untuk sinkronisasi manual 1 pesanan via tombol atau AJAX
     */
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

            // =========================================================================
            // [FITUR BARU: NOTIFIKASI EMAIL]
            // Jika status baru saja berubah menjadi paid, simulasikan pengiriman email ke log
            // =========================================================================
            if ($order->status === 'paid') {
                \Log::info("📧 [SIMULASI EMAIL NOTIFIKASI] Sukses dikirim ke: {$order->customer_email} untuk Order: {$order->order_id} (Total: Rp {$order->gross_amount})");
            }

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

    /**
     * =========================================================================
     * [FITUR BARU: PREVIEW STRUK EMAIL]
     * LANGKAH 4 (PEMBELAJARAN NOTIFIKASI EMAIL):
     * Menampilkan desain email konfirmasi pembayaran lunas di browser.
     * Siswa bisa melihat bagaimana email transaksi e-commerce terlihat nyata!
     * =========================================================================
     */
    public function previewEmail($orderId)
    {
        $order = Order::where('order_id', $orderId)->firstOrFail();
        return view('emails.payment_success', compact('order'));
    }
}

