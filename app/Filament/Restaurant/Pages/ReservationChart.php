<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantDay;
use App\Models\RestaurantTimingSlot;
use App\Models\Table;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ReservationChart extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public $modalTable;

    public $modalDate;

    public $modalStart;

    public $modalEnd;

    public $modalPerson;

    public $modalCapacity;

    public $modalEmail;

    public $modalPhone;

    public $customers;

    public $selectedCustomerId;

    public $numberOfPersons;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.restaurant.pages.reservation-chart';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = ' ';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(route('filament.restaurant.pages.reservations')),
        ];
    }

    public $selectedDate;

    public $startTime = '17:00';

    public $endTime = '23:00';

    public $timePerTable;

    public function mount()
    {
        $this->selectedDate = request()->get('selectedDate', now()->toDateString());
        $this->timePerTable = getUserSettings()['max_booking_time_per_table'] ?? 2;
        $this->customers = Customer::where('restaurant_id', auth()->user()->restaurant_id)->get();
    }

    public function getViewData(): array
    {
        $restaurantId = auth()->user()->restaurant_id;

        $tables = Table::where('restaurant_id', $restaurantId)->get();

        $slots = RestaurantTimingSlot::where('restaurant_id', $restaurantId)
            ->where('day_name', Carbon::parse($this->selectedDate)->format('l'))
            ->get();

        $reservations = Reservation::with('customer')
            ->where('restaurant_id', $restaurantId)
            ->whereDate('start_time', $this->selectedDate)
            ->where('status', 1)
            ->get();

        $restaurant = Restaurant::where('id', $restaurantId)->first();
        $timezone = $restaurant->timezone;
        return [
            'tables' => $tables,
            'reservations' => $reservations,
            'slots' => $slots,
            'selectedDate' => $this->selectedDate,
            'timePerTable' => $this->timePerTable,
            'timezone' => $timezone,
        ];
    }

    public function saveSlot()
    {
        $restaurantId = auth()->user()->restaurant_id; 
        $date = Carbon::parse($this->selectedDate);
        $dayName = (string) $date->format('l');

        $day = RestaurantDay::where('restaurant_id', $restaurantId)
            ->where('day_name', $dayName)
            ->first();

        if (! $day || ! $day->is_active) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('Restaurant is closed on '.$dayName)
                ->send();

            return;
        }

        if (empty($this->selectedCustomerId)) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('Please select a customer.')
                ->send();

            return;
        }
        if (empty($this->modalPerson)) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('Please add a number of persons.')
                ->send();

            return;
        }
        if (empty($this->modalEnd)) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('Please select an end time.')
                ->send();

            return;
        }

        $table = \App\Models\Table::find($this->modalTable);

        if ($table->capacity < $this->modalPerson) {
            redirect(url()->previous());

            return Notification::make()->title('Table capacity is '.$table->capacity)->danger()->send();
        }

        if (! $table) {
            Notification::make()
                ->danger()
                ->title('Invalid Table selected.')
                ->send();

            return;
        }

        // Build data array
        $data = [
            'table_id' => $this->modalTable,
            'date' => $this->modalDate,
            'customer_id' => $this->modalCapacity,
            'no_of_person' => $this->modalPerson,
            'start_time' => $this->modalStart,
            'end_time' => $this->modalEnd,
            'customer_id' => auth()->id(),
        ];

        $data['start_time'] = $data['date'].' '.$data['start_time'].':00';
        $timezone = auth()->user()->restaurant->timezone;
        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $data['start_time'], $timezone);
        $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $data['end_time'], $timezone);

        if ($endTime <= $startTime) {
            redirect(url()->previous());

            return Notification::make()->title('End Time should be greater than Start Time')->danger()->send();
        }

        $reserved = Reservation::where('table_id', $data['table_id'])
            ->where(function ($q) use ($data) {

                $q->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->exists();

        if ($reserved) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('Table is already reserved for this time slot')
                ->send();

            return;
        }

        if ($startTime->isToday() && $startTime->lessThan(now())) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('Please Choose a Start Time in the Future')
                ->send();

            return;
        }

        $restaurantTiming = RestaurantTimingSlot::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('day_name', Carbon::parse($data['date'])->format('l'))
            ->get();

        $reservationValid = false;

        if ($restaurantTiming->isEmpty()) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('No timing slots available for this day')
                ->send();

            return;
        }

        foreach ($restaurantTiming as $timing) {
            if (! is_object($timing)) {
                continue;
            }
            $openTime = Carbon::parse($data['date'] . ' ' . $timing->open_time, $timezone);
            $closeTime = Carbon::parse($data['date'] . ' ' . $timing->close_time, $timezone);

            if ($closeTime->lte($openTime)) {
                $closeTime->addDay();
                if ($endTime->lte($startTime)) {
                    $endTime->addDay();
                }
            }

            $startTime = Carbon::parse($data['start_time'], $timezone);
            $endTime = Carbon::parse($data['end_time'], $timezone);

            if ($startTime->gte($openTime) && $endTime->lt($closeTime)) {
                $reservationValid = true;
                break;
            }
        }

        if (! $reservationValid) {
            redirect(url()->previous());
            Notification::make()
                ->danger()
                ->title('Restaurant is closed for this time slot')
                ->send();

            return;
        }
        $restaurantId = auth()->user()->restaurant_id;

        $reservation = Reservation::create([
            'restaurant_id' => $restaurantId,
            'table_id' => $data['table_id'],
            'customer_id' => $this->selectedCustomerId,
            'no_of_person' => $data['no_of_person'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 1,
        ]);

        $reservation->reservation_unique_id = $reservation->generateUniqueReservationId($restaurantId);
        $reservation->save();

        redirect(url()->previous());
        Notification::make()
            ->success()
            ->title('Reservation Created Successfully')
            ->send();
    }
}
