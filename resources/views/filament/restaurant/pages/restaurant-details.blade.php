<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form1 }}
        
        <div class="flex justify-end gap-x-3">
             <x-filament::button type="submit">
                Update Details
             </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
