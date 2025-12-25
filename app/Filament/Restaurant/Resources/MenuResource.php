<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\RestaurantPanelMenuSorting;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-4';

    protected static ?int $navigationSort = RestaurantPanelMenuSorting::MENU->value;

    public static function getNavigationGroup(): ?string
    {
        return 'Menu';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Menu::formSchema())->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('restaurant_id', auth()->user()->restaurant_id);
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('menu_image')
                    ->collection(Menu::MENU_IMAGE)
                    ->circular()
                    ->defaultImageUrl(asset('images/food-placeholder.png'))
                    ->width(50)
                    ->height(50)
                    ->label('Name'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('')
                    ->description(function ($record) {
                        return $record->category->name;
                    }),

                TextColumn::make('price')
                    ->alignRight()
                    ->formatStateUsing(fn (Menu $record) => currencyIcon().' '.$record->price)
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('today_special')
                    ->label('Today Special')
                    ->disabled(auth()->user()->email === config('app.demo_email'))
                    ->afterStateUpdated(function (Menu $record, bool $state) {
                        $record->update(['today_special' => $state]);
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Menu Updated Successfully')
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('ingredients')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category :')
                    ->relationship('category', 'name', fn ($query) => $query->where('restaurant_id', auth()->user()->restaurant_id))
                    ->placeholder('All Categories')
                    ->preload()
                    ->searchable()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit')
                    ->iconButton()
                    ->modalWidth('lg')
                    ->successNotificationTitle('Menu Updated Successfully')
                    ->before(function ($action) {
                        if (auth()->user()->email === config('app.demo_email')) {
                            Notification::make()
                                ->title('You are not allowed to perform this action.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    })
                    ->mountUsing(function ($form, $record) {
                        usleep(100000);
                        $form->fill($record->toArray());
                    }),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete')
                    ->iconButton()
                    ->successNotificationTitle('Menu Deleted Successfully')
                    ->before(function ($action) {
                        if (auth()->user()->email === config('app.demo_email')) {
                            Notification::make()
                                ->title('You are not allowed to perform this action.')
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
                        ->before(function ($action) {
                            if (auth()->user()->email === config('app.demo_email')) {
                                Notification::make()
                                    ->title('You are not allowed to perform this action.')
                                    ->danger()
                                    ->send();

                                $action->halt();
                            }
                        })
                        ->successNotificationTitle('Menus Deleted Successfully'),
                ]),
            ])
            ->emptyStateHeading(function ($livewire) {
                if (empty($livewire->tableSearch)) {
                    return 'No Menus Found';
                } else {
                    return 'No Menus Found For "'.$livewire->tableSearch.'"';
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMenus::route('/'),
        ];
    }
}
