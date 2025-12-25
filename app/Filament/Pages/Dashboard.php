<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = '/dashboard';


    public static function getNavigationIcon(): string | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? 'heroicon-m-chart-pie' : 'heroicon-o-chart-pie');
    }

    public function getHeading(): string
{
    $user = auth()->user(); // actual user
    $restaurant = $user->restaurant ?? null;

    $timezone = $restaurant?->timezone ?? config('app.timezone');
    $currentTime = Carbon::now($timezone);

    if ($currentTime->hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($currentTime->hour < 17) {
        $greeting = 'Good Afternoon';
    } else {
        $greeting = 'Good Evening';
    }

    return $greeting . '! ' . $user->name;
}
}
