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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('author_name');
            $table->string('author_url')->nullable();
            $table->string('profile_photo_url')->nullable();
            $table->integer('rating'); // 1-5
            $table->text('text')->nullable();
            $table->string('relative_time_description')->nullable();
            $table->timestamp('time')->nullable(); // Actual timestamp from Google
            $table->boolean('is_visible')->default(true);
            $table->string('google_review_id')->unique()->nullable(); // To prevent duplicates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
