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
        // Add admin theme settings as individual settings in the settings table
        // We'll use keys like 'admin_primary_color', 'admin_secondary_color', etc.
        
        // Check if these settings already exist, if not, create them
        $settings = [
            ['key' => 'admin_primary_color', 'value' => '#3b82f6', 'user_id' => null], // Default blue
            ['key' => 'admin_secondary_color', 'value' => '#64748b', 'user_id' => null], // Default gray
            ['key' => 'admin_accent_color', 'value' => '#f59e0b', 'user_id' => null], // Default amber
            ['key' => 'admin_theme_mode', 'value' => 'default', 'user_id' => null],
        ];
        
        $settingModel = new \App\Models\Setting();
        foreach ($settings as $setting) {
            $existing = $settingModel->where('key', $setting['key'])->whereNull('user_id')->first();
            if (!$existing) {
                $settingModel->create($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Setting::whereIn('key', [
            'admin_primary_color', 
            'admin_secondary_color', 
            'admin_accent_color', 
            'admin_theme_mode'
        ])->whereNull('user_id')->delete();
    }
};