<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        $data['restaurant'] = [
            'name' => $this->record->restaurant->name,
            'address' => $this->record->restaurant->address,
            'address_2' => $this->record->restaurant->address_2,
            'city' => $this->record->restaurant->city,
            'state' => $this->record->restaurant->state,
            'country' => $this->record->restaurant->country,
            'zip_code' => $this->record->restaurant->zip_code,
        ];

        return $data;
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        $restaurant = $record->restaurant;

        $restaurant->update($data['restaurant']);

        $record->update($data);

        return $record;
    }

    public function getTitle(): string
    {
        return __('Edit Restaurant');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('Restaurant updated successfully');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
