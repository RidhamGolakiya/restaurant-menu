<?php

namespace App\Filament\Restaurant\Resources\TableResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';

    protected static ?string $title = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('start_time')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->recordTitleAttribute('start_time')
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.phone')
                    ->label('Customer Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => preg_match('/^\d+$/', $state) ? $state : 'N/A'),
                    TextColumn::make('start_time')
                    ->label('Reservation Time')
                    ->sortable()
                    ->dateTime('jS M Y')
                    ->description(function ($record) {
                        $startTime = Carbon::parse($record->start_time)->format('g:i A');
                        $endTime = Carbon::parse($record->end_time)->format('g:i A');

                        return $startTime . ' - ' . $endTime;
                    }),
            ])
            ->filters([
                DateRangeFilter::make('start_time')
                    ->label('Reservation Time :')
                    ->placeholder('Reservation Time'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(function ($livewire) {
                if (empty($livewire->tableSearch)) {
                    return 'No Reservations Found';
                } else {
                    return 'No Reservations Found For "'.$livewire->tableSearch.'"';
                }
            });
    }
}
