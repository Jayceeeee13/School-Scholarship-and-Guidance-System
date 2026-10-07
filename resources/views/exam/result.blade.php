<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Complete – {{ $result->exam->title ?? 'Exam' }} | Green Valley College Foundation</title>
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
                        'btn-glow': '0 0 40px rgba(5, 46, 22, 0.7), 0 10px 40px -10px rgba(20, 83, 45, 0.6)'
                    }
                }
            }
        }
    </script>
    <style>
        /* The checkmark is drawn with stroke-dasharray, so it is hidden only
           when motion is allowed. Without motion it simply shows. */
        @media (prefers-reduced-motion: no-preference) {

            @keyframes riseIn {
                from { opacity: 0; transform: translateY(28px); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes popIn {
                from { opacity: 0; transform: scale(.4); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes cardIn {
                from { opacity: 0; transform: translateY(40px) scale(.96); }
                to   { opacity: 1; transform: none; }
            }
            .rise { animation: riseIn .8s cubic-bezier(.22,1,.36,1) backwards; animation-delay: var(--d, 0ms); }
            .card-in { animation: cardIn .9s cubic-bezier(.22,1,.36,1) backwards; }
            .pop  { animation: popIn .7s cubic-bezier(.34,1.56,.64,1) backwards; animation-delay: var(--d, 0ms); }

            /* Check draws itself */
            @keyframes draw {
                from { stroke-dashoffset: 1; }
                to   { stroke-dashoffset: 0; }
            }
            .check-path {
                stroke-dasharray: 1;
                animation: draw .7s ease-out .75s backwards;
            }

            /* Ripple rings behind the check */
            @keyframes ripple {
                0%   { transform: scale(1);   opacity: .55; }
                100% { transform: scale(2.1); opacity: 0; }
            }
            .ripple::before, .ripple::after {
                content: ""; position: absolute; inset: 0; border-radius: 9999px;
                background: #4ade80; animation: ripple 2.4s ease-out infinite;
            }
            .ripple::after { animation-delay: 1.2s; }

            /* Floating blurred blobs */
            @keyframes floatSlow {
                0%, 100% { translate: 0 0; }
                50%      { translate: 26px -30px; }
            }
            .float-a { animation: floatSlow 12s ease-in-out infinite; }
            .float-b { animation: floatSlow 16s ease-in-out infinite reverse; }

            /* Confetti dots rising from the card */
            @keyframes confetti {
                0%   { transform: translateY(0) rotate(0); opacity: 0; }
                15%  { opacity: 1; }
                100% { transform: translateY(-120px) rotate(200deg); opacity: 0; }
            }
            .confetti {
                position: absolute; width: 8px; height: 8px; border-radius: 2px;
                animation: confetti 2.6s ease-out infinite;
                animation-delay: var(--d, 0ms);
            }

            /* Button shine sweep on hover */
            .btn-shine { position: relative; overflow: hidden; }
            .btn-shine::before {
                content: ""; position: absolute; top: 0; left: -75%; width: 50%; height: 100%;
                background: linear-gradient(120deg, transparent, rgba(255,255,255,.35), transparent);
                transform: skewX(-20deg);
            }
            .btn-shine:hover::before { left: 130%; transition: left .7s ease; }
            .btn-shine:active { transform: scale(.98); }
        }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center overflow-hidden py-10 font-sans text-slate-800 antialiased">

    {{-- Branded background --}}
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat opacity-60"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.08]"
         style="background-image: url('{{ asset('images/gvc.png') }}');"></div>
    <div class="float-a absolute -left-24 top-10 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="float-b absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="relative max-w-lg w-full mx-auto px-4 text-center">

        {{-- Logo --}}
        <div class="rise mb-5 flex items-center justify-center gap-2" style="--d:50ms">
            <img src="{{ asset('images/logo.png') }}" alt="GVCFI" class="w-10 h-10 rounded-lg object-contain">
            <span class="font-display text-sm font-bold text-white tracking-tight">Green Valley College Foundation Inc.</span>
        </div>

        <div class="card-in relative bg-white/95 backdrop-blur rounded-2xl shadow-xl shadow-emerald-950/40 border border-emerald-200/70 p-10">

            {{-- Confetti --}}
            <div class="pointer-events-none absolute inset-x-0 top-24 h-0" aria-hidden="true">
                <span class="confetti bg-emerald-400" style="left:22%; --d:1000ms"></span>
                <span class="confetti bg-lime-300"    style="left:34%; --d:1500ms"></span>
                <span class="confetti bg-emerald-300" style="left:46%; --d:1200ms"></span>
                <span class="confetti bg-green-500"   style="left:58%; --d:1800ms"></span>
                <span class="confetti bg-lime-400"    style="left:70%; --d:1100ms"></span>
                <span class="confetti bg-emerald-500" style="left:80%; --d:1650ms"></span>
            </div>

            {{-- Animated check --}}
            <div class="pop relative mx-auto mb-6 w-20 h-20" style="--d:350ms">
                <div class="ripple absolute inset-0 rounded-full"></div>
                <div class="relative flex items-center justify-center w-20 h-20 rounded-full bg-green-100 ring-4 ring-green-200/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-600" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path class="check-path" pathLength="1" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <h1 class="rise font-display text-3xl font-extrabold text-slate-900 mb-2" style="--d:700ms">Exam Submitted!</h1>

            <p class="rise text-slate-500 mb-1" style="--d:800ms">
                Thank you, <strong class="text-slate-700">{{ $result->user->name }}</strong>.
            </p>
            <p class="rise text-slate-500 mb-6" style="--d:880ms">
                You have successfully completed the
                <strong class="text-slate-700">{{ $result->exam->title ?? 'exam' }}</strong>.
            </p>

            <p class="rise text-sm text-slate-400" style="--d:960ms">
                Submitted on {{ \Carbon\Carbon::parse($result->completed_at)->format('F d, Y \a\t h:i A') }}
            </p>

            <div class="rise mt-8 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800" style="--d:1050ms">
                Your result slip will be released by the scholarship office. Please wait for further instructions.
            </div>

            <div class="rise mt-6" style="--d:1150ms">
                <a href="/gvc"
                   class="btn-shine group inline-flex items-center gap-2 px-6 py-3 bg-gvc-primary text-white rounded-xl font-semibold shadow-btn-glow hover:bg-green-800 hover:-translate-y-0.5 transition-all duration-150">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Home
                </a>
            </div>

        </div>

        <p class="rise mt-6 text-xs text-emerald-100/60" style="--d:1250ms">
            &copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.
        </p>

    </div>
</body>
</html>