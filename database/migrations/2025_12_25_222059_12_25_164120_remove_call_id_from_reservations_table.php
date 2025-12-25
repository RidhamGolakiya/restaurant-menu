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
        Schema::table('reservations', function (Blueprint $table) {
            // Drop the foreign key constraint first if it exists
            if (Schema::hasColumn('reservations', 'call_id')) {
                // Try to drop the foreign key constraint - use a try-catch to handle if constraint doesn't exist
                try {
                    $table->dropForeign(['call_id']);
                } catch (\Exception $e) {
                    // If foreign key doesn't exist, continue
                }
                $table->dropColumn('call_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('call_id')->nullable()->after('table_id');
            $table->foreign('call_id')->references('id')->on('calls')->onDelete('set null');
        });
    }
};