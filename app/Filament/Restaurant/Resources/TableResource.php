<?php

namespace App\Filament\Restaurant\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Table as TableModel;
use App\RestaurantPanelMenuSorting;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Restaurant\Resources\TableResource\Pages;
use App\Filament\Restaurant\Resources\TableResource\RelationManagers;
use App\Filament\Restaurant\Resources\TableResource\RelationManagers\ReservationsRelationManager;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = RestaurantPanelMenuSorting::TABLES->value;

    public static function getNavigationGroup(): ?string
    {
        return 'Tables';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(TableModel::getFormSchema())->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('restaurant_id', auth()->user()->restaurant_id);
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->tooltip('Reservations')
                    ->iconButton()
                    ->color('success')
                    ->icon('heroicon-o-clipboard-document-list'),
                    
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit')
                    ->modalHeading('Edit Table')
                    ->successNotificationTitle('Table updated successfully')
                    ->iconButton()
                    ->modalWidth('md')
                    ->before(function (EditAction $action) {
                        if (auth()->user()->email === config('app.demo_email')) {
                            Notification::make()
                                ->title('You are not allowed to perform this action.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip('Delete')
                    ->successNotificationTitle('Table deleted successfully')
                    ->before(function ($action) {
                        $table = $action->getRecord();
                        if ($table->reservations()->count() > 0) {
                            Notification::make()
                                ->title('This table cannot be deleted.')
                                ->danger()
                                ->send();
                
                            $action->halt();
                        } elseif (auth()->user()->email === config('app.demo_email')) {
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
                        ->successNotificationTitle('Tables deleted successfully')
                        ->before(function ($action) {
                            $tables = $action->getRecords();
                            foreach ($tables as $table) {
                                if ($table->reservations()->count() > 0) {
                                    Notification::make()
                                        ->title('This table cannot be deleted.')
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
                    return 'No Tables Found';
                } else {
                    return 'No Tables Found For "'.$livewire->tableSearch.'"';
                }
            });
    }
    public static function getRelations(): array
    {
        return [
            ReservationsRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTables::route('/'),
            'view' => Pages\ViewTable::route('/{record}'),
        ];
    }
}
