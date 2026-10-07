<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Scholarship and Guidance Management | Green Valley College Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['DM Sans', 'system-ui', 'sans-serif'], display: ['Outfit', 'sans-serif'] },
                colors: { gvc: { dark: '#052e16', primary: '#14532d', light: '#166534', pale: '#bbf7d0', mint: '#4ade80' } },
                backgroundImage: {
                    'hero-gradient': 'linear-gradient(135deg, #022c22 0%, #14532d 35%, #166534 65%, #15803d 100%)',
                    'hero-pattern': "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%2386efac' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E\")"
                },
                boxShadow: {
                    'btn-glow': '0 0 40px rgba(74, 222, 128, 0.35), 0 10px 30px -10px rgba(74, 222, 128, 0.6)',
                    'card-hover': '0 25px 50px -12px rgba(22, 163, 74, 0.35)'
                }
            } }
        }
    </script>
    <style>
        [x-cloak]{display:none!important}
        section[id]{scroll-margin-top:5rem}
        @media (prefers-reduced-motion: reduce){html{scroll-behavior:auto!important}}
    </style>
</head>

@php
    $links = ['home' => 'Home', 'announcement' => 'Announcement', 'activities' => 'Activities', 'scholarship' => 'Scholarship', 'personnels' => 'Personnels', 'contact' => 'Contact info', 'about' => 'About us'];
    $announcements = $announcements ?? collect();
    $activities    = $activities ?? collect();
    $personnels    = $personnels ?? collect();
    $scholarships  = $scholarships ?? collect();
    $cap = 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422A12.083 12.083 0 0121 12c0 5.523-4.477 10-10 10S2 17.523 2 12c0-.538.043-1.065.125-1.578L12 14z';
    $btnPrimary = 'inline-flex items-center justify-center gap-2 rounded-full bg-emerald-400 px-7 py-3.5 text-sm font-semibold text-emerald-950 shadow-btn-glow transition hover:-translate-y-0.5 hover:bg-emerald-300 sm:text-base';
    $btnGlass   = 'inline-flex items-center justify-center gap-2 rounded-full border border-white/25 bg-white/10 px-7 py-3.5 text-sm font-semibold text-white backdrop-blur-md transition hover:-translate-y-0.5 hover:bg-white/20 sm:text-base';
    $h2 = 'font-display text-3xl font-extrabold tracking-tight text-slate-900 md:text-5xl';

    // Mobile app installer
    $appQr   = 'images/app-qr.png';        // QR image: public/images/app-qr.png
    $appUrl  = '';                         // external link (Play Store, Google Drive, etc.). Leave empty to use the APK below.
    $apkPath = 'downloads/gvcfi-app.apk';  // or upload your APK to public/downloads/gvcfi-app.apk

    $downloadUrl = $appUrl ?: (file_exists(public_path($apkPath)) ? asset($apkPath) : null);
@endphp

<body class="bg-emerald-50/60 font-sans text-slate-800 antialiased">

