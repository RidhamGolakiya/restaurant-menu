<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Country;
use App\Models\Restaurant;
use App\Models\RestaurantDay;
use App\RestaurantPanelMenuSorting;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Support\Str;

class RestaurantDetails extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static string $view = 'filament.restaurant.pages.restaurant-details';

    protected static ?int $navigationSort = RestaurantPanelMenuSorting::RESTAURANTS_DETAILS->value;

    public ?array $data1 = [];

    public ?array $data2 = [];

    public array $timingData = [];


    public function getTitle(): string
    {
        return '';
    }

    public function mount(): void
    {
        // Load restaurant details
        $this->data1 = auth()->user()->load('restaurant')->restaurant->toArray();

        $this->form1->fill($this->data1);

        $this->timingData;

        $this->loadTimingData();
    }

    protected function loadTimingData(): void
    {
        $restaurantId = auth()->user()->restaurant_id;
        $days = RestaurantDay::where('restaurant_id', $restaurantId)->get();

        $timingData = [];

        foreach ($days as $day) {
            $slots = $day->timingSlots;

            $timingData[$day->day_name] = [
                'is_active' => $day->is_active,
                'slots' => $slots->map(fn($slot) => [
                    'open_time' => $slot->open_time,
                    'close_time' => $slot->close_time,
                ])->toArray(),
            ];
        }

        $this->timingData = $timingData;
    }

    protected function getForms(): array
    {
        return [
            'form1',
        ];
    }

    public function form1(Form $form1): Form
    {
        $photos = auth()->user()->restaurant->getMedia(Restaurant::PHOTOS);

        return $form1
            ->model(auth()->user())
            ->schema([
                Group::make([
                    TextInput::make('name')
                        ->label('Restaurant Name')
                        ->placeholder('Restaurant Name')
                        ->required()
                        ->maxLength(255)
                        ->live()
                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->label('URL:')
                        ->placeholder('Slug')
                        ->regex('/^[a-zA-Z0-9-]+$/')
                        ->rule('unique:restaurants,slug,' . auth()->user()->restaurant_id)
                        ->required(),
                    PhoneInput::make('phone')
                        ->label('Phone Number:')
                        ->placeholder('Phone Number')
                        ->required(),
                    Select::make('timezone')
                        ->label('Time Zone:')
                        ->options(getTimeZone())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),
                ])->columns(4),
                Group::make([
                    Textarea::make('overview')
                        ->label('Overview:')
                        ->rows(3)
                        ->placeholder('Overview')
                        ->maxLength(600),

                ])->columns(1),
                Group::make([
                    TextInput::make('google_map_link')
                        ->label('Google Map Link:')
                        ->placeholder('Google Map Link')
                        ->url()
                        ->rule('regex:/^(https?:\/\/)?(www\.)?(google\.[a-z.]+\/maps|goo\.gl\/maps)\/.+$/i'),
                    TextInput::make('restaurant_website_link')
                        ->label('Restaurant Website:')
                        ->placeholder('Restaurant Website Link')
                        ->url(),
                ])->columns(2),
                Group::make([
                    Textarea::make('address')
                        ->label('Address 1:')
                        ->rows(3)
                        ->placeholder('Address')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('address_2')
                        ->label('Address 2:')
                        ->placeholder('Address 2')
                        ->nullable()
                        ->rows(3)
                        ->maxLength(255),
                ])->columns(2),
                Group::make([
                    TextInput::make('zip_code')
                        ->label('Zip Code:')
                        ->placeholder('Zip Code')
                        ->required()
                        ->numeric()
                        ->maxLength(20),
                    Select::make('country_id')
                        ->label('Country:')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->optionsLimit(Country::count())
                        ->native(false)
                        ->options(Country::pluck('name', 'id')->toArray()),
                    TextInput::make('state')
                        ->label('State:')
                        ->placeholder('State')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('city')
                        ->label('City:')
                        ->placeholder('City')
                        ->required()
                        ->maxLength(255),
                ])->columns(4),

                Group::make()->relationship('restaurant')->schema([
                    SpatieMediaLibraryFileUpload::make('hero-image')
                        ->label('Hero Image:')
                        ->image()
                        ->disk(config('app.media_disk'))
                        ->collection(Restaurant::HERO_IMAGE)
                        ->maxSize(2048),
                    SpatieMediaLibraryFileUpload::make('photos')
                        ->label('Photos:')
                        ->image()
                        ->panelLayout('grid')
                        ->extraAttributes([
                            'class' => 'custom-photo-upload-container',
                        ])
                        ->disk(config('app.media_disk'))
                        ->collection(Restaurant::PHOTOS)
                        ->multiple()
                        ->maxSize(2048),
                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label('Logo:')
                        ->image()
                        ->avatar()
                        ->disk(config('app.media_disk'))
                        ->collection(Restaurant::LOGO)
                        ->maxSize(2048),
                ])->columns(2),
            ])->statePath('data1')->columns(1);
    }

    public function getFormActions(): array
    {
        return [
            Action::make('saveAll')
                ->extraAttributes(['id' => 'full-save'])
                ->label('Update Details')
                ->submit('saveAll'),
        ];
    }

    public function saveAll(array $timings): void
    {
        $this->saveFromBrowser($timings);
        $this->save();
    }

    public function save(): void
    {
        if (auth()->user()->email === config('app.demo_email')) {
            Notification::make()
                ->danger()
                ->title('You are not allowed to perform this action.')
                ->send();

            return;
        }

        $data = $this->form1->getState();

        $restaurant = auth()->user()->restaurant;
        $restaurant->update($data);

        Notification::make()
            ->success()
            ->title('Restaurant details updated successfully.')
            ->send();

        $this->redirect(route('filament.restaurant.pages.restaurant-details'), navigate: false);
    }

    public function saveFromBrowser(array $timings)
    {
        $restaurantId = auth()->user()->restaurant_id;

        foreach ($timings as $dayName => $data) {
            $slots = $data['slots'] ?? [];

            $day = RestaurantDay::updateOrCreate(
                [
                    'restaurant_id' => $restaurantId,
                    'day_name' => $dayName,
                ],
                [
                    'is_active' => $data['is_active'] ?? false,
                ]
            );

            $day->timingSlots()->delete();

            foreach ($slots as $slot) {
                $day->timingSlots()->create([
                    'restaurant_id' => $restaurantId,
                    'day_name' => $day->day_name,
                    'open_time' => $slot['open_time'],
                    'close_time' => $slot['close_time'],
                ]);
            }
        }
    }
}
