<?php

namespace App\Filament\Admin\Resources\AdminSettingResource\Pages;

use App\Filament\Admin\Resources\AdminSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminSettings extends ListRecords
{
    protected static string $resource = AdminSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}