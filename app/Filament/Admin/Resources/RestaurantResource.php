<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RestaurantResource\Pages;
use App\Filament\Admin\Resources\RestaurantResource\RelationManagers;
use App\Models\Country;
use App\Models\Restaurant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Propaganistas\LaravelPhone\Rules\Phone;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Support\Str;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Restaurant')->tabs([
                    Tab::make('User Details')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('user_name')
                                        ->label('User Name')
                                        ->placeholder('User Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->default(fn ($record) => $record?->user?->name),
                                    TextInput::make('user_email')
                                        ->label('Email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->default(fn ($record) => $record?->user?->email)
                                        ->unique('users', 'email', fn ($record) => $record?->user?->id ?? null),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('user_password')
                                        ->label('Password')
                                        ->placeholder('Password')
                                        ->password()
                                        ->required(fn ($context) => $context === 'create')
                                        ->dehydrated(fn ($state) => filled($state))
                                        ->visibleOn('create'),
                                    TextInput::make('user_password_confirmation')
                                        ->label('Confirm Password')
                                        ->placeholder('Confirm Password')
                                        ->password()
                                        ->required(fn ($context) => $context === 'create')
                                        ->same('user_password')
                                        ->visibleOn('create'),
                                ])
                        ]),
                    Tab::make('Restaurant Details')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('name')
                                    ->label('Restaurant Name:')
                                    ->placeholder('Restaurant Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->label('URL:')
                                    ->placeholder('Slug')
                                    ->required()
                                    ->unique('restaurants', 'slug', ignoreRecord: true),
                                PhoneInput::make('phone')
                                    ->label('Phone Number:')
                                    ->placeholder('Phone Number')
                                    ->required(),
                                Select::make('timezone')
                                    ->label('Time Zone:')
                                    ->options(getTimeZone())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false),
                                TextInput::make('zip_code')
                                    ->label('Zip Code:')
                                    ->placeholder('Zip Code')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxLength(20),
                                TextInput::make('city')
                                    ->label('City:')
                                    ->placeholder('City')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('state')
                                    ->label('State:')
                                    ->placeholder('State')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('country_id')
                                    ->label('Country:')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->optionsLimit(Country::count())
                                    ->native(false)
                                    ->options(Country::pluck('name', 'id')->toArray()),
                                Textarea::make('address')
                                    ->label('Address 1:')
                                    ->rows(3)
                                    ->placeholder('Address')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('address_2')
                                    ->label('Address 2:')
                                    ->placeholder('Address 2')
                                    ->nullable()
                                    ->rows(3)
                                    ->maxLength(255),
                                TextInput::make('google_map_link')
                                    ->label('Google Map Link:')
                                    ->placeholder('Google Map Link')
                                    ->url()
                                    ->rule('regex:/^(https?:\/\/)?(www\.)?(google\.[a-z.]+\/maps|goo\.gl\/maps)\/.+$/i'),
                                TextInput::make('restaurant_website_link')
                                    ->label('Restaurant Website:')
                                    ->placeholder('Restaurant Website Link')
                                    ->url(),
                                Textarea::make('overview')
                                    ->label('Overview:')
                                    ->rows(4)
                                    ->placeholder('Restaurant Overview'),
                            ]),
                        ]),
                    Tab::make('Theme Settings')
                        ->schema([
                            Grid::make(2)->schema([
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
                            ]),
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('')
                    ->description(function ($record) {
                        return $record->user->email;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Restaurant Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->formatStateUsing(fn ($record) => formatPhoneNumber($record?->country_code, $record?->phone))
                    ->label('Phone Number')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotificationTitle('Restaurants deleted successfully'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}