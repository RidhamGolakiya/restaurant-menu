<?php

namespace App\Filament\Restaurant\Resources\QrCodeResource\Pages;

use App\Filament\Restaurant\Resources\QrCodeResource;
use App\Filament\Restaurant\Resources\QrCodeResource\Widgets\QrCodeStats;
use App\Models\QrCodeAnalytic;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;


class ViewQrCode extends ViewRecord
{
    protected static string $resource = QrCodeResource::class;

    protected static string $view = 'filament.restaurant.resources.qr-code-resource.pages.view-qr-code';

    public ?string $startDate = null;
    public ?string $endDate = null;

    protected $listeners = ['updateDates'];

    public function mount(int|string $record): void
    {
        parent::mount($record);
        // Default to last 7 days
        $this->startDate = now()->subDays(6)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QrCodeStats::class,
        ];
    }

    public function getChartData(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->subDays(6)->startOfDay();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : now()->endOfDay();
        

        
        $query = QrCodeAnalytic::query();

        if ($this->record) {
            $query->where('qr_code_id', $this->record->id);
        }

        // Calculate diff in days to decide grouping
        $diffjs = $start->diffInDays($end);

        if ($diffjs <= 1) {
             // Hourly
             $groupBy = 'hour';
        } elseif ($diffjs > 60) {
             // Monthly
             $groupBy = 'month';
        } else {
             // Daily
             $groupBy = 'day';
        }

        // Adjust Query based on GroupBy
        if ($groupBy === 'hour') {
            $data = $query->selectRaw('HOUR(scanned_at) as label, count(*) as count')
                ->whereBetween('scanned_at', [$start, $end])
                ->groupBy('label')
                ->orderBy('label')
                ->pluck('count', 'label');
                
            $labels = [];
            $counts = [];
            for ($i=0; $i < 24; $i++) { 
                $labels[] = sprintf('%02d:00', $i);
                $counts[] = $data[$i] ?? 0;
            }
        
        } elseif ($groupBy === 'month') {
             $data = $query->selectRaw('DATE_FORMAT(scanned_at, "%Y-%m") as label, count(*) as count')
                ->whereBetween('scanned_at', [$start, $end])
                ->groupBy('label')
                ->orderBy('label')
                ->pluck('count', 'label');

            $labels = [];
            $counts = [];
            $current = $start->copy();
            while ($current->format('Y-m') <= $end->format('Y-m')) {
                $key = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $counts[] = $data[$key] ?? 0;
                $current->addMonth();
            }

        } else { // Day
            $data = $query->selectRaw('DATE(scanned_at) as label, count(*) as count')
                ->whereBetween('scanned_at', [$start, $end])
                ->groupBy('label')
                ->orderBy('label')
                ->pluck('count', 'label');

            $labels = [];
            $counts = [];
            $current = $start->copy();
            while ($current <= $end) {
                $key = $current->format('Y-m-d');
                $labels[] = $current->format('M d');
                $counts[] = $data[$key] ?? 0;
                $current->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Scans',
                    'data' => $counts,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getChartType(): string
    {
        return 'line';
    }

    public function getChartOptions(): array
    {
        return [];
    }

    public function updatedStartDate(): void
    {
        $this->dispatch('chart-updated');
    }

    public function updatedEndDate(): void
    {
        $this->dispatch('chart-updated');
    }
}