<!-- NAVBAR -->
<header class="sticky top-0 z-50 border-b border-white/10 bg-green-900/80 shadow-lg shadow-green-950/10 backdrop-blur-xl" x-data="{ menu: false }">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3">

        {{-- Logo --}}
        <a href="{{ url('/gvc') }}" class="flex shrink-0 items-center gap-2.5">
            <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation" class="h-10 w-10 rounded-xl bg-white/10 object-contain p-0.5 ring-1 ring-white/20">
            <span class="hidden font-display text-base font-bold tracking-tight text-white sm:inline md:text-lg">Green Valley College Foundation Inc.</span>
        </a>

        {{-- Section links --}}
        <nav class="hidden items-center gap-0.5 xl:flex" aria-label="Main">
            @foreach ($links as $id => $label)
                <a href="#{{ $id }}" class="rounded-full px-3 py-1.5 text-sm font-medium text-emerald-100/80 transition hover:bg-white/10 hover:text-white">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            @guest
                {{-- Guest menu --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" aria-label="Account menu"
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 transition hover:bg-white/20">
                        <svg class="h-[18px] w-[18px] text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right
                         class="absolute right-0 z-50 mt-3 w-52 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">
                        <a href="/admin/login" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-green-50 hover:text-green-800">
                            <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Login as Admin
                        </a>
                    </div>
                </div>
            @else
                <span class="hidden text-sm text-emerald-100 2xl:inline">Hello, <span class="font-semibold text-white">{{ auth()->user()->name }}</span></span>

                @include('partials.notification-bell')

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-medium text-white transition hover:bg-white/20 sm:text-sm">Logout</button>
                </form>
            @endguest

            {{-- Mobile toggle --}}
            <button @click="menu = !menu" :aria-expanded="menu" aria-label="Toggle menu"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white xl:hidden">
                <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <nav x-show="menu" x-cloak x-transition @click="menu = false" class="border-t border-white/10 bg-green-950/95 px-5 py-3 xl:hidden" aria-label="Mobile">
        @foreach ($links as $id => $label)
            <a href="#{{ $id }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-emerald-50 hover:bg-white/10">{{ $label }}</a>
        @endforeach
    </nav>
</header>

<!-- HERO -->
<section id="home" class="relative overflow-hidden rounded-b-[2.5rem] py-28 md:py-40">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.12]" style="background-image: url('{{ asset('images/gvc.png') }}');"></div>
    <div class="absolute -left-24 top-10 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-4xl px-6 text-center">
        <h1 class="mb-6 font-display text-5xl font-extrabold leading-[1.05] tracking-tight text-emerald-50 drop-shadow-md sm:text-6xl md:text-7xl">
            GVCFI <br class="hidden sm:block">
            <span class="bg-gradient-to-r from-gvc-pale via-gvc-mint to-emerald-300 bg-clip-text text-transparent">Scholarship and Guidance</span>
        </h1>

        @auth
            <p class="mx-auto mb-8 max-w-2xl text-sm text-emerald-50/85 sm:text-base">
                Welcome back, {{ auth()->user()->name }}.
                @if (auth()->user()->isEnrolled())
                    Start a new scholarship application or make a counseling appointment.
                @else
                    You can submit a referral or take the admission exam below. Once enrolled, you'll be able to apply for a scholarship.
                @endif
            </p>

            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                @if (auth()->user()->isEnrolled())
                    <a href="{{ url('/scholarship') }}" class="{{ $btnPrimary }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $cap }}"/></svg>
                        Scholarship
                    </a>
                    <a href="{{ route('guidance') }}" class="{{ $btnGlass }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                        Guidance
                    </a>
                @else
                    <span title="You must be enrolled to access scholarship features"
                          class="inline-flex cursor-not-allowed select-none items-center justify-center gap-2 rounded-full border border-white/15 bg-slate-400/20 px-7 py-3.5 text-sm font-semibold text-white/40 sm:text-base">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        Scholarship <span class="text-xs font-normal opacity-70">(Enrolled Only)</span>
                    </span>
                    <a href="{{ route('guidance_referrals.get') }}" class="{{ $btnPrimary }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                        Submit a Referral
                    </a>
                @endif

                @if (isset($exam) && $exam)
                    <a href="{{ route('exam.admission', $exam->id) }}" class="{{ $btnGlass }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Admission Exam
                    </a>
                @endif
            </div>

            @unless (auth()->user()->isEnrolled())
                <div class="mt-6 inline-flex items-center gap-2 rounded-2xl border border-amber-300/40 bg-amber-500/20 px-4 py-2.5 text-xs text-amber-100 backdrop-blur-sm sm:text-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    You are not yet enrolled. Complete your enrollment to unlock scholarship features.
                </div>
            @endunless
        @else
            <p class="mx-auto mb-8 max-w-2xl text-sm text-emerald-50/85 sm:text-base">Login or register to apply for scholarships and manage your guidance appointments.</p>
            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <a href="{{ route('login') }}" class="{{ $btnPrimary }}">Login to Continue</a>
                <a href="{{ route('register') }}" class="{{ $btnGlass }}">Create an Account</a>
            </div>
        @endauth

        {{-- MOBILE APP --}}
        <div class="mx-auto mt-14 grid max-w-4xl items-center gap-10 overflow-hidden rounded-[2rem] border border-white/20 bg-white/10 p-6 text-center shadow-2xl shadow-green-950/20 backdrop-blur-xl md:grid-cols-[1.1fr_1fr] md:p-10 md:text-left">

            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/30 bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-100">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                    Mobile app
                </span>
                <h2 class="mt-4 font-display text-2xl font-extrabold leading-tight text-white sm:text-3xl">Take GVCFI with you</h2>
                <p class="mt-2 text-sm text-emerald-50/80 sm:text-base">Apply for scholarships, book counseling appointments, and track your requirements from your phone.</p>

                <div class="mt-6 flex flex-col items-center gap-4 sm:flex-row md:justify-start">
                    {{-- QR code (white backing keeps it scannable) --}}
                    <div class="shrink-0 rounded-2xl bg-white p-2 shadow-lg">
                        @if (file_exists(public_path($appQr)))
                            <img src="{{ asset($appQr) }}" alt="QR code to download the GVCFI mobile app" class="h-28 w-28 object-contain">
                        @else
                            {{-- Placeholder shown until the QR image is uploaded --}}
                            <div class="flex h-28 w-28 flex-col items-center justify-center rounded-xl border-2 border-dashed border-green-300 text-center text-xs font-medium text-green-700">
                                QR code<br>coming soon
                            </div>
                        @endif
                    </div>

                    <div class="text-center sm:text-left">
                        <p class="text-sm font-semibold text-white">Scan to install</p>
                        <p class="mt-0.5 text-xs text-emerald-50/70">Point your phone's camera at the code.</p>

                        @if ($downloadUrl)
                            <a href="{{ $downloadUrl }}" @unless ($appUrl) download @else target="_blank" rel="noopener noreferrer" @endunless
                               class="mt-3 inline-flex items-center gap-2 rounded-full bg-emerald-400 px-5 py-2.5 text-sm font-semibold text-emerald-950 shadow-btn-glow transition hover:-translate-y-0.5 hover:bg-emerald-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                                Download the app
                            </a>
                        @else
                            <span class="mt-3 inline-flex cursor-not-allowed items-center gap-2 rounded-full border border-white/15 bg-white/5 px-5 py-2.5 text-sm font-semibold text-white/50">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                                Download link coming soon
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- App screenshots in phone frames --}}
            <div class="relative mx-auto h-[25rem] w-full max-w-[18rem]" aria-hidden="false">
                <div class="absolute left-0 top-10 w-36 -rotate-6 rounded-[1.75rem] bg-slate-900 p-1.5 shadow-2xl shadow-green-950/40 ring-1 ring-white/20">
                    <img src="{{ asset('images/app-login.jpg') }}" alt="GVCFI mobile app login screen" loading="lazy" class="w-full rounded-[1.4rem]">
                </div>
                <div class="absolute right-0 top-0 z-10 w-40 rotate-6 rounded-[1.75rem] bg-slate-900 p-1.5 shadow-2xl shadow-green-950/40 ring-1 ring-white/20">
                    <img src="{{ asset('images/app-dashboard.jpg') }}" alt="GVCFI mobile app dashboard" loading="lazy" class="w-full rounded-[1.4rem]">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ANNOUNCEMENT -->
