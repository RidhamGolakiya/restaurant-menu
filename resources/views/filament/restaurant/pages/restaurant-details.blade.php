<x-filament-panels::page>
    <style>
        /* ----- DARK MODE STYLES ----- */
        html.dark .select2-container--default .select2-selection--single {
            height: 2.25rem !important;
            border-radius: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 0.75rem !important;
            background-color: #343437 !important;
            border-color: #606060dd !important;
            color: #ffffff !important;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        html.dark .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: normal !important;
            color: #ffffff !important;
        }

        html.dark .select2-selection__arrow,
        html.dark .select2-selection__clear {
            top: 50% !important;
            transform: translateY(-50%);
            right: 0.75rem !important;
        }

        html.dark .select2-dropdown {
            border-radius: 0.5rem !important;
            font-size: 0.875rem;
            padding: 0.25rem 0;
        }

        html.dark .select2-container--default .select2-dropdown {
            background-color: #343437 !important;
            border-color: #4b5563 !important;
            color: #ffffff !important;
        }

        html.dark .select2-results__option {
            background-color: #343437 !important;
            color: #ffffff !important;
        }

        html.dark .select2-results__option--highlighted {
            background-color: #f59e0b !important;
            color: #1f2937 !important;
        }

        html.dark .select2-search__field {
            background-color: #343437 !important;
            color: #ffffff !important;
            border-radius: 0.5rem !important;
            border: 1px solid #4b5563 !important;
        }

        /* ----- LIGHT MODE STYLES ----- */
        .select2-container--default .select2-selection--single {
            height: 2.25rem !important;
            border-radius: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 0.75rem !important;
            background-color: #ffffff !important;
            border: 1px solid #d1d5db !important;
            /* gray-300 */
            color: #111827 !important;
            /* gray-900 */
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: normal !important;
            color: #111827 !important;
        }

        .select2-selection__arrow,
        .select2-selection__clear {
            top: 50% !important;
            transform: translateY(-50%);
            right: 0.75rem !important;
        }

        .select2-dropdown {
            border-radius: 0.5rem !important;
            font-size: 0.875rem;
            padding: 0.25rem 0;
        }

        .select2-container--default .select2-dropdown {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            /* gray-200 */
            color: #111827 !important;
        }

        .select2-results__option {
            background-color: #ffffff !important;
            color: #111827 !important;
        }

        .select2-results__option--highlighted {
            background-color: #f59e0b !important;
            color: #1f2937 !important;
        }

        .select2-search__field {
            background-color: #ffffff !important;
            color: #111827 !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
        }

        /* COMMON FOCUS STYLES for BOTH MODES */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #f59e0b !important;
            outline: none;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.5) !important;
        }

        .select2-search__field:focus {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 1px #f59e0b !important;
        }

        .slot-row {
            min-width: 40%;
        }

        .custom-photo-upload-container {
            border: 1px solid #e4e4e7;
            border-radius: .45em;
            overflow-y: auto;
            max-height: 350px;
        }

        .dark .custom-photo-upload-container {
            border: 1px solid #5d5d60;
        }

        .scrollable {
            overflow-y: auto;
            max-height: 350px;
        }

        /* LAYOUT GRID SUPPORT */
        @media (min-width: 1024px) {
            .restaurant-details-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
        }
    </style>

    <!-- Right Section - Restaurant Details -->
    <div class="section-card">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg h-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-building-storefront class="w-5 h-5 mr-2" />
                    Restaurant Details
                </h2>
            </div>
            <div class="p-6">
                <x-filament-panels::form wire:submit="saveAll">
                    {{ $this->form1 }}
                    <h4 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4 mr-2" />
                        <span> Business Hours</span>
                    </h4>
                    <div class="pb-4">
                        <form id="timingForm" class="space-y-4">
                            <div id="timingWrapper" class="flex flex-col gap-4" wire:ignore>
                                <!-- Day sections will be dynamically inserted here -->
                            </div>
                        </form>
                    </div>
                    {{-- {{ $this->form2 }} --}}
                    <x-filament-panels::form.actions :actions="$this->getFormActions()" />
                </x-filament-panels::form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageUploadContainer = document.querySelector('.custom-photo-upload-container');
            const images = imageUploadContainer.querySelectorAll('img');

            if (images.length > 3) {
                imageUploadContainer.classList.add('scrollable');
            }

            imageUploadContainer.addEventListener('change', function() {
                const newImages = imageUploadContainer.querySelectorAll('img');
                if (newImages.length > 3) {
                    imageUploadContainer.classList.add('scrollable');
                } else {
                    imageUploadContainer.classList.remove('scrollable');
                }
            });
        });
    </script>

    <script>
        window.existingTimings = @json($timingData);
    </script>
