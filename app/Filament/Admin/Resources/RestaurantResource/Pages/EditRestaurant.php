<?php

namespace App\Filament\Admin\Resources\RestaurantResource\Pages;

use App\Filament\Admin\Resources\RestaurantResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditRestaurant extends EditRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Restaurant updated successfully';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $userPassword = $data['user_password'] ?? null;
        
        // Remove user-related fields from restaurant data
        $restaurantData = $data;
        unset($restaurantData['user_name'], $restaurantData['user_email'], $restaurantData['user_password']);
        
        // Update restaurant
        $record->update($restaurantData);
        
        // Update user if exists
        if ($record->user) {
            $userData = [];
            if (isset($data['user_name']) && $data['user_name'] !== $record->user->name) {
                $userData['name'] = $data['user_name'];
            }
            if (isset($data['user_email']) && $data['user_email'] !== $record->user->email) {
                $userData['email'] = $data['user_email'];
            }
            if ($userPassword) {
                $userData['password'] = Hash::make($userPassword);
            }
            
            if (!empty($userData)) {
                $record->user->update($userData);
            }
        }
        
        return $record;
    }
}