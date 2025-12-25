<div class="space-y-6">
    <x-filament-panels::page-header>
        <x-slot name="heading">
            Theme Settings
        </x-slot>
    </x-filament-panels::page-header>

    <div class="p-6 bg-white rounded-xl shadow-sm">
        {{ $this->form }}
        
        <div class="mt-6">
            <x-filament::button wire:click="save">
                Save Theme Settings
            </x-filament::button>
        </div>
    </div>
</div>