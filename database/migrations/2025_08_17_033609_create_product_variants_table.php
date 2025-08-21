<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_xxxxxx_create_product_variants_table.php
public function up(): void
{
    Schema::create('product_variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        $table->string('name'); // Contoh: "S", "M", "Merah", "Biru"
        $table->integer('price'); // Harga spesifik untuk varian ini
        $table->string('sku')->nullable()->unique(); // SKU, bisa jadi unik atau kosong
        $table->unsignedInteger('stock')->default(0); // Stok spesifik untuk varian ini
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