<section id="announcement" class="mx-auto max-w-7xl px-6 py-24"
         x-data="{ item: null }"
         x-effect="document.body.classList.toggle('overflow-hidden', !!item)"
         @keydown.escape.window="item = null">

    {{-- Header --}}
    <div class="mb-12 max-w-xl">
        <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-green-800">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
            </span>
            Latest updates
        </span>
        <h2 class="{{ $h2 }} mt-4">Announcements</h2>
        <p class="mt-3 text-sm text-slate-500 sm:text-base">Stay informed about the newest news, schedules, and reminders from the Scholarship and Guidance office.</p>
    </div>

    @if ($announcements->count())
        @php
            $featured = $announcements->first();
            $others   = $announcements->skip(1);
            $payload  = fn ($a) => [
                'title' => $a->title,
                'body'  => $a->body ?? $a->content ?? '',
                'date'  => optional($a->created_at)->format('F d, Y'),
            ];
        @endphp

        <div class="grid gap-6 lg:grid-cols-5">

            {{-- Featured (latest) announcement --}}
            <article class="relative flex min-h-[22rem] flex-col justify-between overflow-hidden rounded-[2rem] bg-hero-gradient p-8 text-white ring-1 ring-white/10 lg:col-span-3 md:p-10">
                <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
                <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-emerald-400/20 blur-3xl"></div>
                <svg class="pointer-events-none absolute -bottom-12 -right-8 h-72 w-72 text-white/[0.07]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>

                <div class="relative flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/30 bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-100">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span> Latest
                    </span>
                    <time class="text-sm font-medium text-emerald-100/80">{{ optional($featured->created_at)->format('F d, Y') }}</time>
                </div>

                <div class="relative mt-10">
                    <h3 class="font-display text-3xl font-bold leading-tight md:text-4xl">{{ $featured->title }}</h3>
                    <p class="mt-4 line-clamp-4 max-w-2xl text-emerald-50/80">{{ $featured->body ?? $featured->content ?? '' }}</p>

                    <button type="button" @click="item = {{ \Illuminate\Support\Js::from($payload($featured)) }}"
                            class="mt-7 inline-flex items-center gap-2 rounded-full bg-white/10 py-1.5 pl-5 pr-1.5 text-sm font-semibold text-white ring-1 ring-white/25 backdrop-blur-md transition hover:bg-white hover:text-green-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300">
                        Read full announcement
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-400 text-emerald-950">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M9 7h8v8"/></svg>
                        </span>
                    </button>
                </div>
            </article>

            {{-- Other announcements --}}
            <div class="flex flex-col gap-4 lg:col-span-2">
                @forelse ($others as $a)
                    @php $isNew = $a->created_at && $a->created_at->gt(now()->subDays(7)); @endphp
                    <button type="button" @click="item = {{ \Illuminate\Support\Js::from($payload($a)) }}"
                            class="group flex w-full items-start gap-4 rounded-3xl border border-green-200/60 bg-white p-5 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-card-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">

                        {{-- Date block --}}
                        <div class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-2xl bg-green-50 text-green-800 ring-1 ring-green-200/70 transition group-hover:bg-green-900 group-hover:text-white group-hover:ring-green-900">
                            <span class="text-[0.65rem] font-semibold uppercase tracking-wider opacity-70">{{ optional($a->created_at)->format('M') }}</span>
                            <span class="font-display text-2xl font-extrabold leading-none">{{ optional($a->created_at)->format('d') }}</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="truncate font-display text-base font-bold text-slate-900">{{ $a->title }}</h3>
                                @if ($isNew)
                                    <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-emerald-700">New</span>
                                @endif
                            </div>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $a->body ?? $a->content ?? '' }}</p>
                        </div>

                        <svg class="mt-1 h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @empty
                    <div class="flex h-full items-center justify-center rounded-3xl border border-dashed border-green-300 bg-white/60 p-8 text-center text-sm text-slate-500">
                        That's the only announcement for now. More will appear here.
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="mx-auto max-w-md rounded-3xl border border-dashed border-green-300 bg-white/60 px-6 py-12 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 text-green-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="font-display text-xl font-bold text-slate-900">No announcements right now</h3>
            <p class="mt-2 text-sm text-slate-500">Check back soon for news and updates.</p>
        </div>
    @endif

    {{-- Full announcement modal --}}
    <div x-show="item" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" :aria-label="item ? item.title : ''">
        <div x-show="item" x-transition.opacity class="absolute inset-0 bg-green-950/70 backdrop-blur-sm" @click="item = null"></div>

        <div x-show="item" x-transition.scale.origin.center.90
             class="relative flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-[2rem] bg-white shadow-2xl">
            <div class="relative bg-hero-gradient px-7 py-6 text-white md:px-9">
                <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
                <button type="button" @click="item = null" aria-label="Close"
                        class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/25 transition hover:bg-white/25">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <time class="relative text-xs font-semibold uppercase tracking-wider text-emerald-100/80" x-text="item ? item.date : ''"></time>
                <h3 class="relative mt-2 pr-10 font-display text-2xl font-bold leading-tight md:text-3xl" x-text="item ? item.title : ''"></h3>
            </div>
            <div class="overflow-y-auto px-7 py-6 md:px-9 md:py-8">
                <p class="whitespace-pre-line text-slate-700" x-text="item ? item.body : ''"></p>
            </div>
        </div>
    </div>
