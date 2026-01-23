<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\ReviewResource\Pages;
use App\Filament\Restaurant\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Review Details')
                    ->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->label('Author Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rating')
                            ->label('Rating (1-5)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5),
                        Forms\Components\DatePicker::make('time') // Using DatePicker for manual entry convenience, casting handles it
                            ->label('Date')
                            ->maxDate(now())
                            ->required(),
                        Forms\Components\Textarea::make('text')
                            ->label('Review Text')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true)
                            ->columnSpanFull(),
                        
                        // Hidden fields for manual creation
                        Forms\Components\Hidden::make('restaurant_id')
                            ->default(auth()->user()->restaurant_id),
                        Forms\Components\Hidden::make('relative_time_description')
                            ->default('Manually added'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('profile_photo_url')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Review&background=random'),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('relative_time_description')
                    ->label('Time'),
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('Visible'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('restaurant_id', auth()->user()->restaurant_id);
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
