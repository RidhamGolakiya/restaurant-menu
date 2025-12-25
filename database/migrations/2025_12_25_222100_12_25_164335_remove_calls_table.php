<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('calls');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the calls table with the same structure as before
        Schema::create('calls', function ($table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->string('vapi_call_id');
            $table->dateTime('called_at');
            $table->boolean('status')->default(false);
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->text('recording_url')->nullable();
            $table->json('cost_json')->nullable(); // JSON to store cost details
            $table->decimal('total_cost', 10, 2)->default(0)->after('cost_json');
            $table->text('transcription')->nullable();
            $table->text('call_summary')->nullable();
            $table->timestamps();
        });
    }
};