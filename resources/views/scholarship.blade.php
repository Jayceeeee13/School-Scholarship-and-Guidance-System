<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship | Green Valley College Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['DM Sans', 'system-ui', 'sans-serif'], display: ['Outfit', 'sans-serif'] },
                colors: { gvc: { dark: '#052e16', primary: '#14532d', light: '#166534', pale: '#bbf7d0', mint: '#4ade80' } },
                backgroundImage: {
                    'hero-gradient': 'linear-gradient(135deg, #022c22 0%, #14532d 35%, #166534 65%, #15803d 100%)',
                    'hero-pattern': "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%2386efac' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E\")"
                },
                boxShadow: { 'card-hover': '0 25px 50px -12px rgba(22, 163, 74, 0.35)' }
            } }
        }
    </script>
    <style>
        @keyframes fadeUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .6s cubic-bezier(.22,1,.36,1) both; }
        .delay-1 { animation-delay: .08s; } .delay-2 { animation-delay: .16s; } .delay-3 { animation-delay: .24s; }

        @keyframes modalIn { from { opacity: 0; transform: scale(.95) translateY(12px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .modal-box { animation: modalIn .25s cubic-bezier(.22,1,.36,1) both; }

        @keyframes pulse-ring { 0% { transform: scale(1); opacity: .6; } 100% { transform: scale(2.4); opacity: 0; } }
        .pulse-dot { position: relative; }
        .pulse-dot::before { content: ''; position: absolute; inset: 0; border-radius: 9999px; background: currentColor; animation: pulse-ring 1.4s ease-out infinite; }

        @media (prefers-reduced-motion: reduce) {
            .fade-up, .modal-box, .pulse-dot::before { animation: none !important; }
        }
    </style>
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    | $alreadyApplied, $applicant, $isAlreadyScholar, $scholarRecord,
    | $isRenewal and $exam all come from ApplicationController::index().
    |   - $isAlreadyScholar : holds a scholarship for the ACTIVE term
    |   - $isRenewal        : scholar from an earlier term, so they renew
    |   - $exam             : the admission exam (null if none exists)
    */
    $appPeriod = \App\Models\Period::scholarshipApplication();
    $reqPeriod = \App\Models\Period::scholarshipRequirement();
    $isStudent = auth()->user()->role && strtolower(auth()->user()->role->name) === 'student';

    // Not-yet-enrolled users can take the admission exam but cannot apply yet.
    $isEnrolled = auth()->user()->isEnrolled();

    $isRenewal        = $isRenewal ?? false;
    $isAlreadyScholar = $isAlreadyScholar ?? false;
    $scholarRecord    = $scholarRecord ?? null;
    $exam             = $exam ?? null;

    // Applying requires a PASSED admission exam (renewals are exempt).
    $examAttempt = $exam ? \App\Models\ExamAttempt::latestAttempt(auth()->id(), $exam->id) : null;
    $examPassed  = ! $exam || $exam->hasBeenPassedBy(auth()->id());
    $needsExam   = $exam && ! $isRenewal && ! $examPassed;

    // Heroicons (outline, v1) path data
    $icons = [
        'check-circle' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x-circle'     => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'            => 'M6 18L18 6M6 6l12 12',
        'clock'        => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'check'        => 'M5 13l4 4L19 7',
        'lock'         => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'clipboard'    => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'document'     => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'report'       => 'M9 17v-2a4 4 0 014-4h4m-4-4l4 4-4 4M3 21h18a2 2 0 002-2V7a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 0014.586 3H5a2 2 0 00-2 2v14a2 2 0 002 2z',
        'exam'         => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422A12.083 12.083 0 0121 12c0 5.523-4.477 10-10 10S2 17.523 2 12c0-.538.043-1.065.125-1.578L12 14z',
        'chevron-r'    => 'M9 5l7 7-7 7',
        'chevron-l'    => 'M15 19l-7-7 7-7',
    ];

    /*
    |--------------------------------------------------------------------------
    | Progress card + pipeline (always shown)
    |--------------------------------------------------------------------------
    | Before applying, the pipeline still appears so students can see what
    | comes next. Only the details grid needs an actual application.
    */
    $hasApplication = $alreadyApplied && $applicant;
    $status         = $hasApplication ? ($applicant->status ?? 'pending') : null;

    if ($hasApplication) {
        $statusConfig = [
            'pending' => [
                'label' => 'Under Review', 'icon' => 'clock', 'pulse' => true,
                'desc'  => $isRenewal
                    ? 'Your renewal application has been received and is currently being reviewed by the scholarship office.'
                    : 'Your application has been received and is currently being reviewed by the scholarship office.',
                'head'  => 'bg-amber-50 border-amber-200',
                'iconbox' => 'bg-amber-100 text-amber-600',
                'pill'  => 'bg-amber-100 text-amber-800 border-amber-300',
                'dot'   => 'bg-amber-400 text-amber-400',
            ],
            'approved' => [
                'label' => 'Approved', 'icon' => 'check-circle', 'pulse' => false,
                'desc'  => $isRenewal
                    ? 'Congratulations! Your scholarship renewal has been approved.'
                    : 'Congratulations! Your scholarship application has been approved.',
                'head'  => 'bg-emerald-50 border-emerald-200',
                'iconbox' => 'bg-emerald-100 text-emerald-600',
                'pill'  => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'dot'   => 'bg-emerald-500 text-emerald-500',
            ],
            'rejected' => [
                'label' => 'Not Approved', 'icon' => 'x-circle', 'pulse' => false,
                'desc'  => 'Unfortunately your application was not approved. Please contact the scholarship office for more information.',
                'head'  => 'bg-red-50 border-red-200',
                'iconbox' => 'bg-red-100 text-red-500',
                'pill'  => 'bg-red-100 text-red-800 border-red-300',
                'dot'   => 'bg-red-500 text-red-500',
            ],
        ];
        $cfg = $statusConfig[$status] ?? $statusConfig['pending'];

        $details = [
            'Application Type' => $applicant->typeOfApplication->name ?? 'N/A',
            'Scholarship Type' => $applicant->typeOfScholarship->name ?? 'N/A',
            'Program'          => trim(($applicant->program->name ?? 'N/A') . ' ' . $applicant->year_level),
            'Submitted On'     => $applicant->created_at ? $applicant->created_at->format('M d, Y') : '—',
            'Last Updated'     => $applicant->updated_at ? $applicant->updated_at->format('M d, Y') : '—',
        ];
        if ($applicant->benefit) {
            $details['Benefit'] = $applicant->benefit;
        }
    } else {
        // No application yet: neutral card that tells the student what to do next.
        if (! $isEnrolled) {
            $progressDesc = 'Complete your enrollment first. Only enrolled students can apply for a scholarship.';
        } elseif ($needsExam) {
            $progressDesc = $examAttempt
                ? 'Your exam score did not reach the passing mark yet. Please contact the Scholarship and Guidance office.'
                : 'Take and pass the admission exam first to unlock your scholarship application.';
        } else {
            $progressDesc = $isRenewal
                ? 'You have not submitted your renewal for this term yet. Use the Renew Scholarship card below to continue.'
                : 'You have not submitted an application yet. Use the Apply Scholarship card below to get started.';
        }

        $cfg = [
            'label' => 'Not Yet Applied', 'icon' => 'clipboard', 'pulse' => false,
            'desc'  => $progressDesc,
            'head'  => 'bg-slate-50 border-slate-200',
            'iconbox' => 'bg-slate-100 text-slate-500',
            'pill'  => 'bg-slate-100 text-slate-700 border-slate-300',
            'dot'   => 'bg-slate-400 text-slate-400',
        ];
        $details = [];
    }

    // ── Scholarship flow ──────────────────────────────────────────────────
    // Admission Exam → Application → Requirements → Scholarship Decision
    $examTaken = ! $exam
        || \App\Models\ExamAttempt::hasAttempted(auth()->id(), $exam->id);

    $requirementsDone = false;
    if ($hasApplication) {
        try {
            $requirementsDone = \App\Filament\Resources\ApplicantResource::hasCompleteRequirements($applicant);
        } catch (\Throwable $e) {
            $requirementsDone = false;
        }
    }

    $steps = [
        ['label' => 'Admission Exam',       'done' => $examTaken],
        ['label' => 'Application',          'done' => (bool) $hasApplication],
        ['label' => 'Requirements',         'done' => $requirementsDone],
        [
            'label' => 'Scholarship Decision',
            'done'  => $hasApplication && in_array($status, ['approved', 'rejected']),
            'tone'  => $status === 'rejected' ? 'red' : 'green',
        ],
    ];

    // The first unfinished step is the "current" one (pulsing dot).
    $currentIdx = collect($steps)->search(fn ($s) => ! $s['done']);

    /*
    |--------------------------------------------------------------------------
    | Action cards
    | state: link (clickable) | code (opens the exam-code popup)
    |        | done (opens the "already applied" popup) | locked (not available)
    |--------------------------------------------------------------------------
    */
    $actions = [];

    // Apply / Renew
    $applyTitle = $isRenewal ? 'Renew Scholarship' : 'Apply Scholarship';

    if (! $isEnrolled) {
        // Not enrolled yet: scholarship features stay locked until enrollment.
        $actions[] = [
            'state' => 'locked', 'icon' => 'lock', 'title' => $applyTitle,
            'desc'  => 'Only enrolled students can apply for a scholarship. Complete your enrollment first.',
            'cta'   => 'Enrolled Only',
        ];
        $actions[] = [
            'state' => 'locked', 'icon' => 'lock', 'title' => 'Submit Requirements',
            'desc'  => 'Only enrolled students can submit scholarship requirements.',
            'cta'   => 'Enrolled Only',
        ];
    } else {
        if ($alreadyApplied || $isAlreadyScholar) {
            $actions[] = [
                'state' => 'done', 'icon' => 'check-circle', 'title' => $applyTitle,
                'desc'  => $alreadyApplied
                    ? ($isRenewal
                        ? 'Your renewal for this term has been submitted. Contact the scholarship office for any changes.'
                        : 'Your application has been submitted. Contact the scholarship office for any changes.')
                    : "You're already an active scholar this term. Contact the scholarship office if you need to make changes.",
                'cta'   => $alreadyApplied
                    ? ($isRenewal ? 'Renewal Submitted' : 'Already Submitted')
                    : 'Already a Scholar',
            ];
        } elseif ($needsExam) {
            $actions[] = [
                'state' => 'locked', 'icon' => 'lock', 'title' => $applyTitle,
                'desc'  => $examAttempt
                    ? 'You need at least ' . ($exam->passing_score ?? 0) . '% on the admission exam to apply. Your score: ' . number_format($examAttempt->percentage, 1) . '%.'
                    : 'Take and pass the admission exam first to unlock your scholarship application.',
                'cta'   => $examAttempt ? 'Exam Not Passed' : 'Pass the Exam First',
            ];
        } elseif ($appPeriod->is_open) {
            $actions[] = [
                'state' => 'link', 'icon' => 'clipboard', 'title' => $applyTitle,
                'desc'  => $isRenewal
                    ? "You're a scholar from a previous term. Submit your renewal application to continue your scholarship."
                    : 'Fill out and submit your scholarship application form to get started.',
                'cta'   => $isRenewal ? 'Renew Now' : 'Apply Now',
                'href'  => route('application_new.get'),
            ];
        } else {
            $actions[] = [
                'state' => 'locked', 'icon' => 'lock', 'title' => $applyTitle,
                'desc'  => 'The application period is currently closed.'
                    . ($appPeriod->open_date ? ' Opens on ' . $appPeriod->opensOnLabel() . '.' : ''),
                'cta'   => 'Period Closed',
            ];
        }

        // Requirements — step by step: the exam must be passed (renewals are
        // exempt) and the application must be submitted before requirements open.
        if ($needsExam) {
            $actions[] = [
                'state' => 'locked', 'icon' => 'lock', 'title' => 'Submit Requirements',
                'desc'  => 'Complete the steps in order. Pass the admission exam and submit your application before you can submit requirements.',
                'cta'   => 'Pass the Exam First',
            ];
        } elseif (! $hasApplication) {
            $actions[] = [
                'state' => 'locked', 'icon' => 'lock', 'title' => 'Submit Requirements',
                'desc'  => 'Complete the steps in order. Submit your scholarship application first to unlock the requirements.',
                'cta'   => 'Apply First',
            ];
        } elseif ($reqPeriod->is_open) {
            $actions[] = [
                'state' => 'link', 'icon' => 'document', 'title' => 'Submit Requirements',
                'desc'  => 'Upload and submit your scholarship requirements and letter of intent for processing.',
                'cta'   => 'Submit Now', 'href' => route('requirements_submission.get'),
            ];
        } else {
            $actions[] = [
                'state' => 'locked', 'icon' => 'lock', 'title' => 'Submit Requirements',
                'desc'  => 'The submission period is currently closed.'
                    . ($reqPeriod->open_date ? ' Opens on ' . $reqPeriod->opensOnLabel() . '.' : ''),
                'cta'   => 'Period Closed',
            ];
        }

        // Accomplishment reports: only shown once the application is APPROVED.
        if ($isStudent && $status === 'approved') {
            $actions[] = [
                'state' => 'link', 'icon' => 'report', 'title' => 'Accomplishment Reports',
                'desc'  => 'For Talents, SSG, and Sports scholars. Submit proof of accomplishments to maintain your scholarship.',
                'cta'   => 'Go to Reports', 'href' => route('accomplishment_reports.get'),
            ];
        }
    }

    // Admission Exam (moved here from the landing page).
    // With an access code the card opens a popup asking for it; without one it goes straight to the exam.
    // Admission Exam. Locked once the student has finished it.
    if ($exam) {
        $finishedAttempt = \App\Models\ExamAttempt::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();

        if ($finishedAttempt) {
            $examAction = [
                'state' => 'locked', 'icon' => 'exam', 'title' => 'Admission Exam',
                'desc'  => 'You have already taken the admission exam. Your score: '
                    . number_format($finishedAttempt->percentage, 1) . '%.',
                'cta'   => 'Exam Already Taken',
            ];
        } elseif ($exam->access_code) {
            $examAction = [
                'state' => 'code', 'icon' => 'exam', 'title' => 'Admission Exam',
                'desc'  => 'Take the scholarship admission exam. You will need the exam code from the Scholarship and Guidance office.',
                'cta'   => 'Take the Exam',
            ];
        } else {
            $examAction = [
                'state' => 'link', 'icon' => 'exam', 'title' => 'Admission Exam',
                'desc'  => 'Take the scholarship admission exam.',
                'cta'   => 'Take the Exam', 'href' => route('exam.admission', $exam->id),
            ];
        }

        // The exam always comes first, before the scholarship application.
        array_unshift($actions, $examAction);
    }

    // Four cards sit in one row on wide screens; fewer cards keep the narrower layout.
    $fourCards = count($actions) === 4;
    $gridCols  = $fourCards ? 'lg:grid-cols-2 xl:grid-cols-4' : 'lg:grid-cols-3';

    $btnNav = 'inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-medium text-white transition hover:bg-white/20 sm:text-sm';
@endphp

<body class="flex min-h-screen flex-col bg-emerald-50/60 font-sans text-slate-800 antialiased">

{{-- ── ALREADY-APPLIED / ALREADY-A-SCHOLAR MODAL ── --}}
<div id="already-applied-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="modal-title"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if (event.target === this || event.target.dataset.backdrop) closeModal()">
    <div data-backdrop="1" class="absolute inset-0 bg-green-950/60 backdrop-blur-sm"></div>

    <div class="modal-box relative z-10 w-full max-w-md rounded-[2rem] bg-white p-8 text-center shadow-2xl">
        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['check-circle'] }}"/>
            </svg>
        </div>

        @if ($alreadyApplied)
            <h2 id="modal-title" class="mb-2 font-display text-xl font-bold text-slate-900">
                {{ $isRenewal ? 'Renewal Already Submitted' : 'Application Already Submitted' }}
            </h2>
            <p class="mb-6 text-sm leading-relaxed text-slate-500">
                @if ($isRenewal)
                    Our records show that you have already submitted your renewal application for this term.
                    Only <strong>one renewal per term</strong> is allowed. Please contact the scholarship
                    office if you need to make changes.
                @else
                    Our records show that you have already submitted a scholarship application.
                    Each account is allowed only <strong>one application</strong>. Please contact
                    the scholarship office if you need to make changes.
                @endif
            </p>
        @else
            <h2 id="modal-title" class="mb-2 font-display text-xl font-bold text-slate-900">You're Already a Scholar</h2>
            <p class="mb-6 text-sm leading-relaxed text-slate-500">
                Our records show you already hold an active scholarship
                ({{ $scholarRecord->type_of_scholarship ?? 'Institutional' }}) for this term. There's no need to
                submit a new application. Please contact the scholarship office if you believe this is a mistake.
            </p>
        @endif

        <button type="button" onclick="closeModal()"
                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-green-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-green-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['chevron-l'] }}"/>
            </svg>
            Got it, go back
        </button>
    </div>