</x-filament-panels::page>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const existingTimings = window.existingTimings || {};

        function getTimeOptions(selected) {
            let options = '';

            for (let i = 0; i < 24; i++) {
                const value = `${i.toString().padStart(2, '0')}:00`;

                if (value === '24:00') continue;

                const label =
                    i === 0 ? '12:00 AM' :
                    i < 12 ? `${i}:00 AM` :
                    i === 12 ? '12:00 PM' :
                    `${i - 12}:00 PM`;
                options += `<option value="${value}" ${value === selected ? 'selected' : ''}>${label}</option>`;
            }

            options += `<option value="23:59" ${selected === '23:59' ? 'selected' : ''}>11:59 PM</option>`;

            return options;
        }

        function timeToMinutes(time) {
            if (!time) return null;
            const [h, m] = time.split(':').map(Number);
            return h * 60 + m;
        }

        function buildTimeOptions({
            selected = null,
            min = null,
            max = null,
            isClose = false,
        }) {
            let options = '<option value=""></option>';

            for (let i = 0; i < 24; i++) {
                const value = `${i.toString().padStart(2, '0')}:00`;
                const minutes = i * 60;

                if (min !== null && minutes <= min) continue;
                if (max !== null && minutes >= max) continue;

                const label =
                    i === 0 ? '12:00 AM' :
                    i < 12 ? `${i}:00 AM` :
                    i === 12 ? '12:00 PM' :
                    `${i - 12}:00 PM`;

                options += `<option value="${value}" ${value === selected ? 'selected' : ''}>${label}</option>`;
            }

            if (isClose && (max === null || max > 1439)) {
                options += `<option value="23:59" ${selected === '23:59' ? 'selected' : ''}>11:59 PM</option>`;
            }

            return options;
        }

        function updateRowTimeLimits($row) {
            const $open = $row.find('.open_time_select');
            const $close = $row.find('.close_time_select');

            const openVal = $open.val();
            const closeVal = $close.val();

            const openMin = timeToMinutes(openVal);
            const closeMin = timeToMinutes(closeVal);

            // CLOSE → must be AFTER open (can be 24:00)
            $close.html(
                buildTimeOptions({
                    selected: closeVal,
                    min: openMin,
                    isClose: true,
                })
            ).trigger('change.select2');

            // OPEN → must be BEFORE close (never 24:00)
            $open.html(
                buildTimeOptions({
                    selected: openVal,
                    max: closeMin,
                    isClose: false,
                })
            ).trigger('change.select2');

            // Clear invalid state
            if (openMin !== null && closeMin !== null && closeMin <= openMin) {
                $close.val(null).trigger('change.select2');
            }
        }

        function normalize(time) {
            if (!time) return '';
            const [h, m] = time.split(':');
            return `${h.padStart(2, '0')}:${m.padStart(2, '0')}`;
        }

        function createSlotRow(day, open = '', close = '') {
            if (!open) open = '00:00';
            if (!close) close = '23:59';

            open = normalize(open);
            close = normalize(close);

            const selectClass =
                "select2-time block rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500";

            return `
                <div class="slot-row w-min ml-auto flex items-center gap-2 justify-end ">
                    <span class="text-gray-500 text-sm">From</span>
                    <select class="${selectClass} open_time_select" style=" width: 30%;">${getTimeOptions(open)}</select>
                    <span class="text-gray-500 text-sm">To</span>
                    <select class="${selectClass} close_time_select" style="width: 30%;">${getTimeOptions(close)}</select>

                    <button type="button" class="remove-slot text-red-600 hover:text-red-800 text-xl font-bold px-2" title="Remove slot">&times;</button>
                </div>
            `;
        }

        function checkNoSlotMessage(day) {
            const $slotList = $(`#slots-${day}`);
            const hasSlots = $slotList.find('.slot-row').length > 0;
            $slotList.find('.no-slot-msg').toggle(!hasSlots);

            const $checkbox = $(`input[name="timings[${day}][is_active]"]`);
            if (!hasSlots) {
                $checkbox.prop('checked', false).prop('disabled', true);
            } else {
                $checkbox.prop('disabled', false);
            }
        }

        function updateTimeRestrictions() {
            days.forEach(day => {
                const $daySection = $(`#slots-${day}`);
                const $slotRows = $daySection.find('.slot-row');
                const allSlots = [];

                $slotRows.each(function() {
                    const start = $(this).find('select').eq(0).val();
                    const end = $(this).find('select').eq(1).val();
                    if (start && end && start < end) {
                        allSlots.push({
                            start,
                            end,
                            row: this
                        });
                    }
                });

                if (allSlots.length > 1) {
                    const lastSlot = allSlots[allSlots.length - 1];

                    for (let i = 0; i < allSlots.length - 1; i++) {
                        const existing = allSlots[i];

                        const startA = timeToMinutes(existing.start);
                        const endA = timeToMinutes(existing.end);

                        const startB = timeToMinutes(lastSlot.start);
                        const endB = timeToMinutes(lastSlot.end);

                        const overlap = !(endB <= startA || startB >= endA);

                        if (overlap) {
                            $(lastSlot.row).remove();
                            new FilamentNotification()
                                .title('Overlapping time slot removed.')
                                .danger()
                                .send();
                            updateTimeRestrictions();
                            return;
                        }
                    }
                }
            });
        }

        $(document).on(
            'select2:select',
            '.open_time_select, .close_time_select',
            function() {
                const $row = $(this).closest('.slot-row');
                updateRowTimeLimits($row);
            }
        );

        // added check box for is_active
        $(function() {
            days.forEach(day => {
                $('#timingWrapper').append(`
               <div class="day-section border border-gray-200 dark:border-gray-700 rounded-md p-4 dark:bg-gray-800 flex flex-col lg:flex-row gap-4 lg:items-start items-stretch w-full" data-day="${day}">
                    <div class="flex gap-2 items-center" style="min-width: 150px;padding:9px 0">
                        <input type="checkbox"
                            name="timings[${day}][is_active]"
                            class="fi-checkbox-input rounded border-none bg-white shadow-sm ring-1 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-offset-0 disabled:pointer-events-none disabled:bg-gray-50 disabled:text-gray-50 disabled:checked:bg-gray-400 disabled:checked:text-gray-400 dark:bg-white/5 dark:disabled:bg-transparent dark:disabled:checked:bg-gray-600 text-primary-600 ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50 dark:text-primary-500 dark:ring-white/20 dark:checked:bg-primary-500 dark:focus:ring-primary-500 dark:checked:focus:ring-primary-400/50 dark:disabled:ring-white/10 day-active-toggle w-5 h-5 cursor-pointer"
                            ${existingTimings[day]?.is_active ? 'checked' : ''} />

                        <h4 class="text-sm font-medium text-gray-800 dark:text-white min-w-[80px]">${day}</h4>
                    </div>

                    <div class="slot-list flex-1 flex flex-col gap-3" id="slots-${day}">
                        <p class="no-slot-msg text-xs text-gray-500 pt-3">No slots added yet.</p>
                    </div>

                    <div style="padding: 6px 0px;">
                        <x-filament::button type="button" size="xs" class="add-slot px-3 py-1 rounded-md text-xs text-white" style="background-color: rgba(var(--primary-600), var(--tw-text-opacity, 1));" data-day="${day}">
                        <x-heroicon-m-plus class="w-4 h-4 mr-2 font-bold"/>
                        </x-filament::button>
                    </div>
                </div>
            `);

                if (existingTimings[day]?.slots) {
                    existingTimings[day].slots.forEach(slot => {
                        const $newSlot = $(createSlotRow(day, slot.open_time, slot.close_time));
                        $newSlot.find('select').select2({
                            width: '50%',
                            dropdownParent: $(`#slots-${day}`),
                            minimumResultsForSearch: 0
                        });
                        $(`#slots-${day}`).append($newSlot);
                        updateRowTimeLimits($newSlot);
                    });
                }

                checkNoSlotMessage(day);
            });

            setTimeout(updateTimeRestrictions, 200);

            $(document).on('click', '.add-slot', function() {
                const day = $(this).data('day');
                const $slots = $(`#slots-${day} .slot-row`);
                let lastCloseTime = null;
                if ($slots.length > 0) {
                    const $lastSlot = $slots.last();
                    lastCloseTime = $lastSlot
                        .find('.close_time_select')
                        .val();
                }
                if (lastCloseTime === '23:59') {
                    new FilamentNotification()
                        .title('A full-day time slot has already been added.')
                        .danger()
                        .send();
                    return;
                }
                const $new = $(createSlotRow(day, lastCloseTime));
                const defaultTimeSlot = '00:00';

                const existingDefaultSlots = $(`#slots-${day} .slot-row`).filter(function() {
                    const open = $(this).find('select').eq(0).val();
                    const close = $(this).find('select').eq(1).val();
                    return open === defaultTimeSlot && close === defaultTimeSlot;
                });

                if (existingDefaultSlots.length > 0) {
                    new FilamentNotification()
                        .title('You cannot have more than one default time slot.')
                        .danger()
                        .send();
                } else {
                    $(`#slots-${day}`).append($new);

                    // Initialize Select2 for new selects
                    $new.find('select').select2({
                        width: '50%',
                        dropdownParent: $(`#slots-${day}`),
                        minimumResultsForSearch: 0 // always show search
                    });

                    updateRowTimeLimits($new);
                    updateTimeRestrictions();
                    checkNoSlotMessage(day);
                }
            });

            $(document).on('click', '.remove-slot', function() {
                const day = $(this).closest('.day-section').data('day');
                $(this).closest('.slot-row').remove();
                updateTimeRestrictions();
                checkNoSlotMessage(day);
            });

            $(document).on('change', '.select2-time', function() {
                updateTimeRestrictions();
            });

            $('#full-save').on('click', function(e) {
                e.preventDefault();

                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Saving...').css('cursor', 'wait');

                const formatted = {};

                days.forEach(day => {
                    const isActive = $(`input[name="timings[${day}][is_active]"]`).is(':checked');
                    const slots = [];

                    $(`#slots-${day} .slot-row`).each(function() {
                        const open = $(this).find('select').eq(0).val();
                        const close = $(this).find('select').eq(1).val();
                        if (open && close) {
                            slots.push({
                                open_time: open,
                                close_time: close
                            });
                        }
                    });

                    formatted[day] = {
                        is_active: isActive,
                        slots: slots,
                    };
                });

                @this.call('saveAll', formatted)
            });
        });
    </script>
@endpush
