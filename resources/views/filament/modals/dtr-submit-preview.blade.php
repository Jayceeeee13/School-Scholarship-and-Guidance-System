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
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr>
                    <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-200">Date</th>
                    <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-200">Office</th>
                    <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-200">Total Hrs</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse ($entries as $entry)
                    <tr>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $entry['date'] }}</td>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $entry['office'] }}</td>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $entry['total_hours'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-center text-gray-400">No approved entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>