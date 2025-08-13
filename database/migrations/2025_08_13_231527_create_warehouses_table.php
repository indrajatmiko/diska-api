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
    Schema::create('warehouses', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama gudang, misal: "Gudang Utama Jakarta"
        $table->string('city_id'); // ID Kota/Kabupaten dari RajaOngkir
        $table->string('city_name'); // Nama Kota/Kabupaten untuk display
        $table->boolean('is_default')->default(false); // Tandai satu gudang sebagai default
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
