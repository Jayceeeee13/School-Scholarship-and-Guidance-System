<x-filament-panels::page>

@php
    $user = auth()->user();

    $initial = strtoupper(substr($user->name ?? 'U', 0, 1));

    $isAdmin = $user->hasRole('Admin') || $user->hasRole('admin');
    $isScholarship = $user->hasRole('Scholarship') || $user->hasRole('scholarship');
    $isGuidance = $user->hasRole('Guidance') || $user->hasRole('guidance');
    $isDepartmentHead = $user->hasRole('Department Head') || $user->hasRole('department head');

    if ($isAdmin) {
        $avatarColor = '#2563eb';
        $roleLabel = 'Administrator';
        $roleIcon = '👑';
        $badgeBg = '#dbeafe';
        $badgeText = '#1d4ed8';
    } elseif ($isScholarship) {
        $avatarColor = '#059669';
        $roleLabel = 'Scholarship Admin';
        $roleIcon = '🎓';
        $badgeBg = '#d1fae5';
        $badgeText = '#047857';
    } elseif ($isDepartmentHead) {
        $avatarColor = '#7c3aed';
        $roleLabel = 'Department Head';
        $roleIcon = '🏢';
        $badgeBg = '#ede9fe';
        $badgeText = '#6d28d9';
    } elseif ($isGuidance) {
        $avatarColor = '#db2777';
        $roleLabel = 'Guidance Admin';
        $roleIcon = '🧭';
        $badgeBg = '#fce7f3';
        $badgeText = '#be185d';
    } else {
        $avatarColor = '#6b7280';
        $roleLabel = 'User';
        $roleIcon = '👤';
        $badgeBg = '#f3f4f6';
        $badgeText = '#374151';
    }

    $avatarUrl = $user->avatar
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar)
        : null;

    $dateRange = $this->getDateRange();
    $selectedTermIds = $this->getSelectedTermIds();

    /*
    |--------------------------------------------------------------------------
    | Existing Dashboard Statistics (Admin / Scholarship / Guidance)
    |--------------------------------------------------------------------------
    */

    $totalScholars = \App\Models\Scholars::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->count();

    $activeScholars = \App\Models\Scholars::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->where('status', 'active')
        ->count();

    $inactiveScholars = \App\Models\Scholars::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->where('status', 'inactive')
        ->count();

    $scholarTypes = \App\Models\Scholars::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->selectRaw('type_of_scholarship, count(*) as count')
        ->groupBy('type_of_scholarship')
        ->orderByDesc('count')
        ->get();

    $chartColors = ['#378ADD','#1D9E75','#7F77DD','#EF9F27','#D85A30','#D4537E','#639922','#888780','#E24B4A','#5DCAA5'];

    $scholarChartData = json_encode([
        'labels' => $scholarTypes->pluck('type_of_scholarship')->values()->toArray(),
        'counts' => $scholarTypes->pluck('count')->values()->toArray(),
        'colors' => $scholarTypes->values()->map(fn ($t, $i) => $chartColors[$i % count($chartColors)])->toArray(),
    ]);

    $totalApplicants = \App\Models\Applicant::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->count();

    $pendingApplicants = \App\Models\Applicant::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->where('status', 'pending')
        ->count();

    $pendingAppts = \App\Models\CounselingAppointments::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->where('status', 'pending')
        ->count();

    $supportNeeds = \App\Models\CounselingAppointments::query()
        ->with('supportNeeded')
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->whereNotNull('support_needed_id')
        ->selectRaw('support_needed_id, count(*) as count')
        ->groupBy('support_needed_id')
        ->orderByDesc('count')
        ->get();

    $supportTotal = $supportNeeds->sum('count');
    $supportUnique = $supportNeeds->count();

    $supportColors = [
        'rgb(59,130,246)','rgb(16,185,129)','rgb(251,191,36)',
        'rgb(239,68,68)','rgb(139,92,246)','rgb(236,72,153)',
        'rgb(249,115,22)','rgb(14,165,233)',
    ];

    $supportChartData = json_encode([
        'labels' => $supportNeeds->map(fn ($n) =>
            ($n->supportNeeded->name ?? 'Unknown') . ' (' . round($n->count / max($supportTotal, 1) * 100, 1) . '%)'
        )->values()->toArray(),
        'counts' => $supportNeeds->pluck('count')->values()->toArray(),
        'colors' => $supportNeeds->values()->map(fn ($n, $i) => $supportColors[$i % count($supportColors)])->toArray(),
    ]);

    $latestApplicants = \App\Models\Applicant::query()
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->latest()
        ->limit(5)
        ->get();

    $latestAppointments = \App\Models\CounselingAppointments::query()
        ->with('timeSlot')
        ->when($dateRange, fn ($q) => $q->whereBetween('created_at', $dateRange))
        ->latest()
        ->limit(5)
        ->get();

    // NOTE: intentionally NOT filtered by school year/semester — these
    // sections are inherently "right now" (today / this calendar week),
    // and applying a past academic-year filter to them would almost
    // always return empty results, defeating their purpose.
    $todayAppts = \App\Models\CounselingAppointments::with('timeSlot')
        ->whereDate('counseling_date', \Carbon\Carbon::today())
        ->orderBy('time_slot_id')
        ->get();

    $weekAppts = \App\Models\CounselingAppointments::with('timeSlot')
        ->whereBetween('counseling_date', [
            \Carbon\Carbon::now()->startOfWeek(),
            \Carbon\Carbon::now()->endOfWeek(),
        ])
        ->orderBy('counseling_date')
        ->get()
        ->groupBy(fn ($a) => $a->counseling_date->format('Y-m-d'));

    /*
    |--------------------------------------------------------------------------
    | Department Head Dashboard
    |--------------------------------------------------------------------------
    */

    $departmentHeadScholars = collect();
    $departmentHeadDtr = collect();

    $departmentHeadTotalScholars = 0;
    $departmentHeadActiveScholars = 0;
    $departmentHeadRevokedScholars = 0;

    $programDistribution = collect();
    $yearLevelDistribution = collect();

    $dtrTotal = 0;
    $dtrCompliant = 0;
    $dtrNonCompliant = 0;
    $dtrCompliance = 0;

    $dtrStatusDistribution = collect();

    if ($isDepartmentHead) {

        /*
        |--------------------------------------------------------------------------
        | Scholars assigned to this Department Head
        |--------------------------------------------------------------------------
        */

        $departmentHeadScholars = \App\Models\Scholars::query()
            ->where('department_head_id', $user->id)
            ->when(
                ! empty($selectedTermIds),
                fn ($q) => $q->whereIn('term_id', $selectedTermIds)
            )
            ->orderBy('program')
            ->orderBy('year_level')
            ->get();

        $departmentHeadTotalScholars = $departmentHeadScholars->count();

        $departmentHeadActiveScholars = $departmentHeadScholars
            ->where('status', 'active')
            ->count();

        $departmentHeadRevokedScholars = $departmentHeadScholars
            ->where('status', 'revoked')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Distribution by Program
        |--------------------------------------------------------------------------
        */

        $programDistribution = $departmentHeadScholars
            ->groupBy(function ($scholar) {
                return trim($scholar->program ?? '') ?: 'Not Assigned';
            })
            ->map(function ($scholars, $program) {
                return [
                    'program' => $program,
                    'count' => $scholars->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Scholars by Year Level
        |--------------------------------------------------------------------------
        */

        $yearLevelLabels = [
            '1' => '1st Year',
            '2' => '2nd Year',
            '3' => '3rd Year',
            '4' => '4th Year',
            '5' => '5th Year',
        ];

        $yearLevelDistribution = $departmentHeadScholars
            ->groupBy(function ($scholar) use ($yearLevelLabels) {
                $level = (string) $scholar->year_level;

                return $yearLevelLabels[$level] ?? ($scholar->year_level ?: 'Not Assigned');
            })
            ->map(function ($scholars, $yearLevel) {
                return [
                    'year_level' => $yearLevel,
                    'count' => $scholars->count(),
                ];
            })
            ->sortBy(function ($item) {
                return match ($item['year_level']) {
                    '1st Year' => 1,
                    '2nd Year' => 2,
                    '3rd Year' => 3,
                    '4th Year' => 4,
                    '5th Year' => 5,
                    default => 99,
                };
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | DTR Records
        |--------------------------------------------------------------------------
        */

        $departmentHeadScholarIds = $departmentHeadScholars
            ->pluck('id')
            ->values()
            ->toArray();

        if (! empty($departmentHeadScholarIds)) {
            $departmentHeadDtr = \App\Models\DailyTimeRecord::query()
                ->whereIn('scholar_id', $departmentHeadScholarIds)
                ->whereNull('archived_at')
                ->when(
                    ! empty($selectedTermIds),
                    function ($q) use ($departmentHeadScholars) {
                        $scholarIds = $departmentHeadScholars
                            ->pluck('id')
                            ->values()
                            ->toArray();

                        $q->whereIn('scholar_id', $scholarIds);
                    }
                )
                ->get();
        }

        $dtrTotal = $departmentHeadDtr->count();

        /*
        |--------------------------------------------------------------------------
        | DTR Compliance
        |--------------------------------------------------------------------------
        |
        | Compliant:
        | Present
        | Late
        | Half Day
        | Excused
        |
        | Non-compliant:
        | Absent
        |
        */

        $dtrCompliant = $departmentHeadDtr
            ->whereIn('attendance_status', [
                'present',
                'late',
                'half_day',
                'excused',
            ])
            ->count();

        $dtrNonCompliant = $departmentHeadDtr
            ->where('attendance_status', 'absent')
            ->count();

        $dtrCompliance = $dtrTotal > 0
            ? round(($dtrCompliant / $dtrTotal) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | DTR Status Distribution
        |--------------------------------------------------------------------------
        */

        $dtrStatusLabels = [
            'present' => 'Present',
            'absent' => 'Absent',
            'excused' => 'Excused',
            'half_day' => 'Half Day',
            'late' => 'Late',
        ];

        $dtrStatusDistribution = $departmentHeadDtr
            ->groupBy(function ($record) use ($dtrStatusLabels) {
                return $dtrStatusLabels[$record->attendance_status]
                    ?? 'No Status';
            })
            ->map(function ($records, $status) use ($dtrTotal) {
                return [
                    'status' => $status,
                    'count' => $records->count(),
                    'percentage' => $dtrTotal > 0
                        ? round(($records->count() / $dtrTotal) * 100, 1)
                        : 0,
                ];
            })
            ->sortByDesc('count')
            ->values();
    }
@endphp

<style>
    .db-wrap { display:flex; flex-direction:column; gap:1.25rem; }

    .db-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .dark .db-card { background:#1f2937; border-color:#374151; }

    .db-grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:1.25rem; }
    .db-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; }
    .db-grid-2 { display:grid; grid-template-columns:repeat(2,1fr); gap:1.25rem; }

    @media(max-width:1100px) {
        .db-grid-4 { grid-template-columns:repeat(2,1fr); }
        .db-grid-3 { grid-template-columns:repeat(2,1fr); }
    }
    @media(max-width:900px) {
        .db-grid-4, .db-grid-3, .db-grid-2 { grid-template-columns:1fr; }
    }

    .db-welcome { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
    .db-welcome-left { display:flex; align-items:center; gap:1rem; }
    .db-avatar { width:52px;height:52px;border-radius:50%;object-fit:cover;box-shadow:0 4px 6px rgba(0,0,0,.15);flex-shrink:0; }
    .db-avatar-init { width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 6px rgba(0,0,0,.15);flex-shrink:0; }
    .db-welcome-name { font-size:1.125rem;font-weight:700;color:#111827;margin:0; }
    .dark .db-welcome-name { color:#f9fafb; }
    .db-welcome-date { font-size:.875rem;color:#6b7280;margin:2px 0 0; }
    .db-welcome-right { display:flex;align-items:center;gap:.75rem;flex-wrap:wrap; }
    .db-badge { display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:9999px;font-size:.8125rem;font-weight:700; }
    .db-btn { display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;transition:all .15s; }
    .db-btn-outline { border:2px solid #16a34a;color:#16a34a; }
    .db-btn-outline:hover { background:#f0fdf4; }
    .db-btn-solid { background:#16a34a;color:#fff;border:2px solid transparent; }
    .db-btn-solid:hover { background:#15803d; }

    .db-stat { display:flex;align-items:center;gap:.875rem; }
    .db-stat-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .db-stat-label { font-size:.75rem;color:#6b7280;margin:0 0 3px;line-height:1.3; }
    .db-stat-value { font-size:1.625rem;font-weight:600;margin:0;line-height:1; }
    .db-stat-sub { font-size:.75rem;margin:3px 0 0; }

    .db-section-label { font-size:.6875rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin:0 0 .875rem; }

    .db-table { width:100%;border-collapse:collapse;font-size:.75rem; }
    .db-table th { text-align:left;padding:5px 8px;font-weight:600;font-size:.6875rem;color:#6b7280;border-bottom:1px solid #e5e7eb;white-space:nowrap; }
    .db-table td { padding:5px 8px;color:#111827;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
    .dark .db-table th { color:#9ca3af;border-bottom-color:#374151; }
    .dark .db-table td { color:#f3f4f6;border-bottom-color:#374151; }
    .db-table tr:last-child td { border-bottom:none; }
    .db-table-wrap { overflow-x:auto; }
    .db-table-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;flex-wrap:wrap;gap:.25rem; }
    .db-table-title { font-size:.8125rem;font-weight:700;color:#111827;margin:0; }
    .dark .db-table-title { color:#f9fafb; }
    .db-table-desc { font-size:.6875rem;color:#9ca3af;margin:1px 0 0; }
    .db-view-link { font-size:.6875rem;color:#16a34a;text-decoration:none;display:inline-flex;align-items:center;gap:3px; }
    .db-view-link:hover { text-decoration:underline; }

    .db-card-sm { padding:.875rem 1rem !important; }

    .pill { display:inline-block;padding:1px 7px;border-radius:9999px;font-size:.65rem;font-weight:600; }
    .pill-pending  { background:#fef3c7;color:#92400e; }
    .pill-approved { background:#d1fae5;color:#065f46; }
    .pill-rejected { background:#fee2e2;color:#991b1b; }
    .pill-info     { background:#dbeafe;color:#1e40af; }
    .pill-time     { background:#ede9fe;color:#5b21b6; }

    .db-apt-item { display:flex;align-items:center;justify-content:space-between;padding:.375rem .625rem;border-radius:6px;border:1px solid;margin-bottom:.375rem;text-decoration:none;transition:box-shadow .15s; }
    .db-apt-item:hover { box-shadow:0 2px 8px rgba(0,0,0,.1); }
    .db-apt-pending  { border-color:#fed7aa;background:#fff7ed; }
    .db-apt-approved { border-color:#bbf7d0;background:#f0fdf4; }
    .db-apt-rejected { border-color:#fecaca;background:#fef2f2; }
    .db-apt-name { font-weight:600;font-size:.75rem;color:#111827;margin:0; }
    .dark .db-apt-name { color:#f9fafb; }
    .db-apt-time { font-size:.6875rem;color:#6b7280;margin:1px 0 0; }
    .db-week-card { padding:.5rem .625rem;border-radius:6px;background:#f9fafb;border:1px solid #e5e7eb; }
    .dark .db-week-card { background:#374151;border-color:#4b5563; }
    .db-week-day { font-weight:600;font-size:.75rem;color:#374151;margin:0 0 2px; }
    .dark .db-week-day { color:#d1d5db; }
    .db-week-count { font-size:.6875rem;color:#6b7280; }
    .db-dot { width:7px;height:7px;border-radius:50%;display:inline-block; }
    .db-empty { text-align:center;padding:.875rem;color:#9ca3af;font-size:.8125rem; }
    .db-chart-wrap { position:relative; }

    .db-filter-bar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .db-filter-label { font-size:.75rem; font-weight:600; color:#6b7280; white-space:nowrap; }
    .db-filter-select {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        font-size: .8125rem;
        padding: 6px 32px 6px 10px !important;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background-color: #fff !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236b7280'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 8px center !important;
        background-size: 16px 16px !important;
        color: #111827;
        min-width: 150px;
        line-height: 1.4;
        box-shadow: none !important;
        background-clip: padding-box;
    }
    .dark .db-filter-select {
        background-color: #111827 !important;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .db-filter-select:disabled { opacity: .5; cursor: not-allowed; }
    .db-filter-clear { font-size:.75rem; font-weight:600; color:#dc2626; background:none; border:none; cursor:pointer; padding:6px 8px; }
    .db-filter-clear:hover { text-decoration:underline; }
    .db-filter-active-note { font-size:.75rem; color:#059669; font-weight:600; }

    .dh-stat-card { min-height:112px; }
    .dh-progress { width:100%; height:9px; background:#e5e7eb; border-radius:9999px; overflow:hidden; margin-top:.75rem; }
    .dh-progress-fill { height:100%; border-radius:9999px; background:#16a34a; }
    .dark .dh-progress { background:#374151; }
    .dh-progress-row { margin-bottom:1rem; }
    .dh-progress-row:last-child { margin-bottom:0; }
    .dh-progress-header { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:.35rem; }
    .dh-progress-name { font-size:.8rem; font-weight:600; color:#374151; }
    .dark .dh-progress-name { color:#e5e7eb; }
    .dh-progress-value { font-size:.75rem; font-weight:700; color:#6b7280; }

    .dh-compliance-circle {
        width:135px; height:135px; border-radius:50%; display:flex; align-items:center; justify-content:center;
        margin:.5rem auto 1rem;
        background: conic-gradient(#16a34a {{ $dtrCompliance }}%, #e5e7eb {{ $dtrCompliance }}%);
    }
    .dark .dh-compliance-circle {
        background: conic-gradient(#16a34a {{ $dtrCompliance }}%, #374151 {{ $dtrCompliance }}%);
    }
    .dh-compliance-inner { width:105px; height:105px; border-radius:50%; background:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .dark .dh-compliance-inner { background:#1f2937; }
    .dh-compliance-value { font-size:1.6rem; font-weight:800; color:#16a34a; }
    .dh-compliance-label { font-size:.65rem; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; }

    .dh-dtr-summary { display:grid; grid-template-columns:repeat(2,1fr); gap:.75rem; margin-top:.75rem; }
    .dh-dtr-summary-item { padding:.75rem; border-radius:8px; background:#f9fafb; border:1px solid #e5e7eb; text-align:center; }
    .dark .dh-dtr-summary-item { background:#111827; border-color:#374151; }
    .dh-dtr-summary-number { font-size:1.1rem; font-weight:700; margin:0; }
    .dh-dtr-summary-label { font-size:.65rem; color:#6b7280; margin:2px 0 0; }

    .dh-empty { text-align:center; padding:2rem 1rem; color:#9ca3af; font-size:.8125rem; }
</style>

<div class="db-wrap">

    {{-- ================================================================
         WELCOME
         ================================================================ --}}

    <div class="db-card db-welcome">
        <div class="db-welcome-left">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="db-avatar">
            @else
                <div class="db-avatar-init" style="background:{{ $avatarColor }};">
                    <span style="color:#fff;font-size:1.25rem;font-weight:700;">{{ $initial }}</span>
                </div>
            @endif

            <div>
                <p class="db-welcome-name">{{ $user->name }}</p>
                <p class="db-welcome-date">{{ now()->format('l, F j, Y') }} — Here's what's happening today.</p>
            </div>
        </div>

        <div class="db-welcome-right">
            <span class="db-badge" style="background:{{ $badgeBg }};color:{{ $badgeText }};">
                {{ $roleIcon }} {{ $roleLabel }}
            </span>

            @if($isAdmin)
                <a href="{{ url('/admin/users') }}" class="db-btn db-btn-outline">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Manage Users
                </a>
            @endif

            @if(!$isDepartmentHead)
                <a href="{{ url('/admin/manage-settings') }}" class="db-btn db-btn-solid">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.5 1.5-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.04 1.56v.09h-2.12v-.09a1.7 1.7 0 0 0-1.04-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06-1.5-1.5.06-.06A1.7 1.7 0 0 0 9.12 15a1.7 1.7 0 0 0-1.56-1.04h-.09v-2.12h.09A1.7 1.7 0 0 0 9.12 10.8a1.7 1.7 0 0 0-.34-1.88l-.06-.06 1.5-1.5.06.06a1.7 1.7 0 0 0 1.88.34A1.7 1.7 0 0 0 13.2 6.2v-.09h2.12v.09a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.88-.34l.06-.06 1.5 1.5-.06.06A1.7 1.7 0 0 0 19.4 10.8a1.7 1.7 0 0 0 1.56 1.04h.09v2.12h-.09A1.7 1.7 0 0 0 19.4 15z"/>
                    </svg>
                    Settings
                </a>
            @endif
        </div>
    </div>

    {{-- ================================================================
         SCHOOL YEAR / SEMESTER FILTER
         ================================================================ --}}

    <div class="db-card db-filter-bar">
        <span class="db-filter-label">📊 Filter dashboard by:</span>

        <select wire:model.live="schoolYear" wire:key="filter-school-year" class="db-filter-select">
            <option value="">All School Years</option>
            @foreach($this->getSchoolYearOptions() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        <select wire:model.live="semester" wire:key="filter-semester" class="db-filter-select" @if(! $schoolYear) disabled @endif>
            <option value="">Full School Year</option>
            @foreach($this->getSemesterOptions() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        @if($schoolYear)
            <button type="button" wire:click="resetFilters" class="db-filter-clear">✕ Clear Filter</button>
            <span class="db-filter-active-note">
                Showing: {{ $schoolYear }}{{ $semester ? ' — ' . $semester : '' }}
            </span>
        @else
            <span style="font-size:.75rem;color:#9ca3af;">Showing all-time data</span>
        @endif
    </div>


    {{-- ================================================================
         DEPARTMENT HEAD DASHBOARD
         ================================================================ --}}

    @if($isDepartmentHead)

        {{-- Department Head Stat Cards --}}
        <div class="db-grid-4">

            <div class="db-card db-stat dh-stat-card">
                <div class="db-stat-icon" style="background:#ede9fe;">
                    <svg width="21" height="21" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <p class="db-stat-label">Total Scholars</p>
                    <p class="db-stat-value" style="color:#7c3aed;">{{ $departmentHeadTotalScholars }}</p>
                    <p class="db-stat-sub" style="color:#6b7280;">Assigned to you</p>
                </div>
            </div>

            <div class="db-card db-stat dh-stat-card">
                <div class="db-stat-icon" style="background:#d1fae5;">
                    <svg width="21" height="21" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                </div>
                <div>
                    <p class="db-stat-label">Active Scholars</p>
                    <p class="db-stat-value" style="color:#059669;">{{ $departmentHeadActiveScholars }}</p>
                    <p class="db-stat-sub" style="color:#6b7280;">Currently active</p>
                </div>
            </div>

            <div class="db-card db-stat dh-stat-card">
                <div class="db-stat-icon" style="background:#fee2e2;">
                    <svg width="21" height="21" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 6l12 12"/><path d="M18 6L6 18"/>
                    </svg>
                </div>
                <div>
                    <p class="db-stat-label">Revoked Scholars</p>
                    <p class="db-stat-value" style="color:#dc2626;">{{ $departmentHeadRevokedScholars }}</p>
                    <p class="db-stat-sub" style="color:#6b7280;">Scholarship revoked</p>
                </div>
            </div>

            <div class="db-card db-stat dh-stat-card">
                <div class="db-stat-icon" style="background:#dcfce7;">
                    <svg width="21" height="21" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 3v18"/><path d="M3 12h18"/><circle cx="12" cy="12" r="9"/>
                    </svg>
                </div>
                <div>
                    <p class="db-stat-label">DTR Compliance</p>
                    <p class="db-stat-value" style="color:#16a34a;">{{ number_format($dtrCompliance, 1) }}%</p>
                    <p class="db-stat-sub" style="color:#6b7280;">{{ $dtrTotal }} DTR record{{ $dtrTotal === 1 ? '' : 's' }}</p>
                </div>
            </div>

        </div>

        {{-- Distribution by Program + Year Level --}}
        <div class="db-grid-2">

            <div class="db-card">
                <div class="db-table-head">
                    <div>
                        <p class="db-table-title">Scholars Distribution by Program</p>
                        <p class="db-table-desc">Scholars assigned to your department</p>
                    </div>
                </div>

                @if($programDistribution->isEmpty())
                    <div class="dh-empty">No scholars found for the selected filter.</div>
                @else
                    @php $maxProgramCount = max((int) $programDistribution->max('count'), 1); @endphp
                    @foreach($programDistribution as $item)
                        @php $programPercentage = round(($item['count'] / $maxProgramCount) * 100, 1); @endphp
                        <div class="dh-progress-row">
                            <div class="dh-progress-header">
                                <span class="dh-progress-name">{{ $item['program'] }}</span>
                                <span class="dh-progress-value">{{ $item['count'] }}</span>
                            </div>
                            <div class="dh-progress">
                                <div class="dh-progress-fill" style="width:{{ $programPercentage }}%;background:#7c3aed;"></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="db-card">
                <div class="db-table-head">
                    <div>
                        <p class="db-table-title">Scholars by Year Level</p>
                        <p class="db-table-desc">Academic year-level distribution</p>
                    </div>
                </div>

                @if($yearLevelDistribution->isEmpty())
                    <div class="dh-empty">No scholars found for the selected filter.</div>
                @else
                    @php $maxYearLevelCount = max((int) $yearLevelDistribution->max('count'), 1); @endphp
                    @foreach($yearLevelDistribution as $item)
                        @php $yearPercentage = round(($item['count'] / $maxYearLevelCount) * 100, 1); @endphp
                        <div class="dh-progress-row">
                            <div class="dh-progress-header">
                                <span class="dh-progress-name">{{ $item['year_level'] }}</span>
                                <span class="dh-progress-value">{{ $item['count'] }}</span>
                            </div>
                            <div class="dh-progress">
                                <div class="dh-progress-fill" style="width:{{ $yearPercentage }}%;background:#2563eb;"></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>

        {{-- DTR Compliance + DTR Status Distribution --}}
        <div class="db-grid-2">

            <div class="db-card">
                <div class="db-table-head">
                    <div>
                        <p class="db-table-title">DTR Compliance</p>
                        <p class="db-table-desc">Attendance compliance of your assigned scholars</p>
                    </div>
                </div>

                <div class="dh-compliance-circle">
                    <div class="dh-compliance-inner">
                        <span class="dh-compliance-value">{{ number_format($dtrCompliance, 1) }}%</span>
                        <span class="dh-compliance-label">Compliance</span>
                    </div>
                </div>

                <div class="dh-dtr-summary">
                    <div class="dh-dtr-summary-item">
                        <p class="dh-dtr-summary-number" style="color:#16a34a;">{{ $dtrCompliant }}</p>
                        <p class="dh-dtr-summary-label">Compliant</p>
                    </div>
                    <div class="dh-dtr-summary-item">
                        <p class="dh-dtr-summary-number" style="color:#dc2626;">{{ $dtrNonCompliant }}</p>
                        <p class="dh-dtr-summary-label">Absent</p>
                    </div>
                </div>

                <div style="margin-top:1rem;font-size:.7rem;color:#9ca3af;text-align:center;">
                    Present, Late, Half Day, and Excused records are counted as compliant.
                </div>
            </div>

            <div class="db-card">
                <div class="db-table-head">
                    <div>
                        <p class="db-table-title">DTR Attendance Breakdown</p>
                        <p class="db-table-desc">Attendance status distribution</p>
                    </div>
                </div>

                @if($dtrStatusDistribution->isEmpty())
                    <div class="dh-empty">No DTR records found for the selected scholars.</div>
                @else
                    @foreach($dtrStatusDistribution as $item)
                        @php
                            $statusColor = match($item['status']) {
                                'Present' => '#16a34a',
                                'Absent' => '#dc2626',
                                'Late' => '#d97706',
                                'Half Day' => '#ca8a04',
                                'Excused' => '#2563eb',
                                default => '#6b7280',
                            };
                        @endphp
                        <div class="dh-progress-row">
                            <div class="dh-progress-header">
                                <span class="dh-progress-name">{{ $item['status'] }}</span>
                                <span class="dh-progress-value">{{ $item['count'] }} ({{ number_format($item['percentage'], 1) }}%)</span>
                            </div>
                            <div class="dh-progress">
                                <div class="dh-progress-fill" style="width:{{ $item['percentage'] }}%;background:{{ $statusColor }};"></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>

        {{-- Department Head Scholar List --}}
        <div class="db-card">
            <div class="db-table-head">
                <div>
                    <p class="db-table-title">Assigned Scholars</p>
                    <p class="db-table-desc">Scholars currently assigned to you</p>
                </div>
                <a href="{{ url('/admin/scholars') }}" class="db-view-link">View Scholars →</a>
            </div>

            @if($departmentHeadScholars->isEmpty())
                <div class="dh-empty">No scholars are currently assigned to you.</div>
            @else
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Program</th>
                                <th>Year</th>
                                <th>Scholarship</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departmentHeadScholars->take(10) as $scholar)
                                @php
                                    $yearLabel = match((string) $scholar->year_level) {
                                        '1' => '1st Year',
                                        '2' => '2nd Year',
                                        '3' => '3rd Year',
                                        '4' => '4th Year',
                                        '5' => '5th Year',
                                        default => $scholar->year_level ?? '—',
                                    };
                                    $statusClass = match($scholar->status) {
                                        'active' => 'pill-approved',
                                        'revoked' => 'pill-rejected',
                                        default => 'pill-pending',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $scholar->student_id ?? '—' }}</td>
                                    <td><strong>{{ trim(($scholar->first_name ?? '') . ' ' . ($scholar->middle_name ?? '') . ' ' . ($scholar->last_name ?? '')) }}</strong></td>
                                    <td>{{ $scholar->program ?? '—' }}</td>
                                    <td>{{ $yearLabel }}</td>
                                    <td>{{ $scholar->type_of_scholarship ?? '—' }}</td>
                                    <td><span class="pill {{ $statusClass }}">{{ ucfirst($scholar->status ?? 'Unknown') }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    @else

        {{-- ================================================================
             EXISTING ADMIN / SCHOLARSHIP / GUIDANCE DASHBOARD
             (restored original Chart.js charts + full tables — unchanged)
             ================================================================ --}}

        {{-- ROW — 3 Stat Cards --}}
        <div class="db-grid-3">

            @if($isAdmin || $isScholarship)
            <div class="db-card db-stat">
                <div class="db-stat-icon" style="background:#dbeafe;">
                    <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <p class="db-stat-label">Total Institutional Scholarship Applicants</p>
                    <p class="db-stat-value" style="color:#1d4ed8;">{{ $totalApplicants }}</p>
                    <p class="db-stat-sub" style="color:#6b7280;">All time applicants</p>
                </div>
            </div>

            <div class="db-card db-stat">
                <div class="db-stat-icon" style="background:#fef3c7;">
                    <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <p class="db-stat-label">Pending Institutional Applications</p>
                    <p class="db-stat-value" style="color:#d97706;">{{ $pendingApplicants }}</p>
                    <p class="db-stat-sub" style="color:#6b7280;">Awaiting review</p>
                </div>
            </div>
            @endif

            @if($isAdmin || $isGuidance)
            <div class="db-card db-stat">
                <div class="db-stat-icon" style="background:#fef3c7;">
                    <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                    <p class="db-stat-label">Pending Counseling Appointments</p>
                    <p class="db-stat-value" style="color:#d97706;">{{ $pendingAppts }}</p>
                    <p class="db-stat-sub" style="color:#f59e0b;">Awaiting approval</p>
                </div>
            </div>
            @endif

        </div>

        {{-- ROW — 2 Charts (Chart.js, restored original) --}}
        <div class="db-grid-2">

            @if($isAdmin || $isScholarship)
            {{--
                FIX: Dropped the custom canvas belowLabels plugin entirely.
                Now using Chart.js built-in x-axis ticks with maxRotation/minRotation=45
                and label truncation (max 14 chars). This lets Chart.js handle spacing
                automatically — no overlap possible. Full name shown in tooltip on hover.
                Count + % rendered above each bar via a clean afterDatasetsDraw plugin.
            --}}
            <div class="db-card"
                wire:key="scholar-chart-{{ $schoolYear }}-{{ $semester }}"
                x-data="{
                    init() {
                        const draw = () => {
                            const canvas = this.$refs.scholarBar;
                            if (!canvas || !window.Chart) return;
                            if (canvas._chart) canvas._chart.destroy();

                            const data = {{ $scholarChartData }};
                            const total = data.counts.reduce((a, b) => a + b, 0);

                            const truncate = (str, max) => str.length > max ? str.slice(0, max - 1) + '…' : str;
                            const shortLabels = data.labels.map(l => truncate(l, 13));

                            canvas._chart = new window.Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels: shortLabels,
                                    datasets: [{
                                        data: data.counts,
                                        backgroundColor: data.colors,
                                        borderRadius: 6,
                                        borderSkipped: false,
                                        barPercentage: 0.55,
                                        categoryPercentage: 0.65,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: { padding: { top: 24 } },
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                title: (items) => data.labels[items[0].dataIndex],
                                                label: (ctx) => {
                                                    const pct = total > 0 ? Math.round(ctx.parsed.y / total * 100) : 0;
                                                    return ' ' + ctx.parsed.y + ' grantees (' + pct + '%)';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            border: { display: false },
                                            ticks: {
                                                color: '#374151',
                                                font: { size: 10, weight: '600' },
                                                maxRotation: 45,
                                                minRotation: 45,
                                                autoSkip: false,
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: '#f3f4f6' },
                                            ticks: { font: { size: 11 }, color: '#9ca3af', precision: 0 }
                                        }
                                    }
                                },
                                plugins: [{
                                    id: 'aboveBar',
                                    afterDatasetsDraw(chart) {
                                        const ctx = chart.ctx;
                                        const meta = chart.getDatasetMeta(0);
                                        meta.data.forEach((bar, i) => {
                                            const count = data.counts[i];
                                            const pct   = total > 0 ? Math.round(count / total * 100) : 0;
                                            ctx.save();
                                            ctx.textAlign    = 'center';
                                            ctx.textBaseline = 'bottom';
                                            ctx.font         = 'bold 10px sans-serif';
                                            ctx.fillStyle    = '#111827';
                                            ctx.fillText(count, bar.x, bar.y - 2);
                                            ctx.font      = '9px sans-serif';
                                            ctx.fillStyle = '#9ca3af';
                                            ctx.fillText(pct + '%', bar.x, bar.y - 14);
                                            ctx.restore();
                                        });
                                    }
                                }]
                            });
                        };

                        if (window.Chart) { draw(); }
                        else {
                            const s = document.createElement('script');
                            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js';
                            s.onload = draw;
                            document.head.appendChild(s);
                        }
                    }
                }"
            >
                <p class="db-section-label">Scholars by Scholarship Type</p>
                <div class="db-chart-wrap" style="height:300px;">
                    <canvas x-ref="scholarBar" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
            @endif

            @if($isAdmin || $isGuidance)
            <div class="db-card"
                wire:key="support-chart-{{ $schoolYear }}-{{ $semester }}"
                x-data="{
                    init() {
                        const draw = () => {
                            const canvas = this.$refs.supportBar;
                            if (!canvas || !window.Chart) return;
                            if (canvas._chart) canvas._chart.destroy();
                            const data = {{ $supportChartData }};
                            if (!data.counts.length) return;
                            canvas._chart = new window.Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels: data.labels,
                                    datasets: [{ data: data.counts, backgroundColor: data.colors, borderColor: data.colors, borderWidth:1, borderRadius:6, borderSkipped:false }]
                                },
                                options: {
                                    responsive: true, maintainAspectRatio: false,
                                    plugins: { legend: { display: false }, tooltip: { enabled: true } },
                                    scales: {
                                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#9ca3af' } },
                                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 }, color: '#9ca3af' } }
                                    }
                                }
                            });
                        };
                        if (window.Chart) { draw(); }
                        else {
                            const s = document.createElement('script');
                            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js';
                            s.onload = draw; document.head.appendChild(s);
                        }
                    }
                }"
            >
                <p class="db-section-label">Support Needs Distribution</p>
                <p style="font-size:.75rem;color:#9ca3af;margin:-8px 0 10px;">
                    Total: {{ $supportTotal }} appointments | {{ $supportUnique }} support types
                </p>
                <div class="db-chart-wrap" style="height:240px;">
                    <canvas x-ref="supportBar" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
            @endif

        </div>

        {{-- ROW — 3 Tables (restored original: Type/Time columns, View links, Week Overview) --}}
        <div class="db-grid-3">

            {{-- Latest Applicants --}}
            @if($isAdmin || $isScholarship)
            <div class="db-card db-card-sm">
                <div class="db-table-head">
                    <div>
                        <p class="db-table-title">Latest Applicants</p>
                        <p class="db-table-desc">Most recently submitted</p>
                    </div>
                    <a href="{{ url('/admin/applicants') }}" class="db-view-link">View all →</a>
                </div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestApplicants as $applicant)
                            <tr>
                                <td style="color:#6b7280;white-space:nowrap;">{{ $applicant->created_at->format('M d, Y') }}</td>
                                <td style="font-weight:600;">{{ trim($applicant->first_name . ' ' . $applicant->last_name) }}</td>
                                <td><span class="pill pill-info">{{ optional($applicant->typeOfScholarship)->name ?? '—' }}</span></td>
                                <td><span class="pill pill-{{ $applicant->status }}">{{ ucfirst($applicant->status) }}</span></td>
                                <td><a href="{{ route('filament.admin.resources.applicants.edit', ['record' => $applicant->id]) }}" class="db-view-link">View</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="db-empty">No applicants{{ $schoolYear ? ' for this period.' : ' yet.' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Latest Appointments --}}
            @if($isAdmin || $isGuidance)
            <div class="db-card db-card-sm">
                <div class="db-table-head">
                    <div>
                        <p class="db-table-title">Latest Appointments</p>
                        <p class="db-table-desc">Most recent appointments</p>
                    </div>
                    <a href="{{ url('/admin/counseling-appointments') }}" class="db-view-link">View all →</a>
                </div>
                <div class="db-table-wrap">
                    <table class="db-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Student</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestAppointments as $apt)
                            <tr>
                                <td style="color:#6b7280;white-space:nowrap;">{{ $apt->counseling_date->format('M d, Y') }}</td>
                                <td><span class="pill pill-time">{{ optional($apt->timeSlot)->name ?? '—' }}</span></td>
                                <td style="font-weight:600;">{{ $apt->full_name }}</td>
                                <td><span class="pill pill-{{ $apt->status }}">{{ ucfirst($apt->status) }}</span></td>
                                <td><a href="{{ route('filament.admin.resources.counseling-appointments.edit', ['record' => $apt->id]) }}" class="db-view-link">View</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="db-empty">No appointments{{ $schoolYear ? ' for this period.' : ' yet.' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- This Week's Appointments --}}
            @if($isAdmin || $isGuidance)
            <div class="db-card db-card-sm">
                <div class="db-table-head">
                    <div>
                        <p class="db-table-title">This Week's Appointments</p>
                        <p class="db-table-desc">{{ now()->format('M j') }} – {{ now()->endOfWeek()->format('M j, Y') }}</p>
                    </div>
                    <a href="{{ url('/admin-calendar') }}" class="db-view-link">Calendar →</a>
                </div>

                <p style="font-size:.6875rem;font-weight:600;color:#374151;margin:0 0 .375rem;">
                    📅 Today ({{ now()->format('D, M j') }})
                </p>

                @if($todayAppts->count() > 0)
                    @foreach($todayAppts as $apt)
                        <a href="{{ route('filament.admin.resources.counseling-appointments.edit', ['record' => $apt->id]) }}"
                           class="db-apt-item db-apt-{{ $apt->status }}">
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <svg width="14" height="14" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div>
                                    <p class="db-apt-name">{{ $apt->first_name }} {{ $apt->last_name }}</p>
                                    <p class="db-apt-time">{{ optional($apt->timeSlot)->name ?? 'No time set' }}</p>
                                </div>
                            </div>
                            <span class="pill pill-{{ $apt->status }}">{{ ucfirst($apt->status) }}</span>
                        </a>
                    @endforeach
                @else
                    <p style="font-size:.8125rem;color:#9ca3af;padding:.5rem 0;">No appointments today.</p>
                @endif

                @if($weekAppts->count() > 0)
                <p style="font-size:.6875rem;font-weight:600;color:#374151;margin:.625rem 0 .375rem;">📆 Week Overview</p>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.5rem;">
                    @foreach($weekAppts as $date => $dayApts)
                    <div class="db-week-card">
                        <p class="db-week-day">{{ \Carbon\Carbon::parse($date)->format('D, M j') }}</p>
                        <p class="db-week-count">{{ $dayApts->count() }} {{ $dayApts->count() === 1 ? 'appointment' : 'appointments' }}</p>
                        <div style="display:flex;gap:4px;margin-top:5px;align-items:center;">
                            @foreach($dayApts->take(3) as $a)
                                <span class="db-dot" style="background:{{ $a->status === 'pending' ? '#f59e0b' : ($a->status === 'approved' ? '#10b981' : '#ef4444') }};"></span>
                            @endforeach
                            @if($dayApts->count() > 3)
                                <span style="font-size:.7rem;color:#9ca3af;">+{{ $dayApts->count() - 3 }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

        </div>

    @endif

</div>

</x-filament-panels::page>