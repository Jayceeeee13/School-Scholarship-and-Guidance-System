<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guidance | Green Valley College Foundation</title>
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

        @media (prefers-reduced-motion: reduce) {
            .fade-up, .modal-box { animation: none !important; }
        }
    </style>
</head>

@php
    /*
    |--------------------------------------------------------------------------
    | Icons (Heroicons outline v1 path data)
    |--------------------------------------------------------------------------
    */
    $icons = [
        'mail'       => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'calendar'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'share'      => 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z',
        'warning'    => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'check'      => 'M5 13l4 4L19 7',
        'x'          => 'M6 18L18 6M6 6l12 12',
        'check-circle' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'chevron-r'  => 'M9 5l7 7-7 7',
        'chevron-l'  => 'M15 19l-7-7 7-7',
    ];

    /*
    |--------------------------------------------------------------------------
    | Status styles shared by invitations and appointments
    | icon: pulse | check | check-circle | x
    |--------------------------------------------------------------------------
    */
    $statusStyles = [
        'pending'   => ['border' => 'border-amber-200',   'badge' => 'bg-amber-100 text-amber-700',     'icon' => 'pulse'],
        'approved'  => ['border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'check'],
        'accepted'  => ['border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'check'],
        'completed' => ['border' => 'border-sky-200',     'badge' => 'bg-sky-100 text-sky-700',         'icon' => 'check-circle'],
        'cancelled' => ['border' => 'border-slate-200',   'badge' => 'bg-slate-100 text-slate-500',     'icon' => 'x'],
        'rejected'  => ['border' => 'border-red-200',     'badge' => 'bg-red-100 text-red-600',         'icon' => 'x'],
        'declined'  => ['border' => 'border-red-200',     'badge' => 'bg-red-100 text-red-600',         'icon' => 'x'],
    ];
    $statusFallback = ['border' => 'border-slate-200', 'badge' => 'bg-slate-100 text-slate-500', 'icon' => 'none'];

    /*
    |--------------------------------------------------------------------------
    | Data (same queries as before)
    |--------------------------------------------------------------------------
    */
    $student = auth()->user()->student ?? null;

    $invitations = collect();
    $appointments = collect();

    if ($student) {
        $invitations = \App\Models\ReferralInvitation::whereHas('referral', function ($q) use ($student) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($student->first_name) . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($student->last_name) . '%']);
            })
            ->with(['referral', 'timeSlot', 'personnel'])
            ->latest()
            ->get();

        $appointments = \App\Models\CounselingAppointments::where('student_id', $student->id)
            ->with(['timeSlot', 'modeOfCounseling', 'supportNeeded'])
            ->orderBy('counseling_date', 'desc')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Panels (invitations + appointments) normalised so one template renders both
    |--------------------------------------------------------------------------
    */
    $panels = [];

    if ($invitations->isNotEmpty()) {
        $pending = $invitations->where('status', 'pending')->count();

        $panels[] = [
            'icon'     => 'mail',
            'title'    => 'Counseling Invitations',
            'subtitle' => 'You have been invited for a counseling session. Please respond below.',
            'badge'    => $pending > 0 ? ['text' => $pending . ' Pending', 'class' => 'bg-amber-100 text-amber-700'] : null,
            'flash'    => session('success') ? ['text' => session('success'), 'tone' => 'success'] : null,
            'rows'     => $invitations->map(function ($inv) {
                $counselor = $inv->personnel
                    ? trim($inv->personnel->first_name . ' ' . $inv->personnel->last_name)
                    : 'Counselor TBA';

                return [
                    'status'  => $inv->status,
                    'title'   => $inv->session_date?->format('F d, Y') ?? 'Date TBD',
                    'slot'    => $inv->timeSlot?->name,
                    'meta'    => $counselor . ($inv->purpose ? ' — ' . \Illuminate\Support\Str::limit($inv->purpose, 60) : ''),
                    'respond' => $inv->status === 'pending' ? route('referral.invitation.respond', $inv->id) : null,
                ];
            })->all(),
        ];
    }

    if ($appointments->isNotEmpty()) {
        $upcoming = $appointments->whereIn('status', ['pending', 'approved'])->count();

        $panels[] = [
            'icon'     => 'calendar',
            'title'    => 'My Appointments',
            'subtitle' => 'Your scheduled counseling sessions.',
            'badge'    => $upcoming > 0 ? ['text' => $upcoming . ' Upcoming', 'class' => 'bg-emerald-100 text-emerald-700'] : null,
            'flash'    => session('appointment_cancelled') ? ['text' => session('appointment_cancelled'), 'tone' => 'danger'] : null,
            'rows'     => $appointments->map(function ($appt) {
                $meta = collect([
                    $appt->modeOfCounseling?->name,
                    $appt->supportNeeded?->name,
                    !empty($appt->concern) ? \Illuminate\Support\Str::limit($appt->concern, 50) : null,
                ])->filter()->implode(' — ');

                $canCancel = $appt->canBeCancelled();

                return [
                    'status'  => $appt->status,
                    'title'   => $appt->counseling_date->format('F d, Y'),
                    'slot'    => $appt->timeSlot?->name,
                    'meta'    => $meta,
                    'cancel'  => $canCancel ? route('guidance.appointment.cancel', $appt->id) : null,
                    'expired' => !$canCancel && $appt->status === 'approved',
                ];
            })->all(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Action cards
    |--------------------------------------------------------------------------
    */
    $cards = [];

    if (auth()->user()->role?->name !== 'guest') {
        $cards[] = [
            'icon'  => 'calendar',
            'title' => 'Book an Appointment',
            'desc'  => 'Schedule a one-on-one session with a guidance counselor for academic, personal, or career concerns.',
            'cta'   => 'Book Now',
            'href'  => route('guidance.appointment'),
        ];
    }

    $cards[] = [
        'icon'  => 'share',
        'title' => 'Referrals',
        'desc'  => 'Submit or view referral requests from faculty or staff for students who may need guidance support.',
        'cta'   => 'View Referrals',
        'href'  => route('guidance_referrals.get'),
    ];

    $btnNav = 'inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-medium text-white transition hover:bg-white/20 sm:text-sm';
@endphp

<body class="flex min-h-screen flex-col bg-emerald-50/60 font-sans text-slate-800 antialiased">

{{-- ── CANCEL APPOINTMENT MODAL (one shared modal) ── --}}
<div id="cancel-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="cancel-title"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if (event.target === this || event.target.dataset.backdrop) closeCancel()">
    <div data-backdrop="1" class="absolute inset-0 bg-green-950/60 backdrop-blur-sm"></div>

    <div class="modal-box relative z-10 w-full max-w-sm rounded-[2rem] bg-white p-7 text-center shadow-2xl">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 text-red-500">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['warning'] }}"/>
            </svg>
        </div>
        <h3 id="cancel-title" class="mb-1 font-display text-lg font-bold text-slate-900">Cancel Appointment?</h3>
        <p id="cancel-label" class="mb-1 text-sm text-slate-500"></p>
        <p class="mb-6 text-xs text-slate-400">This action cannot be undone. You can book a new appointment afterwards.</p>

        <div class="flex gap-3">
            <button type="button" onclick="closeCancel()"
                    class="flex-1 rounded-full border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 active:scale-95">
                Keep It
            </button>
            <form id="cancel-form" method="POST" class="flex-1">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="w-full rounded-full bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-95">
                    Yes, Cancel
                </button>
            </form>
        </div>
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
            Guidance Office
        </span>
        <h1 class="mb-4 font-display text-4xl font-extrabold leading-[1.05] tracking-tight text-emerald-50 drop-shadow-md sm:text-5xl md:text-6xl">
            How Can We <br>
            <span class="bg-gradient-to-r from-gvc-pale via-gvc-mint to-emerald-300 bg-clip-text text-transparent">Help You Today?</span>
        </h1>
        <p class="mx-auto max-w-xl text-sm text-emerald-50/85 sm:text-base">
            The Guidance Office is here to support your academic and personal well-being. Choose an option below to get started.
        </p>
    </div>
</section>

{{-- ── MAIN ── --}}
<main class="flex-1 py-16 md:py-20">
    <div class="mx-auto max-w-4xl px-6">

        {{-- INVITATIONS + APPOINTMENTS --}}
        @foreach ($panels as $p => $panel)
            <section class="fade-up delay-{{ min($p + 1, 3) }} mb-10" aria-label="{{ $panel['title'] }}">

                {{-- Panel header --}}
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$panel['icon']] }}"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-display text-lg font-bold text-slate-900">{{ $panel['title'] }}</h2>
                        <p class="text-xs text-slate-500">{{ $panel['subtitle'] }}</p>
                    </div>
                    @if ($panel['badge'])
                        <span class="ml-auto shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $panel['badge']['class'] }}">{{ $panel['badge']['text'] }}</span>
                    @endif
                </div>

                {{-- Flash message --}}
                @if ($panel['flash'])
                    @php $ok = $panel['flash']['tone'] === 'success'; @endphp
                    <div class="mb-3 flex items-center gap-2 rounded-2xl border px-4 py-3 text-sm
                        {{ $ok ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}"
                         role="status">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ok ? $icons['check'] : $icons['x'] }}"/>
                        </svg>
                        {{ $panel['flash']['text'] }}
                    </div>
                @endif

                {{-- Rows --}}
                <div class="space-y-2.5">
                    @foreach ($panel['rows'] as $row)
                        @php $st = $statusStyles[$row['status']] ?? $statusFallback; @endphp

                        <div class="flex flex-wrap items-center gap-3 rounded-2xl border bg-white px-5 py-4 shadow-sm {{ $st['border'] }}">

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $row['title'] }}
                                    @if ($row['slot'])
                                        <span class="font-normal text-slate-400">&mdash; {{ $row['slot'] }}</span>
                                    @endif
                                </p>
                                @if ($row['meta'])
                                    <p class="truncate text-xs text-slate-400">{{ $row['meta'] }}</p>
                                @endif
                            </div>

                            {{-- Status badge --}}
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $st['badge'] }}">
                                @if ($st['icon'] === 'pulse')
                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                                @elseif ($st['icon'] !== 'none')
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$st['icon']] }}"/>
                                    </svg>
                                @endif
                                {{ ucfirst($row['status']) }}
                            </span>

                            {{-- Invitation: accept / decline --}}
                            @if (!empty($row['respond']))
                                <div class="flex shrink-0 items-center gap-2">
                                    <form method="POST" action="{{ $row['respond'] }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-full bg-green-900 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-green-800 active:scale-95">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['check'] }}"/></svg>
                                            Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ $row['respond'] }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="declined">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-white px-4 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 active:scale-95">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['x'] }}"/></svg>
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- Appointment: cancel --}}
                            @if (!empty($row['cancel']))
                                <button type="button"
                                        data-action="{{ $row['cancel'] }}"
                                        data-label="{{ $row['title'] }}{{ $row['slot'] ? ' — ' . $row['slot'] : '' }}"
                                        onclick="openCancel(this)"
                                        class="inline-flex shrink-0 items-center gap-1 rounded-full border border-red-200 bg-white px-4 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 active:scale-95">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['x'] }}"/></svg>
                                    Cancel
                                </button>
                            @elseif (!empty($row['expired']))
                                <span class="shrink-0 text-xs italic text-slate-400">Cancellation window expired</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        {{-- ACTION CARDS --}}
        <div class="grid gap-6 {{ count($cards) > 1 ? 'sm:grid-cols-2' : 'mx-auto max-w-md' }}">
            @foreach ($cards as $i => $c)
                <a href="{{ $c['href'] }}"
                   class="group fade-up delay-{{ min($i + 1, 3) }} flex flex-col items-center rounded-3xl border border-green-200/60 bg-white p-8 text-center shadow-sm transition duration-300 hover:-translate-y-1.5 hover:border-emerald-300 hover:shadow-card-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">

                    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 transition-colors duration-300 group-hover:bg-emerald-200">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$c['icon']] }}"/>
                        </svg>
                    </div>

                    <h2 class="mb-2 font-display text-xl font-bold text-slate-900 transition-colors group-hover:text-emerald-800">{{ $c['title'] }}</h2>
                    <p class="mb-6 flex-1 text-sm leading-relaxed text-slate-500">{{ $c['desc'] }}</p>

                    <span class="inline-flex items-center gap-2 rounded-full bg-green-900 px-5 py-2.5 text-sm font-semibold text-white transition group-hover:bg-green-800">
                        {{ $c['cta'] }}
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['chevron-r'] }}"/>
                        </svg>
                    </span>
                </a>
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
    const cancelModal = document.getElementById('cancel-modal');

    function openCancel(btn) {
        document.getElementById('cancel-form').action = btn.dataset.action;
        document.getElementById('cancel-label').textContent = btn.dataset.label;
        cancelModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeCancel() {
        cancelModal.style.display = 'none';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCancel(); });
</script>

</body>
</html>