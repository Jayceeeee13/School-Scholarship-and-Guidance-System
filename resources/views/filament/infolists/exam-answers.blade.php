@php
    use App\Models\ExamAttempt;

    $record = ExamAttempt::with([
        'answers.question.examCategory',
        'answers.question.choices',
        'answers.choice',
    ])->find($getRecord()->id);

    $violations     = $record->violations ?? [];
    $violationCount = (int) $record->violation_count;

    $violationLabels = [
        'tab_switch'    => ['label' => 'Tab Switch',    'icon' => '🔀', 'color' => 'red'],
        'window_blur'   => ['label' => 'Window Blur',   'icon' => '🪟', 'color' => 'orange'],
        'copy_attempt'  => ['label' => 'Copy Attempt',  'icon' => '📋', 'color' => 'yellow'],
        'paste_attempt' => ['label' => 'Paste Attempt', 'icon' => '📋', 'color' => 'yellow'],
        'cut_attempt'   => ['label' => 'Cut Attempt',   'icon' => '✂️', 'color' => 'yellow'],
    ];

    // Count by type
    $byType = collect($violations)->groupBy('type')->map->count();

    $grouped = $record->answers
        ->filter(fn($a) => $a->question !== null)
        ->groupBy(
            fn($a) => optional($a->question->examCategory)->name ?? 'Uncategorized'
        );
    $counter = 0;
@endphp

<div class="space-y-6 py-2">

    {{-- ── VIOLATIONS SECTION ── --}}
    @if ($violationCount > 0)
        <div class="rounded-xl border border-red-200 bg-red-50 dark:bg-red-950/40 dark:border-red-800 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 bg-red-100 dark:bg-red-900/50 border-b border-red-200 dark:border-red-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-bold text-red-700 dark:text-red-300">
                        Violations Detected
                    </span>
                </div>
                <span class="text-xs font-bold bg-red-600 text-white px-2.5 py-0.5 rounded-full">
                    {{ $violationCount }} total
                </span>
            </div>

            {{-- Summary by type --}}
            <div class="px-4 py-3 flex flex-wrap gap-2 border-b border-red-200 dark:border-red-800">
                @foreach ($byType as $type => $count)
                    @php $info = $violationLabels[$type] ?? ['label' => ucwords(str_replace('_', ' ', $type)), 'icon' => '⚠️']; @endphp
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white dark:bg-red-900/60 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 px-3 py-1 rounded-full">
                        {{ $info['icon'] }} {{ $info['label'] }}
                        <span class="bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full leading-none">{{ $count }}</span>
                    </span>
                @endforeach
            </div>

            {{-- Detailed log --}}
            <div class="divide-y divide-red-100 dark:divide-red-900 max-h-60 overflow-y-auto">
                @foreach ($violations as $i => $v)
                    @php
                        $info = $violationLabels[$v['type'] ?? ''] ?? ['label' => ucwords(str_replace('_', ' ', $v['type'] ?? 'Unknown')), 'icon' => '⚠️'];
                        $time = isset($v['time']) ? \Carbon\Carbon::parse($v['time'])->format('h:i:s A') : '—';
                        $remaining = isset($v['timeLeft'])
                            ? sprintf('%02d:%02d', intdiv($v['timeLeft'], 60), $v['timeLeft'] % 60) . ' remaining'
                            : '';
                    @endphp
                    <div class="flex items-center justify-between px-4 py-2.5 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-red-200 dark:bg-red-800 text-red-700 dark:text-red-300 font-bold flex items-center justify-center text-[10px]">
                                {{ $i + 1 }}
                            </span>
                            <span class="font-semibold text-red-700 dark:text-red-300">
                                {{ $info['icon'] }} {{ $info['label'] }}
                            </span>
                        </div>
                        <div class="text-right text-red-400 dark:text-red-500 space-x-3">
                            <span>{{ $time }}</span>
                            @if ($remaining)
                                <span class="text-red-300 dark:text-red-600">{{ $remaining }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @else
        <div class="rounded-xl border border-green-200 bg-green-50 dark:bg-green-950/30 dark:border-green-800 px-4 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-semibold text-green-700 dark:text-green-300">No violations recorded — clean exam.</span>
        </div>
    @endif

    {{-- ── ANSWERS SECTION ── --}}
    @forelse ($grouped as $category => $answers)
        <div>
            {{-- Category Header --}}
            <div class="flex items-center gap-3 mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full
                    bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
                    {{ $category }}
                </span>
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    {{ $answers->where('is_correct', true)->count() }} / {{ $answers->count() }} correct
                </span>
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
            </div>

            {{-- Questions --}}
            <div class="space-y-3">
                @foreach ($answers as $answer)
                    @php
                        $counter++;
                        $question = $answer->question;
                        $selected = $answer->choice;
                    @endphp

                    @if (!$question)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                            <p class="text-sm text-gray-400 italic">Question #{{ $counter }} has been deleted.</p>
                        </div>
                        @continue
                    @endif

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">

                        {{-- Question --}}
                        <div class="flex items-start gap-3 mb-3">
                            <span @class([
                                'flex-shrink-0 w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center text-white',
                                'bg-green-500' => $answer->is_correct,
                                'bg-red-500'   => !$answer->is_correct,
                            ])>{{ $counter }}</span>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                {{ $question->question ?? '—' }}
                            </p>
                        </div>

                        {{-- Choices --}}
                        <div class="ml-9 space-y-1.5">
                            @foreach ($question->choices as $choice)
                                @php
                                    $isSelected = $selected && $selected->id === $choice->id;
                                    $isCorrect  = $choice->is_correct;
                                @endphp

                                <div @class([
                                    'flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm',
                                    'bg-green-50 border-green-400 text-green-800 font-semibold dark:bg-green-950 dark:border-green-700 dark:text-green-300' => $isCorrect,
                                    'bg-red-50 border-red-400 text-red-700 dark:bg-red-950 dark:border-red-700 dark:text-red-300'                          => $isSelected && !$isCorrect,
                                    'bg-gray-50 border-gray-200 text-gray-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400'                    => !$isCorrect && !$isSelected,
                                ])>
                                    <span class="w-4 text-center flex-shrink-0">
                                        @if ($isCorrect) ✓
                                        @elseif ($isSelected) ✗
                                        @endif
                                    </span>

                                    @if (isset($choice->choice_letter))
                                        <span class="font-medium">{{ $choice->choice_letter }}.</span>
                                    @endif

                                    <span>{{ $choice->choice_text ?? '' }}</span>

                                    @if ($isSelected && !$isCorrect)
                                        <span class="ml-auto text-xs italic text-red-400 dark:text-red-400">Your answer</span>
                                    @elseif ($isSelected && $isCorrect)
                                        <span class="ml-auto text-xs italic text-green-500 dark:text-green-400">Your answer ✓</span>
                                    @endif
                                </div>
                            @endforeach

                            @if (!$selected)
                                <p class="text-xs italic text-gray-400 dark:text-gray-500">No answer selected.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400 italic">No answers recorded.</p>
    @endforelse

</div>