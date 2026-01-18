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
        Schema::table('menus', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('ingredients');
            $table->enum('dietary_type', ['veg', 'non_veg', 'egg'])->default('veg')->after('price');
            $table->boolean('is_jain')->default(false)->after('dietary_type');
            $table->boolean('is_vegan')->default(false)->after('is_jain');
            $table->boolean('is_best_seller')->default(false)->after('today_special');
            $table->boolean('is_best_food')->default(false)->after('is_best_seller');
            $table->boolean('is_best_drink')->default(false)->after('is_best_food');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 
                'description', 
                'dietary_type', 
                'is_jain', 
                'is_vegan', 
                'is_best_seller', 
                'is_best_food', 
                'is_best_drink'
            ]);
        });
    }
};
