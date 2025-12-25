<div class="fi-dropdown-header flex w-full gap-2 p-1 text-sm  fi-dropdown-header-color-gray fi-color-gray">
    <button
        class="fi-dropdown-list-item flex w-full items-center gap-2 whitespace-nowrap rounded-md p-2 text-sm transition-colors duration-75 outline-none disabled:pointer-events-none disabled:opacity-70 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 fi-dropdown-list-item-color-gray fi-color-gray"
        x-on:click ="$dispatch('open-modal', {id: 'change-password-modal'})">
        <x-heroicon-o-key class="w-5 h-5 text-gray-400 dark:text-gray-500" />
        <span class="fi-dropdown-list-item-label flex-1 truncate text-start text-gray-700 dark:text-gray-200"
            style="">
            {{ __('Change Password') }}
        </span>
    </button>
    <x-filament::modal id="change-password-modal">
        <x-slot name="heading">
            {{ __('Change Password') }}
        </x-slot>
        <div>
            <x-filament-panels::form wire:submit="save">
                {{ $this->form }}
                <div class="flex justify-end">
                    <x-filament::button type="submit" class="px-6 mx-3">Save</x-filament::button>
                    <button type="button" x-on:click="close(); $wire.call('resetFormData')"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg  fi-btn-color-gray fi-color-gray fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 ring-1 ring-gray-950/10 dark:ring-white/20 fi-ac-action fi-ac-btn-action">Cancel</button>
                </div>
            </x-filament-panels::form>
        </div>
    </x-filament::modal>
</div>