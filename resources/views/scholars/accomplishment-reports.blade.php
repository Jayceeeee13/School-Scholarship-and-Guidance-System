<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accomplishment Report | Green Valley College Foundation</title>
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
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp 0.55s cubic-bezier(.22,1,.36,1) both; }
    </style>
</head>

<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

{{-- ── NAVBAR ── --}}
<header class="bg-green-800 border-b border-white sticky top-0 z-40 shadow-sm shadow-green-900/5">
    <div class="max-w-8xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2 flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Green Valley College Foundation" class="w-10 h-10 rounded-lg object-contain flex-shrink-0">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>
        <nav class="flex items-center gap-2 sm:gap-3">
            <span class="hidden sm:inline text-xs sm:text-sm text-emerald-100 mr-2">
                Hello, <span class="font-semibold">{{ auth()->user()->name }}</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-full border border-emerald-200/70 bg-emerald-900/40 px-4 py-1.5 text-xs sm:text-sm font-medium text-emerald-50 hover:bg-emerald-800/80 hover:border-emerald-200 transition">
                    Logout
                </button>
            </form>
        </nav>
    </div>
</header>

{{-- ── HERO ── --}}
<section class="relative overflow-hidden py-16 md:py-20">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="relative max-w-3xl mx-auto px-6 text-center fade-up">
        <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-900/60 border border-emerald-400/30 text-emerald-300 text-sm font-semibold mb-4 tracking-wide">
            Scholar Portal
        </span>
        <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight mb-3 tracking-tight text-emerald-50 drop-shadow-md">
            Accomplishment Report
        </h1>
        <p class="text-emerald-100/80 text-sm sm:text-base max-w-xl mx-auto">
            Institutional scholars — log your activities each term as required by the scholarship office.
        </p>
    </div>
</section>

