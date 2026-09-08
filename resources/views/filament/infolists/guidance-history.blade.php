@php
    $history = $getState();
    $total = $history->count();
    $followups = $history->filter(fn ($a) => $a->isFollowUp())->count();
    $pending = $history->where('status', 'pending')->count();
    $latest = $history->first();
@endphp

<div class="space-y-6">

    {{-- ── Stat cards ─────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3">
        <div class="group flex items-center gap-3 rounded-2xl border border-primary-100 dark:border-primary-500/20 bg-gradient-to-br from-primary-50 to-white dark:from-primary-500/10 dark:to-transparent p-4 transition-all hover:shadow-md">
            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400 transition-transform group-hover:scale-110">
                <x-heroicon-o-rectangle-stack class="w-5 h-5" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Sessions</p>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">{{ $total }}</p>
            </div>
        </div>

        <div class="group flex items-center gap-3 rounded-2xl border border-info-100 dark:border-info-500/20 bg-gradient-to-br from-info-50 to-white dark:from-info-500/10 dark:to-transparent p-4 transition-all hover:shadow-md">
            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-info-100 dark:bg-info-500/20 text-info-600 dark:text-info-400 transition-transform group-hover:scale-110">
                <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Follow-ups</p>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">{{ $followups }}</p>
            </div>
        </div>

        <div class="group flex items-center gap-3 rounded-2xl border border-warning-100 dark:border-warning-500/20 bg-gradient-to-br from-warning-50 to-white dark:from-warning-500/10 dark:to-transparent p-4 transition-all hover:shadow-md">
            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-warning-100 dark:bg-warning-500/20 text-warning-600 dark:text-warning-400 transition-transform group-hover:scale-110">
                <x-heroicon-o-clock class="w-5 h-5" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending</p>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">{{ $pending }}</p>
            </div>
            @if ($pending > 0)
                <span class="flex-shrink-0 flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-warning-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-warning-500"></span>
                </span>
            @endif
        </div>

        <div class="group flex items-center gap-3 rounded-2xl border border-success-100 dark:border-success-500/20 bg-gradient-to-br from-success-50 to-white dark:from-success-500/10 dark:to-transparent p-4 transition-all hover:shadow-md">
            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-success-100 dark:bg-success-500/20 text-success-600 dark:text-success-400 transition-transform group-hover:scale-110">
                <x-heroicon-o-calendar class="w-5 h-5" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Latest Visit</p>
                <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">
                    {{ $latest?->counseling_date ? \Carbon\Carbon::parse($latest->counseling_date)->format('M d, Y') : '—' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ── Timeline ───────────────────────────────────────────── --}}
    @if ($total === 0)
        <div class="rounded-2xl border border-dashed border-gray-300 dark:border-white/10 p-10 text-center">
            <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-2xl bg-gray-100 dark:bg-white/5 mb-3">
                <x-heroicon-o-inbox class="w-7 h-7 text-gray-400" />
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No guidance records found for this student yet.</p>
        </div>
    @else
        <div class="relative">
            {{-- vertical connecting line --}}
            <div class="absolute left-[19px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-gray-200 via-gray-200 to-transparent dark:from-white/10 dark:via-white/10"></div>

            <div class="space-y-4">
                @foreach ($history as $appt)
                    @php
                        $statusColor = match ($appt->status) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'warning',
                        };
                        $statusColors = [
                            'success' => ['bg' => 'bg-success-500', 'chip' => 'bg-success-50 dark:bg-success-500/10 text-success-700 dark:text-success-400 border-success-200 dark:border-success-500/20'],
                            'danger'  => ['bg' => 'bg-danger-500',  'chip' => 'bg-danger-50 dark:bg-danger-500/10 text-danger-700 dark:text-danger-400 border-danger-200 dark:border-danger-500/20'],
                            'warning' => ['bg' => 'bg-warning-500', 'chip' => 'bg-warning-50 dark:bg-warning-500/10 text-warning-700 dark:text-warning-400 border-warning-200 dark:border-warning-500/20'],
                        ][$statusColor];
                        $isFollowUp = $appt->isFollowUp();
                        $hasSessions = $appt->logforms->isNotEmpty();
                        $hasEndorsement = $appt->endorsement !== null;
                        $isLatest = $loop->first;
                    @endphp

                    <div class="flex gap-3 items-start" x-data="{ open: {{ $isLatest ? 'true' : 'false' }} }">
                        {{-- timeline dot --}}
                        <div class="flex-shrink-0 mt-3 flex items-center justify-center w-10 h-10 rounded-full {{ $statusColors['bg'] }}/10 border-2 {{ $statusColors['bg'] }} border-opacity-30 z-10">
                            <div class="w-2.5 h-2.5 rounded-full {{ $statusColors['bg'] }}"></div>
                        </div>

                        <div class="flex-1 min-w-0 rounded-2xl border {{ $isFollowUp ? 'border-l-4 border-l-info-400 border-y-gray-100 border-r-gray-100 dark:border-y-white/10 dark:border-r-white/10' : 'border-l-4 border-l-gray-300 dark:border-l-gray-600 border-y-gray-100 border-r-gray-100 dark:border-y-white/10 dark:border-r-white/10' }} bg-white dark:bg-white/5 shadow-sm hover:shadow-md transition-shadow overflow-hidden">

                            {{-- header (clickable to expand) --}}
                            <button
                                type="button"
                                @click="open = !open"
                                class="w-full flex items-center justify-between gap-3 px-4 py-3.5 text-left hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-xl {{ $isFollowUp ? 'bg-info-50 dark:bg-info-500/10' : 'bg-gray-100 dark:bg-white/5' }}">
                                        @if ($isFollowUp)
                                            <x-heroicon-o-arrow-path-rounded-square class="w-4.5 h-4.5 text-info-500" />
                                        @else
                                            <x-heroicon-o-calendar-days class="w-4.5 h-4.5 text-gray-400" />
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $appt->counseling_date ? \Carbon\Carbon::parse($appt->counseling_date)->format('M d, Y') : 'No date' }}
                                            </p>
                                            @if ($isLatest)
                                                <span class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-500/20 text-primary-700 dark:text-primary-400 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide">
                                                    Latest
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $isFollowUp ? 'Follow-up Session' : 'Initial Session' }}
                                            @if ($appt->modeOfCounseling?->name || $appt->supportNeeded?->name)
                                                · {{ collect([$appt->modeOfCounseling?->name, $appt->supportNeeded?->name])->filter()->implode(' · ') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusColors['chip'] }}">
                                        @if ($appt->status === 'approved')
                                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                                        @elseif ($appt->status === 'rejected')
                                            <x-heroicon-o-x-circle class="w-3.5 h-3.5" />
                                        @else
                                            <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                        @endif
                                        {{ ucfirst($appt->status ?? 'pending') }}
                                    </span>
                                    <x-heroicon-o-chevron-down
                                        class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        x-bind:class="open && 'rotate-180'"
                                    />
                                </div>
                            </button>

                            {{-- expandable body --}}
                            <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-white/10 px-4 py-4 space-y-4">

                                @if ($isFollowUp && $appt->parentAppointment)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 px-3 py-1 text-xs font-medium">
                                        <x-heroicon-o-arrow-uturn-left class="w-3.5 h-3.5" />
                                        Follow-up of {{ \Carbon\Carbon::parse($appt->parentAppointment->counseling_date)->format('M d, Y') }}
                                    </span>
                                @endif

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1.5 flex items-center gap-1.5">
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-3.5 h-3.5" />
                                        Concern
                                    </p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $appt->concern ?: 'No concern recorded' }}</p>
                                </div>

                                @if ($hasSessions)
                                    <div class="rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 p-4 space-y-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                            <x-heroicon-o-document-text class="w-4 h-4" />
                                            Session Records
                                            <span class="ml-auto normal-case font-normal text-gray-400">{{ $appt->logforms->count() }} {{ Str::plural('entry', $appt->logforms->count()) }}</span>
                                        </p>

                                        @foreach ($appt->logforms as $logform)
                                            <div class="space-y-3 {{ !$loop->last ? 'pb-4 border-b border-gray-200 dark:border-white/10' : '' }}">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                                    <div>
                                                        <p class="text-xs text-gray-400 mb-0.5">Concern</p>
                                                        <p class="text-gray-700 dark:text-gray-300">{{ $logform->concern ?: '—' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-400 mb-0.5">Remarks</p>
                                                        <p class="text-gray-700 dark:text-gray-300">{{ $logform->remarks ?: '—' }}</p>
                                                    </div>
                                                </div>

                                                @if ($logform->anecdotals->isNotEmpty())
                                                    <div class="space-y-2">
                                                        @foreach ($logform->anecdotals as $anec)
                                                            <div class="rounded-lg bg-white dark:bg-white/5 border-l-2 border-l-info-400 border-y border-r border-gray-200 dark:border-white/10 p-3 space-y-2">
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 px-2 py-0.5 text-xs font-medium">
                                                                        <x-heroicon-o-tag class="w-3 h-3" />
                                                                        {{ $anec->area_concern }}
                                                                    </span>
                                                                    <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                                                        <x-heroicon-o-user class="w-3 h-3" />
                                                                        {{ $anec->personnel ? trim("{$anec->personnel->first_name} {$anec->personnel->last_name}") : '—' }}
                                                                    </span>
                                                                </div>
                                                                @if ($anec->concern)
                                                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                                                        <span class="text-xs text-gray-400 block mb-0.5">Observation</span>
                                                                        {!! $anec->concern !!}
                                                                    </div>
                                                                @endif
                                                                @if ($anec->intervention)
                                                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                                                        <span class="text-xs text-gray-400 block mb-0.5">Intervention</span>
                                                                        {!! $anec->intervention !!}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($hasEndorsement)
                                    <div class="rounded-xl bg-info-50/60 dark:bg-info-500/5 border border-info-100 dark:border-info-500/20 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-info-600 dark:text-info-400 mb-3 flex items-center gap-1.5">
                                            <x-heroicon-o-paper-airplane class="w-4 h-4" />
                                            Endorsement
                                        </p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Endorsed To</p>
                                                <p class="text-gray-700 dark:text-gray-300">{{ $appt->endorsement->to_where ?: '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Date</p>
                                                <p class="text-gray-700 dark:text-gray-300">
                                                    {{ $appt->endorsement->date ? \Carbon\Carbon::parse($appt->endorsement->date)->format('M d, Y') : '—' }}
                                                </p>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <p class="text-xs text-gray-400 mb-0.5">Issue</p>
                                                <p class="text-gray-700 dark:text-gray-300">{{ $appt->endorsement->issue ?: '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Endorsed By</p>
                                                <p class="text-gray-700 dark:text-gray-300">
                                                    {{ $appt->endorsement->personnel ? trim("{$appt->endorsement->personnel->first_name} {$appt->endorsement->personnel->last_name}") : '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>