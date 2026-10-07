<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Green Valley College Foundation</title>
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
                        gvc: { primary: '#14532d', dark: '#052e16', light: '#166534', pale: '#bbf7d0', mint: '#4ade80' }
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
        .fields-section.hidden { display: none; }

        /* Only animate if the visitor allows motion */
        @media (prefers-reduced-motion: no-preference) {

            /* Entrance animations (staggered with --d) */
            @keyframes riseIn {
                from { opacity: 0; transform: translateY(28px); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(60px) scale(.97); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes slideInLeft {
                from { opacity: 0; transform: translateX(-40px); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes dropIn {
                from { opacity: 0; transform: translateY(-100%); }
                to   { opacity: 1; transform: none; }
            }
            .rise      { animation: riseIn .8s cubic-bezier(.22,1,.36,1) both; animation-delay: var(--d, 0ms); }
            .slide     { animation: slideInRight .9s cubic-bezier(.22,1,.36,1) both; animation-delay: var(--d, 0ms); }
            .slide-l   { animation: slideInLeft .8s cubic-bezier(.22,1,.36,1) both; animation-delay: var(--d, 0ms); }
            .drop      { animation: dropIn .7s cubic-bezier(.22,1,.36,1) both; }

            /* Form fields: animate every time their tab becomes visible.
               First load waits for the card to appear; later tab switches are instant. */
            .fields-section { --base: 700ms; }
            body.is-ready .fields-section { --base: 0ms; }
            .fields-section > * {
                animation: riseIn .55s cubic-bezier(.22,1,.36,1) both;
                animation-delay: calc(var(--base) + var(--i, 0) * 55ms);
            }
            [data-hint]:not(.hidden) { animation: riseIn .45s cubic-bezier(.22,1,.36,1) both; }

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

            /* Soft pulse on the badge dot */
            @keyframes ping2 {
                75%, 100% { transform: scale(2.2); opacity: 0; }
            }
            .ping-dot::after {
                content: ""; position: absolute; inset: 0; border-radius: 9999px;
                background: #6ee7b7; animation: ping2 1.8s cubic-bezier(0,0,.2,1) infinite;
            }

            /* Login pill glow */
            @keyframes glowPulse {
                0%, 100% { box-shadow: 0 0 14px rgba(167,243,208,.15); }
                50%      { box-shadow: 0 0 26px rgba(167,243,208,.45); }
            }
            .glow-pulse { animation: glowPulse 3s ease-in-out infinite; }

            /* Shake the error box */
            @keyframes shake {
                10%, 90%      { transform: translateX(-2px); }
                20%, 80%      { transform: translateX(4px); }
                30%, 50%, 70% { transform: translateX(-6px); }
                40%, 60%      { transform: translateX(6px); }
            }
            .shake { animation: shake .6s cubic-bezier(.36,.07,.19,.97) both; }

            /* Button shine sweep on hover */
            .btn-shine { position: relative; overflow: hidden; }
            .btn-shine::before {
                content: ""; position: absolute; top: 0; left: -75%; width: 50%; height: 100%;
                background: linear-gradient(120deg, transparent, rgba(255,255,255,.35), transparent);
                transform: skewX(-20deg);
            }
            .btn-shine:hover::before { left: 130%; transition: left .7s ease; }
            .btn-shine:active { transform: scale(.98); }

            /* Input lift on focus */
            .field { transition: box-shadow .25s ease, border-color .25s ease, transform .25s ease; }
            .field:focus { transform: translateY(-1px); }

            /* Info cards hover */
            .info-card { transition: transform .3s ease, background-color .3s ease, border-color .3s ease; }
            .info-card:hover { transform: translateX(6px); border-color: rgba(110,231,183,.5); }

            /* Tab buttons */
            [data-tab] { transition: background-color .25s ease, color .25s ease, transform .2s ease, box-shadow .25s ease; }
            [data-tab]:active { transform: scale(.96); }
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

@php
    /*
    |--------------------------------------------------------------------------
    | Page configuration
    | Field names are unchanged, so the RegisterController keeps working as is.
    |--------------------------------------------------------------------------
    */
    $active = old('enrollment_type', 'enrolled');

    // Heroicons (outline) path data
    $icons = [
        'student' => 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5',
        'visitor' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
        'eye'     => 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        'eye-off' => 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88',
    ];

    // Registration types: used for the left info cards, the tabs and the hints
    $types = [
        'enrolled' => [
            'tab'       => 'Student',
            'icon'      => 'student',
            'title'     => 'Enrolled Student',
            'desc'      => 'Use your Student ID, last name, and birthdate to verify your enrollment and instantly link your account.',
            'hint'      => 'Verify your identity using your Student ID, last name, and birthdate.',
            'hintClass' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
        ],
        'unenrolled' => [
            'tab'       => 'Visitor',
            'icon'      => 'visitor',
            'title'     => 'Not Yet Enrolled',
            'desc'      => 'You can still register to take the scholarship qualifying exam. Apply for enrollment afterward to access full scholarship benefits.',
            'hint'      => "You can take the scholarship exam now. You'll need to enroll first before applying for a scholarship.",
            'hintClass' => 'text-amber-700 bg-amber-50 border-amber-200',
        ],
    ];

    // Form fields per registration type.
    // half => true places the field in a half-width column.
    // required => false removes the red asterisk.
    $sections = [
        'enrolled' => [
            ['name' => 'student_id',          'label' => 'Student ID', 'type' => 'text', 'placeholder' => 'e.g. 2024-00001'],
            ['name' => 'last_name_enrolled',  'label' => 'Last Name',  'type' => 'text', 'placeholder' => 'e.g. Dela Cruz'],
            ['name' => 'birthdate_enrolled',  'label' => 'Birthdate',  'type' => 'date'],
            ['type' => 'divider', 'label' => 'Account credentials'],
            ['name' => 'email',                 'label' => 'Email',            'type' => 'email'],
            ['name' => 'password',              'label' => 'Password',         'type' => 'password', 'id' => 'password_enrolled'],
            ['name' => 'password_confirmation', 'label' => 'Confirm Password', 'type' => 'password', 'id' => 'password_confirm_enrolled'],
        ],
        'unenrolled' => [
            ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'half' => true],
            ['name' => 'last_name',  'label' => 'Last Name',  'type' => 'text', 'half' => true],
            ['name' => 'birthdate',  'label' => 'Birthdate',  'type' => 'date'],
            ['name' => 'gender_id',  'label' => 'Gender',     'type' => 'select', 'required' => false,
                'placeholder' => 'Select gender (optional)', 'options' => [1 => 'Male', 2 => 'Female']],
            ['name' => 'contact_no', 'label' => 'Contact Number', 'type' => 'text', 'placeholder' => 'e.g. 09XX XXX XXXX'],
            ['name' => 'address',    'label' => 'Address',        'type' => 'text', 'placeholder' => 'House no., street, barangay, city'],
            ['type' => 'divider', 'label' => 'Account credentials'],
            ['name' => 'email_unenrolled',                 'label' => 'Email',            'type' => 'email'],
            ['name' => 'password_unenrolled',              'label' => 'Password',         'type' => 'password', 'id' => 'password_unenrolled'],
            ['name' => 'password_confirmation_unenrolled', 'label' => 'Confirm Password', 'type' => 'password', 'id' => 'password_confirm_unenrolled'],
        ],
    ];

    // Shared input styling (turns red when the field has a validation error)
    $inputClass = fn (string $name, string $extra = '') =>
        'field block w-full rounded-lg border px-3 py-2.5 text-sm shadow-sm outline-none focus:ring-2 '
        . ($errors->has($name)
            ? 'border-red-400 focus:border-red-500 focus:ring-red-200'
            : 'border-slate-300 focus:border-emerald-500 focus:ring-emerald-200')
        . ' ' . $extra;

    $tabOn  = 'bg-emerald-700 text-white shadow-sm';
    $tabOff = 'text-slate-600 hover:text-slate-800';
@endphp

<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased">

<!-- NAVBAR -->
<header class="drop bg-green-800 border-b border-white sticky top-0 z-40 shadow-sm shadow-green-900/5">
    <div class="max-w-7xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ url('/') }}" class="group flex items-center gap-2 flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation" class="w-10 h-10 rounded-lg object-contain flex-shrink-0 transition duration-300 group-hover:rotate-6 group-hover:scale-110">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>
        <nav class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('login') }}"
               class="glow-pulse inline-flex items-center rounded-full border border-emerald-200/70 bg-emerald-900/40 px-4 py-1.5 text-xs sm:text-sm font-medium text-emerald-50 hover:bg-emerald-800/80 hover:border-emerald-200 hover:-translate-y-0.5 transition">
                Login
            </a>
        </nav>
    </div>
</header>

<!-- HERO BACKGROUND -->
<section class="relative min-h-[calc(100vh-64px)] flex items-center overflow-hidden">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat opacity-60"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.08]"
         style="background-image: url('{{ asset('images/gvc.png') }}');"></div>

    {{-- Floating glow blobs --}}
    <div class="float-a absolute -left-24 top-10 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="float-b absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="relative max-w-6xl mx-auto px-6 py-12 grid gap-10 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] items-start">

        {{-- LEFT SIDE --}}
        <div class="text-emerald-50 space-y-4 max-w-xl">
            <p class="rise inline-flex items-center gap-2 rounded-full bg-emerald-900/40 border border-emerald-300/40 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide" style="--d:150ms">
                <span class="ping-dot relative inline-block h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                Student Registration
            </p>
            <h1 class="rise font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight drop-shadow-md" style="--d:280ms">
                Create your<br class="hidden sm:block">
                <span class="shimmer-text bg-gradient-to-r from-gvc-pale via-gvc-mint to-emerald-300 bg-clip-text text-transparent">Scholarship &amp; Guidance</span> account
            </h1>
            <p class="rise text-sm sm:text-base text-emerald-100/90" style="--d:420ms">
                Tell us a bit about yourself so we can match you with the right scholarship and guidance services.
            </p>

            <div class="space-y-3 pt-2">
                @foreach ($types as $t)
                    <div class="slide-l info-card flex items-start gap-3 bg-emerald-900/40 border border-emerald-300/20 rounded-xl px-4 py-3"
                         style="--d:{{ 550 + $loop->index * 150 }}ms">
                        <div class="mt-0.5 w-7 h-7 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$t['icon']] }}" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-emerald-200">{{ $t['title'] }}</p>
                            <p class="text-xs text-emerald-100/70 mt-0.5">{{ $t['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- RIGHT SIDE: FORM CARD --}}
        <div class="slide w-full max-w-md ml-auto bg-white/95 backdrop-blur rounded-2xl shadow-xl shadow-emerald-950/40 border border-emerald-200/70 p-6 md:p-8" style="--d:350ms">
            <h2 class="rise font-display text-xl md:text-2xl font-semibold mb-1 text-slate-900" style="--d:550ms">Create an account</h2>
            <p class="rise text-sm text-slate-600 mb-5" style="--d:620ms">Register as a student to access the portal.</p>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="shake mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm p-3" role="alert">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ENROLLMENT TYPE TOGGLE --}}
            <div class="rise mb-5" style="--d:680ms">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">I am...</p>

                <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                    @foreach ($types as $key => $t)
                        <button type="button" data-tab="{{ $key }}" onclick="switchType('{{ $key }}')"
                                class="rounded-lg px-3 py-2 text-sm font-semibold inline-flex items-center justify-center gap-2 {{ $active === $key ? $tabOn : $tabOff }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$t['icon']] }}" />
                            </svg>
                            {{ $t['tab'] }}
                        </button>
                    @endforeach
                </div>

                @foreach ($types as $key => $t)
                    <p data-hint="{{ $key }}"
                       class="mt-2 text-xs border rounded-lg px-3 py-2 {{ $t['hintClass'] }} {{ $active === $key ? '' : 'hidden' }}">
                        {{ $t['hint'] }}
                    </p>
                @endforeach
            </div>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4" id="registerForm">
                @csrf
                <input type="hidden" name="enrollment_type" id="enrollment_type" value="{{ $active }}">

                @foreach ($sections as $key => $fields)
                    <div id="fields-{{ $key }}" data-section="{{ $key }}"
                         class="fields-section grid grid-cols-2 gap-4 {{ $active === $key ? '' : 'hidden' }}">

                        @foreach ($fields as $f)
                            @if ($f['type'] === 'divider')
                                <div class="col-span-2 flex items-center gap-2 py-1" style="--i:{{ $loop->index }}">
                                    <div class="flex-1 h-px bg-slate-200"></div>
                                    <span class="text-xs text-slate-400 font-medium">{{ $f['label'] }}</span>
                                    <div class="flex-1 h-px bg-slate-200"></div>
                                </div>
                                @continue
                            @endif

                            @php $id = $f['id'] ?? $f['name']; @endphp

                            <div class="{{ ($f['half'] ?? false) ? 'col-span-1' : 'col-span-2' }} space-y-1.5" style="--i:{{ $loop->index }}">
                                <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
                                    {{ $f['label'] }}
                                    @if ($f['required'] ?? true)<span class="text-red-400">*</span>@endif
                                </label>

                                @if ($f['type'] === 'select')
                                    <select name="{{ $f['name'] }}" id="{{ $id }}" class="{{ $inputClass($f['name'], 'bg-white') }}">
                                        <option value="">{{ $f['placeholder'] }}</option>
                                        @foreach ($f['options'] as $value => $text)
                                            <option value="{{ $value }}" @selected(old($f['name']) == $value)>{{ $text }}</option>
                                        @endforeach
                                    </select>

                                @elseif ($f['type'] === 'password')
                                    <div class="relative">
                                        <input type="password" name="{{ $f['name'] }}" id="{{ $id }}"
                                               class="{{ $inputClass($f['name'], 'pr-10') }}">
                                        <button type="button" onclick="togglePassword('{{ $id }}', this)"
                                                aria-label="Show or hide password"
                                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-emerald-700 transition-colors">
                                            <svg class="eye-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['eye'] }}" />
                                            </svg>
                                            <svg class="eye-slash-icon w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['eye-off'] }}" />
                                            </svg>
                                        </button>
                                    </div>

                                @else
                                    <input type="{{ $f['type'] }}" name="{{ $f['name'] }}" id="{{ $id }}"
                                           value="{{ old($f['name']) }}"
                                           @if (!empty($f['placeholder'])) placeholder="{{ $f['placeholder'] }}" @endif
                                           class="{{ $inputClass($f['name']) }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- SUBMIT --}}
                <button type="submit" id="registerBtn"
                        class="rise btn-shine w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white shadow-btn-glow transition mt-2"
                        style="--d:1300ms">
                    <span class="spinner hidden" id="registerSpinner" aria-hidden="true"></span>
                    <span id="registerLabel">Create Account</span>
                </button>

                <p class="rise mt-3 text-xs text-slate-500 text-center" style="--d:1380ms">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-emerald-700 font-semibold hover:text-emerald-500">Login</a>
                </p>

                {{-- Guest sign-up link (disabled)
                <p class="mt-1 text-xs text-slate-500 text-center">
                    Sign up as
                    <a href="{{ route('guest.form') }}" class="text-emerald-700 font-semibold hover:text-emerald-500">Guest</a>
                </p>
                --}}
            </form>
        </div>
    </div>
</section>

<script>
    const TAB_ON  = @json(explode(' ', $tabOn));
    const TAB_OFF = @json(explode(' ', $tabOff));

    // Show the selected registration type and hide the other one.
    // `byUser` is false for the initial call, so the first load keeps its slow entrance
    // and later tab switches animate quickly.
    function switchType(type, byUser = true) {
        if (byUser) document.body.classList.add('is-ready');

        document.getElementById('enrollment_type').value = type;

        document.querySelectorAll('[data-section]').forEach(el =>
            el.classList.toggle('hidden', el.dataset.section !== type));

        document.querySelectorAll('[data-hint]').forEach(el =>
            el.classList.toggle('hidden', el.dataset.hint !== type));

        document.querySelectorAll('[data-tab]').forEach(btn => {
            const on = btn.dataset.tab === type;
            btn.classList.remove(...TAB_ON, ...TAB_OFF);
            btn.classList.add(...(on ? TAB_ON : TAB_OFF));
        });
    }

    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const show  = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.querySelector('.eye-icon').classList.toggle('hidden', show);
        btn.querySelector('.eye-slash-icon').classList.toggle('hidden', !show);
    }

    // Restore the selected tab after a validation error redirect
    switchType(@json($active), false);

    // After the entrance finishes, make later tab switches instant
    setTimeout(() => document.body.classList.add('is-ready'), 2200);

    // Show a spinner on the button while the form submits
    document.getElementById('registerForm').addEventListener('submit', function () {
        const btn = document.getElementById('registerBtn');
        document.getElementById('registerSpinner').classList.remove('hidden');
        document.getElementById('registerLabel').textContent = 'Creating account...';
        btn.classList.add('opacity-80', 'cursor-wait');
        btn.setAttribute('aria-busy', 'true');
    });

    // If the browser restores this page from the back/forward cache, reset the button
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.getElementById('registerSpinner').classList.add('hidden');
        document.getElementById('registerLabel').textContent = 'Create Account';
        document.getElementById('registerBtn').classList.remove('opacity-80', 'cursor-wait');
    });
</script>

</body>
</html>