<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 1. Identifikasi Transaksi
            // Kode unik invoice pesanan (contoh: INV-171800-123)
            $table->string('order_id')->unique();

            // 2. Data Pelanggan
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // 3. Nominal & Status Transaksi
            // Total tagihan dalam Rupiah
            $table->decimal('gross_amount', 12, 2);

            // Status: 'pending' (menunggu bayar), 'paid' (lunas), 'expired' (kadaluarsa), 'failed' (gagal)
            $table->string('status')->default('pending');

            // 4. Data Integrasi Midtrans
            // Token popup Midtrans Snap
            $table->string('snap_token')->nullable();

            // Metode bayar yang dipakai (contoh: bca_va, qris, gopay, credit_card)
            $table->string('payment_type')->nullable();

            // Simpan respon lengkap dari Midtrans (format JSON) untuk riwayat/audit
            $table->json('payment_response')->nullable();

            // 5. Waktu dibuat (created_at) & waktu diupdate (updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
