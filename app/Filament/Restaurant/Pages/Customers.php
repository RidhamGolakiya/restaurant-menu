<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Tables;
use App\Models\Customer;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\RestaurantPanelMenuSorting;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Restaurant\Pages\ViewCustomer;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;

class Customers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $title = 'Customers';
    protected static ?int $navigationSort = RestaurantPanelMenuSorting::CUSTOMER->value;
    protected static string $view = 'filament.restaurant.pages.customers';

    public static function getNavigationGroup(): ?string
    {
        return 'Users';
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('add_customer')
                ->label('Create Customer')
                ->color('primary')
                ->form(Customer::getForm())
                ->modalWidth('md')
                ->modalHeading('Create New Customer')
                ->action(function (array $data) {
                    $phoneNo = phoneNumberSeparator($data['phone'])->phone;

                    if (Customer::where('phone', $phoneNo)->where('restaurant_id', auth()->user()->restaurant_id)->exists()) {
                        Notification::make()
                            ->title('Phone number already exists.')
                            ->danger()
                            ->send();

                        return;
                    }

                    Customer::create([
                        'name' => $data['name'],
                        'country_code' => phoneNumberSeparator($data['phone'])->country_code,
                        'phone' => $phoneNo,
                        'email' => $data['email'],
                        'restaurant_id' => auth()->user()->restaurant_id,
                    ]);

                    Notification::make()
                        ->title('Customer created successfully.')
                        ->success()
                        ->send();
                })
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()->where('restaurant_id', auth()->user()->restaurant_id)
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->formatStateUsing(fn($record) => is_numeric($record?->phone) ? formatPhoneNumber($record?->country_code, $record?->phone) : 'N/A')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('jS M Y, h:i A')
                    ->searchable(),
            ])
            ->filters([])
            ->actions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View')
                    ->url(fn($record) => ViewCustomer::getUrl(['record' => $record->id])),
                
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit')
                    ->form(Customer::getForm())
                    ->modalWidth('md')
                    ->modalHeading('Edit Customer')
                    ->action(function (array $data, Customer $record) {
                        $oldPhone = $record->phone;
                        $newPhone = phoneNumberSeparator($data['phone'])->phone;

                        if ($oldPhone !== $newPhone) {
                            if (Customer::where('phone', $newPhone)->where('restaurant_id', auth()->user()->restaurant_id)->exists()) {
                                Notification::make()
                                    ->title('Phone number already exists.')
                                    ->danger()
                                    ->send();

                                return;
                            }
                        }

                        $record->update([
                            'name' => $data['name'],
                            'country_code' => phoneNumberSeparator($data['phone'])->country_code,
                            'phone' => $newPhone,
                            'email' => $data['email'],
                        ]);

                        Notification::make()
                            ->title('Customer Updated Successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->actionsColumnLabel('Actions')
            ->bulkActions([])
            ->emptyStateHeading(function ($livewire) {
                if (empty($livewire->tableSearch)) {
                    return 'No Customers Found';
                } else {
                    return 'No Customers Found For "'.$livewire->tableSearch.'"';
                }
            });
    }
}
