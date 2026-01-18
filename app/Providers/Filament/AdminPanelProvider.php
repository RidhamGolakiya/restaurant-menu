<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomEditProfile;
use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Middleware\RoleMiddleware;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Get admin theme settings
        try {
            $themeMode = \Illuminate\Support\Facades\Schema::hasTable('settings') 
                ? (Setting::where('key', 'admin_theme_mode')->whereNull('user_id')->first()?->value ?? 'default')
                : 'default';
            $primaryColor = \Illuminate\Support\Facades\Schema::hasTable('settings')
                ? (Setting::where('key', 'admin_primary_color')->whereNull('user_id')->first()?->value ?? '#3b82f6')
                : '#3b82f6';
            $secondaryColor = \Illuminate\Support\Facades\Schema::hasTable('settings')
                ? (Setting::where('key', 'admin_secondary_color')->whereNull('user_id')->first()?->value ?? '#64748b')
                : '#64748b';
            $accentColor = \Illuminate\Support\Facades\Schema::hasTable('settings')
                ? (Setting::where('key', 'admin_accent_color')->whereNull('user_id')->first()?->value ?? '#f59e0b')
                : '#f59e0b';
        } catch (\Exception $e) {
            $themeMode = 'default';
            $primaryColor = '#3b82f6';
            $secondaryColor = '#64748b';
            $accentColor = '#f59e0b';
        }
        
        // Define colors based on theme mode
        $colors = [
            'primary' => $this->getColorForValue($primaryColor),
        ];
        
        if ($themeMode === 'black_and_white') {
            $colors = [
                'primary' => Color::Gray,
                'gray' => Color::Gray,
            ];
        } elseif ($themeMode === 'custom') {
            $colors = [
                'primary' => $this->getColorForValue($primaryColor),
                'secondary' => $this->getColorForValue($secondaryColor),
                'accent' => $this->getColorForValue($accentColor),
            ];
        } else { // default
            $colors = [
                'primary' => Color::Gray,
                'gray' => Color::Gray,
            ];
        }

        return $panel
            ->id('admin')
            ->path('innomi-tech')
            ->login()
            ->default() 
            ->colors($colors)
            ->profile(CustomEditProfile::class, isSimple: false)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER, fn() => Blade::render('@livewire(\'ChangePassword\')'))
            // ->renderHook(
            //     'panels::user-menu.before',
            //     fn() => view('layout.date-time')
            // )
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
            ])
            ->breadcrumbs(false)
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarWidth('15rem')
            ->sidebarCollapsibleOnDesktop()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RoleMiddleware::class . ':admin',
            ]);
    }
    
    private function getColorForValue($colorValue)
    {
        // Convert hex color to Filament Color
        if (str_starts_with($colorValue, '#')) {
            return \Filament\Support\Colors\Color::hex($colorValue);
        }
        
        // If it's a color name, convert it to the corresponding Color constant
        $colorName = strtoupper($colorValue);
        
        // Handle common color names
        switch ($colorName) {
            case 'GRAY':
                return Color::Gray;
            case 'AMBER':
                return Color::Amber;
            case 'BLUE':
                return Color::Blue;
            case 'RED':
                return Color::Red;
            case 'GREEN':
                return Color::Green;
            case 'YELLOW':
                return Color::Yellow;
            case 'PURPLE':
                return Color::Purple;
            case 'PINK':
                return Color::Pink;
            case 'ORANGE':
                return Color::Orange;
            case 'INDIGO':
                return Color::Indigo;
            case 'EMERALD':
                return Color::Emerald;
            case 'TEAL':
                return Color::Teal;
            case 'CYAN':
                return Color::Cyan;
            default:
                // Default to blue if unable to parse
                return Color::Blue;
        }
    }
}