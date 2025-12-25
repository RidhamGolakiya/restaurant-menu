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
                ->before(function ($action) {
                    if (auth()->user()->email === config('app.demo_email')) {
                        Notification::make()
                            ->title('You are not allowed to perform this action.')
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