</div>

{{-- ── EXAM CODE MODAL (only when the exam has an access code) ── --}}
@if ($exam && $exam->access_code)
<div id="exam-code-modal" style="display:none;" role="dialog" aria-modal="true" aria-label="Enter exam code"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if (event.target === this || event.target.dataset.backdrop) closeExamModal()">
    <div data-backdrop="1" class="absolute inset-0 bg-green-950/60 backdrop-blur-sm"></div>

    <div class="modal-box relative z-10 w-full max-w-md overflow-hidden rounded-[2rem] bg-white shadow-2xl">
        <div class="relative bg-hero-gradient px-7 py-6 text-white">
            <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
            <button type="button" onclick="closeExamModal()" aria-label="Close"
                    class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/25 transition hover:bg-white/25">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="relative font-display text-2xl font-bold">Enter exam code</h3>
            <p class="relative mt-1 text-sm text-emerald-50/80">Ask the Scholarship and Guidance office for the code.</p>
        </div>

        <form method="POST" action="{{ route('exam.verify-code', $exam->id) }}" class="px-7 py-6">
            @csrf

            <label for="exam_code" class="text-sm font-semibold text-slate-700">Exam code</label>
            <input id="exam_code" name="exam_code" type="text" autocomplete="off" maxlength="12" required
                   value="{{ old('exam_code') }}"
                   class="mt-2 w-full rounded-2xl border px-4 py-3 text-center font-display text-2xl font-bold uppercase tracking-[0.3em] text-slate-900 outline-none transition focus:ring-2 focus:ring-green-600
                          {{ $errors->has('exam_code') ? 'border-red-400' : 'border-green-200' }}"
                   placeholder="••••••">

            @error('exam_code')
                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-full bg-green-800 px-6 py-3 text-sm font-semibold text-white transition hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
                Continue to exam
            </button>
        </form>
    </div>
