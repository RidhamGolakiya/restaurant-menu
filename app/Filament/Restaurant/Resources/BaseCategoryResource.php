<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\BaseCategoryResource\Pages;
use App\Filament\Restaurant\Resources\BaseCategoryResource\RelationManagers;
use App\Models\BaseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BaseCategoryResource extends Resource
{
    protected static ?string $model = BaseCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function getNavigationGroup(): ?string
    {
        return 'Menu';
    }

    protected static ?int $navigationSort = \App\RestaurantPanelMenuSorting::BASE_CATEGORY->value;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Hidden::make('restaurant_id')
                    ->default(auth()->user()->restaurant_id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('restaurant_id', auth()->user()->restaurant_id);
            })
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBaseCategories::route('/'),
            'create' => Pages\CreateBaseCategory::route('/create'),
            'edit' => Pages\EditBaseCategory::route('/{record}/edit'),
        ];
    }
}