</section>

<!-- ACTIVITIES -->
<section id="activities" class="relative bg-gradient-to-b from-green-100/70 to-emerald-50/0 py-24"
         x-data="{ item: null }"
         x-effect="document.body.classList.toggle('overflow-hidden', !!item)"
         @keydown.escape.window="item = null">
    <div class="mx-auto max-w-7xl px-6">

        {{-- Header --}}
        <div class="mb-12 max-w-xl">
            <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-green-800">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                What's happening
            </span>
            <h2 class="{{ $h2 }} mt-4">Activities</h2>
            <p class="mt-3 text-sm text-slate-500 sm:text-base">Events, programs, and moments from the Scholarship and Guidance office.</p>
        </div>

        @if ($activities->count())
            <div class="grid auto-rows-[18rem] gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:auto-rows-[16rem]">
                @foreach ($activities as $act)
                    @php
                        $isFeatured = $loop->first;
                        $isNew      = $act->activity_date && $act->activity_date->gt(now()->subDays(14));
                        $actPayload = [
                            'title' => $act->title,
                            'body'  => $act->description ?? '',
                            'date'  => optional($act->activity_date)->format('F d, Y'),
                            'image' => $act->image ? asset('storage/'.$act->image) : null,
                        ];
                    @endphp

                    <button type="button"
                            @click="item = {{ \Illuminate\Support\Js::from($actPayload) }}"
                            class="group relative isolate flex overflow-hidden rounded-[2rem] bg-green-950 text-left ring-1 ring-green-900/10 transition duration-300 hover:-translate-y-1 hover:shadow-card-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700
                                   {{ $isFeatured ? 'sm:col-span-2 lg:row-span-2' : '' }}">

                        {{-- Background: image or branded fallback --}}
                        @if (!empty($act->image))
                            <img src="{{ asset('storage/'.$act->image) }}" alt="{{ $act->title }}" loading="lazy"
                                 class="absolute inset-0 -z-20 h-full w-full object-cover transition duration-700 group-hover:scale-105">
                        @else
                            <div class="absolute inset-0 -z-20 bg-hero-gradient"></div>
                            <div class="absolute inset-0 -z-20 bg-hero-pattern bg-repeat"></div>
                            <svg class="pointer-events-none absolute -bottom-10 -right-10 -z-10 h-64 w-64 text-white/[0.08]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cap }}"/>
                            </svg>
                        @endif

                        {{-- Readability overlay --}}
                        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-green-950/90 via-green-950/35 to-transparent transition-opacity duration-300 group-hover:from-green-950/95"></div>

                        {{-- Top row: date chip + badges --}}
                        <div class="absolute inset-x-0 top-0 flex items-start justify-between p-5">
                            @if ($act->activity_date)
                                <time class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/15 px-3 py-1 text-xs font-semibold text-white backdrop-blur-md">
                                    {{ $act->activity_date->format('M d, Y') }}
                                </time>
                            @else
                                <span></span>
                            @endif

                            @if ($isFeatured || $isNew)
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/30 bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-100 backdrop-blur-md">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                    {{ $isFeatured ? 'Latest' : 'New' }}
                                </span>
                            @endif
                        </div>

                        {{-- Bottom: title, description, arrow --}}
                        <div class="relative mt-auto flex w-full items-end justify-between gap-4 p-6 {{ $isFeatured ? 'md:p-8' : '' }}">
                            <div class="min-w-0">
                                <h3 class="font-display font-bold leading-tight text-white {{ $isFeatured ? 'text-2xl md:text-4xl' : 'text-lg md:text-xl' }}">
                                    {{ $act->title }}
                                </h3>
                                @if (!empty($act->description))
                                    <p class="mt-2 text-emerald-50/80 {{ $isFeatured ? 'line-clamp-3 max-w-xl text-sm md:text-base' : 'line-clamp-2 text-sm' }}">
                                        {{ $act->description }}
                                    </p>
                                @endif
                            </div>

                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-400 text-emerald-950 shadow-btn-glow transition duration-300 group-hover:rotate-45 group-hover:bg-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M9 7h8v8"/></svg>
                            </span>
                        </div>
                    </button>
                @endforeach
            </div>
        @else
            <div class="mx-auto max-w-md rounded-3xl border border-dashed border-green-300 bg-white/60 px-6 py-12 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 text-green-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-display text-xl font-bold text-slate-900">No activities yet</h3>
                <p class="mt-2 text-sm text-slate-500">Upcoming events and programs will be posted here.</p>
            </div>
        @endif
    </div>

    {{-- Activity modal --}}
    <div x-show="item" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" :aria-label="item ? item.title : ''">
        <div x-show="item" x-transition.opacity class="absolute inset-0 bg-green-950/70 backdrop-blur-sm" @click="item = null"></div>

        <div x-show="item" x-transition.scale.origin.center.90
             class="relative flex max-h-[88vh] w-full max-w-2xl flex-col overflow-hidden rounded-[2rem] bg-white shadow-2xl">
            <button type="button" @click="item = null" aria-label="Close"
                    class="absolute right-4 top-4 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-green-950/60 text-white ring-1 ring-white/25 backdrop-blur-md transition hover:bg-green-950">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="overflow-y-auto">
                <template x-if="item && item.image">
                    <img :src="item.image" :alt="item.title" class="aspect-[16/9] w-full object-cover">
                </template>
                <div class="px-7 py-6 md:px-9 md:py-8">
                    <time class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-green-800"
                          x-show="item && item.date" x-text="item ? item.date : ''"></time>
                    <h3 class="mt-3 font-display text-2xl font-bold leading-tight text-slate-900 md:text-3xl" x-text="item ? item.title : ''"></h3>
                    <p class="mt-4 whitespace-pre-line text-slate-700" x-text="item ? item.body : ''"></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SCHOLARSHIPS -->
