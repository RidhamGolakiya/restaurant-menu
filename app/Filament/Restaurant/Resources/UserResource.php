<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\UserResource\Pages;
use App\Models\User;
use App\RestaurantPanelMenuSorting;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = RestaurantPanelMenuSorting::USERS->value;

    public static function getNavigationGroup(): ?string
    {
        return 'Users';
    }

    public static function canCreate(): bool
    {
        if (empty(Auth::user()->parent_user_id)) {
            return true;
        }

        return false;
    }

    public static function canEdit(Model $record): bool
    {
        if (empty(Auth::user()->parent_user_id)) {
            return true;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (empty(Auth::user()->parent_user_id)) {
            return true;
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        if (empty(Auth::user()->parent_user_id)) {
            return true;
        }

        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(User::getFormSchema())->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->role('restaurant')->where('parent_user_id', auth()->user()->id);
            })
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                ToggleColumn::make('status')
                    ->label('Status')
                    ->disabled(Auth::user()->email === config('app.demo_email'))
                    ->afterStateUpdated(function ($state, $record) {
                        $record->status = $state ? User::ACTIVE : User::INACTIVE;
                        $record->save();
                        Notification::make()
                            ->success()
                            ->title('Status Updated Successfully')
                            ->send();
                    }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotificationTitle('User updated successfully')
                    ->modalWidth('md')
                    ->tooltip('Edit')
                    ->modalHeading('Edit User')
                    ->iconButton()
                    ->before(function ($action) {
                        if (auth()->user()->email === config('app.demo_email')) {
                            Notification::make()
                                ->title('You are not allowed to perform this action.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete')
                    ->successNotificationTitle('User deleted successfully')
                    ->iconButton()
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
            ->recordAction(null)
            ->recordUrl(null)
            ->actionsColumnLabel('Actions')
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading(function ($livewire) {
                if (empty($livewire->tableSearch)) {
                    return 'No Users Found';
                } else {
                    return 'No Users Found For "'.$livewire->tableSearch.'"';
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
