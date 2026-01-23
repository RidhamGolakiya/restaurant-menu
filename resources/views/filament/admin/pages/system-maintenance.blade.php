<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-2">
        <x-filament::section>
            <x-slot name="heading">
                Database Upgrade
            </x-slot>

            <x-slot name="description">
                Run this command to update the database schema after a system update.
            </x-slot>

            <div class="prose dark:prose-invert">
                <p>This action runs standard Laravel migrations (`php artisan migrate --force`).</p>
                <p class="text-sm text-gray-500">Ensure you have a backup before running major upgrades.</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                System Backup
            </x-slot>

            <x-slot name="description">
                Create a full backup of your database and application files.
            </x-slot>

            <div class="prose dark:prose-invert">
                <p>This action creates a backup zip file stored in your configured storage command (`php artisan backup:run`).</p>
                <p class="text-sm text-gray-500">Backups are stored in `storage/app/Laravel` by default.</p>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
