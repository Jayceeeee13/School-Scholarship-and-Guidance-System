<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 flex-wrap">
            
            {{-- Left: Avatar + Info --}}
            <div class="flex items-center gap-4">
                @php
    $user = auth()->user();
    $initial = strtoupper(substr($user->name, 0, 1));
    $avatarColor = $user->isAdmin() 
        ? 'background: linear-gradient(135deg, #2563eb, #1d4ed8)' 
        : ($user->isScholarship() 
            ? 'background: linear-gradient(135deg, #059669, #047857)' 
            : 'background: linear-gradient(135deg, #7c3aed, #6d28d9)');
    $roleLabel = $user->isAdmin() ? 'Administrator' : ($user->isScholarship() ? 'Scholarship Admin' : 'Guidance Admin');
    $roleIcon = $user->isAdmin() ? '👑' : ($user->isScholarship() ? '🎓' : '🧭');
    $badgeClass = $user->isAdmin() 
        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' 
        : ($user->isScholarship() 
            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' 
            : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300');
    $avatarUrl = $user->avatar 
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) 
        : null;
@endphp

                {{-- Avatar: photo or initial fallback --}}
                @if($avatarUrl)
                    <img 
                        src="{{ $avatarUrl }}" 
                        alt="{{ $user->name }}"
                        style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 6px rgba(0,0,0,0.15); flex-shrink: 0;"
                    />
                @else
                    <div style="{{ $avatarColor }}; width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.15); flex-shrink: 0;">
                        <span style="color: white; font-size: 1.25rem; font-weight: 700;">{{ $initial }}</span>
                    </div>
                @endif

                {{-- Info --}}
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ now()->format('l, F j, Y') }} — Here's what's happening today.
                    </p>
                </div>
            </div>

            {{-- Right: Badge + Buttons --}}
            <div class="flex items-center gap-3">

                {{-- Role Badge --}}
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-bold {{ $badgeClass }}">
                    {{ $roleIcon }} {{ $roleLabel }}
                </span>

                @if($user->isAdmin())
                <a href="{{ url('/admin/users') }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold border-2 border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950 dark:text-primary-400 dark:border-primary-400 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manage Users
                </a>
                @endif

                <a href="{{ url('/admin/manage-settings') }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-primary-600 text-white hover:bg-primary-500 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>