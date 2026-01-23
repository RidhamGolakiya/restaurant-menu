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
                Forms\Components\Tabs::make('RestaurantTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Restaurant Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->label('URL Slug')
                                        ->required()
                                        ->unique('restaurants', 'slug', ignoreRecord: true),
                                    PhoneInput::make('phone')
                                        ->label('Phone Number')
                                        ->required(),
                                    Select::make('timezone')
                                        ->label('Time Zone')
                                        ->options(getTimeZone())
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                    Select::make('country_id')
                                        ->label('Country')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->optionsLimit(Country::count())
                                        ->options(Country::pluck('name', 'id')->toArray()), 
                                    TextInput::make('city')
                                        ->label('City')
                                        ->required(),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Status')
                                        ->required()
                                        ->default(true)
                                        ->onColor('success')
                                        ->offColor('danger'),
                                    Select::make('theme')
                                        ->label('Theme')
                                        ->options([
                                            'default' => 'Default',
                                            'modern' => 'Modern',
                                            'theme_3' => 'Dynamic Theme 3',
                                        ])
                                        ->required()
                                        ->default('default'),
                                    Select::make('currency_id')
                                        ->label('Currency')
                                        ->relationship('currency', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required(),
                                            Forms\Components\TextInput::make('code')
                                                ->required(),
                                            Forms\Components\TextInput::make('icon')
                                                ->label('Symbol')
                                                ->required(),
                                        ]),
                                    Select::make('type')
                                        ->label('Restaurant Type')
                                        ->options(Restaurant::$types)
                                        ->searchable(),
                                    Forms\Components\Toggle::make('show_on_landing_page')
                                        ->label('Show on Landing Page')
                                        ->default(false)
                                        ->onColor('success')
                                        ->offColor('gray'),
                                ]),

                                Forms\Components\Section::make('Social Media')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('social_links.instagram')->label('Instagram')->prefix('instagram.com/'),
                                            Forms\Components\TextInput::make('social_links.facebook')->label('Facebook')->prefix('facebook.com/'),
                                            Forms\Components\TextInput::make('social_links.twitter')->label('X (Twitter)')->prefix('x.com/'),
                                        ]),
                                    ])->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Business Hours')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Forms\Components\Repeater::make('timingSlots')
                                    ->relationship()
                                    ->schema([
                                        Select::make('day_name')
                                            ->options([
                                                'Monday' => 'Monday',
                                                'Tuesday' => 'Tuesday',
                                                'Wednesday' => 'Wednesday',
                                                'Thursday' => 'Thursday',
                                                'Friday' => 'Friday',
                                                'Saturday' => 'Saturday',
                                                'Sunday' => 'Sunday',
                                            ])
                                            ->required(),
                                        Forms\Components\TimePicker::make('open_time')->required(),
                                        Forms\Components\TimePicker::make('close_time')->required(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Time Slot'),
                            ]),

                        Forms\Components\Tabs\Tab::make('User Account')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('user_name')
                                        ->label('User Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->formatStateUsing(fn ($record) => $record?->user?->name)
                                        ->dehydrated(false),
                                    TextInput::make('user_email')
                                        ->label('Email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->unique('users', 'email', ignoreRecord: true, modifyRuleUsing: function ($rule, $record) {
                                            if ($record && $record->user) {
                                                return $rule->ignore($record->user->id);
                                            }
                                            return $rule;
                                        })
                                        ->formatStateUsing(fn ($record) => $record?->user?->email)
                                        ->dehydrated(false),
                                    TextInput::make('user_password')
                                        ->label('Password')
                                        ->password()
                                        ->dehydrated(false)
                                        ->required(fn ($context) => $context === 'create')
                                        ->revealable(),
                                ]),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Subscription')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('plan_id')
                                        ->relationship('plan', 'name')
                                        ->label('Current Plan')
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state) {
                                                $plan = \App\Models\Plan::find($state);
                                                if ($plan) {
                                                    $set('plan_status', 'active');
                                                    $expiryDate = match ($plan->frequency) {
                                                        'monthly' => now()->addMonth(),
                                                        'yearly' => now()->addYear(),
                                                        default => now()->addMonth(),
                                                    };
                                                    $set('plan_expiry', $expiryDate);
                                                }
                                            }
                                        }),
                                    Select::make('plan_status')
                                        ->options([
                                            'active' => 'Active',
                                            'canceled' => 'Canceled',
                                            'expired' => 'Expired',
                                        ])
                                        ->label('Status'),
                                    Forms\Components\DatePicker::make('plan_expiry')
                                        ->label('Expiry Date'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('show_on_landing_page')
                    ->label('Landing Page')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->url(fn ($record) => url('/r/' . $record->slug))
                    ->openUrlInNewTab(),
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