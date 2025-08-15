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
        Schema::create('cities', function (Blueprint $table) {
            // ID dari RajaOngkir, bukan auto-increment
            $table->unsignedBigInteger('id')->primary();
            
            // Foreign key yang terhubung ke tabel provinces
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
            
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
