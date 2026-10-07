<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form – {{ $exam->title ?? 'Entrance Exam' }} | Green Valley College Foundation</title>
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
                        'card-hover': '0 25px 50px -12px rgba(15, 118, 110, 0.35)',
                        'btn-glow': '0 0 40px rgba(5, 46, 22, 0.7), 0 10px 40px -10px rgba(20, 83, 45, 0.6)'
                    }
                }
            }
        }
    </script>
    <style>
        /* Only animate if the visitor allows motion */
        @media (prefers-reduced-motion: no-preference) {

            /* Entrance animations (staggered with --d). "backwards" releases the
               element afterwards so hover effects keep working. */
            @keyframes riseIn {
                from { opacity: 0; transform: translateY(28px); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes dropIn {
                from { opacity: 0; transform: translateY(-100%); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes popIn {
                from { opacity: 0; transform: scale(.85); }
                to   { opacity: 1; transform: none; }
            }
            .rise { animation: riseIn .8s cubic-bezier(.22,1,.36,1) backwards; animation-delay: var(--d, 0ms); }
            .pop  { animation: popIn .6s cubic-bezier(.34,1.56,.64,1) backwards; animation-delay: var(--d, 0ms); }
            .drop { animation: dropIn .7s cubic-bezier(.22,1,.36,1) backwards; }

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

            /* Badge dot pulse */
            @keyframes ping2 {
                75%, 100% { transform: scale(2.2); opacity: 0; }
            }
            .ping-dot::after {
                content: ""; position: absolute; inset: 0; border-radius: 9999px;
                background: #6ee7b7; animation: ping2 1.8s cubic-bezier(0,0,.2,1) infinite;
            }

            /* Gentle icon bob */
            @keyframes bob {
                0%, 100% { translate: 0 0; }
                50%      { translate: 0 -6px; }
            }
            .bob { animation: bob 3.5s ease-in-out infinite; }

            /* Primary button glow pulse */
            @keyframes glowPulse {
                0%, 100% { box-shadow: 0 0 20px rgba(20,83,45,.25), 0 8px 24px -10px rgba(20,83,45,.5); }
                50%      { box-shadow: 0 0 34px rgba(20,83,45,.5), 0 8px 24px -10px rgba(20,83,45,.8); }
            }
            .glow-pulse { animation: glowPulse 3s ease-in-out infinite; }

            /* Button shine sweep on hover */
            .btn-shine { position: relative; overflow: hidden; }
            .btn-shine::before {
                content: ""; position: absolute; top: 0; left: -75%; width: 50%; height: 100%;
                background: linear-gradient(120deg, transparent, rgba(255,255,255,.35), transparent);
                transform: skewX(-20deg);
            }
            .btn-shine:hover::before { left: 130%; transition: left .7s ease; }
            .btn-shine:active { transform: scale(.98); }

            /* Shake (used when no course is selected) */
            @keyframes shake {
                10%, 90%      { transform: translateX(-2px); }
                20%, 80%      { transform: translateX(4px); }
                30%, 50%, 70% { transform: translateX(-6px); }
                40%, 60%      { transform: translateX(6px); }
            }
            .shake { animation: shake .6s cubic-bezier(.36,.07,.19,.97); }

            /* Course option: small press feedback */
            .course-option:active { transform: scale(.985); }

            /* Logo hover */
            .logo-img { transition: transform .3s ease; }
            a:hover > .logo-img { transform: rotate(6deg) scale(1.1); }
        }

        /* Spinner (only visible while submitting) */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 1rem; height: 1rem; border-radius: 9999px;
            border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
            animation: spin .7s linear infinite;
        }
    </style>
</head>

<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased">

@php
    $examPeriod   = \App\Models\Period::exam();
    $hasAttempted = \App\Models\ExamAttempt::hasAttempted(auth()->id(), $exam->id);
    $priorAttempt = $hasAttempted
        ? \App\Models\ExamAttempt::latestAttempt(auth()->id(), $exam->id)
        : null;
@endphp

<!-- NAVBAR -->
<header class="drop bg-green-800 border-b border-white sticky top-0 z-50 shadow-sm shadow-green-900/5">
    <div class="max-w-8xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">

        <a href="{{ url('/') }}" class="flex items-center gap-2 flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation" class="logo-img w-10 h-10 rounded-lg object-contain flex-shrink-0">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>

        <nav class="flex items-center gap-2 sm:gap-3">
            @auth
                <span class="hidden sm:inline text-xs sm:text-sm text-emerald-100 mr-2">
                    Hello, <span class="font-semibold">{{ auth()->user()->name }}</span>
                </span>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
                    @csrf
                </form>
                <button onclick="document.getElementById('logout-form').submit()"
                    class="inline-flex items-center rounded-full border border-emerald-200/70 bg-emerald-900/40 px-4 py-1.5 text-xs sm:text-sm font-medium text-emerald-50 hover:bg-emerald-800/80 hover:border-emerald-200 hover:-translate-y-0.5 transition">
                    Logout
                </button>
            @endauth
        </nav>

    </div>
</header>

<!-- PAGE HERO -->
<section class="relative overflow-hidden py-14 md:py-20">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.12]"
         style="background-image: url('{{ asset('images/gvc.png') }}');"></div>

    {{-- Floating glow blobs --}}
    <div class="float-a absolute -left-24 top-0 h-80 w-80 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="float-b absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="relative max-w-3xl mx-auto px-6 text-center">

        @if ($hasAttempted)
            <div class="rise inline-flex items-center gap-2 bg-emerald-900/60 border border-emerald-300/30 rounded-full px-4 py-1.5 mb-5" style="--d:100ms">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-emerald-100 text-xs font-medium tracking-wide">Exam Completed</span>
            </div>
            <h1 class="rise font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight mb-3 tracking-tight text-emerald-50 drop-shadow-md" style="--d:220ms">
                You've Already Taken
            </h1>
            <p class="rise text-gvc-pale font-display font-semibold text-lg mb-2" style="--d:340ms">This Entrance Exam</p>
            <p class="rise text-emerald-100/70 text-sm max-w-xl mx-auto" style="--d:440ms">
                Your submission for <strong class="text-emerald-200">{{ $exam->title ?? 'the Entrance Exam' }}</strong> has already been recorded.
                Each applicant may only take the exam once.
            </p>
        @else
            <div class="rise inline-flex items-center gap-2 bg-emerald-900/60 border border-emerald-300/30 rounded-full px-4 py-1.5 mb-5" style="--d:100ms">
                <span class="relative w-5 h-5 rounded-full bg-emerald-400 text-emerald-950 text-xs font-bold flex items-center justify-center">
                    <span class="ping-dot absolute inset-0 rounded-full"></span>
                    <span class="relative">1</span>
                </span>
                <span class="text-emerald-100 text-xs font-medium tracking-wide">Step 1 of 2 — Personal Information</span>
                <span class="text-emerald-400/50 text-xs mx-1">→</span>
                <span class="w-5 h-5 rounded-full bg-emerald-900/60 border border-emerald-400/40 text-emerald-400/60 text-xs font-bold flex items-center justify-center">2</span>
                <span class="text-emerald-300/50 text-xs font-medium tracking-wide">Take Exam</span>
            </div>
            <h1 class="rise font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight mb-3 tracking-tight text-emerald-50 drop-shadow-md" style="--d:220ms">
                Admission &amp; Scholarship
            </h1>
            <p class="rise shimmer-text bg-gradient-to-r from-gvc-pale via-gvc-mint to-emerald-300 bg-clip-text text-transparent font-display font-semibold text-lg mb-2" style="--d:340ms">Answer Sheet</p>
            <p class="rise text-emerald-100/70 text-sm max-w-xl mx-auto" style="--d:440ms">
                Please fill in your personal information completely before proceeding to
                <strong class="text-emerald-200">{{ $exam->title ?? 'the Entrance Exam' }}</strong>.
            </p>
        @endif

    </div>
</section>

<!-- FORM BODY -->
<section class="py-10 md:py-14 bg-gradient-to-b from-green-50/80 to-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- ── ALREADY ATTEMPTED STATE ── --}}
        @if ($hasAttempted)

            @if (session('info'))
                <div class="rise mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700 flex items-center gap-3" style="--d:100ms">
                    <svg class="w-5 h-5 flex-shrink-0 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            <div class="rise bg-white rounded-2xl shadow-sm border border-green-200/60 p-10 flex flex-col items-center text-center" style="--d:250ms">
                <div class="pop bob w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6" style="--d:450ms">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-2">Exam Already Submitted</h2>
                <p class="text-sm text-slate-500 leading-relaxed mb-1 max-w-md">
                    You have already completed this entrance exam. Only one attempt is allowed per applicant.
                </p>
                @if ($priorAttempt)
                    <p class="text-xs text-slate-400 mb-1">
                        Submitted on {{ $priorAttempt->completed_at?->format('F d, Y \a\t g:i A') ?? 'N/A' }}
                    </p>
                    <p class="text-sm font-semibold text-emerald-700 mb-6">
                        Your score: {{ $priorAttempt->score }} / {{ $priorAttempt->total_points }}
                        ({{ number_format($priorAttempt->percentage, 1) }}%)
                    </p>
                @else
                    <div class="mb-6"></div>
                @endif
                <a href="{{ route('exam.result', $priorAttempt) }}"
                   class="btn-shine inline-flex items-center gap-2 rounded-xl bg-gvc-primary px-6 py-2.5 text-sm font-semibold text-white shadow-btn-glow hover:bg-green-800 transition-all duration-150 hover:-translate-y-0.5 mb-3">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View My Result
                </a>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gray-100 border border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Home
                </a>
            </div>

        {{-- ── EXAM PERIOD CLOSED STATE ── --}}
        @elseif (!$examPeriod->is_open)

            <div class="rise bg-white rounded-2xl shadow-sm border border-gray-200 p-10 flex flex-col items-center text-center opacity-80" style="--d:200ms">
                <div class="pop w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-6" style="--d:400ms">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-2">Exam Period is Closed</h2>
                <p class="text-sm text-slate-500 leading-relaxed mb-2 max-w-md">
                    The entrance exam is not currently accepting submissions.
                    Please check back later or contact the admissions office for more information.
                </p>
                @if ($examPeriod->open_date)
                    <p class="text-sm font-semibold text-emerald-700 mb-6">
                        Opens on {{ $examPeriod->opensOnLabel() }}
                    </p>
                @else
                    <div class="mb-6"></div>
                @endif
                <a href="{{ url('/gvc') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gray-100 border border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Home
                </a>
            </div>

        {{-- ── NORMAL FORM STATE ── --}}
        @else

            {{-- Auto-fill notice --}}
            <div class="rise mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 flex items-start gap-3" style="--d:100ms">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Some fields have been <strong>auto-filled</strong> from your account. Please review and fill in the remaining fields before proceeding.</p>
            </div>

            @if ($errors->any())
                <div class="shake mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700" role="alert">
                    <p class="font-semibold mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('exam.admission.store', $exam) }}" id="admissionForm">
                @csrf

                {{-- ── PERSONAL INFORMATION ── --}}
                <div class="rise bg-white rounded-2xl shadow-sm border border-green-200/60 p-6 sm:p-8 mb-5 transition-all duration-300 hover:shadow-card-hover hover:border-emerald-300/50" style="--d:200ms">

                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h2 class="font-display font-bold text-slate-800 text-base tracking-tight">Personal Information</h2>
                    </div>

                    {{-- Locked account row --}}
                    <div class="rise flex items-center gap-4 mb-6 bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-4" style="--d:320ms">
                        <div class="pop w-11 h-11 rounded-full bg-green-700 flex items-center justify-center flex-shrink-0 select-none" style="--d:450ms">
                            <span class="text-white font-bold text-sm font-display">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                            <p class="text-xs text-emerald-700">{{ $user->email }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Pulled from your account · cannot be changed here</p>
                        </div>
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <input type="hidden" name="name"  value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    {{-- Birth Date + Age — both readonly / auto-computed --}}
                    <div class="rise grid grid-cols-2 gap-4 mb-5" style="--d:420ms">

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                                Birth Date <span class="text-red-400">*</span>
                            </label>
                            <input type="date"
                                   name="birth_date"
                                   id="birthDateField"
                                   value="{{ old('birth_date', $formattedBirthdate) }}"
                                   readonly
                                   class="w-full border-0 border-b-2 border-slate-200 bg-transparent py-2 text-sm text-slate-600 outline-none transition cursor-not-allowed select-none @error('birth_date') border-red-400 @enderror">
                            @error('birth_date')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                                Age <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   name="age"
                                   id="ageField"
                                   value="{{ old('age', $computedAge) }}"
                                   placeholder="—"
                                   min="15" max="60"
                                   readonly
                                   class="w-full border-0 border-b-2 border-slate-200 bg-transparent py-2 text-sm text-slate-600 outline-none transition cursor-not-allowed select-none @error('age') border-red-400 @enderror">
                            @error('age')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Address — readonly, auto-filled from users table --}}
                    <div class="rise mb-5" style="--d:500ms">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                            Address <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               name="address"
                               value="{{ old('address', $user->address) }}"
                               readonly
                               class="w-full border-0 border-b-2 border-slate-200 bg-transparent py-2 text-sm text-slate-600 outline-none transition cursor-not-allowed select-none @error('address') border-red-400 @enderror">
                        @error('address')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Number — readonly, auto-filled from users table --}}
                    <div class="rise mb-5" style="--d:580ms">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                            Contact Number <span class="text-red-400">*</span>
                        </label>
                        <input type="tel"
                               name="contact_number"
                               value="{{ old('contact_number', $user->contact_no) }}"
                               readonly
                               class="w-full border-0 border-b-2 border-slate-200 bg-transparent py-2 text-sm text-slate-600 outline-none transition cursor-not-allowed select-none @error('contact_number') border-red-400 @enderror">
                        @error('contact_number')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Track / Strand — manual entry, moved below contact number --}}
                    <div class="rise mb-5" style="--d:660ms">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                            Track / Strand Graduated <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               name="track_strand"
                               value="{{ old('track_strand') }}"
                               placeholder="e.g. STEM, ABM, HUMSS, TVL-ICT"
                               class="w-full border-0 border-b-2 border-slate-200 focus:border-green-600 bg-transparent py-2 text-sm text-slate-800 outline-none transition placeholder:text-slate-300 @error('track_strand') border-red-400 @enderror">
                        @error('track_strand')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Last School Attended — manual entry, moved below track/strand --}}
                    <div class="rise mb-0" style="--d:740ms">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                            Last School Attended <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               name="last_school"
                               value="{{ old('last_school') }}"
                               placeholder="e.g. Koronadal National Comprehensive High School"
                               class="w-full border-0 border-b-2 border-slate-200 focus:border-green-600 bg-transparent py-2 text-sm text-slate-800 outline-none transition placeholder:text-slate-300 @error('last_school') border-red-400 @enderror">
                        @error('last_school')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── PREFERRED COURSE ── --}}
                <div id="courseCard" class="rise bg-white rounded-2xl shadow-sm border border-green-200/60 p-6 sm:p-8 mb-5 transition-all duration-300 hover:shadow-card-hover hover:border-emerald-300/50" style="--d:300ms">

                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 12c0 5.523-4.477 10-10 10S2 17.523 2 12c0-.538.043-1.065.125-1.578L12 14z"/>
                            </svg>
                        </div>
                        <h2 class="font-display font-bold text-slate-800 text-base tracking-tight">Preferred Course</h2>
                    </div>
                    <p class="text-xs text-slate-400 mb-5 ml-9">Please check (✓) your preferred course below.</p>

                    @error('preferred_course')
                        <p class="text-xs text-red-500 mb-3 bg-red-50 border border-red-200 rounded-lg px-3 py-2">{{ $message }}</p>
                    @enderror

                    @if ($programs->isEmpty())
                        <p class="text-sm text-slate-400 italic">No programs are currently available. Please check back later.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($programs as $program)
                                <label class="course-option rise flex items-start gap-3 p-3 rounded-xl border-2 border-slate-100 cursor-pointer transition-all duration-150 hover:border-green-400 hover:bg-green-50/60 hover:-translate-y-0.5 has-[:checked]:border-green-600 has-[:checked]:bg-green-50"
                                       style="--d:{{ 400 + $loop->index * 50 }}ms">
                                    <input type="radio"
                                           name="preferred_course"
                                           value="{{ $program->name }}"
                                           {{ old('preferred_course') === $program->name ? 'checked' : '' }}
                                           class="mt-0.5 w-4 h-4 accent-green-700 flex-shrink-0 cursor-pointer">
                                    <span class="text-sm text-slate-700 leading-snug">
                                        {{ $program->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                </div>

                {{-- ── PROCEED BUTTON ── --}}
                <div class="rise bg-white rounded-2xl shadow-sm border border-green-200/60 p-5 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4" style="--d:500ms">
                    <div class="flex items-center gap-3">
                        <div class="bob w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-medium">You will proceed to</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $exam->title ?? 'Entrance Exam' }}</p>
                        </div>
                    </div>

                    <button type="submit" id="proceedBtn"
                        class="btn-shine glow-pulse group inline-flex items-center justify-center gap-2 rounded-xl bg-gvc-primary px-7 py-3 text-sm font-semibold text-white shadow-btn-glow hover:bg-green-800 transition-all duration-150 hover:-translate-y-0.5 w-full sm:w-auto">
                        <span class="spinner hidden" id="proceedSpinner" aria-hidden="true"></span>
                        <svg id="proceedArrow" class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        <span id="proceedLabel">Proceed to Exam</span>
                    </button>
                </div>

            </form>

        @endif

    </div>
