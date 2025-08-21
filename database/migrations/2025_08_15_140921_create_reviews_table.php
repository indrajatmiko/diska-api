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
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->string('username');
        $table->unsignedTinyInteger('rating'); // Cukup 1-5, jadi tinyInteger lebih efisien
        $table->text('description')->nullable(); // Deskripsi bisa jadi opsional
        $table->dateTime('review_date'); // Kolom khusus untuk tanggal rating yang bisa diubah
        $table->timestamps(); // created_at & updated_at untuk internal tracking
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
