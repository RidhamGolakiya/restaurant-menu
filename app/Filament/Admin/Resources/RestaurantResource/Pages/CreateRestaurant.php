<?php

namespace App\Filament\Admin\Resources\RestaurantResource\Pages;

use App\Filament\Admin\Resources\RestaurantResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;

    protected static bool $canCreateAnother = false;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Restaurant created successfully';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract user data from the form data since we removed dehydrated(false) from the fields
        $user_name = $data['user_name'] ?? null;
        $user_email = $data['user_email'] ?? null;
        $user_password = $data['user_password'] ?? null;
        
        // Store these values in static properties for use in handleRecordCreation
        static::$user_name = $user_name;
        static::$user_email = $user_email;
        static::$user_password = $user_password;
        
        // Remove user-related fields from restaurant data to prevent them from being saved to the restaurant model
        unset($data['user_name'], $data['user_email'], $data['user_password'], $data['user_password_confirmation']);
        
        // Validate that required user fields are present
        if (empty($user_name) || empty($user_email)) {
            throw new \Exception('User name and email are required');
        }
        
        return $data;
    }

    protected function handleRecordCreation(array $data): \App\Models\Restaurant
    {
        // Create user first using the stored values
        $user = User::create([
            'name' => static::$user_name,
            'email' => static::$user_email,
            'password' => Hash::make(static::$user_password),
            'restaurant_id' => null, // Will be set after restaurant creation
        ]);
        
        // Create restaurant
        $restaurantData = $data;
        $restaurantData['user_id'] = $user->id;
        $restaurant = \App\Models\Restaurant::create($restaurantData);
        
        // Update user with restaurant_id
        $user->update(['restaurant_id' => $restaurant->id]);
        
        // Assign role and create settings
        // Check if the 'restaurant' role exists, create it if it doesn't
        $role = Role::firstOrCreate(['name' => 'restaurant', 'guard_name' => 'web']);
        $user->assignRole($role);
        
        $user->settings()->create([
            'user_id' => $user->id,
            'key' => 'currency_id',
            'value' => 1,
        ]);
        
        return $restaurant;
    }
    
    protected static ?string $user_name = null;
    protected static ?string $user_email = null;
    protected static ?string $user_password = null;
}