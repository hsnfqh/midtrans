# 📘 MODUL AJAR: Integrasi Payment Gateway Midtrans Snap dengan Laravel 11

Modul ini disusun sebagai panduan langkah demi langkah (step-by-step) pembuatan simulasi pembayaran online menggunakan framework **Laravel 11** dan **Midtrans Payment Gateway (Mode Sandbox)**.

---

## 🎯 Tujuan Pembelajaran
Setelah mengikuti modul ini, siswa / mahasiswa diharapkan mampu:
1. Memahami konsep dasar transaksi e-commerce dan payment gateway di Indonesia.
2. Mengonfigurasi kredensial API (*Server Key* dan *Client Key*) Midtrans Sandbox.
3. Menginstal dan mengintegrasikan library resmi `midtrans/midtrans-php` ke dalam Laravel.
4. Membuat arsitektur Controller, Routing, dan View (Blade) untuk proses checkout dan pembuatan **Snap Token**.
5. Menampilkan antarmuka checkout dan popup resmi Midtrans Snap di browser.
6. Melakukan simulasi pembayaran menggunakan simulator Midtrans (Virtual Account / QRIS / Kartu Kredit).

---

## 🛠️ Prasyarat (Prerequisites)
Pastikan perangkat kerja sudah terinstal:
- **PHP** versi 8.2 atau yang lebih baru (disarankan via Laragon / XAMPP).
- **Composer** (Package Manager PHP).
- Akun **Midtrans Sandbox** (daftar gratis di [dashboard.sandbox.midtrans.com](https://dashboard.sandbox.midtrans.com)).

---

## 📑 Langkah 1: Membuat Project Laravel 11 Baru

Buka Terminal / Command Prompt / PowerShell di folder project Anda, lalu jalankan perintah:

```bash
composer create-project laravel/laravel:^11.0 payment
cd payment
```

Pastikan server Laravel dapat berjalan dengan mencoba perintah:
```bash
php artisan serve
```
*(Buka `http://127.0.0.1:8000` di browser untuk memastikan halaman selamat datang Laravel muncul).*

---

## 📦 Langkah 2: Menginstal Library Midtrans PHP SDK

Matikan server sebentar (tekan `Ctrl + C`), lalu instal library resmi Midtrans melalui Composer:

```bash
composer require midtrans/midtrans-php
```

Library ini menyediakan class `\Midtrans\Config`, `\Midtrans\Snap`, dan `\Midtrans\Notification` yang memudahkan pembuatan token dan penerimaan callback transaksi.

---

## 🔑 Langkah 3: Mengambil & Memasang API Key Midtrans

1. Buka browser dan login ke **[dashboard.sandbox.midtrans.com](https://dashboard.sandbox.midtrans.com)**.
2. Di menu sebelah kiri, klik:
   👉 **PENGATURAN** &rarr; **ACCESS KEYS**.
3. Anda akan melihat data:
   - **Merchant ID** (contoh: `M415012523`)
   - **Client Key** (contoh: `Mid-client-xxxxxxxxxxxx`)
   - **Server Key** (contoh: `Mid-server-xxxxxxxxxxxx`)

### Masukkan ke file `.env` Laravel:
Buka file `.env` yang berada di direktori root project Laravel, lalu tambahkan baris berikut di bagian paling bawah:

```env
# Konfigurasi Midtrans Sandbox (Gunakan Key dari Akun Midtrans Anda)
MIDTRANS_MERCHANT_ID=G123456789
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

> **Catatan Penting Guru/Dosen:**
> - `MIDTRANS_IS_PRODUCTION=false` menandakan kita menggunakan mode Sandbox (simulasi tanpa uang nyata).
> - `Server Key` bersifat rahasia dan HANYA boleh diproses di backend (controller).
> - `Client Key` bersifat publik dan digunakan di frontend (Blade) untuk memunculkan popup Midtrans Snap.

---

## ⚙️ Langkah 4: Membuat File Konfigurasi `config/midtrans.php`

Buat file baru bernama `midtrans.php` di dalam folder `config/`:
📁 **`config/midtrans.php`**

Isikan kode berikut:

```php
<?php

return [
    'merchant_id'   => env('MIDTRANS_MERCHANT_ID', ''),
    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'  => true,
    'is_3ds'        => true,
];
```

---

## 🎮 Langkah 5: Membuat Controller (`PaymentController`)

Jalankan perintah Artisan untuk membuat controller baru:

```bash
php artisan make:controller PaymentController
```

Buka file yang baru saja dibuat di:
📁 **`app/Http/Controllers/PaymentController.php`**

Ganti kodenya menjadi:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    /**
     * Helper inisialisasi konfigurasi Midtrans
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
        // Data produk simulasi (bisa diambil dari database di project nyata)
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
     * Endpoint API (AJAX) untuk membuat Snap Token dari Midtrans
     */
    public function createSnapToken(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'items'          => 'required|array|min:1',
            'gross_amount'   => 'required|numeric|min:1000',
        ]);

        $this->initMidtrans();

        $orderId = 'INV-' . time() . '-' . rand(100, 999);
        $grossAmount = (int) round($request->gross_amount);

        // Format rincian produk untuk Midtrans
        $itemDetails = [];
        foreach ($request->items as $item) {
            $itemDetails[] = [
                'id'       => $item['id'],
                'price'    => (int) round($item['price']),
                'quantity' => (int) $item['quantity'],
                'name'     => substr($item['name'], 0, 50),
            ];
        }

        // Parameter transaksi
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
            // Meminta Snap Token ke Server Midtrans
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
                'amount'     => $grossAmount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat Snap Token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook Handler dari Midtrans (dipanggil server-to-server)
     */
    public function callback(Request $request)
    {
        $this->initMidtrans();

        try {
            $notif = new Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraud = $notif->fraud_status;

            // Di sini siswa dapat menulis logika update database (cth: ubah status jadi LUNAS)
            return response()->json([
                'status'  => 'success',
                'message' => "Order $orderId berstatus $transaction"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 🚦 Langkah 6: Mendaftarkan Route (`routes/web.php`)

Buka file:
📁 **`routes/web.php`**

Ganti kodenya menjadi:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// 1. Tampilan utama checkout
Route::get('/', [PaymentController::class, 'index'])->name('payment.index');

// 2. Endpoint AJAX pembuatan Snap Token
Route::post('/payment/snap-token', [PaymentController::class, 'createSnapToken'])->name('payment.snapToken');

// 3. Webhook callback dari Midtrans
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
```

### Pengecualian CSRF untuk Webhook (`bootstrap/app.php`):
Karena webhook Midtrans dikirimkan oleh server luar tanpa token CSRF Laravel, kecualikan route `payment/callback` pada file **`bootstrap/app.php`**:

```php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'payment/callback',
        ]);
    })
```

---

## 🖥️ Langkah 7: Membuat View Blade (`resources/views/checkout.blade.php`)

Buat file baru:
📁 **`resources/views/checkout.blade.php`**

Isikan dengan template checkout toko yang rapi dan elegan:

```html
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Simulasi Pembayaran Midtrans (Laravel 11)</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; padding-bottom: 50px; }
    .container { max-width: 1050px; margin: 0 auto; padding: 0 20px; }
    .header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 18px 0; margin-bottom: 30px; }
    .header-inner { display: flex; justify-content: space-between; align-items: center; }
    .brand { font-size: 18px; font-weight: 700; }
    .grid { display: grid; grid-template-columns: 1fr 400px; gap: 28px; }
    @media(max-width: 800px) { .grid { grid-template-columns: 1fr; } }
    .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; margin-bottom: 20px; }
    .card-title { font-size: 16px; font-weight: 700; margin-bottom: 15px; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; }
    .btn-pay { width: 100%; background: #0f766e; color: #fff; font-size: 15px; font-weight: 700; padding: 14px; border: none; border-radius: 8px; cursor: pointer; }
    .btn-pay:hover { background: #115e59; }
    .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
    .price-row { display: flex; justify-content: space-between; margin-top: 10px; font-size: 14px; }
    .total-row { border-top: 1px dashed #cbd5e1; padding-top: 12px; margin-top: 14px; font-weight: 800; font-size: 16px; color: #0f766e; }
  </style>

  <!-- Script Snap Midtrans Sandbox -->
  <script 
    src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
    data-client-key="{{ $clientKey }}">
  </script>
</head>
<body>

  <header class="header">
    <div class="container header-inner">
      <div class="brand">Kala Coffee Roastery &bull; Checkout</div>
      <span style="font-size: 13px; color: #059669; font-weight: 600;">● Midtrans Sandbox Ready</span>
    </div>
  </header>

  <main class="container">
    <div class="grid">
      <!-- Form Pembeli -->
      <div>
        <div class="card">
          <h3 class="card-title">1. Data Pelanggan</h3>
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" id="custName" class="form-control" value="Ahmad Syarif">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" id="custEmail" class="form-control" value="ahmad@example.com">
          </div>
          <div class="form-group">
            <label>Nomor WhatsApp / HP</label>
            <input type="tel" id="custPhone" class="form-control" value="081234567890">
          </div>
        </div>

        <div class="card">
          <h3 class="card-title">2. Metode Pembayaran Didukung</h3>
          <p style="font-size: 13px; color: #64748b;">
            BCA VA, BNI VA, BRI VA, Mandiri Bill, Permata VA, QRIS, GoPay, dan Kartu Kredit Testing Sandbox.
          </p>
        </div>
      </div>

      <!-- Ringkasan Belanja -->
      <div>
        <div class="card">
          <h3 class="card-title">3. Ringkasan Pesanan</h3>
          <div id="cartList">
            @foreach($products as $p)
              <div class="cart-item">
                <div>
                  <strong>{{ $p['name'] }}</strong><br>
                  <span style="font-size: 12px; color: #64748b;">Rp {{ number_format($p['price'], 0, ',', '.') }}</span>
                </div>
                <span>x {{ $p['quantity'] }}</span>
              </div>
            @endforeach
          </div>

          <div class="price-row" style="margin-top: 15px;">
            <span>Biaya Layanan:</span>
            <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
          </div>
          <div class="price-row total-row">
            <span>Total Pembayaran:</span>
            <span id="grandTotalText">Rp 151.000</span>
          </div>

          <button type="button" class="btn-pay" id="btnPay" style="margin-top: 20px;">
            Bayar Sekarang &rarr;
          </button>
        </div>
      </div>
    </div>
  </main>

  <script>
    const btnPay = document.getElementById('btnPay');

    btnPay.addEventListener('click', async () => {
      btnPay.disabled = true;
      btnPay.textContent = 'Menghubungkan Midtrans...';

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Request Snap Token ke Controller Laravel
        const response = await fetch("{{ route('payment.snapToken') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            customer_name: document.getElementById('custName').value,
            customer_email: document.getElementById('custEmail').value,
            customer_phone: document.getElementById('custPhone').value,
            gross_amount: 151000,
            items: [
              { id: 'PROD-01', name: 'Kala Flores Bajawa 250g', price: 85000, quantity: 1 },
              { id: 'PROD-02', name: 'Cold Brew Concentrate 500ml', price: 65000, quantity: 1 },
              { id: 'FEE-ADMIN', name: 'Biaya Layanan', price: 1000, quantity: 1 }
            ]
          })
        });

        const data = await response.json();

        if (data.status === 'success') {
          // Buka Popup Midtrans Snap Resmi
          window.snap.pay(data.snap_token, {
            onSuccess: function(result) {
              alert('Pembayaran Berhasil! Order ID: ' + result.order_id);
              window.location.reload();
            },
            onPending: function(result) {
              alert('Menunggu Pembayaran! Silakan bayar menggunakan nomor VA yang tertera.');
              console.log(result);
            },
            onError: function(result) {
              alert('Pembayaran Gagal!');
              console.error(result);
            },
            onClose: function() {
              alert('Anda menutup popup pembayaran.');
            }
          });
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Terjadi kesalahan: ' + err.message);
      } finally {
        btnPay.disabled = false;
        btnPay.textContent = 'Bayar Sekarang →';
      }
    });
  </script>
</body>
</html>
```

---

## 🚀 Langkah 8: Menjalankan & Menguji Aplikasi

1. Buka terminal di folder project, jalankan:
   ```bash
   php artisan serve
   ```
2. Buka browser ke alamat:
   👉 **`http://127.0.0.1:8000`**
3. Klik tombol **"Bayar Sekarang"**.
4. Popup Midtrans Snap akan muncul di layar.

---

## 🧪 Langkah 9: Simulasi Pembayaran di Simulator Midtrans

Untuk menguji pembayaran berhasil di mode Sandbox tanpa uang sungguhan:

1. **Virtual Account (BCA / BNI / BRI / Mandiri)**:
   - Pilih salah satu bank pada popup Midtrans Snap.
   - Salin nomor **Virtual Account** yang muncul di layar.
   - Buka link simulator resmi: 👉 **[https://simulator.sandbox.midtrans.com](https://simulator.sandbox.midtrans.com)**
   - Pilih tab bank terkait (misal: BCA Virtual Account).
   - Masukkan nomor VA & klik **Inquire**, lalu klik **Pay**.
   - Di aplikasi kita akan langsung muncul respon sukses!

2. **Kartu Kredit Testing**:
   - Nomor Kartu: `4811 1111 1111 1114`
   - Expired: Sembarang (contoh: `12/28`)
   - CVV: `123`
   - Kode OTP: `112233`
