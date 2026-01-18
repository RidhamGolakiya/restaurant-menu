<?php

namespace App\Filament\Admin\Resources\DemoBookingResource\Pages;

use App\Filament\Admin\Resources\DemoBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDemoBooking extends EditRecord
{
    protected static string $resource = DemoBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
