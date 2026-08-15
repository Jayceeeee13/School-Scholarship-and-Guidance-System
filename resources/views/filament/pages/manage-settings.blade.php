<x-filament-panels::page>
    <div class="space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Counseling Card - Only for Admin and Guidance --}}
            @if(auth()->user()->hasAnyRole(['admin', 'guidance']))
            <div class="relative overflow-hidden bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Counseling</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">3 configurations</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-1">
                    <a href="{{ route('filament.admin.resources.support-neededs.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Support Types</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.mode-of-counselings.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Counseling Modes</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.counseling-time-slots.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Time Slots</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif

            {{-- Scholarship Card - Only for Admin and Scholarship --}}
            @if(auth()->user()->hasAnyRole(['admin', 'scholarship']))
            @php
                $appPeriod = \App\Models\Period::scholarshipApplication();
                $reqPeriod = \App\Models\Period::scholarshipRequirement();
                $examPeriod = \App\Models\Period::exam();
            @endphp
            <div class="relative overflow-hidden bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Scholarship</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">5 configurations</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-1">

                    <a href="{{ route('filament.admin.resources.type-of-applications.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Application Types</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.requirements.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Requirements</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.exam-categories.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Exam Categories</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    {{-- Divider --}}
                    <div class="pt-1 pb-1">
                        <div class="border-t border-gray-100 dark:border-gray-800"></div>
                        <p class="px-4 pt-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                            Period Control
                        </p>
                    </div>

                    {{-- Application Period --}}
                    <a href="{{ route('filament.admin.resources.periods.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <div class="flex items-center gap-2">
                            <span>Application Period</span>
                            @if($appPeriod->is_open)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Open
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
                                    Closed
                                </span>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    {{-- Requirement Submission Period --}}
                    <a href="{{ route('filament.admin.resources.periods.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <div class="flex items-center gap-2">
                            <span>Submission Period</span>
                            @if($reqPeriod->is_open)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Open
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
                                    Closed
                                </span>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    {{-- Exam Period --}}
                    <a href="{{ route('filament.admin.resources.periods.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <div class="flex items-center gap-2">
                            <span>Exam Period</span>
                            @if($examPeriod->is_open)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Open
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
                                    Closed
                                </span>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                </div>
            </div>
            @endif

            {{-- General Card - Only for Admin --}}
            @if(auth()->user()->hasRole('admin'))
            <div class="relative overflow-hidden bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">General</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">7 configurations</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 space-y-1">
                    <a href="{{ route('filament.admin.resources.departments.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Departments</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.programs.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Programs</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.genders.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Genders</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.school-positions.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>School Positions</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.roles.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Roles</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.resources.terms.index') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Terms</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('filament.admin.pages.archived-records') }}"
                       class="group flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <span>Archived Records</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @endif

        </div>

        {{-- Empty State if No Permissions --}}
        @if(!auth()->user()->hasAnyRole(['admin', 'guidance', 'scholarship']))
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Access</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You don't have permission to access any settings.</p>
        </div>
        @endif

    </div>
</x-filament-panels::page>