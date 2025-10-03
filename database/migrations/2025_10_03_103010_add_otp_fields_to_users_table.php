<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_otp_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // Kolom untuk kode OTP, bisa di-hash untuk keamanan tambahan jika perlu
            $table->string('otp_code')->nullable()->after('password');
            // Kolom untuk waktu kedaluwarsa OTP
            $table->dateTime('otp_expires_at')->nullable()->after('otp_code');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at']);
        });
    }
};