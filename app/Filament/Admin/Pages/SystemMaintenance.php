<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class SystemMaintenance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'filament.admin.pages.system-maintenance';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    public function getTitle(): string
    {
        return 'System Maintenance';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upgrade_database')
                ->label('Upgrade Database')
                ->color('primary')
                ->icon('heroicon-o-circle-stack')
                ->requiresConfirmation()
                ->modalHeading('Upgrade Database')
                ->modalDescription('Are you sure you want to run the database migrations? This will modify the database structure.')
                ->modalSubmitActionLabel('Yes, run migrations')
                ->action(function () {
                    try {
                        $output = new BufferedOutput();
                        Artisan::call('migrate', ['--force' => true], $output);
                        
                        Notification::make()
                            ->title('Database upgraded successfully')
                            ->body(nl2br($output->fetch()))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Database upgrade failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('backup_database')
                ->label('Backup Database')
                ->color('success')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->requiresConfirmation()
                ->modalHeading('Backup Database')
                ->modalDescription('This will create a new backup of the database and files. This process might take a few moments.')
                ->modalSubmitActionLabel('Start Backup')
                ->action(function () {
                    try {
                        $output = new BufferedOutput();
                        Artisan::call('backup:run', [], $output);
                        
                        Notification::make()
                            ->title('Backup created successfully')
                            ->body(nl2br($output->fetch()))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Backup creation failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
