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
        Schema::create('resellers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_path'); // Hanya menyimpan path relatif ke gambar
            $table->longText('description'); // longText untuk menampung HTML yang panjang
            $table->string('whatsapp_number');
            $table->timestamps(); // Ini akan menangani 'tanggal update' (updated_at) secara otomatis
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resellers');
    }
};
