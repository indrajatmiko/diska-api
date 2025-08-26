<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_activities_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            // User yang melakukan aksi. Nullable untuk aktivitas anonim (misal: lihat produk).
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->string('event_type'); // Contoh: 'user_registered', 'product_viewed', 'order_created'
            $table->string('subject_type')->nullable(); // Class dari model terkait, e.g., App\Models\Product
            $table->unsignedBigInteger('subject_id')->nullable(); // ID dari model terkait, e.g., ID produk yg dilihat
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at');
            
            // Index untuk mempercepat query
            $table->index(['subject_type', 'subject_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('user_activities'); }
};