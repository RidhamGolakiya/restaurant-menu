<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile;

class CustomEditProfile extends EditProfile
{
    public static function getLabel(): string
    {
        return __('Account Settings');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make(__('Your Account Information'))
                            ->columns(1)
                            ->schema([
                                // Group::make([
                                //     SpatieMediaLibraryFileUpload::make('profile')
                                //         ->label(__('Profile') . ':')
                                //         ->validationAttribute(__('Profile'))
                                //         ->disk(config('app.media_disk'))
                                //         ->collection(User::PROFILE)
                                //         ->image()
                                //         ->imagePreviewHeight(150)
                                //         ->imageEditor('cropper')
                                //         ->required(),
                                // ]),
                                Group::make([
                                    TextInput::make('name')
                                        ->label(__('Name') . ':')
                                        ->placeholder(__('Name'))
                                        ->validationAttribute(__('Name'))
                                        ->required()
                                        ->maxLength(255)
                                        ->autofocus(),
                                    TextInput::make('email')
                                        ->label(__('Email') . ':')
                                        ->placeholder(__('Email'))
                                        ->validationAttribute(__('Email'))
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true),
                                ])->columnSpan(3)->columns(1),
                            ]),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data'),
            ),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        if (auth()->user()->hasRole('admin')) {
            return route('filament.admin.pages.dashboard');
        }
        return route('filament.restaurant.pages.dashboard');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('Account Settings Updated Successfully');
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

        parent::save();
    }

}
