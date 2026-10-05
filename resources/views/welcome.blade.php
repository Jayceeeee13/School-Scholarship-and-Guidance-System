<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship and Guidance | Green Valley College Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['Figtree', 'system-ui', 'sans-serif'], display: ['Bricolage Grotesque', 'sans-serif'] },
                colors: { gvc: { 900: '#052e16', 800: '#064e2b', 700: '#15803d', 500: '#22c55e', 300: '#86efac', 100: '#dcfce7', 50: '#f0fdf4', sun: '#bef264' } }
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
    $links = [
        'home' => 'Home', 'announcement' => 'Announcement', 'activities' => 'Activities',
        'scholarship' => 'Scholarship', 'personnels' => 'Personnels',
        'contact' => 'Contact info', 'about' => 'About us',
    ];
    $announcements = $announcements ?? collect();
    $activities    = $activities ?? collect();
    $personnels    = $personnels ?? collect();
    $scholarships  = $scholarships ?? collect();
@endphp

<body class="bg-gvc-50 text-slate-800 font-sans antialiased">

{{-- NAVBAR --}}
<header class="sticky top-0 z-50 border-b border-white/10 bg-gvc-900/90 backdrop-blur-md"
        x-data="{ menu: false }">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3">

        <a href="{{ url('/gvc') }}" class="flex items-center gap-3 shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="" class="h-10 w-10 rounded-lg object-contain">
            <span class="font-display text-base font-extrabold leading-tight text-white">
                Green Valley College<br class="sm:hidden"> Foundation
            </span>
        </a>

        {{-- Desktop links --}}
        <nav class="hidden lg:flex items-center gap-1" aria-label="Main">
            @foreach ($links as $id => $label)
                <a href="#{{ $id }}"
                   class="rounded-full px-3 py-1.5 text-sm font-medium text-emerald-100/80 transition hover:bg-white/10 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-gvc-500">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @guest
                <a href="{{ route('login') }}" class="hidden sm:inline-flex rounded-full bg-gvc-sun px-4 py-2 text-sm font-semibold text-gvc-900 transition hover:brightness-110">Log in</a>
                <a href="/admin/login" class="hidden md:inline-flex rounded-full border border-white/25 px-4 py-2 text-sm font-medium text-emerald-50 transition hover:bg-white/10">Admin</a>
            @else
                <span class="hidden xl:inline text-sm text-emerald-100/70">Hello, <strong class="text-white">{{ auth()->user()->name }}</strong></span>

                @php
                    $unreadCount = \App\Models\AppointmentNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
                    $notifications = \App\Models\AppointmentNotification::where('user_id', auth()->id())
                        ->latest()->take(10)->get()
                        ->map(fn ($n) => [
                            'id' => $n->id, 'type' => $n->type, 'message' => $n->message,
                            'is_unread' => $n->isUnread(), 'time_ago' => $n->created_at->diffForHumans(),
                        ]);
                @endphp

                {{-- Notification bell --}}
                <div class="relative"
                     x-data="{
                        open: false, unreadCount: {{ $unreadCount }}, notifications: {{ Js::from($notifications) }},
                        fetchNotifications() {
                            if (document.hidden) return;
                            fetch('{{ route('notifications.fetch') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                .then(r => r.json())
                                .then(d => { this.unreadCount = d.unread_count; this.notifications = d.notifications; })
                                .catch(e => console.error('Notification fetch error:', e));
                        },
                        tone(type) {
                            if (['approved','accepted'].includes(type)) return 'bg-green-100 text-green-700';
                            if (type === 'referral_invitation') return 'bg-blue-100 text-blue-700';
                            if (type === 'rescheduled') return 'bg-amber-100 text-amber-700';
                            return 'bg-red-100 text-red-600';
                        },
                        label(type) {
                            return { referral_invitation: 'Counseling invite', rescheduled: 'Rescheduled' }[type] ?? null;
                        }
                     }"
                     x-init="setInterval(() => fetchNotifications(), 30000)">

                    <button @click="open = !open" @click.outside="open = false" aria-label="Notifications"
                            class="relative grid h-10 w-10 place-items-center rounded-full border border-white/25 text-emerald-50 transition hover:bg-white/10">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/></svg>
                        <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 9 ? '9+' : unreadCount"
                              class="absolute -right-1 -top-1 grid h-4 min-w-4 place-items-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"></span>
                    </button>

                    <div x-show="open" x-cloak x-transition.origin.top.right
                         class="absolute right-0 mt-2 w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="text-sm font-semibold">Notifications</span>
                            <form x-show="unreadCount > 0" action="{{ route('notifications.readAll') }}" method="POST">
                                @csrf
                                <button class="text-xs font-medium text-gvc-700 hover:underline">Mark all as read</button>
                            </form>
                        </div>
                        <div class="max-h-80 divide-y divide-slate-100 overflow-y-auto">
                            <p x-show="notifications.length === 0" class="px-4 py-10 text-center text-sm text-slate-400">No notifications yet.</p>
                            <template x-for="n in notifications" :key="n.id">
                                <form :action="`{{ url('notifications') }}/${n.id}/read`" method="POST">
                                    @csrf
                                    <button class="flex w-full items-start gap-3 px-4 py-3 text-left transition hover:bg-slate-50"
                                            :class="n.is_unread ? 'bg-gvc-100/60' : ''">
                                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full" :class="n.is_unread ? 'bg-gvc-500' : 'bg-transparent'"></span>
                                        <span class="min-w-0 flex-1">
                                            <span x-show="label(n.type)" x-text="label(n.type)" class="mb-1 inline-block rounded px-1.5 py-0.5 text-[11px] font-semibold" :class="tone(n.type)"></span>
                                            <span class="block text-sm leading-snug text-slate-700" x-text="n.message"></span>
                                            <span class="mt-1 block text-xs text-slate-400" x-text="n.time_ago"></span>
                                        </span>
                                    </button>
                                </form>
                            </template>
                        </div>
                        <a x-show="notifications.length > 0" href="{{ route('guidance') }}"
                           class="block border-t border-slate-100 bg-slate-50 px-4 py-2.5 text-center text-xs font-medium text-gvc-700 hover:underline">Open guidance portal</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-full border border-white/25 px-4 py-2 text-sm font-medium text-emerald-50 transition hover:bg-white/10">Log out</button>
                </form>
            @endguest

            {{-- Mobile toggle --}}
            <button class="grid h-10 w-10 place-items-center rounded-full border border-white/25 text-white lg:hidden"
                    @click="menu = !menu" :aria-expanded="menu" aria-label="Toggle menu">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <nav x-show="menu" x-cloak x-transition @click="menu = false" class="border-t border-white/10 bg-gvc-900 px-5 py-3 lg:hidden" aria-label="Mobile">
        @foreach ($links as $id => $label)
            <a href="#{{ $id }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-emerald-50 hover:bg-white/10">{{ $label }}</a>
        @endforeach
        @guest
            <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-gvc-sun">Log in</a>
            <a href="/admin/login" class="block rounded-lg px-3 py-2.5 text-sm text-emerald-100/60">Admin login</a>
        @endguest
    </nav>
