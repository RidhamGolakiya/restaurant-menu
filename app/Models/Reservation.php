<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use HusamTariq\FilamentTimePicker\Forms\Components\TimePickerField;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'table_id',
        'status',
        'no_of_person',
        'start_time',
        'end_time',
        'cancel_reason',
        'reservation_unique_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function generateUniqueReservationId(int $restaurantId): string
    {
        do {
            $alphaNumeric = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
            $numeric = rand(1000, 9999);
            $reservationId = $alphaNumeric . $numeric;

            $exists = Reservation::where('restaurant_id', $restaurantId)
                ->where('reservation_unique_id', $reservationId)
                ->exists();
        } while ($exists);

        return $reservationId;
    }

    public static function getForm(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('table_id')
                        ->options(function () {
                            return Table::where('restaurant_id', auth()->user()->restaurant_id)->pluck('name', 'id');
                        })
                        ->label('Table:')
                        ->required()
                        ->reactive()
                        ->native(false),

                    Select::make('customer_id')
                        ->options(function () {
                            return Customer::where('restaurant_id', auth()->user()->restaurant_id)->pluck('name', 'id');
                        })
                        ->label('Customer:')
                        ->required()
                        ->native(false),
                ]),

            TextInput::make('no_of_person')
                ->label('Number of Person:')
                ->placeholder('Number of Person')
                ->numeric()
                ->required()
                ->reactive()
                ->minValue(1)
                ->disabled(fn($get) => ! $get('table_id'))
                ->rules([
                    function ($get) {
                        return function ($attribute, $value, $fail) use ($get) {
                            $tableId = $get('table_id');
                            if (! $tableId) {
                                return;
                            }

                            $table = Table::find($tableId);
                            if ($value > $table->capacity) {
                                $fail('Number of person should not be greater than ' . $table->capacity);
                            }
                        };
                    },
                ]),

            DatePicker::make('date')
                ->label('Date:')
                ->required()
                ->minDate(Carbon::today())
                ->placeholder('Date')
                ->native(false),

            Grid::make(2)
                ->schema([
                    TimePickerField::make('start_time')
                        ->label('Start Time:')
                        ->required()
                        ->afterStateUpdated(function ($set, $get, $state) {
                            if ($state) {
                                $time = Carbon::parse($state)->setSeconds(0);
                                $set('start_time', $time->format('H:i'));
                            }
                        }),

                    TimePickerField::make('end_time')
                        ->label('End Time:')
                        ->live()
                        ->afterStateUpdated(function ($set, $get, $state) {
                            if ($state) {
                                $time = Carbon::parse($state)->setSeconds(0);
                                $set('end_time', $time->format('H:i'));
                            }
                            $startTime = $get('start_time');
                            $endTime = $get('end_time');

                            if (! $startTime || ! $endTime) {
                                return;
                            }

                            $gap = (float) getPerTableTime();

                            $start = \Carbon\Carbon::parse($startTime);
                            $end = \Carbon\Carbon::parse($endTime);

                            $allowedEndTime = $start->copy()->addHours($gap);

                            if ($end->greaterThan($allowedEndTime)) {
                                $set('end_time', $allowedEndTime->format('H:i'));
                            }
                        })
                        ->required(),

                ]),

            Toggle::make('status')
                ->label('Status')
                ->default(true),
        ];
    }
}