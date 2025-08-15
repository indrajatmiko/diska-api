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
        $table->dropUnique('warehouse_coverages_warehouse_id_province_id_unique');
        
        // LANGKAH 2: Hapus kolom lama setelah constraint-nya dilepas.
        $table->dropColumn('province_id');

        // LANGKAH 3: Tambahkan kolom-kolom baru.
        $table->string('coverage_type')->after('warehouse_id');
        $table->string('coverage_id')->after('coverage_type');
        $table->string('coverage_name')->after('coverage_id');

        // (Opsional tapi direkomendasikan) Buat unique constraint baru
        $table->unique(['warehouse_id', 'coverage_type', 'coverage_id']);
    });
}

public function down(): void
{
    Schema::table('warehouse_coverages', function (Blueprint $table) {
        // Hapus constraint baru dulu
        $table->dropUnique(['warehouse_id', 'coverage_type', 'coverage_id']);

        // Buat ulang kolom lama
        $table->string('province_id')->after('warehouse_id');

        // Hapus kolom baru
        $table->dropColumn(['coverage_type', 'coverage_id', 'coverage_name']);
        
        // Buat ulang constraint lama
        $table->unique(['warehouse_id', 'province_id']);
    });
}
};