</header>

{{-- HOME / HERO --}}
<section id="home" class="relative overflow-hidden rounded-b-[2.5rem] bg-gvc-900 text-white">
    <div class="absolute inset-0 bg-cover bg-center opacity-15" style="background-image:url('{{ asset('images/gvc.png') }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-gvc-900 via-gvc-800 to-gvc-700/90"></div>
    <div class="absolute -right-24 -top-24 h-[28rem] w-[28rem] rounded-full bg-gvc-500/30 blur-3xl"></div>
    <div class="absolute -bottom-32 left-1/3 h-80 w-80 rounded-full bg-gvc-sun/15 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-5 py-24 md:py-36">
        <h1 class="font-display max-w-3xl text-5xl font-extrabold leading-[1.02] tracking-tight sm:text-6xl md:text-7xl">
            Scholarships and guidance, in one place.
        </h1>

        @auth
            <p class="mt-6 max-w-xl text-lg text-emerald-50/80">
                Welcome back, {{ auth()->user()->name }}.
                @if (auth()->user()->isEnrolled())
                    Apply for a scholarship or book a counseling appointment.
                @else
                    Submit a referral or take the admission exam. Once you are enrolled, you can apply for a scholarship.
                @endif
            </p>

            <div class="mt-10 flex flex-wrap gap-3">
                @if (auth()->user()->isEnrolled())
                    <a href="{{ url('/scholarship') }}" class="rounded-full bg-gvc-sun px-7 py-3.5 font-semibold text-gvc-900 transition hover:brightness-110">Apply for a scholarship</a>
                    <a href="{{ route('guidance') }}" class="rounded-full border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white/10">Book guidance</a>
                @else
                    <span title="You must be enrolled to access scholarship features" class="cursor-not-allowed select-none rounded-full border border-white/15 px-7 py-3.5 font-semibold text-white/40">Scholarship (enrolled students only)</span>
                    <a href="{{ route('guidance_referrals.get') }}" class="rounded-full bg-gvc-sun px-7 py-3.5 font-semibold text-gvc-900 transition hover:brightness-110">Submit a referral</a>
                @endif

                @if (isset($exam) && $exam)
                    <a href="{{ route('exam.admission', $exam->id) }}" class="rounded-full border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white/10">Take the admission exam</a>
                @endif
            </div>

            @unless (auth()->user()->isEnrolled())
                <p class="mt-6 inline-block rounded-xl border border-amber-300/40 bg-amber-400/15 px-4 py-2.5 text-sm text-amber-100">
                    You are not enrolled yet. Complete your enrollment to unlock scholarships.
                </p>
            @endunless
        @else
            <p class="mt-6 max-w-xl text-lg text-emerald-50/80">Log in or register to apply for scholarships and manage your guidance appointments.</p>
            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('login') }}" class="rounded-full bg-gvc-sun px-7 py-3.5 font-semibold text-gvc-900 transition hover:brightness-110">Log in</a>
                <a href="{{ route('register') }}" class="rounded-full border border-white/40 px-7 py-3.5 font-semibold transition hover:bg-white/10">Create an account</a>
            </div>
        @endauth
    </div>
