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
        Schema::create('qr_code_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_code_id')->constrained()->cascadeOnDelete();
            
            // Device/Location Info
            $table->string('ip_address')->nullable();
            $table->string('device_type')->nullable(); // Mobile, Tablet, Desktop
            $table->string('os')->nullable(); // iOS, Android, Windows
            $table->string('browser')->nullable(); // Chrome, Safari
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_code_analytics');
    }
};
