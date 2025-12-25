<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Restaurant;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        $restaurant = Restaurant::create($data['restaurant']);

        $data['restaurant_id'] = $restaurant->id;

        $user = User::create($data);

        $user->assignRole('restaurant');

        return $restaurant;
    }

    public function getTitle(): string
    {
        return 'Create Restaurant';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Restaurant created successfully';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