</section>

{{-- ANNOUNCEMENT --}}
<section id="announcement" class="mx-auto max-w-7xl px-5 py-20">
    <h2 class="border-l-4 border-gvc-500 pl-4 font-display text-3xl font-extrabold text-gvc-800 md:text-4xl">Announcements</h2>
    <div class="mt-8 divide-y divide-gvc-100 rounded-3xl bg-white px-6 shadow-sm ring-1 ring-gvc-500/20">
        @forelse ($announcements as $a)
            <article class="grid gap-2 py-6 md:grid-cols-[10rem_1fr] md:gap-8">
                <time class="text-sm font-semibold text-gvc-700">{{ optional($a->created_at)->format('M d, Y') }}</time>
                <div>
                    <h3 class="font-display text-xl font-semibold text-slate-900">{{ $a->title }}</h3>
                    <p class="mt-1 max-w-2xl text-slate-600">{{ \Illuminate\Support\Str::limit($a->body ?? $a->content ?? '', 180) }}</p>
                </div>
            </article>
        @empty
            <p class="py-10 text-slate-500">No announcements right now. Check back soon.</p>
        @endforelse
    </div>
</section>

{{-- ACTIVITIES --}}
<section id="activities" class="bg-gvc-100 py-20">
    <div class="mx-auto max-w-7xl px-5">
        <h2 class="border-l-4 border-gvc-500 pl-4 font-display text-3xl font-extrabold text-gvc-800 md:text-4xl">Activities</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($activities as $act)
                <article class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-gvc-500/20 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-gvc-500/20">
                    @if (!empty($act->image))
                        <img src="{{ asset('storage/'.$act->image) }}" alt="" class="aspect-[16/10] w-full object-cover" loading="lazy">
                    @else
                        <div class="aspect-[16/10] bg-gradient-to-br from-gvc-700 to-gvc-500"></div>
                    @endif
                    <div class="p-5">
                        <h3 class="font-display text-lg font-semibold">{{ $act->title }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($act->description ?? '', 110) }}</p>
                    </div>
                </article>
            @empty
                <p class="col-span-full text-slate-500">No activities posted yet.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- SCHOLARSHIP --}}
