@php
    $timezone = isset($restaurant) && $restaurant?->timezone ? $restaurant->timezone : config('app.timezone');
@endphp

<div class="flex flex-col items-end mr-4 sm:mr-8 text-right">
    <div class="text-base font-semibold text-gray-800 dark:text-gray-100">
        {{ now($timezone)->format('h:i A') }}
    </div>
    <div class="text-xs text-gray-800 dark:text-gray-400 sm:text-sm">
        {{ now($timezone)->format('l, F d, Y') }}
    </div>
</div>
