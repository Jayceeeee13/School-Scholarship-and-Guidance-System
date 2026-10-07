<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Scope – {{ $exam->title ?? 'Admission & Scholarship Test' }} | Green Valley College Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'system-ui', 'sans-serif'],
                        display: ['Outfit', 'sans-serif']
                    },
                    colors: {
                        gvc: {
                            primary: '#14532d',
                            dark: '#052e16',
                            light: '#166534',
                            pale: '#bbf7d0',
                            mint: '#4ade80'
                        }
                    },
                    backgroundImage: {
                        'hero-gradient': 'linear-gradient(135deg, #022c22 0%, #14532d 30%, #166534 60%, #15803d 100%)',
                        'hero-pattern': "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23014524' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")"
                    },
                    boxShadow: {
                        'card-hover': '0 25px 50px -12px rgba(15, 118, 110, 0.25)',
                        'btn-glow': '0 0 40px rgba(5, 46, 22, 0.7), 0 10px 40px -10px rgba(20, 83, 45, 0.6)'
                    }
                }
            }
        }
    </script>
    <style>
        .cat-num {
            background: linear-gradient(135deg, #14532d, #166534);
        }

        .progress-step-active   { background: #14532d; color: #fff; }
        .progress-step-done     { background: #bbf7d0; color: #14532d; }
        .progress-step-inactive { background: #f1f5f9; color: #94a3b8; }

        .scope-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .scope-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -12px rgba(20, 83, 45, 0.16);
            border-color: #86efac;
        }
        .scope-card:hover .cat-num { transform: rotate(-6deg) scale(1.08); }
        .cat-num { transition: transform .25s ease; }

        /* Only animate if the visitor allows motion */
        @media (prefers-reduced-motion: no-preference) {

            /* Entrance animations (staggered with --d). "backwards" releases the
               element afterwards so hover effects keep working. */
            @keyframes riseIn {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes dropIn {
                from { opacity: 0; transform: translateY(-100%); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes popIn {
                from { opacity: 0; transform: scale(.88); }
                to   { opacity: 1; transform: none; }
            }
            .rise { animation: riseIn .75s cubic-bezier(.22,1,.36,1) backwards; animation-delay: var(--d, 0ms); }
            .pop  { animation: popIn .55s cubic-bezier(.34,1.56,.64,1) backwards; animation-delay: var(--d, 0ms); }
            .drop { animation: dropIn .7s cubic-bezier(.22,1,.36,1) backwards; }

            /* Scope cards: slide in from the left, one after another */
            @keyframes slideInLeft {
                from { opacity: 0; transform: translateX(-36px); }
                to   { opacity: 1; transform: none; }
            }
            .slide-l { animation: slideInLeft .7s cubic-bezier(.22,1,.36,1) backwards; animation-delay: var(--d, 0ms); }

            /* Floating blurred blobs */
            @keyframes floatSlow {
                0%, 100% { translate: 0 0; }
                50%      { translate: 26px -30px; }
            }
            .float-a { animation: floatSlow 12s ease-in-out infinite; }
            .float-b { animation: floatSlow 16s ease-in-out infinite reverse; }

            /* Heading gradient shimmer */
            @keyframes shimmer {
                0%   { background-position: 0% 50%; }
                100% { background-position: 100% 50%; }
            }
            .shimmer-text {
                background-size: 200% auto;
                animation: shimmer 5s ease-in-out infinite alternate;
            }

            /* Active step ring pulse */
            @keyframes ringPulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(74,222,128,.55); }
                50%      { box-shadow: 0 0 0 7px rgba(74,222,128,0); }
            }
            .step-pulse { animation: ringPulse 2s ease-in-out infinite; }

            /* Amber bullets gently pulse */
            @keyframes dotPulse {
                0%, 100% { transform: scale(1); opacity: 1; }
                50%      { transform: scale(1.5); opacity: .6; }
            }
            .dot-pulse { animation: dotPulse 2.4s ease-in-out infinite; }

            /* Start button: glow when enabled + pop when it unlocks */
            @keyframes glow-pulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(20, 83, 45, 0.4); }
                50%      { box-shadow: 0 0 0 10px rgba(20, 83, 45, 0); }
            }
            #startBtn:not(.opacity-40) { animation: glow-pulse 2s ease-in-out infinite; }

            @keyframes unlock {
                0%   { transform: scale(.94); }
                60%  { transform: scale(1.06); }
                100% { transform: scale(1); }
            }
            .unlock { animation: unlock .45s cubic-bezier(.34,1.56,.64,1); }

            /* Button shine sweep on hover */
            .btn-shine { position: relative; overflow: hidden; }
            .btn-shine::before {
                content: ""; position: absolute; top: 0; left: -75%; width: 50%; height: 100%;
                background: linear-gradient(120deg, transparent, rgba(255,255,255,.35), transparent);
                transform: skewX(-20deg);
            }
            .btn-shine:hover::before { left: 130%; transition: left .7s ease; }

            /* Logo hover */
            .logo-img { transition: transform .3s ease; }
            a:hover > .logo-img { transform: rotate(6deg) scale(1.1); }
        }

        /* Spinner (only visible after clicking Begin Exam) */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 1rem; height: 1rem; border-radius: 9999px;
            border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
            animation: spin .7s linear infinite;
        }
    </style>
</head>

<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased min-h-screen">

{{-- ── NAVBAR ── --}}
<header class="drop bg-green-800 border-b border-white/10 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center gap-4">
        <a href="{{ url('/gvc') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="GVCFI" class="logo-img w-10 h-10 rounded-lg object-contain">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight">
                Green Valley College Foundation Inc.
            </span>
        </a>
        <div class="flex items-center gap-3">
            <span class="hidden sm:inline text-xs text-emerald-200">
                {{ auth()->user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-xs font-medium text-emerald-100 border border-emerald-200/50 px-3 py-1.5 rounded-full hover:bg-emerald-700 hover:-translate-y-0.5 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

{{-- ── STEP INDICATOR HERO ── --}}
<section class="relative overflow-hidden py-12 md:py-16">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute inset-0 bg-cover bg-center opacity-[0.08]"
         style="background-image: url('{{ asset('images/gvc.png') }}');"></div>

    {{-- Floating glow blobs --}}
    <div class="float-a absolute -left-24 top-0 h-80 w-80 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="float-b absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="relative max-w-3xl mx-auto px-6 text-center">

        {{-- 3-step progress --}}
        <div class="rise inline-flex items-center gap-1 sm:gap-2 bg-emerald-900/60 border border-emerald-300/20 rounded-2xl px-4 py-3 mb-6" style="--d:100ms">

            {{-- Step 1 - Done --}}
            <div class="flex items-center gap-1.5">
                <div class="pop progress-step-done w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0" style="--d:300ms">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-emerald-300/70 text-xs font-medium hidden sm:inline">Personal Info</span>
            </div>

            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>

            {{-- Step 2 - Active --}}
            <div class="flex items-center gap-1.5">
                <div class="pop step-pulse progress-step-active w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 ring-2 ring-emerald-400 ring-offset-1 ring-offset-emerald-900/60" style="--d:450ms">
                    2
                </div>
                <span class="text-emerald-100 text-xs font-semibold hidden sm:inline">Exam Scope</span>
            </div>

            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>

            {{-- Step 3 - Pending --}}
            <div class="flex items-center gap-1.5">
                <div class="pop progress-step-inactive w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0" style="--d:600ms">
                    3
                </div>
                <span class="text-emerald-400/40 text-xs font-medium hidden sm:inline">Take Exam</span>
            </div>
        </div>

        <h1 class="rise font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight mb-3 tracking-tight text-emerald-50 drop-shadow-md" style="--d:220ms">
            Scope of the<br>
            <span class="shimmer-text bg-gradient-to-r from-gvc-pale via-gvc-mint to-emerald-300 bg-clip-text text-transparent">{{ $exam->title ?? 'Admission & Scholarship Test' }}</span>
        </h1>
        <p class="rise text-emerald-100/70 text-sm max-w-xl mx-auto" style="--d:340ms">
            Read and understand the coverage of each section before you begin.
            The exam timer starts only when you click <strong class="text-emerald-200">Begin Exam</strong>.
        </p>

        {{-- Total items badge --}}
        <div class="rise inline-flex items-center gap-3 mt-5 bg-emerald-900/50 border border-emerald-400/20 rounded-xl px-5 py-2.5" style="--d:460ms">
            <span class="text-emerald-200 text-xs font-medium">Total Items:</span>
            <span class="font-display text-xl font-bold text-emerald-300" data-count="{{ $totalItems }}">{{ $totalItems }}</span>
            <span class="w-px h-4 bg-emerald-600"></span>
            <span class="text-emerald-200 text-xs font-medium">Duration:</span>
            <span class="font-display text-xl font-bold text-emerald-300">{{ $exam->duration_minutes }} mins</span>
        </div>

    </div>
</section>

{{-- ── MAIN CONTENT ── --}}
<section class="py-10 md:py-14 bg-gradient-to-b from-green-50/80 to-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <p class="rise text-sm text-slate-500 leading-relaxed mb-8 text-center" style="--d:100ms">
            The admission test will cover the following key areas:
        </p>

        {{-- ── SCOPE CARDS (dynamic) ── --}}
        <div class="space-y-3 mb-10">
            @php
                $numerals = ['I','II','III','IV','V','VI','VII','VIII','IX','X'];
            @endphp

            @forelse ($categories as $index => $category)
                <div class="scope-card slide-l bg-white rounded-2xl border border-slate-200 p-5 flex items-start gap-4"
                     style="--d:{{ 200 + min($index, 8) * 90 }}ms">
                    <div class="cat-num w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                        <span class="font-display font-bold text-white text-sm">
                            {{ $numerals[$index] ?? ($index + 1) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h3 class="font-display font-bold text-slate-800 text-sm">
                                {{ $category->name }}
                            </h3>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0">
                                {{ $category->questions_count }} items
                            </span>
                        </div>
                        @if ($category->description)
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                {{ $category->description }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rise text-center py-12 bg-white rounded-2xl border border-slate-200" style="--d:200ms">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-slate-400">No exam categories available yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Summary note --}}
        <div class="rise bg-emerald-50 border border-emerald-200/70 rounded-2xl p-5 mb-8" style="--d:150ms">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-emerald-800 leading-relaxed">
                    The test will be comprehensive but will focus primarily on fundamental concepts, ensuring it is appropriate
                    for applicants with varying levels of prior academic exposure. The purpose of this scope is to give a
                    well-rounded assessment of a student's overall academic potential, rather than specializing in one
                    particular subject area.
                </p>
            </div>
        </div>

        {{-- Reminders --}}
        <div class="rise bg-white border border-amber-200/70 rounded-2xl p-5 mb-8" style="--d:200ms">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h4 class="font-display font-bold text-slate-800 text-sm">Important Reminders</h4>
            </div>
            <ul class="space-y-2">
                @foreach ([
                    'The timer starts immediately once you click <strong>Begin Exam</strong> and cannot be paused.',
                    'You can only take this exam <strong>once</strong>. Your answers are final upon submission.',
                    'Do not refresh or close the browser tab during the exam.',
                    'Unanswered questions will be automatically marked as incorrect.',
                    'Make sure you have a stable internet connection before proceeding.',
                ] as $reminder)
                    <li class="rise flex items-start gap-2 text-xs text-slate-600" style="--d:{{ 300 + $loop->index * 90 }}ms">
                        <span class="dot-pulse w-1.5 h-1.5 rounded-full bg-amber-400 mt-1.5 flex-shrink-0"></span>
                        <span>{!! $reminder !!}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Agreement & CTA --}}
        <div class="rise bg-white rounded-2xl border border-green-200/60 shadow-sm p-6" id="ctaBox" style="--d:250ms">

            <label class="flex items-start gap-3 cursor-pointer mb-5 group">
                <input type="checkbox" id="agreeCheck"
                       class="mt-0.5 w-4 h-4 accent-green-700 cursor-pointer flex-shrink-0"
                       onchange="toggleStart()">
                <span class="text-sm text-slate-600 leading-relaxed group-hover:text-slate-800 transition">
                    I have read and understood the scope of the
                    <strong class="text-slate-800">{{ $exam->title ?? 'Admission and Scholarship Test' }}</strong>.
                    I confirm that I am ready to begin and I understand that this exam can only be taken once.
                </span>
            </label>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ url()->previous() }}"
                   class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>

                <a href="{{ route('exam.show', $exam->id) }}" id="startBtn"
                   class="btn-shine inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-gvc-primary text-white font-display font-bold text-sm transition-all duration-150 pointer-events-none opacity-40 w-full sm:w-auto"
                   aria-disabled="true">
                    <span class="spinner hidden" id="startSpinner" aria-hidden="true"></span>
                    <svg id="startIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="startLabel">Begin Exam</span>
                </a>
            </div>
        </div>

    </div>
