<?php

namespace App\Filament\Restaurant\Resources\TableResource\Pages;

use App\Filament\Restaurant\Resources\TableResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageTables extends ManageRecords
{
    protected static string $resource = TableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Table')
                ->modalHeading('Create New Table')
                ->modalWidth('md')
                ->createAnother(false)
                ->successNotificationTitle('Table Created Successfully')
                ->action(function (array $data) {
                    $restaurantId = auth()->user()->restaurant_id ?? null;
                    $data['restaurant_id'] = $restaurantId;
                    return $this->getResource()::getModel()::create($data);
                })
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
