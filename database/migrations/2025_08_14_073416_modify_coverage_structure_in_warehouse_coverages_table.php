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
        Schema::table('warehouse_coverages', function (Blueprint $table) {
            // LANGKAH 1: Hapus unique constraint yang lama terlebih dahulu.
            // Nama constraint biasanya adalah: nama_tabel_nama_kolom_unique
            // Note: This name might also be too long if province_id was longer. 
            // If this line fails, you might need to find the shorter auto-generated name 
            // from your database schema or provide its custom name here too.
            $table->dropUnique('warehouse_coverages_warehouse_id_province_id_unique');
            
            // LANGKAH 2: Hapus kolom lama setelah constraint-nya dilepas.
            $table->dropColumn('province_id');

            // LANGKAH 3: Tambahkan kolom-kolom baru.
            $table->string('coverage_type')->after('warehouse_id');
            $table->string('coverage_id')->after('coverage_type');
            $table->string('coverage_name')->after('coverage_id');

            // -- FIX HERE --
            // Buat unique constraint baru dengan NAMA KUSTOM YANG LEBIH PENDEK.
            $table->unique(
                ['warehouse_id', 'coverage_type', 'coverage_id'],
                'warehouse_coverage_unique' // This is the custom, shorter name
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_coverages', function (Blueprint $table) {
            // -- FIX HERE --
            // Hapus constraint dengan NAMA KUSTOM YANG SAMA.
            $table->dropUnique('warehouse_coverage_unique');

            // Buat ulang kolom lama
            $table->string('province_id')->after('warehouse_id');

            // Hapus kolom baru
            $table->dropColumn(['coverage_type', 'coverage_id', 'coverage_name']);
            
            // Buat ulang constraint lama
            $table->unique(['warehouse_id', 'province_id']);
        });
    }
};