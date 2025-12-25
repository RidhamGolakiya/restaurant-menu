<?php

namespace App\Http\Responses;

use App\Models\Role;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        if ($user && $user->status == 1) {
            return redirect()->route('filament.restaurant.pages.dashboard');
        }

        session()->flush();
        
        Notification::make()
            ->danger()
            ->title('Your account is inactive')
            ->send();

        return redirect()->route('filament.restaurant.auth.login');
    }
}
