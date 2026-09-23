<div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="mb-3">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
            Entries That Will Be Submitted
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            These {{ count($entries) }} Approved DTR entrie(s) for {{ $scholarName }} will all be sent to Admin/Scholarship together.
        </p>
    </div>

    <div class="overflow-x-auto rounded-lg ring-1 ring-gray-950/5 dark:ring-white/10">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr>
                    <th rowspan="2" class="border-b border-r border-gray-200 px-3 py-2 text-center font-semibold text-gray-700 dark:border-white/10 dark:text-gray-200">
                        Day
                    </th>
                    <th colspan="2" class="border-b border-r border-gray-200 px-3 py-1.5 text-center font-semibold text-gray-700 dark:border-white/10 dark:text-gray-200">
                        AM
                    </th>
                    <th colspan="2" class="border-b border-r border-gray-200 px-3 py-1.5 text-center font-semibold text-gray-700 dark:border-white/10 dark:text-gray-200">
                        PM
                    </th>
                    <th rowspan="2" class="border-b border-r border-gray-200 px-3 py-2 text-center font-semibold text-gray-700 dark:border-white/10 dark:text-gray-200">
                        Total Hours
                    </th>
                    <th rowspan="2" class="border-b border-gray-200 px-3 py-2 text-center font-semibold text-gray-700 dark:border-white/10 dark:text-gray-200">
                        Remarks
                    </th>
                </tr>
                <tr>
                    <th class="border-b border-r border-gray-200 px-3 py-1.5 text-center font-medium text-gray-600 dark:border-white/10 dark:text-gray-300">In</th>
                    <th class="border-b border-r border-gray-200 px-3 py-1.5 text-center font-medium text-gray-600 dark:border-white/10 dark:text-gray-300">Out</th>
                    <th class="border-b border-r border-gray-200 px-3 py-1.5 text-center font-medium text-gray-600 dark:border-white/10 dark:text-gray-300">In</th>
                    <th class="border-b border-r border-gray-200 px-3 py-1.5 text-center font-medium text-gray-600 dark:border-white/10 dark:text-gray-300">Out</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse ($entries as $entry)
                    <tr>
                        <td class="border-r border-gray-100 px-3 py-2 text-center text-gray-700 dark:border-white/5 dark:text-gray-200">
                            {{ $entry['date'] }}
                        </td>
                        <td class="border-r border-gray-100 px-3 py-2 text-center text-gray-700 dark:border-white/5 dark:text-gray-200">
                            {{ $entry['am_in'] ?? '—' }}
                        </td>
                        <td class="border-r border-gray-100 px-3 py-2 text-center text-gray-700 dark:border-white/5 dark:text-gray-200">
                            {{ $entry['am_out'] ?? '—' }}
                        </td>
                        <td class="border-r border-gray-100 px-3 py-2 text-center text-gray-700 dark:border-white/5 dark:text-gray-200">
                            {{ $entry['pm_in'] ?? '—' }}
                        </td>
                        <td class="border-r border-gray-100 px-3 py-2 text-center text-gray-700 dark:border-white/5 dark:text-gray-200">
                            {{ $entry['pm_out'] ?? '—' }}
                        </td>
                        <td class="border-r border-gray-100 px-3 py-2 text-center font-medium text-gray-700 dark:border-white/5 dark:text-gray-200">
                            {{ $entry['total_hours'] }}
                        </td>
                        <td class="px-3 py-2 text-left text-gray-700 dark:text-gray-200">
                            {{ $entry['remarks'] ?: '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-center text-gray-400">No approved entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>