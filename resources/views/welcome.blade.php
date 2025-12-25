<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Table-Wise Day View Calendar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.18/index.global.min.css" rel="stylesheet">
  <style>  .fc-event-title {
    line-height: 5;
    white-space: normal !important;
  }
</style>

  </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-white min-h-screen p-6 font-sans">

  <!-- Modal -->
  <div id="bookingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">Create Reservation</h2>
      <form id="bookingForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Table Name</label>
          <input type="text" id="modalTable" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Date</label>
          <input type="text" id="modalDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Start Time</label>
          <input type="text" id="modalStart" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">End Time</label>
          <input type="time" id="modalEnd" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div class="flex justify-end gap-2">
          <button type="button" id="modalCancel" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Save</button>
        </div>
      </form>
    </div>
  </div>

  <div class="max-w-full mx-auto bg-white shadow-xl rounded-2xl">
    <div class="px-6 py-4 bg-indigo-600 text-white rounded-t-2xl flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="text-lg font-semibold">📅 Table-Wise Day Calendar</div>
      <div class="flex flex-wrap gap-2" id="week-buttons"></div>
    </div>

    <div class="p-4">
      <div id="calendar" class="rounded-lg shadow-inner"></div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.18/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const calendarEl = document.getElementById('calendar');
      const weekButtonsEl = document.getElementById('week-buttons');

      const today = new Date();
      const weekStart = new Date(today.setDate(today.getDate() - today.getDay() + 1));
      const dates = [];
      let dateSet = false;

      for (let i = 0; i < 7; i++) {
        const date = new Date(weekStart);
        date.setDate(weekStart.getDate() + i);
        const label = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });

        dates.push({
          label,
          date: new Date(date)
        });
      }

      const calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'resourceTimelineDay',
        nowIndicator: true,
        height: 'auto',
        headerToolbar: {
          left: 'today prev,next',
          center: 'title',
          right: ''
        },
        resourceAreaHeaderContent: 'Tables',
        resourceAreaWidth: '15%',
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '02:00:00',
        slotLabelInterval: '02:00:00',
        slotLabelFormat: function(info) {
            const slotDurationInHours = 2;
    const baseHour = 6; // you want to start at 6AM

    // Calculate slot index from slot start (UTC hour)
    const utcHour = info.date.marker.getUTCHours();
    const slotIndex = Math.floor((utcHour - baseHour) / slotDurationInHours);

    const base = new Date();
    base.setHours(baseHour, 0, 0, 0); // base = 6:00 AM

    const s = new Date(base.getTime() + slotIndex * slotDurationInHours * 60 * 60 * 1000);
    const e = new Date(s.getTime() + slotDurationInHours * 60 * 60 * 1000);

    const format = (d) =>
        d.toLocaleTimeString([], {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });

    return `${format(s)} - ${format(e)}`;
},
        resources: [
          { id: 't1', title: 'Table 1' },
          { id: 't2', title: 'Table 2' },
          { id: 't3', title: 'Table 3' }
        ],
        events: [
          {
            id: 'e1',
            resourceId: 't1',
            title: 'Walk in Customer - 9AM - 10AM',
            start: '2025-07-11T09:00:00',
            end: '2025-07-11T10:00:00',
            backgroundColor: '#34d399'
          },
          {
            id: 'e2',
            resourceId: 't2',
            title: 'Walk in Customer - 9AM - 10AM',
            start: '2025-07-11T16:00:00',
            end: '2025-07-11T18:00:00',
            backgroundColor: '#34d399'
          }
        ],
        dateClick: function(info) {
          const resource = calendar.getResourceById(info.resource.id);
          document.getElementById('modalTable').value = resource.title;
          document.getElementById('modalDate').value = info.dateStr.slice(0, 10);
          document.getElementById('modalStart').value = info.date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
          document.getElementById('bookingModal').classList.remove('hidden');
        }
      });

      calendar.render();

      // Generate week buttons
      dates.forEach(d => {
        const btn = document.createElement('button');
        btn.textContent = d.label;
        btn.className = 'bg-white text-indigo-600 hover:bg-indigo-100 border border-indigo-300 px-3 py-1 rounded-md text-sm shadow-sm';
        btn.onclick = () => calendar.gotoDate(d.date);
        weekButtonsEl.appendChild(btn);
      });

      // Modal controls
      document.getElementById('modalCancel').onclick = () => {
        document.getElementById('bookingModal').classList.add('hidden');
      };
      document.getElementById('bookingForm').onsubmit = e => {
        e.preventDefault();
        const table = document.getElementById('modalTable').value;
        const date = document.getElementById('modalDate').value;
        const start = document.getElementById('modalStart').value;
        const end = document.getElementById('modalEnd').value;
        alert(`Booked: ${table} on ${date} from ${start} to ${end}`);
        document.getElementById('bookingModal').classList.add('hidden');
      };
    });
  </script>
</body>
</html>
