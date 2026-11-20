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
<div x-data="{ mode: 'all' }" style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:16px;">
    <span style="font-size:14px;font-weight:500;color:#6b7280;">Show:</span>

    @foreach (['all' => 'All', 'appointments' => 'Appointments', 'followups' => 'Follow-ups'] as $key => $label)
        <button type="button"
                x-on:click="mode = '{{ $key }}'; $dispatch('calendar-filter', { mode: '{{ $key }}' })"
                x-bind:style="mode === '{{ $key }}'
                    ? 'background:#059669;color:#ffffff;border:1px solid #059669;'
                    : 'background:#ffffff;color:#374151;border:1px solid #d1d5db;'"
                style="border-radius:8px;padding:6px 14px;font-size:14px;font-weight:500;cursor:pointer;transition:all .15s;">
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