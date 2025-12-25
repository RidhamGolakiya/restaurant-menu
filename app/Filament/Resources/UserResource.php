<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Restaurants';
    }

    public static function getLabel(): string
    {
        return 'Restaurant';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(User::getFormSchema())->columns(1);
    }

    public static function table(Table $table): Table
    {
        $table->modifyQueryUsing(function (Builder $query) {
            return $query->role('restaurant');
        });

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                ToggleColumn::make('status')
                    ->label('Status')
                    ->afterStateUpdated(function ($state, $record) {
                        $record->status = $state ? User::ACTIVE : User::INACTIVE;
                        $record->save();
                        Notification::make()
                            ->success()
                            ->title('Status Updated Successfully')
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordAction(null)
            ->recordUrl(null)
            ->actions([
                Impersonate::make()
                    ->tooltip(__('Impersonate'))
                    ->redirectTo(route('filament.restaurant.pages.dashboard'))
                    ->color('gray')
                    ->label(__('Impersonate')),
                Tables\Actions\EditAction::make()->tooltip('Edit')->iconButton(),
                DeleteAction::make()->tooltip('Delete')->iconButton()->successNotificationTitle('User deleted successfully'),
            ])->actionsColumnLabel('Actions')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
