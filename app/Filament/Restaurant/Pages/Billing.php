<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\Plan;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.restaurant.pages.billing';

    protected static ?string $navigationGroup = 'Settings';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 10;

    public function getViewData(): array
    {
        return [
            'plans' => Plan::where('is_active', true)->get(),
            'currentRestaurant' => Auth::user()->restaurant ?? null,
            'pendingRequest' => Auth::user()->restaurant->planRequests()->where('status', 'pending')->first(),
        ];
    }

    public function requestPlan(int $planId)
    {
        $restaurant = Auth::user()->restaurant;
        
        if (!$restaurant) {
            return;
        }

        // Check if there is already a pending request
        if ($restaurant->planRequests()->where('status', 'pending')->exists()) {
            $this->dispatch('notify', [
                'status' => 'danger',
                'message' => 'You already have a pending plan change request.',
            ]);
            return;
        }

        // Create new request
        $restaurant->planRequests()->create([
            'plan_id' => $planId,
            'status' => 'pending',
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Plan change requested successfully.')
            ->success()
            ->send();
    }
}