@php
    // Where the card button sends people: enrolled students go to the application page, guests to login.
    $applyUrl = auth()->check()
        ? (auth()->user()->isEnrolled() ? url('/scholarship') : '#home')
        : route('login');
@endphp
<section id="scholarship" class="py-24">
    <div class="mx-auto max-w-7xl px-6">

        @if ($scholarships->count())
        <div x-data="{
                n: {{ $scholarships->count() }}, loop: false, active: 0,
                timer: null, paused: false, settle: null,
                step() { return this.$refs.track.firstElementChild.offsetWidth + 20; },
                jump(delta) {
                    const e = this.$refs.track;
                    e.style.scrollBehavior = 'auto'; e.style.scrollSnapType = 'none';
                    e.scrollLeft += delta;
                    void e.offsetWidth;
                    e.style.scrollBehavior = ''; e.style.scrollSnapType = '';
                },
                init() {
                    const e = this.$refs.track, items = [...e.children];
                    this.loop = this.n * this.step() > e.clientWidth + 4;
                    if (this.loop) {
                        const copy = (c) => {
                            const k = c.cloneNode(true);
                            k.setAttribute('aria-hidden', 'true');
                            k.querySelectorAll('a, button').forEach(x => x.tabIndex = -1);
                            return k;
                        };
                        const first = items[0];
                        items.forEach(c => e.appendChild(copy(c)));
                        items.forEach(c => e.insertBefore(copy(c), first));
                        this.$nextTick(() => { this.jump(this.n * this.step() - e.scrollLeft); this.update(); });
                        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches)
                            this.timer = setInterval(() => { if (!this.paused && !document.hidden) this.go(1); }, 3000);
                    }
                    this.update();
                },
                update() {
                    const e = this.$refs.track, s = this.step(), i = Math.round(e.scrollLeft / s);
                    this.active = this.loop ? ((i % this.n) + this.n) % this.n : Math.min(i, this.n - 1);
                    if (!this.loop) return;
                    clearTimeout(this.settle);
                    this.settle = setTimeout(() => {
                        const w = this.n * s;
                        if (e.scrollLeft >= 2 * w - 4) this.jump(-w);
                        else if (e.scrollLeft < w - 4) this.jump(w);
                    }, 120);
                },
                go(dir) { this.$refs.track.scrollBy({ left: dir * this.step(), behavior: 'smooth' }); },
                goTo(i) { this.$refs.track.scrollTo({ left: (this.n + i) * this.step(), behavior: 'smooth' }); }
             }"
             @mouseenter="paused = true" @mouseleave="paused = false" @focusin="paused = true" @focusout="paused = false"
             role="region" aria-roledescription="carousel" aria-label="Scholarship programs">

            {{-- Header + controls --}}
            <div class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-xl">
                    <h2 class="{{ $h2 }}">What we offer</h2>
                    <p class="mt-3 text-sm text-slate-500 sm:text-base">Explore our active scholarship programs available for qualified students.</p>
                </div>

                <div class="flex gap-2" x-show="loop" x-cloak>
                    <button @click="go(-1)" aria-label="Previous scholarships"
                            class="flex h-12 w-12 items-center justify-center rounded-full border border-green-900/15 bg-white text-green-900 transition hover:bg-green-900 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="go(1)" aria-label="Next scholarships"
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-green-900 text-white transition hover:bg-green-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- Track: cards bleed to the right edge so the next one peeks in --}}
            <div x-ref="track" @scroll.throttle.60ms="update()"
                 class="-mx-6 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-6 pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                 style="scroll-padding-inline: 1.5rem">
                @foreach ($scholarships as $s)
                    <article class="relative flex min-h-[19rem] w-[82%] shrink-0 snap-start flex-col justify-between overflow-hidden rounded-[2rem] bg-hero-gradient p-7 text-white ring-1 ring-white/10 sm:w-[calc(50%-10px)] lg:w-[calc(33.333%-14px)]">

                        {{-- Oversized cap as a watermark --}}
                        <svg class="pointer-events-none absolute -bottom-10 -right-10 h-64 w-64 text-white/[0.07]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cap }}"/>
                        </svg>

                        <div class="relative flex items-start justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/20 bg-white/10 backdrop-blur-md">
                                <svg class="h-6 w-6 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $cap }}"/></svg>
                            </div>

                            @if (($s->status ?? 'active') === 'active')
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/30 bg-emerald-400/15 px-3 py-1 text-xs font-medium text-emerald-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span> Open for applications
                                </span>
                            @endif
                        </div>

                        <div class="relative mt-10">
                            <h3 class="font-display text-2xl font-bold leading-tight text-white">{{ $s->name }}</h3>
                            @if (!empty($s->description))
                                <p class="mt-2 line-clamp-2 text-sm text-emerald-50/75">{{ $s->description }}</p>
                            @endif

                            <a href="{{ $applyUrl }}"
                               class="mt-6 inline-flex items-center gap-2 rounded-full bg-white/10 py-1.5 pl-5 pr-1.5 text-sm font-semibold text-white ring-1 ring-white/25 backdrop-blur-md transition hover:bg-white hover:text-green-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-300">
                                Apply now
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-400 text-emerald-950">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M9 7h8v8"/></svg>
                                </span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Position dots --}}
            <div class="mt-6 flex justify-center gap-2" x-show="loop && n > 1" x-cloak role="group" aria-label="Choose scholarship">
                @foreach ($scholarships as $s)
                    <button @click="goTo({{ $loop->index }})" aria-label="Go to {{ $s->name }}"
                            class="h-2 rounded-full transition-all duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700"
                            :class="active === {{ $loop->index }} ? 'w-8 bg-green-800' : 'w-2 bg-green-900/20 hover:bg-green-900/40'"></button>
                @endforeach
            </div>
        </div>
        @else
            <div class="mx-auto max-w-md rounded-3xl border border-dashed border-green-300 bg-white/60 px-6 py-12 text-center">
                <h2 class="font-display text-xl font-bold text-slate-900">No scholarships open right now</h2>
                <p class="mt-2 text-sm text-slate-500">New programs are posted here as soon as they open. Check the announcements for updates.</p>
            </div>
        @endif
    </div>
