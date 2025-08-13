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
// di dalam function up()
Schema::create('videos', function (Blueprint $table) {
    $table->id();
    $table->string('video_url');
    $table->string('thumbnail_url');
    $table->string('user_name');
    $table->string('user_avatar_url');
    $table->text('caption');
    $table->string('likes')->default('0');
    $table->string('comments')->default('0');
    $table->timestamps();
});    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
