<?php

namespace App\Filament\Admin\Resources\AdminSettingResource\Pages;

use App\Filament\Admin\Resources\AdminSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminSettings extends EditRecord
{
    protected static string $resource = AdminSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}