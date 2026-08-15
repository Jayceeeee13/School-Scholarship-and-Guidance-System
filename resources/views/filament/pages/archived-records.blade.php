<x-filament-panels::page>
    <div class="flex gap-2 mb-4 flex-wrap">
        <button
            type="button"
            wire:click="setActiveTab('users')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-primary-600 text-white' => $activeTab === 'users',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $activeTab !== 'users',
            ])
        >
            Archived Users
        </button>
        <button
            type="button"
            wire:click="setActiveTab('personnels')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-primary-600 text-white' => $activeTab === 'personnels',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $activeTab !== 'personnels',
            ])
        >
            Archived Personnels
        </button>
        <button
            type="button"
            wire:click="setActiveTab('applicants')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-primary-600 text-white' => $activeTab === 'applicants',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $activeTab !== 'applicants',
            ])
        >
            Archived Applicants
        </button>
        <button
            type="button"
            wire:click="setActiveTab('appointments')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-primary-600 text-white' => $activeTab === 'appointments',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $activeTab !== 'appointments',
            ])
        >
            Archived Appointments
        </button>
        <button
            type="button"
            wire:click="setActiveTab('referrals')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-primary-600 text-white' => $activeTab === 'referrals',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $activeTab !== 'referrals',
            ])
        >
            Archived Referrals
        </button>
        <button
            type="button"
            wire:click="setActiveTab('logforms')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-primary-600 text-white' => $activeTab === 'logforms',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $activeTab !== 'logforms',
            ])
        >
            Archived Logforms
        </button>
        <button
            type="button"
            wire:click="setActiveTab('examinees')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-primary-600 text-white' => $activeTab === 'examinees',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $activeTab !== 'examinees',
            ])
        >
            Archived Examinees
        </button>
    </div>

    {{ $this->table }}
</x-filament-panels::page>