</div>
@endif

{{-- ── NAVBAR (same as landing page) ── --}}
<header class="sticky top-0 z-40 border-b border-white/10 bg-green-900/80 shadow-lg shadow-green-950/10 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3">
        <a href="{{ url('/gvc') }}" class="flex shrink-0 items-center gap-2.5">
            <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation" class="h-10 w-10 rounded-xl bg-white/10 object-contain p-0.5 ring-1 ring-white/20">
            <span class="hidden font-display text-base font-bold tracking-tight text-white sm:inline md:text-lg">Green Valley College Foundation Inc.</span>
        </a>

        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                <span class="hidden text-sm text-emerald-100 md:inline">Hello, <span class="font-semibold text-white">{{ auth()->user()->name }}</span></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="{{ $btnNav }}">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</header>

{{-- ── HERO ── --}}
<section class="relative overflow-hidden rounded-b-[2.5rem] py-20 md:py-28">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.12]" style="background-image: url('{{ asset('images/gvc.png') }}');"></div>
    <div class="absolute -left-24 top-10 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="fade-up relative mx-auto max-w-3xl px-6 text-center">
        <span class="mb-5 inline-flex items-center rounded-full border border-emerald-300/30 bg-emerald-900/40 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-emerald-200">
            Scholarship Office
        </span>
        <h1 class="mb-4 font-display text-4xl font-extrabold leading-[1.05] tracking-tight text-emerald-50 drop-shadow-md sm:text-5xl md:text-6xl">
            Scholarship <br>
            <span class="bg-gradient-to-r from-gvc-pale via-gvc-mint to-emerald-300 bg-clip-text text-transparent">Services</span>
        </h1>
        <p class="mx-auto max-w-xl text-sm text-emerald-50/85 sm:text-base">
            Take the admission exam, submit your requirements, and track your scholarship application status here.
        </p>
    </div>
