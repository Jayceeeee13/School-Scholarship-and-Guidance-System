{{-- resources/views/partials/notification-bell.blade.php --}}
@php
    $unreadCount = \App\Models\AppointmentNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
    $notifications = \App\Models\AppointmentNotification::where('user_id', auth()->id())
        ->latest()->take(10)->get()
        ->map(fn ($n) => [
            'id' => $n->id, 'type' => $n->type, 'message' => $n->message,
            'is_unread' => $n->isUnread(), 'time_ago' => $n->created_at->diffForHumans(),
        ]);
@endphp

<div class="relative"
     x-data="{
        open: false,
        unreadCount: {{ $unreadCount }},
        notifications: {{ Js::from($notifications) }},
        fetchNotifications() {
            if (document.hidden) return;
            fetch('{{ route('notifications.fetch') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => { this.unreadCount = d.unread_count; this.notifications = d.notifications; })
                .catch(e => console.error('Notification fetch error:', e));
        },
        iconClass(t) {
            if (['approved','accepted'].includes(t)) return 'bg-green-100 text-green-600';
            if (t === 'referral_invitation') return 'bg-blue-100 text-blue-600';
            if (t === 'rescheduled') return 'bg-amber-100 text-amber-600';
            return 'bg-red-100 text-red-500';
        },
        dotClass(t) {
            if (t === 'referral_invitation') return 'bg-blue-500';
            if (t === 'rescheduled') return 'bg-amber-500';
            return 'bg-green-500';
        }
     }"
     x-init="setInterval(() => fetchNotifications(), 30000)">

    <button @click="open = !open" @click.outside="open = false" aria-label="Notifications"
            class="relative flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white/20">
        <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 9 ? '9+' : unreadCount"
              class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-green-900"></span>
    </button>

    <div x-show="open" x-cloak x-transition.origin.top.right
         class="absolute right-0 mt-3 w-80 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
            <span class="text-sm font-semibold text-slate-700">Notifications
                <span x-show="unreadCount > 0" x-text="unreadCount" class="ml-1.5 rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-bold text-red-600"></span>
            </span>
            <form x-show="unreadCount > 0" action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button class="text-xs font-medium text-green-600 transition hover:text-green-800">Mark all read</button>
            </form>
        </div>

        <div class="max-h-80 divide-y divide-slate-50 overflow-y-auto">
            <p x-show="notifications.length === 0" class="px-4 py-10 text-center text-sm text-slate-400">No notifications yet.</p>

            <template x-for="n in notifications" :key="n.id">
                <form :action="`{{ url('notifications') }}/${n.id}/read`" method="POST">
                    @csrf
                    <button class="flex w-full items-start gap-3 px-4 py-3 text-left transition hover:bg-slate-50"
                            :class="n.is_unread ? 'bg-green-50/70' : 'bg-white'">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full" :class="iconClass(n.type)">
                            <template x-if="['approved','accepted'].includes(n.type)">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="n.type === 'referral_invitation'">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </template>
                            <template x-if="n.type === 'rescheduled'">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16M4 7a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7zm4.5 8.5l2 2 3.5-4"/></svg>
                            </template>
                            <template x-if="!['approved','accepted','referral_invitation','rescheduled'].includes(n.type)">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </template>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span x-show="n.type === 'referral_invitation'" class="mb-1 inline-block rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-blue-600">Counseling Invite</span>
                            <span x-show="n.type === 'rescheduled'" class="mb-1 inline-block rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-600">Rescheduled</span>
                            <span class="block text-sm leading-snug text-slate-700" x-text="n.message"></span>
                            <span class="mt-1 block text-xs text-slate-400" x-text="n.time_ago"></span>
                        </span>
                        <span x-show="n.is_unread" class="mt-2 h-2 w-2 shrink-0 rounded-full" :class="dotClass(n.type)"></span>
                    </button>
                </form>
            </template>
        </div>

        <a x-show="notifications.length > 0" href="{{ route('guidance') }}"
           class="block border-t border-slate-100 bg-slate-50 px-4 py-2.5 text-center text-xs font-medium text-green-600 transition hover:text-green-800">View guidance portal →</a>
    </div>
</div>