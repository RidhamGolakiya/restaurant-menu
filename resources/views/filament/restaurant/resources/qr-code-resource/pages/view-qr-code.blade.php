@php
    use Filament\Support\Facades\FilamentView;
@endphp

<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-filament::section>
            <x-slot name="heading">
                QR Code
            </x-slot>
            
            <div class="flex flex-col items-center justify-center p-4">
                <div class="mb-4">
                    <img src="data:image/png;base64,{{ base64_encode($record->qr_image) }}" alt="QR Code" />
                </div>
                
                <div class="flex gap-2 mt-4">
                    <x-filament::button
                        href="data:image/png;base64,{{ base64_encode($record->qr_image) }}"
                        tag="a"
                        target="_blank"
                        download="qr-code-{{ $record->name ?? $record->uuid }}.png"
                    >
                        Download QR Code
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
