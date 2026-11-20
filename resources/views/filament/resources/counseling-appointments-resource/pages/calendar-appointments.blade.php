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

    {{-- Filter --}}
    <div x-data="{ mode: 'all' }" class="mb-4 flex flex-wrap items-center gap-2">
        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Show:</span>

        @foreach (['all' => 'All', 'appointments' => 'Appointments', 'followups' => 'Follow-ups'] as $key => $label)
            <button type="button"
                    x-on:click="mode = '{{ $key }}'; $dispatch('calendar-filter', { mode: '{{ $key }}' })"
                    :class="mode === '{{ $key }}'
                        ? 'bg-emerald-600 text-white border-emerald-600'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600'"
                    class="rounded-lg border px-3 py-1.5 text-sm font-medium transition">
                {{ $label }}
            </button>
        @endforeach
    </div>

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
            <div class="w-4 h-4 rounded" style="background-color: #3b82f6;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Follow-up</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #6b7280;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
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