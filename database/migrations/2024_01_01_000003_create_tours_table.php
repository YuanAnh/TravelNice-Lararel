<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('tour_categories')->nullOnDelete();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->longText('description')->nullable();
            $table->integer('duration_days');
            $table->decimal('price_adult', 14, 2);
            $table->decimal('price_child', 12, 2)->nullable();
            $table->integer('max_slots');
            $table->string('thumbnail', 255)->nullable();
            $table->text('cancel_policy')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
