<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('event_type');        // 'tour_view' | 'tour_search' | 'chat_mention' | 'booking' | 'wishlist'
            $table->unsignedBigInteger('tour_id')->nullable();
            $table->string('destination')->nullable();
            $table->string('category')->nullable();
            $table->unsignedSmallInteger('duration_days')->nullable();
            $table->unsignedBigInteger('price_point')->nullable();
            $table->unsignedSmallInteger('view_seconds')->default(0); // thời gian xem (giây)
            $table->json('meta')->nullable();    // dữ liệu thêm tùy event
            $table->timestamps();

            $table->index(['user_id', 'event_type']);
            $table->index(['user_id', 'created_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
};