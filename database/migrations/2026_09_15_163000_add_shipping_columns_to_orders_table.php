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
        Schema::table('orders', function (Blueprint $table) {
            // Kolom baru untuk menyimpan informasi pengiriman / kurir
            $table->string('shipping_courier')->nullable()->after('customer_phone');
            $table->string('shipping_service')->nullable()->after('shipping_courier');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_service');
            $table->text('shipping_address')->nullable()->after('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus kolom pengiriman jika di-rollback
            $table->dropColumn(['shipping_courier', 'shipping_service', 'shipping_cost', 'shipping_address']);
        });
    }
};