<section class="flex-1 py-14 md:py-20 bg-gradient-to-b from-green-50/80 to-white">
    <div class="max-w-4xl mx-auto px-6">

        @if(session('success'))
            <div class="fade-up mb-8 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-5 py-4">
                {{ session('success') }}
            </div>
        @endif

        @if(!$isEligible)
            {{-- ── NOT ELIGIBLE ── --}}
            <div class="fade-up bg-white rounded-2xl border border-gray-200 shadow-sm p-10 text-center">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-2">Not Available for Your Scholarship</h2>
                <p class="text-sm text-slate-500 leading-relaxed max-w-md mx-auto mb-2">
                    Accomplishment reports are only required for scholars under the
                    <strong>Talents</strong>, <strong>Supreme Student Government</strong>, or <strong>Sports</strong> scholarship types.
                </p>
                @if($scholar)
                    <p class="text-xs text-slate-400">
                        Your current scholarship type: <span class="font-semibold text-slate-500">{{ $scholar->type_of_scholarship }}</span>
                    </p>
                @else
                    <p class="text-xs text-slate-400">
                        We couldn't find a scholar record linked to your account. Please contact the scholarship office.
                    </p>
                @endif
                <a href="{{ url('/scholarship') }}" class="inline-flex items-center gap-2 mt-6 text-sm text-emerald-700 hover:text-emerald-800 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Scholarship Portal
                </a>
            </div>
        @elseif(!$activeTerm)
            <div class="fade-up bg-white rounded-2xl border border-gray-200 shadow-sm p-10 text-center">
                <h2 class="font-display text-xl font-bold text-slate-900 mb-2">No Active Term</h2>
                <p class="text-sm text-slate-500">There is currently no active school term. Please contact the scholarship office.</p>
            </div>
        @else
            {{-- ── OFFICIAL FORM REPLICA ── --}}
            <div class="fade-up bg-white rounded-2xl border border-gray-300 shadow-sm overflow-hidden mb-10">

                <div class="text-center py-2.5 border-b-2 border-slate-800 bg-slate-50">
                    <p class="font-semibold text-slate-700 text-sm sm:text-base">
                        {{ strtoupper($activeTerm->semester) }} — A.Y. {{ $activeTerm->school_year }}
                    </p>
                </div>

                {{-- Scholar info (auto-filled, read-only) --}}
                <div class="p-6 pb-2 space-y-2 text-sm">
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 flex-shrink-0">Name</span>
                        <span class="flex-1 border-b border-dotted border-slate-400 pb-0.5">{{ $scholar->full_name }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 flex-shrink-0">Course &amp; Year</span>
                        <span class="flex-1 border-b border-dotted border-slate-400 pb-0.5">
                            {{ $scholar->program }} @if($scholar->year_level) — Year {{ $scholar->year_level }} @endif
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 flex-shrink-0">Scholarship</span>
                        <span class="flex-1 border-b border-dotted border-slate-400 pb-0.5">{{ $scholar->type_of_scholarship }}</span>
                    </div>
                </div>

                {{-- Activity card-list form --}}
                <form action="{{ route('accomplishment_reports.store') }}" method="POST" class="p-6 pt-4">
                    @csrf

                    @if($errors->any())
                        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-5 py-4">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="activity-rows" class="space-y-3">
                        @php
                            $seedRows = $existingReport && $existingReport->activities->isNotEmpty()
                                ? $existingReport->activities
                                : collect([null, null, null]);
                        @endphp
                        @foreach($seedRows as $i => $row)
                            <div class="activity-row rounded-xl border border-slate-300 bg-slate-50/60 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="seq-badge inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-700 text-white text-xs font-bold">
                                        {{ $i + 1 }}
                                    </span>
                                    <button type="button" onclick="removeRow(this)" class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-semibold" title="Remove activity">
                                        ✕ Remove
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">Date</label>
                                        <input type="date" name="activities[{{ $i }}][activity_date]"
                                               value="{{ $row?->activity_date?->format('Y-m-d') }}"
                                               class="w-full text-sm border border-slate-300 bg-white rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">Venue</label>
                                        <input type="text" name="activities[{{ $i }}][venue]"
                                               value="{{ $row?->venue }}"
                                               placeholder="Venue"
                                               class="w-full text-sm border border-slate-300 bg-white rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">Activity</label>
                                        <input type="text" name="activities[{{ $i }}][activity]"
                                               value="{{ $row?->activity }}"
                                               placeholder="Describe the activity"
                                               class="w-full text-sm border border-slate-300 bg-white rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" onclick="addRow()"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Add Activity
                    </button>

                    <div class="mt-6 flex items-center justify-between flex-wrap gap-3">
                        <p class="text-xs text-slate-400">
                            @if($existingReport)
                                You already submitted a report for this term. Submitting again will replace it.
                            @else
                                This report covers {{ strtoupper($activeTerm->semester) }}, A.Y. {{ $activeTerm->school_year }}.
                            @endif
                        </p>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm px-6 py-3 transition-colors">
                            {{ $existingReport ? 'Update Report' : 'Submit Report' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── PAST TERM REPORTS ── --}}
            @if($pastReports->isNotEmpty())
                <div class="fade-up">
                    <h2 class="font-display text-lg font-bold text-slate-900 mb-4">Submission History</h2>
                    <div class="space-y-4">
                        @foreach($pastReports as $report)
                            @php
                                $statusStyles = [
                                    'pending'  => ['bg' => 'bg-amber-100', 'tx' => 'text-amber-800', 'label' => 'Pending Review'],
                                    'approved' => ['bg' => 'bg-emerald-100', 'tx' => 'text-emerald-800', 'label' => 'Approved'],
                                    'rejected' => ['bg' => 'bg-red-100', 'tx' => 'text-red-800', 'label' => 'Rejected'],
                                ];
                                $s = $statusStyles[$report->status] ?? $statusStyles['pending'];
                            @endphp
                            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                                <div class="flex items-start justify-between gap-4 flex-wrap mb-3">
                                    <h3 class="font-semibold text-slate-800">
                                        {{ $report->term?->semester }} — A.Y. {{ $report->term?->school_year }}
                                    </h3>
                                    <span class="inline-flex items-center rounded-full {{ $s['bg'] }} {{ $s['tx'] }} px-3 py-1 text-xs font-bold whitespace-nowrap">
                                        {{ $s['label'] }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    @foreach($report->activities as $activity)
                                        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5 text-xs bg-slate-50 rounded-lg px-3 py-2">
                                            <span class="font-semibold text-slate-600 w-24 flex-shrink-0">
                                                {{ $activity->activity_date?->format('M d, Y') ?? '—' }}
                                            </span>
                                            <span class="text-slate-400">·</span>
                                            <span class="text-slate-500">{{ $activity->venue ?? '—' }}</span>
                                            <span class="text-slate-400">·</span>
                                            <span class="text-slate-700">{{ $activity->activity ?? '—' }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                @if($report->status !== 'pending' && $report->remarks)
                                    <p class="text-xs text-slate-500 mt-3 bg-slate-50 rounded-lg px-3 py-2">
                                        <span class="font-semibold">Office remarks:</span> {{ $report->remarks }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <div class="text-center mt-12">
            <a href="{{ url('/scholarship') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-emerald-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Scholarship Portal
            </a>
        </div>

    </div>
</section>

<footer class="py-10 bg-slate-800 border-t border-green-300/20">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="text-slate-400 text-sm">© {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.</p>
    </div>
</footer>

<script>
    let rowIndex = document.querySelectorAll('#activity-rows .activity-row').length;

    function addRow() {
        const container = document.getElementById('activity-rows');
        const div = document.createElement('div');
        div.className = 'activity-row rounded-xl border border-slate-300 bg-slate-50/60 p-4';
        div.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <span class="seq-badge inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-700 text-white text-xs font-bold"></span>
                <button type="button" onclick="removeRow(this)" class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-semibold" title="Remove activity">
                    ✕ Remove
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Date</label>
                    <input type="date" name="activities[${rowIndex}][activity_date]" class="w-full text-sm border border-slate-300 bg-white rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Venue</label>
                    <input type="text" name="activities[${rowIndex}][venue]" placeholder="Venue" class="w-full text-sm border border-slate-300 bg-white rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Activity</label>
                    <input type="text" name="activities[${rowIndex}][activity]" placeholder="Describe the activity" class="w-full text-sm border border-slate-300 bg-white rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-400 focus:border-emerald-400">
                </div>
            </div>
        `;
        container.appendChild(div);
        rowIndex++;
        renumberRows();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('#activity-rows .activity-row');
        if (rows.length <= 1) return; // keep at least one row
        btn.closest('.activity-row').remove();
        renumberRows();
    }

    function renumberRows() {
        document.querySelectorAll('#activity-rows .activity-row').forEach((row, i) => {
            row.querySelector('.seq-badge').textContent = i + 1;
        });
    }
</script>

</body>
</html>