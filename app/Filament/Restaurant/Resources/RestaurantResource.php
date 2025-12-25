<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\RestaurantResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Restaurant Details')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Restaurant Name:')
                                ->placeholder('Restaurant Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('phone')
                                ->label('Phone Number:')
                                ->placeholder('Phone Number')
                                ->required()
                                ->tel(),
                            Forms\Components\Textarea::make('address')
                                ->label('Address 1:')
                                ->rows(3)
                                ->placeholder('Address')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('address_2')
                                ->label('Address 2:')
                                ->placeholder('Address 2')
                                ->nullable()
                                ->rows(3)
                                ->maxLength(255),
                            Forms\Components\TextInput::make('city')
                                ->label('City:')
                                ->placeholder('City')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('state')
                                ->label('State:')
                                ->placeholder('State')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('zip_code')
                                ->label('Zip Code:')
                                ->placeholder('Zip Code')
                                ->required()
                                ->maxLength(20),
                            Forms\Components\Select::make('country_id')
                                ->label('Country:')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false)
                                ->options(\App\Models\Country::pluck('name', 'id')->toArray()),
                            Forms\Components\TextInput::make('google_map_link')
                                ->label('Google Map Link:')
                                ->placeholder('Google Map Link')
                                ->url(),
                            Forms\Components\TextInput::make('restaurant_website_link')
                                ->label('Restaurant Website:')
                                ->placeholder('Restaurant Website Link')
                                ->url(),
                            Forms\Components\Textarea::make('overview')
                                ->label('Overview:')
                                ->rows(4)
                                ->placeholder('Restaurant Overview'),
                            Forms\Components\Select::make('timezone')
                                ->label('Time Zone:')
                                ->options(getTimeZone())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),
                        ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Restaurant Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
            'theme-settings' => Pages\ThemeSettings::route('/theme-settings'),
        ];
    }
}