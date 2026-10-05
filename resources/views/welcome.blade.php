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
    </div>
</section>

<!-- ANNOUNCEMENT -->
<section id="announcement" class="mx-auto max-w-7xl px-6 py-24">
    <h2 class="{{ $h2 }}">Announcements</h2>
    <div class="mt-10 grid gap-5 md:grid-cols-2">
        @forelse ($announcements as $a)
            <article class="rounded-3xl border border-green-200/60 bg-white p-7 shadow-sm transition hover:border-emerald-300 hover:shadow-card-hover">
                <time class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">{{ optional($a->created_at)->format('M d, Y') }}</time>
                <h3 class="mt-3 font-display text-xl font-bold text-slate-900">{{ $a->title }}</h3>
                <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($a->body ?? $a->content ?? '', 160) }}</p>
            </article>
        @empty
            <p class="col-span-full text-sm text-slate-500">No announcements right now. Check back soon.</p>
        @endforelse
    </div>
</section>

<!-- ACTIVITIES -->
<section id="activities" class="bg-gradient-to-b from-green-100/70 to-emerald-50/0 py-24">
    <div class="mx-auto max-w-7xl px-6">
        <h2 class="{{ $h2 }}">Activities</h2>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($activities as $act)
                <article class="group overflow-hidden rounded-3xl border border-green-200/60 bg-white shadow-sm transition hover:-translate-y-1.5 hover:shadow-card-hover">
                    @if (!empty($act->image))
                        <img src="{{ asset('storage/'.$act->image) }}" alt="" loading="lazy" class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <div class="aspect-[16/10] bg-hero-gradient"></div>
                    @endif
                    <div class="p-6">
                        <h3 class="font-display text-lg font-bold text-slate-900">{{ $act->title }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($act->description ?? '', 110) }}</p>
                    </div>
                </article>
            @empty
                <p class="col-span-full text-sm text-slate-500">No activities posted yet.</p>
            @endforelse
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