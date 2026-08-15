<div class="space-y-3">
    @forelse ($record->activities as $activity)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-sm">
            <div class="flex items-center justify-between gap-3">
                <span class="font-semibold">{{ $activity->activity_date?->format('M d, Y') ?? '—' }}</span>
                <span class="text-gray-500 dark:text-gray-400">{{ $activity->venue ?? '—' }}</span>
            </div>
            <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $activity->activity ?? '—' }}</p>
        </div>
    @empty
        <p class="text-gray-500 dark:text-gray-400">No activities recorded.</p>
    @endforelse

    @if($record->status !== 'pending' && $record->remarks)
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-3 text-sm">
            <span class="font-semibold">Office remarks:</span> {{ $record->remarks }}
        </div>
    @endif
</div>