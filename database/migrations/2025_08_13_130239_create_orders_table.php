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
    Schema::create('orders', function (Blueprint $table) {
        $table->id(); // Order ID
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('status')->default('unpaid'); // Status pesanan
        $table->integer('total_amount'); // Total dalam sen/rupiah tanpa desimal
        $table->integer('shipping_cost');
        $table->string('courier');
        $table->string('province');
        $table->string('city');
        $table->string('subdistrict');
        $table->string('postal_code');
        $table->text('address_detail');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
