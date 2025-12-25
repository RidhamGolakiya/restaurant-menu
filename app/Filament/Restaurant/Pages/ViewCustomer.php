<?php

namespace App\Filament\Restaurant\Pages;

use App\Filament\Restaurant\Resources\CustomerResource\Widgets\CustomerCallTable;
use App\Filament\Restaurant\Resources\CustomerResource\Widgets\CustomerReservationTable;
use Carbon\Carbon;
use App\Models\Customer;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\Tabs;

class ViewCustomer extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.restaurant.pages.view-customer';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'view-customer/{record}';

    public ?Customer $customer = null;

    public function mount($record): void
    {
        $this->customer = Customer::with('reservations.table')->findOrFail($record);
    }

    protected function getViewData(): array
    {
        return [
            'customer' => $this->customer,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(route('filament.restaurant.pages.customers')),
        ];
    }


    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->customer)
            ->schema([
                Section::make('Customer Information')->schema([
                    TextEntry::make('name'),
                    TextEntry::make('phone')->formatStateUsing(fn($record) => is_numeric($record?->phone) ? formatPhoneNumber($record?->country_code, $record?->phone) : 'N/A'),
                    TextEntry::make('email')->default('N/A'),
                ])->columns(3),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Reservation Details')
                            ->schema([
                                Livewire::make(CustomerReservationTable::class)
                            ]),
                    ])
            ]);
    }
}
