<?php

namespace App\Filament\Restaurant\Resources\QrCodeResource\Pages;

use App\Filament\Restaurant\Resources\QrCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQrCode extends CreateRecord
{
    protected static string $resource = QrCodeResource::class;
    
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = (string) \Illuminate\Support\Str::uuid();
        $data['restaurant_id'] = auth()->user()->restaurant_id;

        return $data;
    }
}