</section>

<!-- PERSONNELS -->
<section id="personnels" class="relative overflow-hidden bg-hero-gradient py-24 text-white md:py-28">
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute -left-32 top-0 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="absolute -right-32 bottom-0 h-96 w-96 rounded-full bg-lime-300/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="font-display text-3xl font-extrabold tracking-tight text-emerald-50 md:text-5xl">Meet our personnel</h2>
            <p class="mt-4 text-sm text-emerald-50/80 sm:text-base">The guidance and scholarship team here to support you.</p>
        </div>

        <div class="mt-14 grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 lg:grid-cols-4">
            @forelse ($personnels as $p)
                <article class="group rounded-[2rem] border border-white/15 bg-white/10 px-5 py-7 text-center backdrop-blur-xl transition duration-300 hover:-translate-y-1 hover:border-emerald-300/50 hover:bg-white/[0.14] hover:shadow-btn-glow">

                    <div class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-green-950/40 ring-4 ring-white/20 transition duration-300 group-hover:ring-emerald-300/60 sm:h-28 sm:w-28">
                        @if (!empty($p->profile))
                            <img src="{{ asset('storage/'.$p->profile) }}" alt="{{ $p->full_name }}" loading="lazy"
                                 class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-green-700 to-emerald-900 font-display text-3xl font-extrabold text-emerald-100/90">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($p->first_name, 0, 1).\Illuminate\Support\Str::substr($p->last_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h3 class="mt-5 font-display text-base font-bold leading-snug text-white sm:text-lg">{{ $p->full_name }}</h3>
                    @if (!empty($p->position))
                        <p class="mx-auto mt-2 inline-block max-w-full truncate rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-medium text-emerald-100 ring-1 ring-emerald-300/25">{{ $p->position }}</p>
                    @endif
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-white/25 bg-white/5 px-6 py-12 text-center">
                    <p class="font-display text-lg font-bold">Our team will be listed here soon</p>
                    <p class="mt-1 text-sm text-emerald-50/75">Check back for the people who can help you.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- CONTACT -->
