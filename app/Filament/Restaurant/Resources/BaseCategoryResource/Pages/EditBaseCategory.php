<?php

namespace App\Filament\Restaurant\Resources\BaseCategoryResource\Pages;

use App\Filament\Restaurant\Resources\BaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBaseCategory extends EditRecord
{
    protected static string $resource = BaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
