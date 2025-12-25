<?php

namespace App\Filament\Restaurant\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Reservation;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingBooking extends BaseWidget
{
    protected static ?string $heading = 'Upcoming Reservations';
    protected static ?int $sort = 3;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reservation::query()
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->where('start_time', '>=', Carbon::now())
                    ->orderBy('start_time', 'asc')->take(5)
                    ->where('status', 1)
            )
            ->columns([
                Tables\Columns\TextColumn::make('table.name')
                    ->label('Table'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->description(function ($record) {
                        $phoneNumber = $record->customer?->phone;
                        return is_numeric($phoneNumber) ? formatPhoneNumber($record->customer?->country_code, $phoneNumber) : 'N/A';
                    })
                    ->label('Customer')
                    ->sortable(),

                Tables\Columns\TextColumn::make('no_of_person')
                    ->label('Person')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Reservation Time')
                    ->dateTime('jS M Y')
                    ->sortable()
                    ->description(function ($record) {
                        $startTime = Carbon::parse($record->start_time)->format('g:i A');
                        $endTime = Carbon::parse($record->end_time)->format('g:i A');

                        return $startTime . ' - ' . $endTime;
                    }),

            ])->paginated(false)
            ->emptyStateHeading('No Reservations Found');
    }
}
