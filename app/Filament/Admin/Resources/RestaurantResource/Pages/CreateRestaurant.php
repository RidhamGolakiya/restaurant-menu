<?php

namespace App\Filament\Admin\Resources\RestaurantResource\Pages;

use App\Filament\Admin\Resources\RestaurantResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

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

    protected function handleRecordCreation(array $data): \App\Models\Restaurant
    {
        $userPassword = $data['user_password'] ?? null;
        
        // Remove user-related fields from restaurant data
        $restaurantData = $data;
        unset($restaurantData['user_name'], $restaurantData['user_email'], $restaurantData['user_password'], $restaurantData['user_password_confirmation']);
        
        // Create user first
        $user = User::create([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'password' => Hash::make($userPassword),
            'restaurant_id' => null, // Will be set after restaurant creation
        ]);
        
        // Create restaurant
        $restaurantData['user_id'] = $user->id;
        $restaurant = \App\Models\Restaurant::create($restaurantData);
        
        // Update user with restaurant_id
        $user->update(['restaurant_id' => $restaurant->id]);
        
        // Assign role and create settings
        if (!$user->hasRole('restaurant')) {
            $user->assignRole('restaurant');
        }

        $user->settings()->create([
            'user_id' => $user->id,
            'key' => 'currency_id',
            'value' => 1,
        ]);
        
        return $restaurant;
    }
}