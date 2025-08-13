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
    Schema::create('warehouse_coverages', function (Blueprint $table) {
        $table->id();
        // Relasi ke gudang
        $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
        // ID Provinsi dari RajaOngkir yang dicakup oleh gudang ini
        $table->string('province_id'); 
        
        // Pastikan kombinasi gudang dan provinsi unik
        $table->unique(['warehouse_id', 'province_id']);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_coverages');
    }
};
