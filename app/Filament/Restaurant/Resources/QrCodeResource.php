<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\QrCodeResource\Pages;
use App\Filament\Restaurant\Resources\QrCodeResource\RelationManagers;
use App\Models\QrCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QrCodeResource extends Resource
{
    protected static ?string $model = QrCode::class;

    protected static ?string $pluralModelLabel = 'QR Code';

    public static function getRecordRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Section::make('Customization')
                    ->schema([
                        Forms\Components\ColorPicker::make('settings.color')
                            ->label('Color')
                            ->default('#000000'),
                        Forms\Components\ColorPicker::make('settings.background_color')
                            ->label('Background Color')
                            ->default('#ffffff'),
                        Forms\Components\Select::make('settings.style')
                            ->label('Style')
                            ->options([
                                'square' => 'Square',
                                'dot' => 'Dot',
                                'round' => 'Round',
                            ])
                            ->default('square'),
                        Forms\Components\Select::make('settings.eye_style')
                            ->label('Eye Style')
                            ->options([
                                'square' => 'Square',
                                'circle' => 'Circle',
                            ])
                            ->default('square'),
                         Forms\Components\FileUpload::make('settings.logo_url')
                            ->label('Logo')
                            ->image()
                            ->directory('qr-logos')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(1024) // Max 1MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml']),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('QR Link')
                    ->formatStateUsing(fn (string $state) => route('qr.scan', $state))
                    ->copyable()
                    ->copyMessage('Link copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('scans_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->url(fn (QrCode $record) => route('qr.scan', $record->uuid))
                    ->openUrlInNewTab()
                    ->icon('heroicon-m-arrow-top-right-on-square'),
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
            'index' => Pages\ListQrCodes::route('/'),
            'create' => Pages\CreateQrCode::route('/create'),
            'view' => Pages\ViewQrCode::route('/{record}'), // View page for analytics
            'edit' => Pages\EditQrCode::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
       // Only allow creation if the user's restaurant doesn't have a QR code yet.
       $user = auth()->user();
       if (!$user) {
           return false;
       }
       $restaurantId = $user->restaurant_id;
       return $restaurantId && !QrCode::where('restaurant_id', $restaurantId)->exists();
    }
}
