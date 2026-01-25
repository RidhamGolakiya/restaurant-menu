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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->integer('gallery_max_size_mb')->default(2)->after('type');
            $table->string('zomato_link')->nullable()->after('gallery_max_size_mb');
            $table->string('swiggy_link')->nullable()->after('zomato_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['gallery_max_size_mb', 'zomato_link', 'swiggy_link']);
        });
    }
};
