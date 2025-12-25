<x-filament::link href="{{ route('restaurant.index', ['slug' => $slug]) }}" tag="a" color="primary" target="_blank">
    <button type="button"
        class="flex items-center justify-center gap-2 px-2 py-1 text-gray-950 dark:text-white border border-primary-600 focus:outline-none transition-colors rounded-md">
        Open Website<x-heroicon-m-arrow-top-right-on-square class="w-4 h-4" />
    </button>
</x-filament::link>
