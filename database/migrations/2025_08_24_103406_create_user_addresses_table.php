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
    Schema::create('user_addresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('label'); // Label alamat, misal: "Rumah", "Kantor Ayah"
        $table->string('recipient_name');
        $table->string('phone_number');
        $table->string('province');
        $table->string('city');
        $table->string('district');
        $table->string('sub_district')->nullable();
        $table->string('postal_code')->nullable();
        $table->text('address_detail');
        $table->boolean('is_primary')->default(false); // Penanda alamat utama
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
