<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi.
     *
     * File ini mendefinisikan skema final dari tabel warehouse_coverages secara langsung,
     * menggabungkan migrasi create dan modify menjadi satu.
     */
    public function up(): void
    {
        Schema::create('warehouse_coverages', function (Blueprint $table) {
            // Kolom dasar
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            
            // Kolom final yang fleksibel (menggantikan province_id)
            $table->string('coverage_type'); // Tipe: 'province' atau 'city'
            $table->string('coverage_id');   // ID dari provinsi atau kota
            $table->string('coverage_name'); // Nama provinsi atau kota
            
            // Timestamps
            $table->timestamps();

            // --- FIX IS HERE ---
            // Unique constraint final dengan NAMA KUSTOM YANG LEBIH PENDEK
            // untuk menghindari error nama terlalu panjang.
            $table->unique(
                ['warehouse_id', 'coverage_type', 'coverage_id'],
                'warehouse_coverage_unique_idx' // <-- Nama kustom yang lebih pendek
            );
        });
    }

    /**
     * Membatalkan migrasi.
     *
     * Cukup hapus seluruh tabel jika migrasi ini di-rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_coverages');
    }
};