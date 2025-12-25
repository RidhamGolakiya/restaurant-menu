<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\RestaurantWidget;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.admin.pages.dashboard';
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            RestaurantWidget::class,
        ];
    }
}
