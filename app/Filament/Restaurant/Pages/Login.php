<?php

namespace App\Filament\Restaurant\Pages;

use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                Form::make($this)
                    ->schema([
                        TextInput::make('email')
                            ->label(__('filament-panels::pages/auth/login.form.email.label'))
                            ->email()
                            ->required()
                            ->placeholder('Email')
                            ->autocomplete()
                            ->autofocus()
                            ->extraInputAttributes(['tabindex' => 1]),
                        TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/login.form.password.label'))
                            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
                            ->placeholder('Password')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('current-password')
                            ->required()
                            ->extraInputAttributes(['tabindex' => 2]),
                        Checkbox::make('remember')
                            ->label(__('filament-panels::pages/auth/login.form.remember.label')),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->extraAttributes(['class' => 'w-full'])
            ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
            ->submit('authenticate');
    }
}
