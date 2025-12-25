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
            $table->string('theme_mode')->default('default')->after('slug'); // 'default', 'black_and_white', 'custom'
            $table->string('primary_color')->nullable()->after('theme_mode'); // For custom theme
            $table->string('secondary_color')->nullable()->after('primary_color'); // For custom theme
            $table->string('accent_color')->nullable()->after('secondary_color'); // For custom theme
            $table->json('theme_config')->nullable()->after('accent_color'); // For storing additional theme settings
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'theme_mode',
                'primary_color', 
                'secondary_color',
                'accent_color',
                'theme_config'
            ]);
        });
    }
};