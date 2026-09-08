@php
    $history = $getState();
    $total = $history->count();
    $followups = $history->filter(fn ($a) => $a->isFollowUp())->count();
    $pending = $history->where('status', 'pending')->count();
    $latest = $history->first();
@endphp

<div class="space-y-5">

    {{-- ── Stat strip ─────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 p-4">
        <div class="flex items-center gap-2.5 pr-4 border-r border-gray-200 dark:border-white/10 last:border-r-0">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-600 text-white">
                <x-heroicon-o-rectangle-stack class="w-4 h-4" />
            </div>
            <div>
                <p class="text-base font-bold text-gray-900 dark:text-white leading-none">{{ $total }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-tight">Sessions</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 pr-4 border-r border-gray-200 dark:border-white/10 last:border-r-0">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-info-600 text-white">
                <x-heroicon-o-arrow-path-rounded-square class="w-4 h-4" />
            </div>
            <div>
                <p class="text-base font-bold text-gray-900 dark:text-white leading-none">{{ $followups }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-tight">Follow-ups</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 pr-4 border-r border-gray-200 dark:border-white/10 last:border-r-0">
            <div class="relative flex items-center justify-center w-8 h-8 rounded-lg bg-warning-500 text-white">
                <x-heroicon-o-clock class="w-4 h-4" />
                @if ($pending > 0)
                    <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-warning-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-warning-500 ring-2 ring-gray-50 dark:ring-gray-900"></span>
                    </span>
                @endif
            </div>
            <div>
                <p class="text-base font-bold text-gray-900 dark:text-white leading-none">{{ $pending }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-tight">Pending</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-700 dark:bg-gray-600 text-white">
                <x-heroicon-o-calendar class="w-4 h-4" />
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">
                    {{ $latest?->counseling_date ? \Carbon\Carbon::parse($latest->counseling_date)->format('M d, Y') : '—' }}
                </p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-tight">Latest visit</p>
            </div>
        </div>
    </div>

    {{-- ── Records list ───────────────────────────────────────── --}}
    @if ($total === 0)
        <div class="rounded-2xl border border-dashed border-gray-300 dark:border-white/10 p-10 text-center">
            <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 dark:bg-white/5 mb-3">
                <x-heroicon-o-inbox class="w-7 h-7 text-gray-400" />
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No guidance records found for this student yet.</p>
        </div>
    @else
        <div class="space-y-2.5">
            @foreach ($history as $appt)
                @php
                    $statusStyles = [
                        'approved' => ['dot' => 'bg-success-500', 'chip' => 'bg-success-50 dark:bg-success-500/10 text-success-700 dark:text-success-400', 'edge' => 'bg-success-500'],
                        'rejected' => ['dot' => 'bg-danger-500', 'chip' => 'bg-danger-50 dark:bg-danger-500/10 text-danger-700 dark:text-danger-400', 'edge' => 'bg-danger-500'],
                        'pending'  => ['dot' => 'bg-warning-500', 'chip' => 'bg-warning-50 dark:bg-warning-500/10 text-warning-700 dark:text-warning-400', 'edge' => 'bg-warning-500'],
                    ];
                    $style = $statusStyles[$appt->status] ?? $statusStyles['pending'];
                    $isFollowUp = $appt->isFollowUp();
                    $hasSessions = $appt->logforms->isNotEmpty();
                    $hasEndorsement = $appt->endorsement !== null;
                    $isLatest = $loop->first;
                @endphp

                <div
                    x-data="{ open: {{ $isLatest ? 'true' : 'false' }} }"
                    class="relative rounded-xl bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-white/10 overflow-hidden"
                >
                    {{-- left status edge --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $style['edge'] }}"></div>

                    <button
                        type="button"
                        @click="open = !open"
                        class="w-full flex items-center justify-between gap-3 pl-5 pr-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex-shrink-0 w-2 h-2 rounded-full {{ $style['dot'] }}"></span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-sm text-gray-900 dark:text-white truncate">
                                        {{ $appt->counseling_date ? \Carbon\Carbon::parse($appt->counseling_date)->format('M d, Y') : 'No date' }}
                                    </p>
                                    @if ($isFollowUp)
                                        <span class="inline-flex items-center rounded bg-info-50 dark:bg-info-500/10 text-info-600 dark:text-info-400 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide">
                                            Follow-up
                                        </span>
                                    @endif
                                    @if ($isLatest)
                                        <span class="inline-flex items-center rounded bg-primary-600 text-white px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide">
                                            Latest
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                    @if ($appt->modeOfCounseling?->name || $appt->supportNeeded?->name)
                                        {{ collect([$appt->modeOfCounseling?->name, $appt->supportNeeded?->name])->filter()->implode(' · ') }}
                                    @else
                                        {{ $isFollowUp ? 'Follow-up session' : 'Initial session' }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $style['chip'] }}">
                                {{ ucfirst($appt->status ?? 'pending') }}
                            </span>
                            <x-heroicon-o-chevron-down
                                class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                                x-bind:class="open && 'rotate-180'"
                            />
                        </div>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-white/10 pl-5 pr-4 py-3.5 space-y-3.5">

                        @if ($isFollowUp && $appt->parentAppointment)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 px-2.5 py-1 text-[11px] font-medium">
                                <x-heroicon-o-arrow-uturn-left class="w-3 h-3" />
                                Follow-up of {{ \Carbon\Carbon::parse($appt->parentAppointment->counseling_date)->format('M d, Y') }}
                            </span>
                        @endif

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 mb-1">Concern</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $appt->concern ?: 'No concern recorded' }}</p>
                        </div>

                        @if ($hasSessions)
                            <div class="rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 p-3.5 space-y-3.5">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <x-heroicon-o-document-text class="w-3.5 h-3.5" />
                                    Session Records
                                    <span class="ml-auto normal-case font-normal text-gray-400">{{ $appt->logforms->count() }} {{ Str::plural('entry', $appt->logforms->count()) }}</span>
                                </p>

                                @foreach ($appt->logforms as $logform)
                                    <div class="space-y-2.5 {{ !$loop->last ? 'pb-3.5 border-b border-gray-200 dark:border-white/10' : '' }}">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <p class="text-[11px] text-gray-400 mb-0.5">Concern</p>
                                                <p class="text-gray-700 dark:text-gray-300">{{ $logform->concern ?: '—' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] text-gray-400 mb-0.5">Remarks</p>
                                                <p class="text-gray-700 dark:text-gray-300">{{ $logform->remarks ?: '—' }}</p>
                                            </div>
                                        </div>

                                        @if ($logform->anecdotals->isNotEmpty())
                                            <div class="space-y-2">
                                                @foreach ($logform->anecdotals as $anec)
                                                    <div class="rounded-lg bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 p-3 space-y-2">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span class="inline-flex items-center gap-1 rounded bg-info-50 dark:bg-info-500/10 text-info-600 dark:text-info-400 px-1.5 py-0.5 text-[11px] font-medium">
                                                                <x-heroicon-o-tag class="w-3 h-3" />
                                                                {{ $anec->area_concern }}
                                                            </span>
                                                            <span class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                                                                <x-heroicon-o-user class="w-3 h-3" />
                                                                {{ $anec->personnel ? trim("{$anec->personnel->first_name} {$anec->personnel->last_name}") : '—' }}
                                                            </span>
                                                        </div>
                                                        @if ($anec->concern)
                                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                                <span class="text-[11px] text-gray-400 block mb-0.5">Observation</span>
                                                                {!! $anec->concern !!}
                                                            </div>
                                                        @endif
                                                        @if ($anec->intervention)
                                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                                <span class="text-[11px] text-gray-400 block mb-0.5">Intervention</span>
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
                            <div class="rounded-lg bg-primary-50/60 dark:bg-primary-500/5 border border-primary-100 dark:border-primary-500/20 p-3.5">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-primary-600 dark:text-primary-400 mb-2.5 flex items-center gap-1.5">
                                    <x-heroicon-o-paper-airplane class="w-3.5 h-3.5" />
                                    Endorsement
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-[11px] text-gray-400 mb-0.5">Endorsed To</p>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $appt->endorsement->to_where ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] text-gray-400 mb-0.5">Date</p>
                                        <p class="text-gray-700 dark:text-gray-300">
                                            {{ $appt->endorsement->date ? \Carbon\Carbon::parse($appt->endorsement->date)->format('M d, Y') : '—' }}
                                        </p>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <p class="text-[11px] text-gray-400 mb-0.5">Issue</p>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $appt->endorsement->issue ?: '—' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] text-gray-400 mb-0.5">Endorsed By</p>
                                        <p class="text-gray-700 dark:text-gray-300">
                                            {{ $appt->endorsement->personnel ? trim("{$appt->endorsement->personnel->first_name} {$appt->endorsement->personnel->last_name}") : '—' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>