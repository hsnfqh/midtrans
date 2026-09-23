<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/payment/snap-token', [PaymentController::class, 'createSnapToken'])->name('payment.snapToken');
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/orders', [PaymentController::class, 'history'])->name('payment.history');
Route::get('/orders/{orderId}/check', [PaymentController::class, 'checkStatus'])->name('payment.checkStatus');
Route::post('/coupon/check', [PaymentController::class, 'checkCoupon'])->name('coupon.check');
Route::get('/orders/{orderId}/email', [PaymentController::class, 'previewEmail'])->name('payment.emailPreview');

// [FITUR AI] Endpoint Chatbot Rekomendasi Produk menggunakan Google Gemini API
Route::post('/ai/recommend', [PaymentController::class, 'chatRecommend'])->name('ai.recommend');
