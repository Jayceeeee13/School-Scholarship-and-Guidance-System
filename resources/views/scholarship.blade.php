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
    | Data (unchanged logic)
    |--------------------------------------------------------------------------
    */
    $appPeriod = \App\Models\Period::scholarshipApplication();
    $reqPeriod = \App\Models\Period::scholarshipRequirement();
    $isStudent = auth()->user()->role && strtolower(auth()->user()->role->name) === 'student';

    // A student may already hold a Scholars record without ever going through
    // the Applicant flow (e.g. imported by the scholarship office), so
    // "already applied" alone isn't enough to gate re-applying.
    $scholarRecord    = \App\Models\Scholars::forUser(auth()->user());
    $isAlreadyScholar = (bool) $scholarRecord;

    // Heroicons (outline, v1) path data
    $icons = [
        'check-circle' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x-circle'     => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'clock'        => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'check'        => 'M5 13l4 4L19 7',
        'lock'         => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        'clipboard'    => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'document'     => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'report'       => 'M9 17v-2a4 4 0 014-4h4m-4-4l4 4-4 4M3 21h18a2 2 0 002-2V7a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 0014.586 3H5a2 2 0 00-2 2v14a2 2 0 002 2z',
        'chevron-r'    => 'M9 5l7 7-7 7',
        'chevron-l'    => 'M15 19l-7-7 7-7',
    ];

    /*
    |--------------------------------------------------------------------------
    | Application status card
    |--------------------------------------------------------------------------
    */
    if ($alreadyApplied && $applicant) {
        $status = $applicant->status ?? 'pending';

        $statusConfig = [
            'pending' => [
                'label' => 'Under Review', 'icon' => 'clock', 'pulse' => true,
                'desc'  => 'Your application has been received and is currently being reviewed by the scholarship office.',
                'head'  => 'bg-amber-50 border-amber-200',
                'iconbox' => 'bg-amber-100 text-amber-600',
                'pill'  => 'bg-amber-100 text-amber-800 border-amber-300',
                'dot'   => 'bg-amber-400 text-amber-400',
            ],
            'approved' => [
                'label' => 'Approved', 'icon' => 'check-circle', 'pulse' => false,
                'desc'  => 'Congratulations! Your scholarship application has been approved.',
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

        $steps = [
            ['label' => 'Submitted',    'done' => true],
            ['label' => 'Under Review', 'done' => in_array($status, ['pending', 'approved', 'rejected'])],
            ['label' => 'Decision',     'done' => in_array($status, ['approved', 'rejected'])],
            ['label' => 'Granted',      'done' => $status === 'approved'],
        ];

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
    }

    /*
    |--------------------------------------------------------------------------
    | Action cards
    | state: link (clickable) | done (opens modal) | locked (period closed)
    |--------------------------------------------------------------------------
    */
    $actions = [];

    // Apply
    if ($alreadyApplied || $isAlreadyScholar) {
        $actions[] = [
            'state' => 'done', 'icon' => 'check-circle', 'title' => 'Apply Scholarship',
            'desc'  => $alreadyApplied
                ? 'Your application has been submitted. Contact the scholarship office for any changes.'
                : "You're already an active scholar. Contact the scholarship office if you need to make changes.",
            'cta'   => $alreadyApplied ? 'Already Submitted' : 'Already a Scholar',
        ];
    } elseif ($appPeriod->is_open) {
        $actions[] = [
            'state' => 'link', 'icon' => 'clipboard', 'title' => 'Apply Scholarship',
            'desc'  => 'Fill out and submit your scholarship application form to get started.',
            'cta'   => 'Apply Now', 'href' => route('application_new.get'),
        ];
    } else {
        $actions[] = [
            'state' => 'locked', 'icon' => 'lock', 'title' => 'Apply Scholarship',
            'desc'  => 'The application period is currently closed.'
                . ($appPeriod->open_date ? ' Opens on ' . $appPeriod->opensOnLabel() . '.' : ''),
            'cta'   => 'Period Closed',
        ];
    }

    // Requirements
    if ($reqPeriod->is_open) {
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

    // Accomplishment reports (students only)
    if ($isStudent) {
        $actions[] = [
            'state' => 'link', 'icon' => 'report', 'title' => 'Accomplishment Reports',
            'desc'  => 'For Talents, SSG, and Sports scholars. Submit proof of accomplishments to maintain your scholarship.',
            'cta'   => 'Go to Reports', 'href' => route('accomplishment_reports.get'),
        ];
    }

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
            <h2 id="modal-title" class="mb-2 font-display text-xl font-bold text-slate-900">Application Already Submitted</h2>
            <p class="mb-6 text-sm leading-relaxed text-slate-500">
                Our records show that you have already submitted a scholarship application.
                Each account is allowed only <strong>one application</strong>. Please contact
                the scholarship office if you need to make changes.
            </p>
        @else
            <h2 id="modal-title" class="mb-2 font-display text-xl font-bold text-slate-900">You're Already a Scholar</h2>
            <p class="mb-6 text-sm leading-relaxed text-slate-500">
                Our records show you already hold an active scholarship
                ({{ $scholarRecord->type_of_scholarship ?? 'Institutional' }}). There's no need to
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
            Submit your requirements and track your scholarship application status here.
        </p>
    </div>
</section>

{{-- ── MAIN ── --}}
<main class="flex-1 py-16 md:py-20">
    <div class="mx-auto max-w-5xl px-6">

        {{-- APPLICATION STATUS (only when applied) --}}
        @if ($alreadyApplied && $applicant)
            <section class="fade-up mb-10 overflow-hidden rounded-3xl border border-green-200/60 bg-white shadow-sm" aria-label="Application status">

                {{-- Header --}}
                <div class="flex flex-wrap items-center justify-between gap-4 border-b px-6 py-5 {{ $cfg['head'] }}">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $cfg['iconbox'] }}">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$cfg['icon']] }}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Application Status</p>
                            <p class="font-display text-base font-bold text-slate-900">{{ $applicant->first_name }} {{ $applicant->last_name }}</p>
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-xs font-bold tracking-wide {{ $cfg['pill'] }}">
                        <span class="inline-block h-2 w-2 rounded-full {{ $cfg['dot'] }} {{ $cfg['pulse'] ? 'pulse-dot' : '' }}"></span>
                        {{ $cfg['label'] }}
                    </span>
                </div>

                <div class="space-y-8 p-6 md:p-8">
                    <p class="max-w-2xl text-sm leading-relaxed text-slate-600">{{ $cfg['desc'] }}</p>

                    {{-- Progress stepper --}}
                    <ol class="grid grid-cols-4" aria-label="Progress">
                        @foreach ($steps as $i => $step)
                            <li class="relative flex flex-col items-center text-center">
                                @if ($i > 0)
                                    <span class="absolute left-[-50%] top-3 h-0.5 w-full {{ $step['done'] ? 'bg-emerald-400' : 'bg-slate-200' }}" aria-hidden="true"></span>
                                @endif
                                <span class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full
                                    {{ $step['done'] ? 'bg-emerald-500 text-white' : 'border-2 border-slate-300 bg-white' }}">
                                    @if ($step['done'])
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['check'] }}"/>
                                        </svg>
                                    @endif
                                </span>
                                <span class="mt-2 text-xs font-semibold {{ $step['done'] ? 'text-emerald-700' : 'text-slate-400' }}">{{ $step['label'] }}</span>
                            </li>
                        @endforeach
                    </ol>

                    {{-- Details --}}
                    <dl class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($details as $label => $value)
                            <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                <dt class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </section>
        @endif

        {{-- ACTION CARDS --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($actions as $i => $a)
                @php
                    $tag    = $a['state'] === 'link' ? 'a' : ($a['state'] === 'done' ? 'button' : 'div');
                    $locked = $a['state'] === 'locked';
                @endphp

                <{{ $tag }}
                    @if ($a['state'] === 'link') href="{{ $a['href'] }}" @endif
                    @if ($a['state'] === 'done') type="button" onclick="openModal()" @endif
                    class="group fade-up delay-{{ min($i + 1, 3) }} flex flex-col items-center rounded-3xl border p-8 text-center shadow-sm transition duration-300
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

                    @if ($a['state'] === 'link')
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
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

</body>
</html>