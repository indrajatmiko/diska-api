<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_is_admin_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom is_admin setelah kolom password
            // Default-nya adalah false (0), artinya semua user baru adalah user biasa
            $table->boolean('is_admin')->after('password')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};