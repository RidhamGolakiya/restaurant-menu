<?php

namespace App\Filament\Restaurant\Resources\QrCodeResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\QrCode;
use Filament\Widgets\Concerns\InteractsWithPageTable;

class QrCodeStats extends BaseWidget
{
    public ?QrCode $record = null;

    protected function getStats(): array
    {
        $record = $this->record;

        if (! $record) {
             return [
                Stat::make('Total Scans', QrCode::sum('scans_count')),
            ];
        }

        return [
            Stat::make('Total Scans', $record->scans_count)
                ->description('Total number of times this QR code has been scanned'),
        ];
    }
}
