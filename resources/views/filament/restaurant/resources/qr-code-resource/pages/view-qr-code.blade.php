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
        
        <!-- Chart Section -->
        <x-filament::section>
            <x-slot name="heading">
                Scan Analytics
            </x-slot>
            
            <div class="flex items-center gap-2 mb-4">
                <x-filament::input.wrapper inline-prefix>
                    <x-filament::input
                        type="date"
                        wire:model.live="startDate"
                    />
                </x-filament::input.wrapper>
                <span class="text-gray-500">-</span>
                <x-filament::input.wrapper inline-prefix>
                    <x-filament::input
                        type="date"
                        wire:model.live="endDate"
                    />
                </x-filament::input.wrapper>
            </div>
            
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                x-data="{
                    chart: null,
                    init() {
                        this.updateChart();
                        
                        // Watch for Livewire updates
                        \$wire.\$on('chart-updated', () => {
                            this.updateChart();
                        });
                    },
                    updateChart() {
                        // Destroy existing chart if present
                        if (this.chart && typeof this.chart.destroy === 'function') {
                            this.chart.destroy();
                        }
                        
                        // Initialize new chart with current data
                        this.chart = chart({
                            cachedData: @js($this->getChartData()),
                            options: @js($this->getChartOptions()),
                            type: @js($this->getChartType()),
                        });
                    }
                }"
                class="fi-wi-chart fi-color-gray"
            >
                <canvas
                    x-ref="canvas"
                    style="max-height: 300px"
                ></canvas>

                <span
                    x-ref="backgroundColorElement"
                    class="text-gray-100 dark:text-gray-800"
                ></span>

                <span
                    x-ref="borderColorElement"
                    class="text-gray-400"
                ></span>

                <span
                    x-ref="gridColorElement"
                    class="text-gray-200 dark:text-gray-800"
                ></span>

                <span
                    x-ref="textColorElement"
                    class="text-gray-500 dark:text-gray-400"
                ></span>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
