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

    public function getTitle(): string
    {
        return 'Restaurant Details';
    }

    public function mount(): void
    {
        // Load restaurant details with timing slots
        $restaurant = auth()->user()->load(['restaurant.timingSlots'])->restaurant;
        $this->data1 = $restaurant->toArray();

        // Convert snake_case relationship key to camelCase for Repeater
        $existingSlots = $this->data1['timing_slots'] ?? [];
        
        // Ensure all 7 days are present
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $completeSlots = [];

        foreach ($days as $day) {
            // Find existing slot for this day
            $found = null;
            foreach ($existingSlots as $slot) {
                if ($slot['day_name'] === $day) {
                    $found = $slot;
                    break;
                }
            }

            if ($found) {
                $completeSlots[] = $found;
            } else {
                // Add empty slot for missing day
                $completeSlots[] = [
                    'day_name' => $day,
                    'open_time' => null,
                    'close_time' => null,
                ];
            }
        }

        $this->data1['timingSlots'] = $completeSlots;
        unset($this->data1['timing_slots']);

        $this->form1->fill($this->data1);
    }

    protected function getForms(): array
    {
        return [
            'form1',
        ];
    }

    public function form1(Form $form1): Form
    {
        return $form1
            ->model(auth()->user()->restaurant)
            ->schema([
                \Filament\Forms\Components\Tabs::make('RestaurantTabs')
                    ->tabs([
                        \Filament\Forms\Components\Tabs\Tab::make('General Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Group::make([
                                    TextInput::make('name')
                                        ->label('Restaurant Name')
                                        ->placeholder('Restaurant Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->label('URL Slug')
                                        ->placeholder('Slug')
                                        ->regex('/^[a-zA-Z0-9-]+$/')
                                        ->unique('restaurants', 'slug', ignoreRecord: true)
                                        ->required(),
                                    PhoneInput::make('phone')
                                        ->label('Phone Number')
                                        ->placeholder('Phone Number')
                                        ->required(),
                                    TextInput::make('restaurant_website_link')
                                        ->label('Website')
                                        ->placeholder('https://...')
                                        ->url(),
                                ])->columns(2),
                                
                                Textarea::make('overview')
                                    ->label('Overview')
                                    ->rows(3)
                                    ->placeholder('Short description of your restaurant')
                                    ->maxLength(600)
                                    ->columnSpanFull(),

                                \Filament\Forms\Components\Section::make('Social Media')
                                    ->schema([
                                        Group::make()->schema([
                                            TextInput::make('social_links.instagram')->label('Instagram')->prefix('instagram.com/'),
                                            TextInput::make('social_links.facebook')->label('Facebook')->prefix('facebook.com/'),
                                            TextInput::make('social_links.twitter')->label('X (Twitter)')->prefix('x.com/'),
                                        ])->columns(3),
                                    ])->collapsible(),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('Location')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Group::make([
                                    TextInput::make('address')
                                        ->label('Address Line 1')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('address_2')
                                        ->label('Address Line 2')
                                        ->nullable()
                                        ->maxLength(255),
                                    TextInput::make('city')
                                        ->label('City')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('state')
                                        ->label('State')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('zip_code')
                                        ->label('Zip Code')
                                        ->required()
                                        ->maxLength(20),
                                    Select::make('country_id')
                                        ->label('Country')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->options(Country::pluck('name', 'id')),
                                    TextInput::make('google_map_link')
                                        ->label('Google Map Link')
                                        ->url()
                                        ->columnSpanFull(),
                                ])->columns(2),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->avatar()
                                    ->disk(config('app.media_disk'))
                                    ->collection(Restaurant::LOGO)
                                    ->maxSize(2048),
                                SpatieMediaLibraryFileUpload::make('hero-image')
                                    ->label('Hero Image')
                                    ->image()
                                    ->disk(config('app.media_disk'))
                                    ->collection(Restaurant::HERO_IMAGE)
                                    ->maxSize(2048),
                                SpatieMediaLibraryFileUpload::make('photos')
                                    ->label('Gallery Photos')
                                    ->image()
                                    ->panelLayout('grid')
                                    ->disk(config('app.media_disk'))
                                    ->collection(Restaurant::PHOTOS)
                                    ->multiple()
                                    ->maxSize(2048)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        \Filament\Forms\Components\Tabs\Tab::make('Business Hours')
                            ->icon('heroicon-o-clock')
                            ->schema([

                                \Filament\Forms\Components\Actions::make([
                                    \Filament\Forms\Components\Actions\Action::make('copyMonday')
                                        ->label('Copy Monday to All')
                                        ->icon('heroicon-m-clipboard-document-check')
                                        ->action(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set) {
                                            $state = $get('timingSlots');
                                            $mondaySlot = null;

                                            // Find Monday
                                            foreach ($state as $key => $slot) {
                                                if (($slot['day_name'] ?? '') === 'Monday') {
                                                    $mondaySlot = $slot;
                                                    break;
                                                }
                                            }

                                            if ($mondaySlot) {
                                                $open = $mondaySlot['open_time'];
                                                $close = $mondaySlot['close_time'];

                                                // Apply to all other slots
                                                foreach ($state as $key => $slot) {
                                                    $set("timingSlots.{$key}.open_time", $open);
                                                    $set("timingSlots.{$key}.close_time", $close);
                                                }
                                                
                                                Notification::make()
                                                    ->title('Copied Monday timings to all days')
                                                    ->success()
                                                    ->send();
                                            }
                                        }),
                                ]),
                                \Filament\Forms\Components\Repeater::make('timingSlots')
                                    ->label('Opening Hours')
                                    ->schema([
                                        TextInput::make('day_name')
                                            ->hiddenLabel()
                                            ->required()
                                            ->readOnly()
                                            ->columnSpan(1),
                                        \Filament\Forms\Components\TimePicker::make('open_time')
                                            ->hiddenLabel()
                                            ->required()
                                            ->seconds(false)
                                            ->columnSpan(1),
                                        \Filament\Forms\Components\TimePicker::make('close_time')
                                            ->hiddenLabel()
                                            ->required()
                                            ->seconds(false)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false),

                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data1');
    }

    public function save(): void
    {
        if (auth()->user()->email === config('app.demo_email')) {
            Notification::make()->danger()->title('Action not allowed in demo.')->send();
            return;
        }

        $data = $this->form1->getState();

        // Extract timing slots
        $timingSlots = $data['timingSlots'] ?? [];
        unset($data['timingSlots']);

        $restaurant = auth()->user()->restaurant;
        
        // Update Restaurant Details
        $restaurant->update($data);

        // Update Timing Slots
        $restaurant->timingSlots()->delete();
        if (!empty($timingSlots)) {
            $restaurant->timingSlots()->createMany($timingSlots);
        }

        Notification::make()
            ->success()
            ->title('Restaurant details updated successfully.')
            ->send();

        // Reload to reflect changes
        $this->redirect(route('filament.restaurant.pages.restaurant-details'), navigate: false);
    }
}
