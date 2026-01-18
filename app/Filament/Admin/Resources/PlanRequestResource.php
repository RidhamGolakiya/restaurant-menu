<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PlanRequestResource\Pages;
use App\Filament\Admin\Resources\PlanRequestResource\RelationManagers;
use App\Models\PlanRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanRequestResource extends Resource
{
    protected static ?string $model = PlanRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Requested Plan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (PlanRequest $record) => $record->status === 'pending')
                    ->action(function (PlanRequest $record) {
                        $restaurant = $record->restaurant;
                        $plan = $record->plan;

                        $expiryDate = match ($plan->frequency) {
                            'monthly' => now()->addMonth(),
                            'yearly' => now()->addYear(),
                            default => now()->addMonth(),
                        };

                        $restaurant->update([
                            'plan_id' => $plan->id,
                            'plan_status' => 'active',
                            'plan_expiry' => $expiryDate,
                        ]);

                        $record->update(['status' => 'approved']);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (PlanRequest $record) => $record->status === 'pending')
                    ->action(fn (PlanRequest $record) => $record->update(['status' => 'rejected'])),
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
            'index' => Pages\ListPlanRequests::route('/'),
            'create' => Pages\CreatePlanRequest::route('/create'),
            'edit' => Pages\EditPlanRequest::route('/{record}/edit'),
        ];
    }
}
