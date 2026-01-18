<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomEditProfile;
use App\Filament\Pages\Dashboard;
use App\Filament\Restaurant\Pages\Login;
use App\Models\Restaurant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Spatie\Permission\Middleware\RoleMiddleware;

class RestaurantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('restaurant')
            ->path('')
            ->login(Login::class)
            ->colors([
                'primary' => "#6F4E37",
                'gray' => \Filament\Support\Colors\Color::Stone,
            ])
            ->profile(CustomEditProfile::class, isSimple: false)
            ->discoverResources(in: app_path('Filament/Restaurant/Resources'), for: 'App\\Filament\\Restaurant\\Resources')
            ->discoverPages(in: app_path('Filament/Restaurant/Pages'), for: 'App\\Filament\\Restaurant\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->plugins([
                FilamentApexChartsPlugin::make(),
            ])
            ->breadcrumbs(false)
            ->discoverWidgets(in: app_path('Filament/Restaurant/Widgets'), for: 'App\\Filament\\Restaurant\\Widgets')
            ->renderHook(PanelsRenderHook::SCRIPTS_AFTER, fn () => view('layout.scripts'))
            ->renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER, fn () => Blade::render('@livewire(\'ChangePassword\')'))
            ->renderHook(
                PanelsRenderHook::RESOURCE_RELATION_MANAGER_BEFORE,
                fn() => '<h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                Reservations
            </h1>'
            )
            // ->renderHook(
            //     'panels::user-menu.before',
            //     fn() => view('layout.date-time', ['restaurant' => auth()->user()->restaurant])
            // )
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, function () {
                $user = auth()->user();

                if ($user->hasRole('restaurant') && $user->restaurant?->slug) {
                    $slug = $user->restaurant->slug;

                    return view('layout.front-url', ['slug' => $slug]);
                }

                return '';
            })

            ->widgets([
                //
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarWidth('15rem')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Settings',
                'Management',
            ])
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
                RoleMiddleware::class . ':restaurant',
            ]);
    }
}