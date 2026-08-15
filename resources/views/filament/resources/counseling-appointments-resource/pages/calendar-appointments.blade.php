<x-filament-panels::page>
    <style>
        .fc .fc-button-primary {
            background-color: #059669 !important;
            border-color: #059669 !important;
        }
        .fc .fc-button-primary:hover {
            background-color: #047857 !important;
            border-color: #047857 !important;
        }
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #047857 !important;
            border-color: #047857 !important;
        }
        .fc-day-today {
            background-color: rgba(5, 150, 105, 0.1) !important;
        }
        .fc-bg-event {
            opacity: 0.4 !important;
        }
        .fc-daygrid-day {
            cursor: pointer !important;
        }
    </style>

    {{-- Legend --}}
    <div class="mb-4 flex gap-4 flex-wrap items-center">
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #f59e0b;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Approved</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #ef4444;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Rejected</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #fecaca; border: 1px solid #ef4444;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Inactive / Unavailable</span>
        </div>
        <div class="ml-auto text-sm text-gray-500 dark:text-gray-400 italic">
            💡 Click any date to manage it
        </div>
    </div>

</x-filament-panels::page>