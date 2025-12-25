<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ChangePassword extends Component implements HasForms
{
    use InteractsWithForms;
    public $current_password;
    public $new_password;
    public $confirm_password;

    protected $rules = [
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8',
        'confirm_password' => 'required|string|min:8|same:new_password',
    ];

    public function render()
    {
        return view('livewire.change-password');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->revealable()
                    ->password()
                    ->required()
                    ->placeholder('Enter your current password'),

                TextInput::make('new_password')
                    ->label('New Password')
                    ->revealable()
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->placeholder('Enter a new password'),

                TextInput::make('confirm_password')
                    ->label('Confirm Password')
                    ->revealable()
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->placeholder('Confirm your new password'),
            ]);
    }

    public function save()
    {
        if (auth()->user()->email === config('app.demo_email')) {
            Notification::make()
                ->danger()
                ->title('You are not allowed to perform this action.')
                ->send();

            return;
        }
        $this->validate();

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        if ($this->new_password != $this->confirm_password) {
            $this->addError('confirm_password', 'The password confirmation does not match.');
            return;
        }

        if ($this->new_password == $this->current_password) {
            $this->addError('new_password', 'The new password cannot be the same as the current password.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        Session::forget('password_hash_web');
        Auth::login($user);

        Notification::make()
            ->title('Password Updated')
            ->body('Your password has been successfully updated.')
            ->success()
            ->send();
        $this->dispatch('close-modal', id: 'change-password-modal');
        $this->reset(['current_password', 'new_password', 'confirm_password']);
    }
    #[On('close-modal')]

    public function resetFormData()
    {
        $this->resetValidation();
        $this->reset();
    }
}
