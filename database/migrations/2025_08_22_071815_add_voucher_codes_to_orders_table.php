<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_voucher_codes_to_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'courier' agar logis
            $table->string('product_voucher_code')->nullable()->after('courier');
            $table->string('shipping_voucher_code')->nullable()->after('product_voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['product_voucher_code', 'shipping_voucher_code']);
        });
    }
};