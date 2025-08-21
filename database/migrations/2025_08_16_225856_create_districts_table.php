<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_xxxxxx_create_districts_table.php
public function up(): void
{
    Schema::create('districts', function (Blueprint $table) {
        // ID dari RajaOngkir, bukan auto-increment
        $table->unsignedBigInteger('id')->primary();
        
        // Foreign key yang terhubung ke tabel cities
        $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
        
        $table->string('name');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
