<?php

namespace App\Filament\Restaurant\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MenuCategory;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\RestaurantPanelMenuSorting;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Restaurant\Resources\MenuCategoryResource\Pages;
use App\Filament\Restaurant\Resources\MenuCategoryResource\RelationManagers;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;

class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = RestaurantPanelMenuSorting::MENU_CATEGORY->value;

    public static function getNavigationGroup(): ?string
    {
        return 'Menu';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(MenuCategory::formSchema())->columns(1);
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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('baseCategory.name')
                    ->label('Base Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('menus_count')
                    ->label('Items Count')
                    ->badge()
                    ->color('success')
                    ->counts('menus')
                    ->alignment(Alignment::Center),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->tooltip('View')
                    ->iconButton()
                    ->modalHeading('Menu Category')
                    ->modalWidth('md'),
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit')
                    ->iconButton()
                    ->modalWidth('md')
                    ->successNotificationTitle('Menu Category Updated Successfully'),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete')
                    ->iconButton()
                    ->successNotificationTitle('Menu Category Deleted Successfully')
                    ->before(function ($action) {
                        $menuCategory = $action->getRecord();
                        if ($menuCategory->menus()->count() > 0) {
                            Notification::make()
                                ->title('This menu category cannot be deleted.')
                                ->danger()
                                ->send();
                
                            $action->halt();
                        }
                    }),
            ])
            ->actionsColumnLabel('Actions')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotificationTitle('Menu Categories Deleted Successfully')
                        ->before(function ($action) {
                            $menuCategories = $action->getRecords();
                            foreach ($menuCategories as $menuCategory) {
                                if ($menuCategory->menus()->count() > 0) {
                                    Notification::make()
                                        ->title('This menu category cannot be deleted.')
                                        ->danger()
                                        ->send();
                
                                    $action->halt();
                                }
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading(function ($livewire) {
                if (empty($livewire->tableSearch)) {
                    return 'No Menu Categories Found';
                } else {
                    return 'No Menu Categories Found For "'.$livewire->tableSearch.'"';
                }
            });
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                    ->label('Name :'),
                TextEntry::make('description')
                    ->label('Description :')
            ])->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMenuCategories::route('/'),
        ];
    }
}
