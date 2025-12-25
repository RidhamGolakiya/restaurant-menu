<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminSettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminSettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Admin Settings';

    protected static ?string $modelLabel = 'Admin Setting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Admin Theme Settings')
                    ->description('Customize the admin panel theme')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('key')
                                ->label('Setting')
                                ->options([
                                    'admin_theme_mode' => 'Theme Mode',
                                    'admin_primary_color' => 'Primary Color',
                                    'admin_secondary_color' => 'Secondary Color',
                                    'admin_accent_color' => 'Accent Color',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) {
                                    // Set default values based on the selected setting
                                    if ($state === 'admin_theme_mode') {
                                        $set('value', 'default');
                                    } elseif ($state === 'admin_primary_color') {
                                        $set('value', '#3b82f6');
                                    } elseif ($state === 'admin_secondary_color') {
                                        $set('value', '#64748b');
                                    } elseif ($state === 'admin_accent_color') {
                                        $set('value', '#f59e0b');
                                    }
                                }),
                            TextInput::make('value')
                                ->label('Value')
                                ->required()
                                ->visible(fn ($get) => $get('key') !== 'admin_theme_mode')
                                ->regex('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/')
                                ->helperText('Enter color in hex format (e.g., #3b82f6)'),
                            Select::make('value')
                                ->label('Value')
                                ->options([
                                    'default' => 'Default',
                                    'black_and_white' => 'Black & White',
                                    'custom' => 'Custom',
                                ])
                                ->visible(fn ($get) => $get('key') === 'admin_theme_mode')
                                ->helperText('Select the admin panel theme mode'),
                        ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting')
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'admin_theme_mode' => 'Theme Mode',
                            'admin_primary_color' => 'Primary Color',
                            'admin_secondary_color' => 'Secondary Color',
                            'admin_accent_color' => 'Accent Color',
                        ];
                        return $labels[$state] ?? $state;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->key === 'admin_theme_mode') {
                            $modes = [
                                'default' => 'Default',
                                'black_and_white' => 'Black & White',
                                'custom' => 'Custom',
                            ];
                            return $modes[$state] ?? $state;
                        }
                        return $state;
                    }),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNull('user_id') // Only show global settings, not user-specific ones
            ->whereIn('key', [
                'admin_theme_mode',
                'admin_primary_color',
                'admin_secondary_color',
                'admin_accent_color',
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
            'index' => Pages\ListAdminSettings::route('/'),
            'edit' => Pages\EditAdminSettings::route('/{record}/edit'),
        ];
    }
}