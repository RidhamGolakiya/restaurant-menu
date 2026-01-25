<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static string $view = 'filament.admin.pages.site-settings';

    protected static ?string $title = 'Site Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = getGlobalSettings();
        
        // Decode JSON values for repeater
        foreach ($settings as $key => $value) {
            if (is_string($value) && is_array(json_decode($value, true))) {
                $settings[$key] = json_decode($value, true);
            }
        }

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Settings')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required(),
                        FileUpload::make('site_icon')
                            ->label('Site Icon')
                            ->image()
                            ->disk('public')
                            ->directory('site-assets')
                            ->visibility('public'),
                        FileUpload::make('site_favicon')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('site-assets')
                            ->visibility('public'),
                    ])->columns(2),

                Section::make('SEO Settings')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO Title')
                            ->required(),
                        Textarea::make('seo_description')
                            ->label('SEO Description')
                            ->rows(3),
                    ]),
                
                Section::make('Contact Support')
                    ->description('Contact details displayed on the "Restaurant Offline" page.')
                    ->schema([
                        TextInput::make('support_email')
                            ->label('Support Email')
                            ->email(),
                        TextInput::make('support_phone')
                            ->label('Support Phone'),
                        \Filament\Forms\Components\Repeater::make('support_whatsapp')
                            ->label('WhatsApp Numbers')
                            ->schema([
                                TextInput::make('number')
                                    ->label('WhatsApp Number')
                                    ->tel(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            
            Setting::updateOrCreate(
                ['key' => $key, 'user_id' => null],
                ['value' => $value]
            );
        }

        Notification::make()
            ->success()
            ->title('Settings saved successfully.')
            ->send();
    }
}
