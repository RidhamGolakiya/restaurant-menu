<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Currency;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RestaurantWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        function formatNumber($num)
        {
            $units = ['', 'k', 'm', 'b', 't'];
            $index = 0;
            while ($num >= 1000 && $index < count($units) - 1) {
                $num /= 1000;
                $index++;
            }

            return number_format($num, 2).$units[$index];
        }

        $currencyIcon = Currency::find(getUserSettings()['currency_id'] ?? '')?->icon ?? '$';

        return [
            Stat::make('Restaurants', \App\Models\Restaurant::count())->icon('heroicon-s-home')->chart([0, 0])->color('info'),
            Stat::make('Customers', \App\Models\Customer::count())->icon('heroicon-s-users')->chart([0, 0])->color('PastelBlue'),
            Stat::make('Reservations', \App\Models\Reservation::count())->icon('heroicon-s-calendar')->chart([0, 0])->color('warning'),
            Stat::make('Tables', \App\Models\Table::count())->icon('heroicon-s-square-3-stack-3d')->chart([0, 0])->color('success'),
        ];
    }
}