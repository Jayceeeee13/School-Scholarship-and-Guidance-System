<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeModal"></div>

                <!-- Modal -->
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6 z-10">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            🚫 Mark Date as Inactive
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Date -->
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Selected Date: 
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('F j, Y') : '' }}
                        </span>
                    </p>

                    <!-- Reason Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Reason <span class="text-gray-400">(optional)</span>
                        </label>
                        <input
                            type="text"
                            wire:model="reason"
                            placeholder="e.g. Holiday, No classes..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                        />
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">
                        <button
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button
                            wire:click="save"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Mark as Inactive
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>