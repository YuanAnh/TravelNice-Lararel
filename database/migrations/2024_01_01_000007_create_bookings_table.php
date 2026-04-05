<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tour_slot_id')->constrained('tour_slots')->cascadeOnDelete();
            $table->integer('num_adults')->default(1);
            $table->integer('num_children')->default(0);
            $table->decimal('total_price', 14, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'paid', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
