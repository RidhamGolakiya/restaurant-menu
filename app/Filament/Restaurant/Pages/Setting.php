<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Currency;
use App\Models\Setting as SettingModel;
use App\RestaurantPanelMenuSorting;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Setting extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.restaurant.pages.setting';

    protected static ?int $navigationSort = RestaurantPanelMenuSorting::SETTINGS->value;

    protected static ?string $title = 'Settings';

    public ?array $data = null;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('restaurant') && auth()->user()->parent_user_id == null;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public function mount()
    {
            $data = getUserSettings();

        foreach ($data as $key => $value) {
            if ($key === 'vapi_auth_token') {
                try {
                    $data[$key] = decrypt($value);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $data[$key] = $value;
                }
            }
        }

        $this->form->fill($data);
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('max_booking_time_per_table')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('Max Booking Time Per Table')
                                    ->label('Max Booking Time Per Table (In Hours) :'),
                                Select::make('currency_id')
                                    ->options(Currency::pluck('name', 'id'))
                                    ->label('Currency:')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->optionsLimit(Currency::count())
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->icon} - {$record->name}")
                                    ->required(),
                            ])->columns(3),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {

        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($key === 'vapi_auth_token') {
                $value = encrypt($value);
            }
            SettingModel::updateOrCreate(
                ['key' => $key, 'user_id' => auth()->user()->id],
                ['value' => $value ?? '', 'user_id' => auth()->user()->id]
            );
        }

        Notification::make()
            ->success()
            ->title('Settings updated successfully.')
            ->send();
    }
}
