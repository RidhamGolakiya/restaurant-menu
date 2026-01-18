<?php

namespace App\Filament\Admin\Resources\PlanRequestResource\Pages;

use App\Filament\Admin\Resources\PlanRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanRequest extends EditRecord
{
    protected static string $resource = PlanRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
