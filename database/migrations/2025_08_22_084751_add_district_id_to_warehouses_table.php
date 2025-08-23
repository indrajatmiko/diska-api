<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_district_id_to_warehouses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'city_name'
            $table->string('district_id')->nullable()->after('city_name');
            $table->string('district_name')->nullable()->after('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn(['district_id', 'district_name']);
        });
    }
};