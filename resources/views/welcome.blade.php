<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Scholarship and Guidance Management | Green Valley College Foundation</title>
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
</head>

<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased">

<!-- NAVBAR -->
<header class="bg-green-800 border-b border-white sticky top-0 z-50 shadow-sm shadow-green-900/5">
    <div class="max-w-8xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">

        {{-- Logo --}}
        <a href="{{ url('/gvc') }}" class="flex items-center gap-2 flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation"
                 class="w-10 h-10 rounded-lg object-contain flex-shrink-0">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>

        {{-- Nav --}}
        <nav class="flex items-center gap-2 sm:gap-3">

            @guest
                {{-- Guest menu --}}
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        @click.outside="open = false"
                        class="flex items-center justify-center w-9 h-9 rounded-full border
                               border-emerald-200/70 bg-emerald-900/40 hover:bg-emerald-800/80
                               hover:border-emerald-200 transition"
                    >
                        <svg class="w-4 h-4 text-emerald-50" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg
                               border border-slate-100 overflow-hidden z-50"
                        style="display:none;"
                    >
                        <a href="/admin/login"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700
                                  font-medium hover:bg-green-50 hover:text-green-800 transition">
                            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                                         11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824
                                         10.29 9 11.622 5.176-1.332 9-6.03 9-11.622
                                         0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Login as Admin
                        </a>
                    </div>
                </div>

            @else
                {{-- Hello text --}}
                <span class="hidden sm:inline text-xs sm:text-sm text-emerald-100 mr-1">
                    Hello, <span class="font-semibold">{{ auth()->user()->name }}</span>
                </span>

                {{-- ── NOTIFICATION BELL ─────────────────────────────────────────── --}}
                @php
                    $unreadCount = \App\Models\AppointmentNotification::where('user_id', auth()->id())
                        ->whereNull('read_at')
                        ->count();

                    $notifications = \App\Models\AppointmentNotification::where('user_id', auth()->id())
                        ->with(['appointment', 'referralInvitation.referral'])
                        ->latest()
                        ->take(10)
                        ->get()
                        ->map(fn ($n) => [
                            'id'        => $n->id,
                            'type'      => $n->type,
                            'message'   => $n->message,
                            'is_unread' => $n->isUnread(),
                            'time_ago'  => $n->created_at->diffForHumans(),
                        ]);
                @endphp

                <div class="relative"
                     x-data="{
                         open: false,
                         unreadCount: {{ $unreadCount }},
                         notifications: {{ Js::from($notifications) }},

                         fetchNotifications() {
                             fetch('{{ route('notifications.fetch') }}', {
                                 headers: { 'X-Requested-With': 'XMLHttpRequest' }
                             })
                             .then(res => res.json())
                             .then(data => {
                                 this.unreadCount   = data.unread_count;
                                 this.notifications = data.notifications;
                             })
                             .catch(err => console.error('Notification fetch error:', err));
                         },

                         iconClass(type) {
                             if (['approved', 'accepted'].includes(type)) return 'bg-green-100 text-green-600';
                             if (type === 'referral_invitation')           return 'bg-blue-100 text-blue-600';
                             if (type === 'rescheduled')                    return 'bg-amber-100 text-amber-600';
                             return 'bg-red-100 text-red-500';
                         },

                         dotClass(type) {
                             if (type === 'referral_invitation') return 'bg-blue-500';
                             if (type === 'rescheduled')          return 'bg-amber-500';
                             return 'bg-green-500';
                         }
                     }"
                     x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 10000)"
                >

                    {{-- Bell button --}}
                    <button
                        @click="open = !open"
                        @click.outside="open = false"
                        class="relative flex items-center justify-center w-9 h-9 rounded-full
                               border border-emerald-200/70 bg-emerald-900/40
                               hover:bg-emerald-800/80 hover:border-emerald-200 transition"
                        aria-label="Notifications"
                    >
                        <svg class="w-4 h-4 text-emerald-50" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0
                                     10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3
                                     3 0 11-6 0v-1m6 0H9"/>
                        </svg>

                        <span
                            x-show="unreadCount > 0"
                            x-text="unreadCount > 9 ? '9+' : unreadCount"
                            class="absolute -top-1 -right-1 flex h-4 w-4 items-center
                                   justify-center rounded-full bg-red-500 text-[10px]
                                   font-bold text-white leading-none"
                        ></span>
                    </button>

                    {{-- Dropdown panel --}}
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl
                               border border-slate-100 z-50 overflow-hidden"
                        style="display:none;"
                    >
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
                            <span class="text-sm font-semibold text-slate-700">
                                Notifications
                                <span
                                    x-show="unreadCount > 0"
                                    x-text="unreadCount"
                                    class="ml-1.5 inline-flex items-center justify-center
                                           px-1.5 py-0.5 rounded-full bg-red-100
                                           text-red-600 text-[10px] font-bold"
                                ></span>
                            </span>
                            <form x-show="unreadCount > 0"
                                  action="{{ route('notifications.readAll') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="text-xs text-green-600 hover:text-green-800 font-medium transition">
                                    Mark all read
                                </button>
                            </form>
                        </div>

                        {{-- Notification list --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-10 text-center">
                                    <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118
                                                 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214
                                                 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6
                                                 0v-1m6 0H9"/>
                                    </svg>
                                    <p class="text-sm text-slate-400">No notifications yet.</p>
                                </div>
                            </template>

                            <template x-for="notif in notifications" :key="notif.id">
                                <form :action="`{{ url('notifications') }}/${notif.id}/read`" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left flex items-start gap-3 px-4 py-3
                                                   hover:bg-slate-50 transition"
                                            :class="notif.is_unread ? 'bg-green-50/70' : 'bg-white'">

                                        <span class="mt-0.5 flex-shrink-0 flex items-center justify-center
                                                     w-8 h-8 rounded-full"
                                              :class="iconClass(notif.type)">
                                            <template x-if="['approved','accepted'].includes(notif.type)">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </template>
                                            <template x-if="notif.type === 'referral_invitation'">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2
                                                             2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </template>
                                            <template x-if="notif.type === 'rescheduled'">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M16 3v4M8 3v4M4 11h16M4 7a2 2 0 012-2h12a2 2 0
                                                             012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7zm4.5 8.5l2 2
                                                             3.5-4"/>
                                                </svg>
                                            </template>
                                            <template x-if="!['approved','accepted','referral_invitation','rescheduled'].includes(notif.type)">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </template>
                                        </span>

                                        <span class="flex-1 min-w-0">
                                            <template x-if="notif.type === 'referral_invitation'">
                                                <span class="inline-block mb-1 px-1.5 py-0.5 rounded text-[10px]
                                                             font-semibold bg-blue-100 text-blue-600
                                                             uppercase tracking-wide">
                                                    Counseling Invite
                                                </span>
                                            </template>
                                            <template x-if="notif.type === 'rescheduled'">
                                                <span class="inline-block mb-1 px-1.5 py-0.5 rounded text-[10px]
                                                             font-semibold bg-amber-100 text-amber-600
                                                             uppercase tracking-wide">
                                                    Rescheduled
                                                </span>
                                            </template>
                                            <span class="block text-sm text-slate-700 leading-snug"
                                                  x-text="notif.message"></span>
                                            <span class="block text-xs text-slate-400 mt-1"
                                                  x-text="notif.time_ago"></span>
                                        </span>

                                        <template x-if="notif.is_unread">
                                            <span class="mt-2 flex-shrink-0 w-2 h-2 rounded-full"
                                                  :class="dotClass(notif.type)"></span>
                                        </template>
                                    </button>
                                </form>
                            </template>
                        </div>

                        <template x-if="notifications.length > 0">
                            <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50 text-center">
                                <a href="{{ route('guidance') }}"
                                   class="text-xs text-green-600 hover:text-green-800 font-medium transition">
                                    View guidance portal →
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
                {{-- ── END NOTIFICATION BELL ──────────────────────────────────────── --}}

                {{-- Logout --}}
                <form id="logout-form" method="POST" action="{{ route('logout') }}"
                      style="display:none;">
                    @csrf
                </form>

                <button
                    onclick="document.getElementById('logout-form').submit()"
                    class="inline-flex items-center rounded-full border border-emerald-200/70
                           bg-emerald-900/40 px-4 py-1.5 text-xs sm:text-sm font-medium
                           text-emerald-50 hover:bg-emerald-800/80 hover:border-emerald-200
                           transition"
                >
                    Logout
                </button>
            @endguest
        </nav>
    </div>
</header>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- HERO -->
<section class="relative overflow-hidden py-28 md:py-40">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.12]"
         style="background-image: url('{{ asset('images/gvc.png') }}');"></div>

    <div class="relative max-w-4xl mx-auto px-6 text-center">
        <h1 class="font-display text-4xl sm:text-5xl md:text-8xl font-extrabold leading-[1.08]
                   mb-6 tracking-tight text-emerald-50 drop-shadow-md">
            GVCFI <br class="hidden sm:block">
            <span class="text-gvc-pale drop-shadow-sm">Scholarship and Guidance</span>
        </h1>

        @auth
            <p class="mb-6 text-emerald-50/90 text-sm sm:text-base max-w-2xl mx-auto">
                Welcome back, {{ auth()->user()->name }}.
                @if(auth()->user()->isEnrolled())
                    Start a new scholarship application or make a counseling appointment.
                @else
                    You can submit a referral or take the scholarship qualifying exam below.
                    Once enrolled, you'll be able to apply for a scholarship.
                @endif
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">

                @if(auth()->user()->isEnrolled())
                    {{-- Enrolled: Scholarship + Guidance --}}
                    <a href="{{ url('/scholarship') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl
                              bg-emerald-400 px-6 py-3 text-sm sm:text-base font-semibold
                              text-emerald-950 shadow-btn-glow hover:bg-emerald-300 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 14l6.16-3.422A12.083 12.083 0 0121 12c0 5.523-4.477
                                     10-10 10S2 17.523 2 12c0-.538.043-1.065.125-1.578L12 14z"/>
                        </svg>
                        Scholarship
                    </a>

                    <a href="{{ route('guidance') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl
                              bg-emerald-900/70 border border-emerald-200/70 px-6 py-3
                              text-sm sm:text-base font-semibold text-emerald-50
                              hover:bg-emerald-800/80 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7
                                     20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0
                                     0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        Guidance
                    </a>

                @else
                    {{-- Unenrolled: disabled Scholarship + Referral button --}}
                    <span title="You must be enrolled to access scholarship features"
                          class="inline-flex items-center justify-center gap-2 rounded-xl
                                 bg-slate-400/30 border border-white/20 px-6 py-3 text-sm
                                 sm:text-base font-semibold text-white/40 cursor-not-allowed
                                 select-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                        Scholarship
                        <span class="text-xs font-normal opacity-70">(Enrolled Only)</span>
                    </span>

                    {{-- Referral — available to unenrolled students --}}
                    <a href="{{ route('guidance_referrals.get') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl
                              bg-emerald-400 px-6 py-3 text-sm sm:text-base font-semibold
                              text-emerald-950 shadow-btn-glow hover:bg-emerald-300 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"/>
                            <circle cx="6" cy="12" r="3"/>
                            <circle cx="18" cy="19" r="3"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                        Submit a Referral
                    </a>
                @endif

                @if(isset($exam) && $exam)
                    <a href="{{ route('exam.admission', $exam->id) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl
                              bg-white/10 border border-white/30 px-6 py-3 text-sm sm:text-base
                              font-semibold text-white hover:bg-white/20 hover:border-white/50
                              transition backdrop-blur-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2
                                     0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0
                                     012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Admission Exam
                    </a>
                @endif
            </div>

            @if(!auth()->user()->isEnrolled())
                <div class="mt-6 inline-flex items-center gap-2 bg-amber-500/20
                            border border-amber-300/40 text-amber-100 text-xs sm:text-sm
                            rounded-xl px-4 py-2.5">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73
                                 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898
                                 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    You are not yet enrolled. Complete your enrollment to unlock scholarship features.
                </div>
            @endif

        @else
            <p class="mb-6 text-emerald-50/90 text-sm sm:text-base max-w-2xl mx-auto">
                Login or register to apply for scholarships and manage your guidance appointments.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-emerald-400
                          px-6 py-3 text-sm sm:text-base font-semibold text-emerald-950
                          shadow-btn-glow hover:bg-emerald-300 transition">
                    Login to Continue
                </a>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-emerald-900/70
                          border border-emerald-200/70 px-6 py-3 text-sm sm:text-base
                          font-semibold text-emerald-50 hover:bg-emerald-800/80 transition">
                    Create an Account
                </a>
            </div>
        @endauth
    </div>
</section>

<!-- SCHOLARSHIPS -->
<section id="features" class="py-24 md:py-32 bg-gradient-to-b from-green-50/80 to-white scroll-mt-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-20">
            <span class="inline-block px-4 py-1.5 rounded-full bg-green-100 text-green-700
                         text-sm font-semibold mb-4">Scholarships</span>
            <h2 class="font-display text-3xl md:text-5xl font-bold text-slate-900 mb-5">
                What We Offer
            </h2>
            <p class="text-slate-500 text-sm sm:text-base max-w-xl mx-auto">
                Explore our active scholarship programs available for qualified students.
            </p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($scholarships as $scholarship)
                <div class="group bg-white p-8 rounded-2xl shadow-sm border border-green-200/60
                            transition-all duration-300 ease-in-out hover:-translate-y-1.5
                            hover:shadow-card-hover hover:border-emerald-300/50">

                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center
                                mb-4 group-hover:bg-green-200 transition-colors duration-300">
                        <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 14l6.16-3.422A12.083 12.083 0 0121 12c0 5.523-4.477
                                     10-10 10S2 17.523 2 12c0-.538.043-1.065.125-1.578L12 14z"/>
                        </svg>
                    </div>

                    <h3 class="font-display font-bold text-lg text-slate-900 mb-2 leading-snug">
                        {{ $scholarship->name }}
                    </h3>

                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700
                                 bg-green-100 px-2.5 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                        Active
                    </span>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                    <p class="text-slate-400 text-sm">No scholarships available at this time.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-10 bg-slate-800 border-t border-green-300/20">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="text-xs text-slate-500">
            &copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.
        </p>
    </div>
</footer>

</body>
</html>