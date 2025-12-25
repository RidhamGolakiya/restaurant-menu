<?php

namespace App\Filament\Restaurant\Resources\RestaurantResource\Pages;

use App\Filament\Restaurant\Resources\RestaurantResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;

class ThemeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = RestaurantResource::class;

    protected static string $view = 'filament.restaurant.resources.restaurant-resource.pages.theme-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(array $context = []): bool
    {
        // Only show if the user has access to their own restaurant
        return auth()->user()?->restaurant_id !== null;
    }

    public function mount(): void
    {
        // Ensure user can only access their own restaurant
        $this->authorizeAccess();
        $this->fillForm();
    }

    private function authorizeAccess(): void
    {
        $restaurant = auth()->user()->restaurant;
        if (!$restaurant) {
            abort(403, 'Access denied. You do not have a restaurant associated with your account.');
        }
    }

    protected function fillForm(): void
    {
        $restaurant = auth()->user()->restaurant;
        $this->form->fill([
            'theme_mode' => $restaurant->theme_mode ?? 'default',
            'primary_color' => $restaurant->primary_color,
            'secondary_color' => $restaurant->secondary_color,
            'accent_color' => $restaurant->accent_color,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('theme_mode')
                    ->label('Theme Mode')
                    ->options([
                        'default' => 'Default',
                        'black_and_white' => 'Black & White',
                        'custom' => 'Custom',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state === 'black_and_white') {
                            $set('primary_color', '#000000');
                            $set('secondary_color', '#ffffff');
                            $set('accent_color', '#000000');
                        } elseif ($state === 'default') {
                            $set('primary_color', null);
                            $set('secondary_color', null);
                            $set('accent_color', null);
                        }
                    }),
                TextInput::make('primary_color')
                    ->label('Primary Color')
                    ->helperText('Enter color in hex format (e.g., #da3743)')
                    ->visible(fn ($get) => $get('theme_mode') === 'custom')
                    ->regex('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/')
                    ->placeholder('#da3743'),
                TextInput::make('secondary_color')
                    ->label('Secondary Color')
                    ->helperText('Enter color in hex format (e.g., #247f9e)')
                    ->visible(fn ($get) => $get('theme_mode') === 'custom')
                    ->regex('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/')
                    ->placeholder('#247f9e'),
                TextInput::make('accent_color')
                    ->label('Accent Color')
                    ->helperText('Enter color in hex format (e.g., #f59e0b)')
                    ->visible(fn ($get) => $get('theme_mode') === 'custom')
                    ->regex('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/')
                    ->placeholder('#f59e0b'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $restaurant = auth()->user()->restaurant;
        if (!$restaurant) {
            abort(403, 'Access denied. You do not have a restaurant associated with your account.');
        }

        $restaurant->update([
            'theme_mode' => $data['theme_mode'],
            'primary_color' => $data['primary_color'] ?? null,
            'secondary_color' => $data['secondary_color'] ?? null,
            'accent_color' => $data['accent_color'] ?? null,
        ]);

        $this->fillForm();
        
        $this->notify('success', 'Theme settings updated successfully.');
    }

    public static function getNavigationLabel(): string
    {
        return 'Theme Settings';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-paint-brush';
    }
    
    public static function getRoutePath(): string
    {
        return 'theme-settings';
    }
}