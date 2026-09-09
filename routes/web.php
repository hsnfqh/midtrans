<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// 1. Tampilan utama checkout
Route::get('/', [PaymentController::class, 'index'])->name('payment.index');

// 2. Endpoint AJAX pembuatan Snap Token & pencatatan pesanan ke database
Route::post('/payment/snap-token', [PaymentController::class, 'createSnapToken'])->name('payment.snapToken');

// 3. Webhook callback dari Midtrans untuk update status otomatis
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// 4. Halaman Riwayat Transaksi (Melihat data dari database MySQL)
Route::get('/orders', [PaymentController::class, 'history'])->name('payment.history');

// 5. Cek & Sinkronkan Status Pesanan ke Midtrans API
Route::get('/orders/{orderId}/check', [PaymentController::class, 'checkStatus'])->name('payment.checkStatus');

// =========================================================================
// [FITUR BARU: NOTIFIKASI STRUK EMAIL]
// 6. Preview Email Struk Konfirmasi Pembayaran
// =========================================================================
Route::get('/orders/{orderId}/email', [PaymentController::class, 'previewEmail'])->name('payment.emailPreview');

