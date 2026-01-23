<?php

namespace App\Filament\Restaurant\Resources\MenuCategoryResource\Pages;

use App\Filament\Restaurant\Resources\MenuCategoryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageMenuCategories extends ManageRecords
{
    protected static string $resource = MenuCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Menu Category')
                ->createAnother(false)
                ->modalWidth('md')
                ->successNotificationTitle('Menu Category Created Successfully')
        ];
    }
}
