<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_api_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->ipAddress('ip_address');
            $table->string('method'); // GET, POST, etc.
            $table->string('route');  // e.g., /api/products/{product}
            $table->integer('status_code'); // 200, 404, 500
            $table->integer('duration_ms'); // Durasi dalam milidetik
            $table->timestamp('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('api_logs'); }
};