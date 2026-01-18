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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // For public access
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Table 1", "Entrance", "Instagram Promo"
            $table->string('destination_url')->nullable(); // Null = Default to Menu
            $table->integer('scans_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
