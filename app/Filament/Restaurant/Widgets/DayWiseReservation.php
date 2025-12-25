<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\Select;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DayWiseReservation extends ApexChartWidget
{
    protected static ?string $chartId = 'dayWiseReservation';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public string $headingTitle = 'Day Wise Appointments';

    protected function getHeading(): string
    {
        return $this->headingTitle;
    }

    protected static array $filterHeadings = [
        'today' => 'Today',
        'week' => 'Last 7 Days',
        'last_15_days' => 'Last 15 Days',
        'month' => 'This Month',
        'last_month' => 'Last Month',
        'year' => 'This Year',
    ];

    protected function getFormSchema(): array
    {
        return [
            Select::make('filter')
                ->label('Filter By')
                ->native(false)
                ->live()
                ->options([
                    'today' => 'Today',
                    'week' => 'Last 7 Days',
                    'last_15_days' => '(default) Last 15 Days',
                    'month' => 'This Month',
                    'last_month' => 'Last Month',
                    'year' => 'This Year',
                ])
                ->default('last_15_days')
                ->afterStateUpdated(function ($state) {
                    $this->filter = $state;
                }),
        ];
    }

    /**
     * Get start and end dates based on the selected filter.
     */
    private function getDateRange(string $filter)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'today':
                $this->filterFormData['date_start'] = $now->copy()->startOfDay();
                $this->filterFormData['date_end'] = $now->copy()->endOfDay();
                break;
            case 'week':
                $this->filterFormData['date_start'] = $now->copy()->subDays(6)->startOfDay();
                $this->filterFormData['date_end'] = $now->copy()->endOfDay();
                break;
            case 'last_15_days':
                $this->filterFormData['date_start'] = $now->copy()->subDays(14)->startOfDay();
                $this->filterFormData['date_end'] = $now->copy()->endOfDay();
                break;
            case 'month':
                $this->filterFormData['date_start'] = $now->copy()->startOfMonth();
                $this->filterFormData['date_end'] = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $this->filterFormData['date_start'] = $now->copy()->subMonth()->startOfMonth();
                $this->filterFormData['date_end'] = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'year':
                $this->filterFormData['date_start'] = $now->copy()->startOfYear();
                $this->filterFormData['date_end'] = $now->copy()->endOfYear();
                break;
            default:
                $this->filterFormData['date_start'] = $now->copy()->subDays(14)->startOfDay();
                $this->filterFormData['date_end'] = $now->copy()->endOfDay();
                break;
        }
    }

    /**
     * Chart configuration options.
     */
    protected function getOptions(): array
    {
        $filter = $this->filter ?? 'last_15_days';
        $title = self::$filterHeadings[$filter] ?? 'Day Wise Appointments';
        $this->headingTitle = $title;
        $this->getDateRange($filter);

        $labels = [];
        $data = [];

        if ($filter === 'year') {
            $months = range(1, 12);
            foreach ($months as $month) {
                $labels[] = date('F', mktime(0, 0, 0, $month, 1));
                $data[] = Reservation::whereMonth('start_time', $month)
                    ->where('restaurant_id', auth()->user()->restaurant_id)
                    ->whereYear('start_time', $this->filterFormData['date_start']->year)
                    ->count();
            }
        } else {
            $period = CarbonPeriod::create($this->filterFormData['date_start'], $this->filterFormData['date_end']);
            foreach ($period as $date) {
                $labels[] = $date->format('M d');
                $data[] = Reservation::where('restaurant_id', auth()->user()->restaurant_id)->whereDate('start_time', $date->toDateString())->count();
            }
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 400,
                'toolbar' => [
                    'show' => false,
                ],
                'animations' => [
                    'enabled' => true,
                    'easing' => 'easeinout',
                    'speed' => 800,
                    'animateGradually' => [
                        'enabled' => true,
                        'delay' => 150,
                    ],
                    'dynamicAnimation' => [
                        'enabled' => true,
                        'speed' => 350,
                    ],
                ],
                'zoom' => [
                    'enabled' => false,
                ],
                'dropShadow' => [
                    'enabled' => true,
                    'color' => '#000',
                    'top' => 18,
                    'left' => 7,
                    'blur' => 10,
                    'opacity' => 0.1,
                ],
            ],
            'series' => [
                [
                    'name' => 'Total Reservations',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 500,
                        'fontSize' => '12px',
                        'colors' => ['#64748b'],
                    ],
                ],
                'axisBorder' => [
                    'show' => false,
                ],
                'axisTicks' => [
                    'show' => false,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 500,
                        'fontSize' => '12px',
                        'colors' => ['#64748b'],
                    ],
                    'formatter' => 'function (value) { return Number.isInteger(value) ? value : ""; }',

                ],
                'forceNiceScale' => true,
                'tickAmount' => 6,
                'min' => 0,
                'axisBorder' => [
                    'show' => false,
                ],
                'axisTicks' => [
                    'show' => false,
                ],
            ],
            'colors' => [
                '#5b65d4', '#684395', '#d885e0', '#dc4a60', '#4692df', '#00d4df',
                '#3bd06d', '#32dac2', '#e6d5bd', '#e09c8d', '#96d1d5', '#e6c1ce',
                '#e1b6ae', '#e6bce6', '#abd3e0', '#8baee2', '#b87da8', '#e6e0b9',
                '#5b65d4', '#684395', '#d885e0', '#dc4a60', '#4692df', '#00d4df',
                '#3bd06d', '#32dac2', '#e6d5bd', '#e09c8d', '#96d1d5', '#e6c1ce',
            ],

            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 8,
                    'borderRadiusApplication' => 'end',
                    'horizontal' => false,
                    'columnWidth' => '50%',
                    'distributed' => true,
                ],
            ],

            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'light',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.4,
                    'inverseColors' => false,
                    'opacityFrom' => 1,
                    'opacityTo' => 0.7,
                    'stops' => [0, 100],
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'show' => true,
                'width' => 2,
                'colors' => ['rgba(255,255,255,0.3)'],
            ],
            'legend' => [
                'show' => false,
            ],
            'tooltip' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '14px',
                    'fontFamily' => 'inherit',
                ],
                'y' => [
                    'formatter' => "function(value) { return value + ' reservations'; }",
                ],
            ],
            'grid' => [
                'show' => true,
                'borderColor' => '#e2e8f0',
                'strokeDashArray' => 4,
                'xaxis' => [
                    'lines' => [
                        'show' => false,
                    ],
                ],
                'yaxis' => [
                    'lines' => [
                        'show' => true,
                    ],
                ],
                'padding' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ],
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return Number.isInteger(val) ? val : '';
                    }
                }
            }
        }
    JS);
    }
}
