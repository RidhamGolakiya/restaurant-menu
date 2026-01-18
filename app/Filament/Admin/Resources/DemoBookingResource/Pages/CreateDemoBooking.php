<?php

namespace App\Filament\Admin\Resources\DemoBookingResource\Pages;

use App\Filament\Admin\Resources\DemoBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDemoBooking extends CreateRecord
{
    protected static string $resource = DemoBookingResource::class;
}