<section id="contact" class="mx-auto max-w-7xl px-6 py-24">
    <h2 class="{{ $h2 }}">Contact info</h2>
    <dl class="mt-10 grid gap-5 sm:grid-cols-3">
        <div class="rounded-3xl border border-green-200/60 bg-white p-7"><dt class="text-sm font-semibold text-green-700">Address</dt><dd class="mt-2 font-medium">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</dd></div>
        <div class="rounded-3xl border border-green-200/60 bg-white p-7"><dt class="text-sm font-semibold text-green-700">Facebook Page</dt><dd class="mt-2 break-words font-medium"><a href="https://www.facebook.com/GVCguidanceandscholarships2022" target="_blank" rel="noopener noreferrer" class="text-green-700 hover:underline">GVC Guidance and Scholarships</a></dd></div>
        <div class="rounded-3xl border border-green-200/60 bg-white p-7"><dt class="text-sm font-semibold text-green-700">Email</dt><dd class="mt-2 font-medium"><a href="mailto:guidance@gvcfi.edu.ph" class="text-green-700 hover:underline">guidance@gvcfi.edu.ph</a></dd></div>
    </dl>
</section>

<!-- ABOUT -->
<section id="about" class="relative overflow-hidden bg-hero-gradient py-24 text-white">
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="relative mx-auto grid max-w-7xl gap-10 px-6 md:grid-cols-[1fr_1.4fr]">
        <h2 class="font-display text-3xl font-extrabold tracking-tight text-emerald-50 md:text-5xl">About us</h2>
        <div class="max-w-2xl space-y-4 text-emerald-50/85">
            {{-- TODO: replace with the foundation's real history, mission, and vision --}}
            <p>Green Valley College Foundation Inc. supports students through scholarships and guidance services, so financial need and personal challenges do not stop anyone from finishing their studies.</p>
            <p>Our guidance office offers counseling, referrals, and appointments, and our scholarship programs are open to qualified, enrolled students.</p>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="border-t border-green-300/20 bg-slate-900 py-10">
    <p class="text-center text-xs text-slate-400">&copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.</p>
</footer>

</body>
</html>