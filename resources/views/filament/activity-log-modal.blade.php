    <div class="space-y-2 text-sm">
        @forelse ($properties as $key => $value)
            <div class="flex justify-between border-b pb-1">
                <span class="font-medium capitalize">{{ str_replace('_', ' ', $key) }}</span>
                <span class="text-gray-600 dark:text-gray-300">
                    @if (is_array($value))
                        {{ json_encode($value) }}
                    @else
                        {{ $value }}
                    @endif
                </span>
            </div>
        @empty
            <p class="text-gray-500">No additional details recorded.</p>
        @endforelse
    </div>