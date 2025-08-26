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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            // 'key' adalah penanda unik yang akan digunakan API
            $table->string('key')->unique();
            $table->boolean('show_in_menu')->default(false);
            $table->string('icon')->nullable();
            $table->string('title');
            $table->longText('content'); // longText untuk menampung HTML
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
