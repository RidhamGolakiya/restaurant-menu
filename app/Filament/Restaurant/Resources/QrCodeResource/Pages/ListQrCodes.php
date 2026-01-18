<?php

namespace App\Filament\Restaurant\Resources\QrCodeResource\Pages;

use App\Filament\Restaurant\Resources\QrCodeResource;
use App\Models\QrCode;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQrCodes extends ListRecords
{
    protected static string $resource = QrCodeResource::class;

    public function mount(): void
    {
        // Check if user has a QR code
        $restaurantId = auth()->user()->restaurant_id;
        $qrCode = QrCode::where('restaurant_id', $restaurantId)->first();

        if ($qrCode) {
            // Redirect to view/manage page of that QR code
            redirect()->route('filament.restaurant.resources.qr-codes.view', $qrCode->uuid);
        } else {
             // If not, maybe redirect to create? or just let them stay on list which will show empty state and create button
             // For better UX, let's redirect to create if none exists.
             redirect()->route('filament.restaurant.resources.qr-codes.create');
        }
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
