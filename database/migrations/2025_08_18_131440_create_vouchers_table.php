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
    Schema::create('vouchers', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // Kode voucher yang unik, misal: "HEMAT20K"
        $table->text('description')->nullable();
        $table->enum('type', [
            'product_fixed', 
            'product_percentage', 
            'shipping_fixed']);
        $table->integer('value'); // Nilai diskon (Rp atau %)
        $table->integer('min_purchase')->default(0); // Syarat minimal pembelian
        $table->dateTime('start_date'); // Tanggal mulai berlaku
        $table->dateTime('end_date');   // Tanggal kedaluwarsa
        $table->boolean('is_active')->default(true); // Untuk menonaktifkan voucher secara manual
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