</section>

<!-- FOOTER -->
<footer class="py-10 bg-slate-800 border-t border-green-300/20">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.</p>
        <p class="text-xs text-slate-600 mt-1">FM-AAD-063 · Admission and Scholarship Answer Sheet</p>
    </div>
</footer>

<script>
    /**
     * Compute age from a Y-m-d date string and write it into #ageField.
     * Returns early and clears the field if the date is missing or invalid.
     */
    function syncAge(dobValue) {
        const ageField = document.getElementById('ageField');
        if (!dobValue || !ageField) return;

        const dob   = new Date(dobValue);
        const today = new Date();

        // Guard against invalid dates
        if (isNaN(dob.getTime())) {
            ageField.value = '';
            return;
        }

        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        // Adjust if the birthday hasn't occurred yet this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        // Only set if the result is a plausible age
        ageField.value = (age >= 0 && age < 120) ? age : '';
    }

    // ── On page load: compute age from the pre-filled birthdate ──────────────
    const birthField = document.getElementById('birthDateField');
    if (birthField) {
        syncAge(birthField.value);
        // birthDateField is readonly so no 'change' listener needed
    }

    // ── Form validation: require preferred_course before submit ───────────────
    document.getElementById('admissionForm')?.addEventListener('submit', function (e) {
        const selected = document.querySelector('input[name="preferred_course"]:checked');

        if (!selected) {
            e.preventDefault();

            // Draw attention to the course card, then show the alert
            const card = document.getElementById('courseCard');
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.remove('shake');
                void card.offsetWidth;
                card.classList.add('shake');
            }
            alert('Please select your preferred course before proceeding.');
            return;
        }

        // Valid: show a spinner while the form submits
        const btn = document.getElementById('proceedBtn');
        document.getElementById('proceedSpinner').classList.remove('hidden');
        document.getElementById('proceedArrow').classList.add('hidden');
        document.getElementById('proceedLabel').textContent = 'Loading exam...';
        btn.classList.add('opacity-80', 'cursor-wait');
        btn.setAttribute('aria-busy', 'true');
    });

    // If the browser restores this page from the back/forward cache, reset the button
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        const btn = document.getElementById('proceedBtn');
        if (!btn) return;
        document.getElementById('proceedSpinner').classList.add('hidden');
        document.getElementById('proceedArrow').classList.remove('hidden');
        document.getElementById('proceedLabel').textContent = 'Proceed to Exam';
        btn.classList.remove('opacity-80', 'cursor-wait');
    });
</script>

</body>
</html>