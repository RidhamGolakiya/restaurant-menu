<x-filament::page>
    <style>
        #calendar {
            padding: 10px !important;
        }

        .fc-event-title {
            line-height: 3;
        }

        .fc .fc-button {
            padding: 6px 12px;
            font-size: 0.875rem;
            border-radius: 6px;
            margin-top: 3px !important;
        }

        .fc .fc-button-primary {
            background-color: #d97706;
            border: none;
            color: white;
        }

        .fc .fc-button:hover {
            background-color: #d97706;
        }

        .fc-h-event {
            display: flex !important;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 19px !important;
            margin-top: 12px !important;
            border: none !important;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .fc-h-event:hover {
            transition: all 0.3s ease-in-out;
            box-shadow: 0 0 20px 0.5px rgb(77, 77, 77);
            cursor: pointer;
        }

        .dark .fc-h-event:hover {
            transition: all 0.3s ease-in-out;
            box-shadow: 0 0 20px 2px rgb(160, 160, 160);
            cursor: pointer;
        }

        .fc-h-event .fc-event-title {
            word-break: break-word !important;
            white-space: pre-line !important;
            font-weight: 600 !important;
            line-height: 1.3;
            font-size: 12px !important;
        }

        .fc-datagrid-cell {
            color: #111827 !important;
            background-color: #ffffff !important;
        }

        .dark .fc-datagrid-cell {
            color: #e5e7eb !important;
            background-color: #09090b !important;
        }

        .fc .fc-timeline-slot-cushion {
            text-align: center;
            width: 100%;
            display: block;
        }

        .dark .fc-toolbar-title {
            color: #e5e7eb !important;
        }
    </style>

    <div class="bg-gray-50 dark:bg-gray-950 font-sans text-gray-950 dark:text-gray-100">
        <div>
            <!-- Modal -->
            <div id="bookingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-950/50">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md p-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-950 dark:text-white mb-6">Create Reservation</h2>
                    <form wire:submit.prevent="saveSlot">
                        <div class="mb-4">
                            <input type="hidden" id="modalTable" wire:model.defer="modalTable">

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Table
                                    Name:</label>
                                <input type="text" id="modalTableName"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300"
                                    readonly>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Customer: <span style="color: red">*</span></label>
                            <select wire:model.defer="selectedCustomerId" name="modalCustomer"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Select a customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Person Field -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Number of
                                Persons: <span style="color: red">*</span></label>
                            <input type="number" wire:model.defer="modalPerson" name="modalPerson" min="1"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Date:</label>
                            <input type="text" id="modalDate" wire:model.defer="modalDate"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">Start
                                Time: <span style="color: red">*</span></label>
                            <input type="time" wire:model.defer="modalStart"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">End
                                Time: <span style="color: red">*</span></label>
                            <input type="hidden" id="modalStart" wire:model.defer="modalStart">
                            <input type="hidden" id="modalEnd" wire:model.defer="modalEnd">

                            <input type="time" id="endTimeInput" oninput="updateEndTime()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">

                        </div>
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" id="modalCancel"
                                class="px-4 py-2 text-gray-700 dark:text-white bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary-600 dark:bg-primary-500 text-white rounded-lg hover:bg-primary-700 dark:hover:bg-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                Save Reservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div
                class="max-w-full mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
                <div
                    class="px-6 py-4 bg-primary-600 dark:bg-primary-500 text-white rounded-t-xl flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex flex-wrap gap-2" id="week-buttons"></div>
                </div>

                <div class="p-6">
                    <div id="calendar"
                        class="rounded-lg border border-gray-200 dark:border-gray-700  bg-white dark:bg-gray-950"
                        style="overflow: hidden">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.18/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendarEl = document.getElementById('calendar');
            const weekButtonsEl = document.getElementById('week-buttons');
            const viewData = @json($this->getViewData());

            const processCalendarData = (data) => {
                let minTime = '00:00:00';
                let maxTime = '24:00:00';
                const duration = data.timePerTable;

                const resources = data.tables.map(table => ({
                    id: `t${table.id}`,
                    extendedProps: {
                        tableId: table.id,
                        tableName: table.name,
                        capacity: table.capacity,
                        restaurantId: table.restaurant_id
                    }
                }));

                const events = data.reservations.map(res => {
                    const timezone = viewData.timezone;
                    const start = new Date(res.start_time);
                    const end = new Date(res.end_time);

                    const startFormatted = start.toLocaleTimeString([], {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    console.log(startFormatted);
                    const endFormatted = end.toLocaleTimeString([], {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });

                    const table = data.tables.find(t => t.id === res.table_id);
                    const color = table?.color || '#000000'; // fallback if no color is found


                    return {
                        id: `r${res.id}`,
                        resourceId: `t${res.table_id}`,
                        title: `${res.customer.name}\n${res.no_of_person} Person\n${startFormatted} - ${endFormatted}`,
                        start: res.start_time,
                        end: res.end_time,
                        backgroundColor: color,
                        borderColor: '#000000',
                        textColor: '#ffffff',
                        extendedProps: {
                            ...res
                        }
                    };
                });

                return {
                    minTime,
                    maxTime,
                    duration,
                    resources,
                    events
                };
            };

            const generateWeekButtons = (selectedDate) => {
                const today = new Date(selectedDate);
                const weekStart = new Date(today.setDate(today.getDate() - today.getDay() + 1));
                const dates = [];

                for (let i = 0; i < 7; i++) {
                    const date = new Date(weekStart);
                    date.setDate(weekStart.getDate() + i);
                    const label = date.toLocaleDateString('en-US', {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric'
                    });

                    dates.push({
                        label,
                        date
                    });
                }

                return dates;
            };

            const calendarData = processCalendarData(viewData);

            const dates = generateWeekButtons(viewData.selectedDate);

            const numberToTime = (num) => {
                const hours = String(num).padStart(2, '0');
                return `${hours}:00:00`;
            };

            function formatDateInTimezone(date, timeZone) {
                const options = {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                    timeZone
                };

                const formatter = new Intl.DateTimeFormat('en-US', options);

                const parts = formatter.formatToParts(date);
                const dateParts = {};
                for (const part of parts) {
                    dateParts[part.type] = part.value;
                }

                return `${dateParts.year}-${dateParts.month}-${dateParts.day}T${dateParts.hour}:${dateParts.minute}:${dateParts.second}`;
            }

            const now = new Date();
            const dateString = formatDateInTimezone(now, viewData.timezone);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                now: dateString,
                timezone: viewData.timezone,
                slotLabelDidMount: function(info) {
                    info.el.classList.add(
                        'bg-white', 'text-gray-800',
                        'dark:bg-gray-950', 'dark:text-gray-200',
                    );
                },
                resourceLabelContent: function(arg) {
                    const {
                        tableName,
                        capacity
                    } = arg.resource.extendedProps;

                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = `
                            <span class="inline-flex items-center gap-1">
                                ${tableName} - ${capacity}
                                <x-heroicon-o-user-group class="w-5 h-5" />
                            </span>
                        `;
                    return {
                        domNodes: [wrapper]
                    };
                },
                initialView: 'resourceTimelineDay',
                initialDate: viewData.selectedDate,
                nowIndicator: true,
                height: 'auto',
                headerToolbar: {
                    left: '',
                    center: 'title',
                    right: 'prev,next today'
                },
                buttonText: {
                    today: 'Today',
                },
                resourceAreaHeaderContent: 'Tables',
                resourceAreaWidth: '15%',
                slotMinWidth: 300,
                slotMinTime: calendarData.minTime,
                slotMaxTime: calendarData.maxTime,
                slotDuration: numberToTime(calendarData.duration),
                resources: calendarData.resources,
                events: calendarData.events,
                slotLabelInterval: numberToTime(calendarData.duration),
                slotLabelFormat: function(info) {
                    const slotDurationInHours = calendarData.duration;
                    const baseHour = 0; // ← start from 6 AM

                    // Calculate slot index from slot start (UTC hour)
                    const utcHour = info.date.marker.getUTCHours();
                    const slotIndex = Math.floor((utcHour - baseHour) / slotDurationInHours);

                    const base = new Date();
                    base.setHours(baseHour, 0, 0, 0); // base = 6:00 AM

                    const s = new Date(base.getTime() + slotIndex * slotDurationInHours * 60 * 60 *
                        1000);
                    const e = new Date(s.getTime() + slotDurationInHours * 60 * 60 * 1000);

                    const format = (d) => {
                        const hours = d.getHours();
                        const rawMinutes = d.getMinutes();
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        const hour12 = hours % 12 === 0 ? 12 : hours % 12;

                        return rawMinutes === 0 ?
                            `${hour12} ${ampm}` :
                            `${hour12}:${rawMinutes.toString().padStart(2, '0')} ${ampm}`;
                    };

                    return `${format(s)} - ${format(e)}`;
                },
                dateClick: function(info) {
                    let endTimeInputInitialized = false;
                    const resource = calendar.getResourceById(info.resource.id);
                    const tableId = info.resource.id.replace('t', '');
                    const table = viewData.tables.find(t => t.id == tableId);
                    const capacity = table.capacity ?? 0;
                    const selectedDate = info.dateStr.slice(0, 10);
                    const jsDate = new Date(info.date);

                    let adjustedStartTime = new Date(info.date);
                    const resourceId = `t${table.id}`;

                    let hasMoreAppointments = true;
                    while (hasMoreAppointments) {
                        hasMoreAppointments = false;

                        calendar.getEvents().forEach(event => {
                            if (
                                event.getResources()?.some(r => r.id === resourceId) &&
                                adjustedStartTime >= event.start &&
                                adjustedStartTime < event.end
                            ) {
                                adjustedStartTime = event.end;
                                hasMoreAppointments = true;
                            }
                        });
                    }

                    const hours = String(adjustedStartTime.getHours()).padStart(2, '0');
                    const minutes = String(adjustedStartTime.getMinutes()).padStart(2, '0');
                    const fullStart = `${hours}:${minutes}`; // 24-hour format, e.g., "08:00"

                    // Set values
                    document.getElementById('modalTable').value = table.id;
                    document.getElementById('modalTableName').value = table.name;
                    document.getElementById('modalDate').value = selectedDate;
                    document.getElementById('modalStart').value = fullStart;
                    document.getElementById('modalEnd').value = ''; // reset end time
                    document.getElementById('endTimeInput').value = ''; // reset end time

                    ['modalTable', 'modalDate', 'modalStart', 'modalEnd'].forEach(id => {
                        document.getElementById(id).dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                    });

                    if (!endTimeInputInitialized) {
                        document.getElementById('endTimeInput').addEventListener('change', (e) => {
                            const selectedTime = e.target.value;
                            const selectedDate = document.getElementById('modalDate').value;

                            if (selectedTime && selectedDate) {
                                const fullEnd = `${selectedDate} ${selectedTime}:00`;
                                document.getElementById('modalEnd').value = fullEnd;
                                document.getElementById('modalEnd').dispatchEvent(new Event(
                                    'input', {
                                        bubbles: true
                                    }));
                            }
                        });
                        endTimeInputInitialized = true;
                    }

                    document.getElementById('bookingModal').classList.remove('hidden');
                    document.getElementById('bookingModal').classList.add('flex');
                }
            });

            calendar.render();

            document.querySelector('.fc-today-button')?.addEventListener('click', () => {
                updateSelectedDateAndReload(new Date());
            });

            document.querySelector('.fc-prev-button')?.addEventListener('click', () => {
                const newDate = new Date(calendar.getDate());
                newDate.setDate(newDate.getDate() + 1);
                updateSelectedDateAndReload(newDate);
            });

            document.querySelector('.fc-next-button')?.addEventListener('click', () => {
                const newDate = new Date(calendar.getDate());
                newDate.setDate(newDate.getDate() + 1);
                updateSelectedDateAndReload(newDate);
            });

            dates.forEach(d => {
                const btn = document.createElement('button');
                btn.textContent = d.label;

                const isSelected = d.date.toDateString() === new Date(viewData.selectedDate).toDateString();
                btn.className = isSelected ?
                    'bg-primary-600 dark:bg-primary-500 text-white border-2 border-white shadow-lg px-3 py-2 rounded-lg text-sm font-semibold transition' :
                    'bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-300 border border-primary-200 dark:border-primary-700 px-3 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-primary-50 dark:hover:bg-gray-700 transition';

                btn.onclick = () => {
                    calendar.gotoDate(d.date);
                    updateSelectedDateAndReload(d.date);
                };

                weekButtonsEl.appendChild(btn);
            });

            function updateSelectedDateAndReload(date) {
                const formatted = date.toISOString().slice(0, 10);
                const url = new URL(window.location.href);
                url.searchParams.set('selectedDate', formatted);
                window.location.href = url.href;
            }

            document.getElementById('modalCancel').onclick = () => {
                document.getElementById('bookingModal').classList.add('hidden');
                document.getElementById('bookingModal').classList.remove('flex');
            };

            document.getElementById('bookingModal').onclick = (e) => {
                if (e.target.id === 'bookingModal') {
                    document.getElementById('bookingModal').classList.add('hidden');
                    document.getElementById('bookingModal').classList.remove('flex');
                }
            };
        });

        function updateEndTime() {
            const input = document.getElementById('endTimeInput');
            const selectedTime = input.value;

            if (selectedTime) {
                const [hours] = selectedTime.split(':');
                const roundedTime = `${hours.padStart(2, '0')}:00`;
                input.value = roundedTime;

                const selectedDate = document.getElementById('modalDate').value;
                if (selectedDate) {
                    const fullEnd = `${selectedDate} ${roundedTime}:00`;

                    const modalEndEl = document.getElementById('modalEnd');
                    modalEndEl.value = fullEnd;
                    modalEndEl.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            }
        }
    </script>
</x-filament::page>
