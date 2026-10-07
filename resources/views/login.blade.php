<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Green Valley College Foundation</title>
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

            /* Entrance animations (staggered with --d) */
            @keyframes riseIn {
                from { opacity: 0; transform: translateY(28px); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(60px) scale(.97); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes dropIn {
                from { opacity: 0; transform: translateY(-100%); }
                to   { opacity: 1; transform: none; }
            }
            .rise  { animation: riseIn .8s cubic-bezier(.22,1,.36,1) both; animation-delay: var(--d, 0ms); }
            .slide { animation: slideInRight .9s cubic-bezier(.22,1,.36,1) both; animation-delay: var(--d, 0ms); }
            .drop  { animation: dropIn .7s cubic-bezier(.22,1,.36,1) both; }

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

            /* Pulsing glow on the Register pill */
            @keyframes glowPulse {
                0%, 100% { box-shadow: 0 0 20px rgba(74,222,128,.25), 0 6px 20px -8px rgba(74,222,128,.5); }
                50%      { box-shadow: 0 0 36px rgba(74,222,128,.55), 0 6px 20px -8px rgba(74,222,128,.85); }
            }
            .glow-pulse { animation: glowPulse 3s ease-in-out infinite; }

            /* Soft pulse on the "Student Portal" dot */
            @keyframes ping2 {
                75%, 100% { transform: scale(2.2); opacity: 0; }
            }
            .ping-dot::after {
                content: ""; position: absolute; inset: 0; border-radius: 9999px;
                background: #6ee7b7; animation: ping2 1.8s cubic-bezier(0,0,.2,1) infinite;
            }

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
        }

        /* Spinner (always fine, it only shows while submitting) */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 1rem; height: 1rem; border-radius: 9999px;
            border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
            animation: spin .7s linear infinite;
        }
    </style>
</head>
<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased">

<!-- NAVBAR -->
<header class="drop bg-green-800 border-b border-white sticky top-0 z-40 shadow-sm shadow-green-900/5">
    <div class="max-w-8xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2 group flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation" class="w-10 h-10 rounded-lg object-contain flex-shrink-0 transition duration-300 group-hover:rotate-6 group-hover:scale-110">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>
        <nav class="flex items-center gap-2 sm:gap-3">
            <a href="{{ url('/') }}"
               class="group inline-flex items-center gap-1.5 rounded-full bg-white/10 hover:bg-white/20 px-4 py-1.5 text-xs sm:text-sm font-semibold text-white transition">
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back
            </a>
            <a href="{{ route('register') }}"
               class="glow-pulse inline-flex items-center rounded-full bg-emerald-400 px-4 py-1.5 text-xs sm:text-sm font-semibold text-emerald-950 shadow-btn-glow hover:bg-emerald-300 hover:-translate-y-0.5 transition">
                Register
            </a>
        </nav>
    </div>
</header>

<!-- HERO BACKGROUND -->
<section class="relative min-h-[calc(100vh-64px)] flex items-center overflow-hidden">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat opacity-60"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.08]" style="background-image: url('{{ asset('images/gvc.png') }}');"></div>

    {{-- Floating glow blobs --}}
    <div class="float-a absolute -left-24 top-10 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="float-b absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="relative max-w-6xl mx-auto px-6 py-12 grid gap-10 lg:grid-cols-[minmax(0,1.2fr),minmax(0,1fr)] items-center">
        <div class="text-emerald-50 space-y-4 max-w-xl">
            <p class="rise inline-flex items-center gap-2 rounded-full bg-emerald-900/40 border border-emerald-300/40 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide" style="--d:150ms">
                <span class="ping-dot relative inline-block h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                Student Portal
            </p>
            <h1 class="rise font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight drop-shadow-md" style="--d:280ms">
                Login to your<br class="hidden sm:block">
                <span class="shimmer-text bg-gradient-to-r from-gvc-pale via-gvc-mint to-emerald-300 bg-clip-text text-transparent">Scholarship & Guidance</span> account
            </h1>
            <p class="rise text-sm sm:text-base text-emerald-100/90" style="--d:420ms">
                Access your scholarship applications, renewal status, and guidance appointments all in one place.
            </p>
        </div>

        <div class="slide w-full max-w-md ml-auto bg-white/95 backdrop-blur rounded-2xl shadow-xl shadow-emerald-950/40 border border-emerald-200/70 p-6 md:p-8" style="--d:350ms">
            <h2 class="rise font-display text-xl md:text-2xl font-semibold mb-1 text-slate-900" style="--d:600ms">Login</h2>
            <p class="rise text-sm text-slate-600 mb-5" style="--d:680ms">Sign in using your registered email and password.</p>

            @if (session('success'))
                <div class="rise mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-3" style="--d:700ms">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="shake mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm p-3" role="alert">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4" id="loginForm">
            @csrf
            <div class="rise space-y-1.5" style="--d:760ms">
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="{{ old('email') }}"
                    class="field block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"
                >
            </div>

            <div class="rise space-y-1.5" style="--d:840ms">
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="field block w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-10 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"
                    >
                    <button
                        type="button"
                        onclick="togglePassword('password', this)"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-emerald-700 transition-colors"
                        aria-label="Toggle password visibility"
                    >
                        <!-- Eye icon (show) -->
                        <svg class="eye-icon w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <!-- Eye-slash icon (hide) — hidden by default -->
                        <svg class="eye-slash-icon w-4 h-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <button
                type="submit"
                id="loginBtn"
                class="rise btn-shine w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white shadow-btn-glow transition"
                style="--d:920ms"
            >
                <span class="spinner hidden" id="loginSpinner" aria-hidden="true"></span>
                <span id="loginLabel">Login</span>
            </button>
            <p class="rise mt-3 text-xs text-slate-500 text-center" style="--d:1000ms">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-emerald-700 font-semibold hover:text-emerald-500">Register</a>
            </p>
        </form>
        </div>
    </div>
</section>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const eyeIcon = btn.querySelector('.eye-icon');
        const eyeSlashIcon = btn.querySelector('.eye-slash-icon');

        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }

    // Show a spinner on the Login button while the form submits
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        document.getElementById('loginSpinner').classList.remove('hidden');
        document.getElementById('loginLabel').textContent = 'Signing in...';
        btn.classList.add('opacity-80', 'cursor-wait');
        btn.setAttribute('aria-busy', 'true');
    });

    // If the browser restores this page from the back/forward cache, reset the button
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.getElementById('loginSpinner').classList.add('hidden');
        document.getElementById('loginLabel').textContent = 'Login';
        document.getElementById('loginBtn').classList.remove('opacity-80', 'cursor-wait');
    });
</script>

</body>
</html>