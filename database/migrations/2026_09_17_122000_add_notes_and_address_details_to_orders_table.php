<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // [KODE BARU] Menambahkan kolom detail alamat (kota, kode pos) dan catatan khusus pembeli
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_postal_code', 10)->nullable()->after('shipping_city');
            $table->text('order_notes')->nullable()->after('shipping_postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_city', 'shipping_postal_code', 'order_notes']);
        });
    }
};