</section>

{{-- ── FOOTER ── --}}
<footer class="mt-6 py-8 bg-slate-800 border-t border-green-300/20">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.</p>
        <p class="text-xs text-slate-600 mt-1">FM-AAD-063 · Admission and Scholarship Answer Sheet</p>
    </div>
</footer>

<script>
    function toggleStart() {
        const btn   = document.getElementById('startBtn');
        const check = document.getElementById('agreeCheck');
        if (check.checked) {
            btn.classList.remove('pointer-events-none', 'opacity-40');
            btn.classList.add('hover:-translate-y-0.5', 'shadow-btn-glow');
            btn.removeAttribute('aria-disabled');

            // Small "unlock" pop
            btn.classList.remove('unlock');
            void btn.offsetWidth;
            btn.classList.add('unlock');
        } else {
            btn.classList.add('pointer-events-none', 'opacity-40');
            btn.classList.remove('hover:-translate-y-0.5', 'shadow-btn-glow', 'unlock');
            btn.setAttribute('aria-disabled', 'true');
        }
    }

    // Show a spinner after clicking Begin Exam
    document.getElementById('startBtn').addEventListener('click', function () {
        if (this.getAttribute('aria-disabled') === 'true') return;
        document.getElementById('startSpinner').classList.remove('hidden');
        document.getElementById('startIcon').classList.add('hidden');
        document.getElementById('startLabel').textContent = 'Starting...';
        this.classList.add('cursor-wait');
    });

    // Reset if the page is restored from the back/forward cache
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.getElementById('startSpinner').classList.add('hidden');
        document.getElementById('startIcon').classList.remove('hidden');
        document.getElementById('startLabel').textContent = 'Begin Exam';
        document.getElementById('startBtn').classList.remove('cursor-wait');
    });
</script>

</body>
</html>