<section id="scholarship" class="mx-auto max-w-7xl px-5 py-20">
    <h2 class="border-l-4 border-gvc-500 pl-4 font-display text-3xl font-extrabold text-gvc-800 md:text-4xl">Scholarships</h2>
    <p class="mt-3 max-w-xl text-slate-600">Programs currently open to qualified students.</p>
    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($scholarships as $s)
            <div class="flex items-center justify-between gap-4 rounded-3xl bg-gradient-to-br from-gvc-700 to-gvc-800 p-6 text-white shadow-lg shadow-gvc-700/25 transition hover:-translate-y-1">
                <h3 class="font-display text-lg font-semibold leading-snug">{{ $s->name }}</h3>
                @if (($s->status ?? 'active') === 'active')
                    <span class="shrink-0 rounded-full bg-gvc-sun px-2.5 py-1 text-xs font-semibold text-gvc-900">Open</span>
                @endif
            </div>
        @empty
            <p class="col-span-full text-slate-500">No scholarships available at this time.</p>
        @endforelse
    </div>
</section>

{{-- PERSONNELS --}}
<section id="personnels" class="bg-gvc-100 py-20">
    <div class="mx-auto max-w-7xl px-5">
        <h2 class="border-l-4 border-gvc-500 pl-4 font-display text-3xl font-extrabold text-gvc-800 md:text-4xl">Our personnel</h2>
        <div class="mt-8 grid grid-cols-2 gap-6 md:grid-cols-4">
            @forelse ($personnels as $p)
                <div>
                    @if (!empty($p->photo))
                        <img src="{{ asset('storage/'.$p->photo) }}" alt="{{ $p->name }}" class="aspect-square w-full rounded-3xl object-cover" loading="lazy">
                    @else
                        <div class="grid aspect-square w-full place-items-center rounded-3xl bg-gradient-to-br from-gvc-700 to-gvc-900 font-display text-4xl font-extrabold text-gvc-300">{{ \Illuminate\Support\Str::substr($p->name, 0, 1) }}</div>
                    @endif
                    <h3 class="mt-3 font-semibold text-slate-900">{{ $p->name }}</h3>
                    <p class="text-sm text-slate-500">{{ $p->position ?? '' }}</p>
                </div>
            @empty
                <p class="col-span-full text-slate-500">Personnel list coming soon.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- CONTACT --}}
<section id="contact" class="mx-auto max-w-7xl px-5 py-20">
    <h2 class="border-l-4 border-gvc-500 pl-4 font-display text-3xl font-extrabold text-gvc-800 md:text-4xl">Contact info</h2>
    <dl class="mt-8 grid gap-6 sm:grid-cols-3">
        {{-- TODO: replace placeholders with real details or config() values --}}
        <div class="rounded-3xl bg-gvc-100 p-6"><dt class="text-sm font-semibold text-gvc-700">Address</dt><dd class="mt-1 font-semibold">Green Valley College Foundation Inc.<br>Street, Barangay, City, Province</dd></div>
        <div class="rounded-3xl bg-gvc-100 p-6"><dt class="text-sm font-semibold text-gvc-700">Phone</dt><dd class="mt-1 font-semibold">(000) 000-0000</dd></div>
        <div class="rounded-3xl bg-gvc-100 p-6"><dt class="text-sm font-semibold text-gvc-700">Email</dt><dd class="mt-1 font-semibold"><a href="mailto:info@gvcfi.edu.ph" class="text-gvc-700 hover:underline">info@gvcfi.edu.ph</a></dd></div>
    </dl>
</section>

{{-- ABOUT --}}
<section id="about" class="bg-gradient-to-br from-gvc-900 via-gvc-800 to-gvc-700 py-20 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-5 md:grid-cols-[1fr_1.4fr]">
        <h2 class="font-display text-3xl font-extrabold md:text-4xl">About us</h2>
        <div class="max-w-2xl space-y-4 text-emerald-50/80">
            {{-- TODO: replace with the foundation's real history, mission, and vision --}}
            <p>Green Valley College Foundation Inc. supports students through scholarships and guidance services, so financial need and personal challenges do not stop anyone from finishing their studies.</p>
            <p>Our guidance office offers counseling, referrals, and appointments, and our scholarship programs are open to qualified, enrolled students.</p>
        </div>
    </div>
</section>

<footer class="bg-gvc-900 border-t border-white/10 py-8 text-center text-xs text-emerald-100/50">
    &copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.
</footer>

</body>
</html>