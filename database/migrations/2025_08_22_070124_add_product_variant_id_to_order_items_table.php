<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_product_variant_id_to_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Tambahkan kolom baru setelah product_id
            $table->foreignId('product_variant_id')
                  ->after('product_id')
                  ->nullable() // Jadikan nullable untuk data order lama (opsional tapi aman)
                  ->constrained('product_variants') // Terhubung ke tabel 'product_variants'
                  ->onDelete('set null'); // Jika varian dihapus, jangan hapus order item
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};