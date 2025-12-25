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
        Schema::table('restaurant_timing_slots', function (Blueprint $table) {
            $table->foreignId('restaurant_day_id')->nullable()->references('id')->on('restaurant_days')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_timing_slots', function (Blueprint $table) {
            $table->dropForeign(['restaurant_day_id']);
            $table->dropColumn('restaurant_day_id');
        });
    }
};
