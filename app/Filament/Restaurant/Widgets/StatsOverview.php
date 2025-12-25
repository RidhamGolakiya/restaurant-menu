<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Table;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
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

        $restaurantId = auth()->user()->restaurant_id;
        return [
            Stat::make('Reservations', number_format(Reservation::where('restaurant_id', $restaurantId)->count()))
                ->icon('heroicon-o-calendar')
                ->color('info')
                ->chart([5, 8, 10, 6, 12, 14, 18])
                ->description('Bookings')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Customers', number_format(Customer::where('restaurant_id', $restaurantId)->count()))
                ->icon('heroicon-o-users')
                ->color('PastelBlue')
                ->chart([2, 3, 4, 5, 6, 7, 8])
                ->description('Customers')
                ->descriptionIcon('heroicon-m-user-plus'),

            Stat::make('Total Tables', number_format(Table::where('restaurant_id', $restaurantId)->count()))
                ->icon('heroicon-o-viewfinder-circle')
                ->color('PastelMagenta')
                ->chart([4, 5, 6, 6, 7, 7, 7])
                ->description('Available in venue')
                ->descriptionIcon('heroicon-m-square-3-stack-3d'),
        ];
    }
}