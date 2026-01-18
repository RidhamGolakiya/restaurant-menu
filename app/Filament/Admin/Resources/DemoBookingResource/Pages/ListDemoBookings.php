<?php

namespace App\Filament\Admin\Resources\DemoBookingResource\Pages;

use App\Filament\Admin\Resources\DemoBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDemoBookings extends ListRecords
{
    protected static string $resource = DemoBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
