<?php

namespace App\Filament\Restaurant\Resources\CustomerResource\Widgets;

use App\Filament\Restaurant\Pages\ViewCustomer;
use App\Models\Customer;
use App\Models\Reservation;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class CustomerReservationTable extends BaseWidget
{
    public ?Model $record = null;

    public function getTableHeading(): string
    {
        return '';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reservation::query()->where('customer_id', $this->record->id)
            )

            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('table.name')->searchable()->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->description(function ($record) {
                        return formatPhoneNumber($record->customer->country_code, $record->customer->phone) ?? 'N/A';
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_of_person')->label('Number of Person')->searchable()->sortable()->alignCenter(),
                TextColumn::make('start_time')
                    ->label('Reservation Time')
                    ->dateTime('jS M Y')
                    ->description(function ($record) {
                        $startTime = Carbon::parse($record->start_time)->format('g:i A');
                        $endTime = Carbon::parse($record->end_time)->format('g:i A');

                        return $startTime . ' - ' . $endTime;
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('jS M Y')
                    ->description(function ($record) {
                        return $record->created_at ? \Carbon\Carbon::parse($record->created_at)->translatedFormat('g:i A') : '';
                    }),
                ToggleColumn::make('status')
                    ->label('Status')
                    ->afterStateUpdated(function ($state, $record) {
                        $record->update(['status' => $state]);

                        Notification::make()
                            ->success()
                            ->title('Reservation Status Updated Successfully')
                            ->send();
                    }),
            ])
            ->emptyStateHeading(function ($livewire) {
                if (empty($livewire->tableSearch)) {
                    return 'No Reservations Found';
                } else {
                    return 'No Reservations Found For "' . $livewire->tableSearch . '"';
                }
            });
    }
}
