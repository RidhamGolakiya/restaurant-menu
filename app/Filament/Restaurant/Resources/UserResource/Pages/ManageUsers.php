<?php

namespace App\Filament\Restaurant\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Restaurant\Resources\UserResource;
use App\Models\User;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('New User'))
                ->modalWidth('md')
                ->modalHeading(__('New User'))
                ->successNotificationTitle(__('User Created Successfully'))
                ->createAnother(false)
                ->action(function (array $data) {
                    $data['restaurant_id'] = auth()->user()->restaurant_id;
                    $data['parent_user_id'] = auth()->user()->id;
                    $user = User::create($data);
                    $user->assignRole('restaurant');

                    return Notification::make()
                        ->success()
                        ->title(__('User Created Successfully'))
                        ->send();
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