</section>

{{-- ── MAIN ── --}}
<main class="flex-1 py-16 md:py-20">
    <div class="mx-auto max-w-7xl px-6">

        {{-- Flash message (e.g. redirected here because the exam isn't passed yet) --}}
        @if (session('error'))
            <div class="fade-up mx-auto mb-8 flex max-w-5xl items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700" role="alert">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['x-circle'] }}"/>
                </svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- PROGRESS / APPLICATION STATUS (always shown, so the pipeline never goes missing) --}}
        <section class="fade-up mx-auto mb-10 max-w-5xl overflow-hidden rounded-3xl border border-green-200/60 bg-white shadow-sm" aria-label="Application status">

            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-b px-6 py-5 {{ $cfg['head'] }}">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $cfg['iconbox'] }}">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$cfg['icon']] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            @if ($hasApplication)
                                {{ $isRenewal ? 'Renewal Application Status' : 'Application Status' }}
                            @else
                                Scholarship Progress
                            @endif
                        </p>
                        <p class="font-display text-base font-bold text-slate-900">
                            @if ($hasApplication)
                                {{ $applicant->first_name }} {{ $applicant->last_name }}
                            @else
                                {{ auth()->user()->name }}
                            @endif
                        </p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-xs font-bold tracking-wide {{ $cfg['pill'] }}">
                    <span class="inline-block h-2 w-2 rounded-full {{ $cfg['dot'] }} {{ $cfg['pulse'] ? 'pulse-dot' : '' }}"></span>
                    {{ $cfg['label'] }}
                </span>
            </div>

            <div class="space-y-8 p-6 md:p-8">
                <p class="max-w-2xl text-sm leading-relaxed text-slate-600">{{ $cfg['desc'] }}</p>

                {{-- Progress stepper: Exam → Application → Requirements → Scholarship Decision --}}
                <ol class="grid grid-cols-4" aria-label="Progress">
                    @foreach ($steps as $i => $step)
                        @php
                            $isCurrent = $currentIdx === $i;
                            $isRed     = $step['done'] && ($step['tone'] ?? '') === 'red';
                        @endphp
                        <li class="relative flex flex-col items-center text-center">
                            @if ($i > 0)
                                <span class="absolute left-[-50%] top-3 h-0.5 w-full {{ $step['done'] ? ($isRed ? 'bg-red-400' : 'bg-emerald-400') : 'bg-slate-200' }}" aria-hidden="true"></span>
                            @endif

                            <span class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full
                                {{ $step['done']
                                    ? ($isRed ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white')
                                    : ($isCurrent ? 'pulse-dot border-2 border-emerald-500 bg-white text-emerald-500' : 'border-2 border-slate-300 bg-white') }}">
                                @if ($step['done'])
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $isRed ? $icons['x'] : $icons['check'] }}"/>
                                    </svg>
                                @endif
                            </span>

                            <span class="mt-2 px-1 text-xs font-semibold leading-tight
                                {{ $step['done'] ? ($isRed ? 'text-red-600' : 'text-emerald-700') : ($isCurrent ? 'text-emerald-700' : 'text-slate-400') }}">
                                {{ $step['label'] }}
                            </span>
                        </li>
                    @endforeach
                </ol>

                {{-- Details (only once an application exists) --}}
                @if ($hasApplication && ! empty($details))
                    <dl class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($details as $label => $value)
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <dt class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>
        </section>

        {{-- ACTION CARDS --}}
        <div class="mx-auto grid gap-6 sm:grid-cols-2 {{ $gridCols }} {{ $fourCards ? 'max-w-7xl' : 'max-w-5xl' }}">
            @foreach ($actions as $i => $a)
                @php
                    $tag    = $a['state'] === 'link' ? 'a' : (in_array($a['state'], ['done', 'code']) ? 'button' : 'div');
                    $locked = $a['state'] === 'locked';
                @endphp

                <{{ $tag }}
                    @if ($a['state'] === 'link') href="{{ $a['href'] }}" @endif
                    @if ($a['state'] === 'done') type="button" onclick="openModal()" @endif
                    @if ($a['state'] === 'code') type="button" onclick="openExamModal()" @endif
                    class="group fade-up delay-{{ min($i + 1, 3) }} flex flex-col items-center rounded-3xl border {{ $fourCards ? 'p-6' : 'p-8' }} text-center shadow-sm transition duration-300
                        {{ $locked
                            ? 'cursor-not-allowed border-slate-200 bg-white/70 opacity-70'
                            : 'cursor-pointer border-green-200/60 bg-white hover:-translate-y-1.5 hover:border-emerald-300 hover:shadow-card-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700' }}">

                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl transition-colors duration-300
                        {{ $locked ? 'bg-slate-100 text-slate-400' : 'bg-emerald-100 text-emerald-700 group-hover:bg-emerald-200' }}">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$a['icon']] }}"/>
                        </svg>
                    </div>

                    <h2 class="mb-2 font-display text-xl font-bold text-slate-900 transition-colors {{ $locked ? '' : 'group-hover:text-emerald-800' }}">{{ $a['title'] }}</h2>
                    <p class="mb-6 flex-1 text-sm leading-relaxed text-slate-500">{{ $a['desc'] }}</p>

                    @if (in_array($a['state'], ['link', 'code']))
                        <span class="inline-flex items-center gap-2 rounded-full bg-green-900 px-5 py-2.5 text-sm font-semibold text-white transition group-hover:bg-green-800">
                            {{ $a['cta'] }}
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['chevron-r'] }}"/>
                            </svg>
                        </span>
                    @elseif ($a['state'] === 'done')
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300 bg-emerald-100 px-5 py-2.5 text-sm font-semibold text-emerald-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['check'] }}"/>
                            </svg>
                            {{ $a['cta'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['lock'] }}"/>
                            </svg>
                            {{ $a['cta'] }}
                        </span>
                    @endif
                </{{ $tag }}>
            @endforeach
        </div>

        {{-- Back to Home --}}
        <div class="fade-up delay-3 mt-12 text-center">
            <a href="{{ url('/gvc') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-400 transition-colors hover:text-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['chevron-l'] }}"/>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</main>

{{-- ── FOOTER (same as landing page) ── --}}
<footer class="border-t border-green-300/20 bg-slate-900 py-10">
    <p class="text-center text-xs text-slate-400">&copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.</p>
</footer>

<script>
    function openModal() {
        document.getElementById('already-applied-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('already-applied-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Exam code popup (only exists when the exam has an access code)
    function openExamModal() {
        const m = document.getElementById('exam-code-modal');
        if (!m) return;
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => { const i = document.getElementById('exam_code'); if (i) i.focus(); }, 80);
    }
    function closeExamModal() {
        const m = document.getElementById('exam-code-modal');
        if (!m) return;
        m.style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(); closeExamModal(); }
    });
</script>

@if ($exam && $exam->access_code && $errors->has('exam_code'))
    {{-- Wrong code: reopen the popup so the error message is visible --}}
    <script>document.addEventListener('DOMContentLoaded', openExamModal);</script>
@endif

</body>
</html>