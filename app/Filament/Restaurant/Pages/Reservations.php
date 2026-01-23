<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Reservation;
use App\Models\RestaurantDay;
use App\Models\RestaurantTimingSlot;
use App\Models\Table as TableModel;
use App\RestaurantPanelMenuSorting;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class Reservations extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.restaurant.pages.reservations';

    protected static ?int $navigationSort = RestaurantPanelMenuSorting::RESERVATION->value;

    public ?array $editReservation = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Tables';
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('calendar_view')
                ->label('Calendar View')
                ->icon('heroicon-o-calendar-days')
                ->url(route('filament.restaurant.pages.reservation-chart'))
                ->color('primary'),

            Action::make('add_reservation')
                ->label('Add Reservation')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form(Reservation::getForm())
                ->before(function (array $data, $action) {
                    $startTime = $data['start_time'];
                    $endTime = $data['end_time'];

                    if ($endTime <= $startTime) {
                        Notification::make()->title('End Time should be greater than Start Time')->danger()->send();
                        $action->halt();
                    }
                })
                ->action(function (array $data, $action): void {
                    $reserved = Reservation::where('table_id', $data['table_id'])
                        ->where(function ($q) use ($data) {
                            $q->where('start_time', '<', $data['date'] . ' ' . $data['end_time'])
                                ->where('end_time', '>', $data['date'] . ' ' . $data['start_time']);
                        })
                        ->exists();

                    $restaurantId = auth()->user()->restaurant_id; // or from $data
                    $date = Carbon::parse($data['date']);
                    $dayName = (string) $date->format('l'); // e.g. "Monday"

                    $day = RestaurantDay::where('restaurant_id', $restaurantId)
                        ->where('day_name', $dayName)
                        ->first();

                    if (! $day || ! $day->is_active) {
                        Notification::make()
                            ->danger()
                            ->title('Restaurant is closed on ' . $dayName)
                            ->send();

                        $action->halt();
                    }

                    if ($reserved) {
                        Notification::make()
                            ->danger()
                            ->title('Table is already reserved for this time slot')
                            ->send();

                        $action->halt();
                    }
                    $timezone = auth()->user()->restaurant->timezone;
                    $startDateTime = Carbon::parse($data['date'] . ' ' . $data['start_time'], $timezone);
                    $endDateTime = Carbon::parse($data['date'] . ' ' . $data['end_time'], $timezone);

                    if ($startDateTime->isToday() && $startDateTime->lessThan(Carbon::now())) {
                        Notification::make()
                            ->danger()
                            ->title('Please choose a time in the future')
                            ->send();

                        $action->halt();
                    }


                    $restaurantTiming = RestaurantTimingSlot::where('restaurant_id', auth()->user()->restaurant_id)
                        ->where('day_name', Carbon::parse($data['date'])->format('l'))
                        ->get();

                    $reservationValid = false;

                    if ($restaurantTiming->isEmpty()) {
                        Notification::make()
                            ->danger()
                            ->title('No timing slots available for this day')
                            ->send();
                        $action->halt();
                    }

                    foreach ($restaurantTiming as $timing) {
                        if (! is_object($timing)) {
                            continue;
                        }

                        $openTime = Carbon::parse($data['date'] . ' ' . $timing->open_time, $timezone);
                        $closeTime = Carbon::parse($data['date'] . ' ' . $timing->close_time, $timezone);

                        if ($closeTime->lte($openTime)) {
                            $closeTime->addDay();
                            if ($endDateTime->lte($startDateTime)) {
                                $endDateTime->addDay();
                            }
                        }

                        $startDateTime = Carbon::parse($data['date'] . ' ' . $data['start_time'], $timezone);
                        $endDateTime = Carbon::parse($data['date'] . ' ' . $data['end_time'], $timezone);

                        if ($startDateTime->gte($openTime) && $endDateTime->lt($closeTime)) {
                            $reservationValid = true;
                            break;
                        }
                    }

                    if (! $reservationValid) {
                        Notification::make()
                            ->danger()
                            ->title('Restaurant is closed for this time slot')
                            ->send();
                        $action->halt();
                    }

                    $restaurantId = auth()->user()->restaurant_id;

                    $reservation = Reservation::create([
                        'restaurant_id' => $restaurantId,
                        'table_id' => $data['table_id'],
                        'customer_id' => $data['customer_id'],
                        'no_of_person' => $data['no_of_person'],
                        'start_time' => $data['date'] . ' ' . $data['start_time'],
                        'end_time' => $data['date'] . ' ' . $data['end_time'],
                        'status' => 1,
                    ]);

                    $reservation->reservation_unique_id = $reservation->generateUniqueReservationId($restaurantId);
                    $reservation->save();

                    Notification::make()
                        ->success()
                        ->title('Reservation successfully booked for ' . $data['no_of_person'] . ' person')
                        ->send();
                })
                ->modalHeading('Create Reservation')
                ->modalSubmitActionLabel('Create Reservation')
                ->modalWidth('lg')
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Reservation::query()->where('restaurant_id', auth()->user()->restaurant_id))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('reservation_unique_id')
                    ->label('Reservation ID')
                    ->default('N/A')
                    ->formatStateUsing(fn($record) => '#' . $record->reservation_unique_id),
                TextColumn::make('table.name')->searchable()->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->description(function ($record) {
                        $phoneNumber = $record->customer->phone;
                        $email = $record->customer->email;

                        return preg_match('/^\d+$/', $phoneNumber) ?
                            new HtmlString(formatPhoneNumber($record->customer->country_code, $phoneNumber) . '<br>' . $email) :
                            'N/A';
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
            ->filters([
                SelectFilter::make('table')
                    ->relationship('table', 'name')
                    ->placeholder('All Tables')
                    ->options(TableModel::query()->where('restaurant_id', auth()->user()->restaurant_id)->pluck('name', 'id'))
                    ->label('Table :')
                    ->native(false)
                    ->preload()
                    ->searchable(),
                DateRangeFilter::make('start_time')
                    ->label('Reservation Time :')
                    ->icon('heroicon-o-arrow-path')
                    ->placeholder('Reservation Time'),
            ])
            ->actionsColumnLabel('Actions')
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->iconButton()
                    ->form(function ($form) {
                        $form->statePath('editReservation');

                        return Reservation::getForm();
                    })
                    ->fillForm(function ($record) {
                        return [
                            'table_id' => $record->table_id,
                            'customer_id' => $record->customer_id,
                            'no_of_person' => $record->no_of_person,
                            'date' => Carbon::parse($record->start_time)->format('Y-m-d'),
                            'start_time' => Carbon::parse($record->start_time)->format('H:i'),
                            'end_time' => Carbon::parse($record->end_time)->format('H:i'),
                            'status' => $record->status,
                        ];
                    })
                    ->before(function (array $data, $action) {
                        $startTime = $data['start_time'];
                        $endTime = $data['end_time'];

                        if ($endTime <= $startTime) {
                            Notification::make()->title('End Time should be greater than Start Time')->danger()->send();
                            $action->halt();
                        }
                    })
                    ->action(function (array $data, $action, $record): void {

                        $restaurantId = auth()->user()->restaurant_id; // or from $data
                        $date = Carbon::parse($data['date']);
                        $dayName = (string) $date->format('l'); // e.g. "Monday"

                        $day = RestaurantDay::where('restaurant_id', $restaurantId)
                            ->where('day_name', $dayName)
                            ->first();

                        if (! $day || ! $day->is_active) {
                            Notification::make()
                                ->danger()
                                ->title('Restaurant is closed on ' . $dayName)
                                ->send();

                            $action->halt();
                        }
                        $timezone = auth()->user()->restaurant->timezone;
                        $startDateTime = Carbon::parse($data['date'] . ' ' . $data['start_time'], $timezone);
                        $endDateTime = Carbon::parse($data['date'] . ' ' . $data['end_time'], $timezone);

                        $reserved = Reservation::where('table_id', $data['table_id'])
                            ->where('id', '!=', $record->id)
                            ->where(function ($q) use ($startDateTime, $endDateTime) {
                                $q->where('start_time', '<', $endDateTime)
                                    ->where('end_time', '>', $startDateTime);
                            })
                            ->exists();

                        if ($reserved) {
                            Notification::make()
                                ->danger()
                                ->title('Table is already reserved for this time slot')
                                ->send();
                            $action->halt();
                        }


                        if ($startDateTime->isToday() && $startDateTime->lessThan(Carbon::now())) {
                            Notification::make()
                                ->danger()
                                ->title('Please choose a time in the future')
                                ->send();

                            $action->halt();
                        }

                        $restaurantTiming = RestaurantTimingSlot::where('restaurant_id', auth()->user()->restaurant_id)
                            ->where('day_name', Carbon::parse($data['date'])->format('l'))
                            ->get();

                        $reservationValid = false;

                        if ($restaurantTiming->isEmpty()) {
                            Notification::make()
                                ->danger()
                                ->title('No timing slots available for this day')
                                ->send();
                            $action->halt();
                        }

                        foreach ($restaurantTiming as $timing) {
                            if (! is_object($timing)) {
                                continue;
                            }

                            $openTime = Carbon::parse($data['date'] . ' ' . $timing->open_time, $timezone);
                            $closeTime = Carbon::parse($data['date'] . ' ' . $timing->close_time, $timezone);

                            if ($closeTime->lte($openTime)) {
                                $closeTime->addDay();
                                if ($endDateTime->lte($startDateTime)) {
                                    $endDateTime->addDay();
                                }
                            }

                            $startDateTime = Carbon::parse($data['date'] . ' ' . $data['start_time']);
                            $endDateTime = Carbon::parse($data['date'] . ' ' . $data['end_time']);

                            if ($startDateTime->gte($openTime) && $endDateTime->lt($closeTime)) {
                                $reservationValid = true;
                                break;
                            }
                        }

                        // if (! $reservationValid) {
                        //     Notification::make()
                        //         ->danger()
                        //         ->title('Restaurant is closed for this time slot')
                        //         ->send();
                        //     $action->halt();
                        // }

                        // Save updates
                        $record->update([
                            'table_id' => $data['table_id'],
                            'customer_id' => $data['customer_id'],
                            'no_of_person' => $data['no_of_person'],
                            'start_time' => $startDateTime,
                            'end_time' => $endDateTime,
                            'status' => $data['status'],
                            'reservation_unique_id' => $record->generateUniqueReservationId($record->restaurant_id),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Reservation Updated Successfully')
                            ->send();
                    })
                    ->modalWidth('lg'),
                DeleteAction::make()->label('Delete')->iconButton()->color('danger')
                    ->successNotificationTitle('Reservation Deleted Successfully'),
            ])
            ->bulkActions([])
            ->emptyStateHeading(function ($livewire) {
                if (empty($livewire->tableSearch)) {
                    return 'No Reservations Found';
                } else {
                    return 'No Reservations Found For "' . $livewire->tableSearch . '"';
                }
            });
    }
}
