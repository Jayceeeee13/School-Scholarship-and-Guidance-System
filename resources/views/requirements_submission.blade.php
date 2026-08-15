<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Requirements | Green Valley College Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-deep:   #0a3d20;
            --green-mid:    #145a32;
            --green-accent: #1e8449;
            --green-soft:   #d5f5e3;
            --green-muted:  #a9dfbf;
            --slate-light:  #f8fafb;
            --slate-border: #e2e8f0;
            --text-main:    #1a202c;
            --text-muted:   #64748b;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Lato', sans-serif;
            background-color: #f0f4f1;
            background-image:
                radial-gradient(circle at 10% 20%, rgba(20,90,50,0.06) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(20,90,50,0.04) 0%, transparent 50%);
            min-height: 100vh;
        }
        .navbar {
            background: var(--green-deep);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: sticky; top: 0; z-index: 50;
        }
        .hero {
            background: linear-gradient(160deg, var(--green-deep) 0%, var(--green-mid) 55%, var(--green-accent) 100%);
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background-image:
                radial-gradient(ellipse at 80% 50%, rgba(255,255,255,0.04) 0%, transparent 60%),
                radial-gradient(ellipse at 20% 80%, rgba(0,0,0,0.15) 0%, transparent 60%);
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(8px); color: #a7f3c0;
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; padding: 6px 14px; border-radius: 100px;
        }
        .card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 10px 40px -5px rgba(10,61,32,0.08);
            border: 1px solid rgba(20,90,50,0.08); overflow: hidden;
        }
        .card-header {
            background: linear-gradient(to right, #f7fdf9, #edfbf2);
            border-bottom: 1px solid var(--green-soft); padding: 20px 32px;
        }
        .section-header {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 20px; padding-bottom: 12px;
            border-bottom: 1px solid var(--slate-border);
        }
        .section-num {
            width: 28px; height: 28px; border-radius: 8px;
            background: var(--green-mid); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem; font-weight: 600; color: var(--green-deep);
        }
        .field-label {
            display: block; font-size: 0.72rem; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.06em; margin-bottom: 6px;
        }
        .field-input {
            width: 100%; border: 1.5px solid var(--slate-border);
            border-radius: 10px; padding: 10px 14px; font-size: 0.875rem;
            font-family: 'Lato', sans-serif; color: var(--text-main);
            background: #fff; transition: border-color 0.15s, box-shadow 0.15s;
            outline: none; appearance: none;
        }
        .field-input::placeholder { color: #b0bec5; }
        .field-input:focus {
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(30,132,73,0.12);
        }
        .field-input.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 3px rgba(229,62,62,0.12);
        }
        .field-error { font-size: 0.72rem; color: #e53e3e; margin-top: 4px; }
        .field-hint  { font-size: 0.7rem; color: var(--text-muted); margin-top: 4px; }

        /* Requirement rows */
        .req-row {
            background: var(--slate-light);
            border: 1.5px solid var(--slate-border);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .req-row:hover { border-color: #c8d8cf; }
        .req-row.is-done {
            background: #f0fdf4;
            border-color: var(--green-muted);
        }
        .req-row-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }
        .req-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.4;
        }
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.65rem; font-weight: 700;
            letter-spacing: 0.07em; text-transform: uppercase;
            padding: 3px 9px; border-radius: 100px; white-space: nowrap; flex-shrink: 0;
        }
        .badge-done    { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; }
        .badge-pending { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; }
        .badge-optional{ background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; }

        .section-divider { border: none; border-top: 1px solid var(--slate-border); margin: 0; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px; border-radius: 10px;
            border: 1.5px solid var(--slate-border); color: var(--text-muted);
            font-size: 0.875rem; font-weight: 600; background: #fff;
            text-decoration: none; transition: background 0.15s, color 0.15s, border-color 0.15s;
        }
        .btn-back:hover { background: var(--slate-light); border-color: #cbd5e1; color: var(--text-main); }
        .btn-submit {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 28px; border-radius: 10px; background: var(--green-mid);
            color: #fff; font-size: 0.875rem; font-weight: 700; border: none;
            cursor: pointer; transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow: 0 2px 8px rgba(20,90,50,0.25);
        }
        .btn-submit:hover { background: var(--green-deep); box-shadow: 0 4px 14px rgba(20,90,50,0.3); }
        .btn-submit:active { transform: scale(0.98); }

        footer { background: var(--green-deep); border-top: 1px solid rgba(255,255,255,0.06); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .card { animation: fadeUp 0.4s ease both; }

        .alert-success {
            background: #d5f5e3; border: 1px solid #1e8449; color: #145a32;
            padding: 14px 20px; border-radius: 12px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            font-size: 0.875rem; font-weight: 600;
        }
        .alert-error {
            background: #fee2e2; border: 1px solid #e53e3e; color: #c53030;
            padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.875rem;
        }
        .alert-error ul { margin: 6px 0 0; padding-left: 18px; }

        /* Info notice banner */
        .notice-banner {
            display: flex; align-items: flex-start; gap: 12px;
            background: #eff6ff; border: 1px solid #bfdbfe;
            border-radius: 12px; padding: 14px 18px;
            font-size: 0.82rem; color: #1e40af; line-height: 1.6;
            margin-bottom: 4px;
        }

        /* Progress bar */
        .progress-wrap { background: #e5e7eb; border-radius: 100px; height: 8px; overflow: hidden; }
        .progress-bar  { background: linear-gradient(90deg, var(--green-mid), var(--green-accent)); height: 100%; border-radius: 100px; transition: width 0.4s ease; }

        .info-label { font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; }
        .info-value { font-size: 0.875rem; font-weight: 600; color: var(--text-main); margin-top: 3px; }

        /* File input styling */
        input[type="file"].field-input {
            padding: 7px 12px;
            font-size: 0.82rem;
            cursor: pointer;
        }
        input[type="file"].field-input::-webkit-file-upload-button {
            background: var(--green-soft);
            border: 1px solid var(--green-muted);
            color: var(--green-mid);
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            margin-right: 10px;
            font-family: 'Lato', sans-serif;
        }
    </style>
</head>
<body>

{{-- ── NAVBAR ── --}}
<header class="navbar shadow-sm">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between;">
        <a href="{{ url('/') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
            <img src="{{ asset('images/logo.png') }}" alt="GVC" style="width:34px;height:34px;border-radius:8px;object-fit:contain;">
            <span style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;letter-spacing:-0.01em;">
                Green Valley College Foundation Inc.
            </span>
        </a>
    </div>
</header>

{{-- ── HERO ── --}}
<section class="hero" style="padding:56px 24px;">
    <div style="position:relative;max-width:640px;margin:0 auto;text-align:center;">
        <div class="hero-badge" style="margin-bottom:16px;display:inline-flex;">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><circle cx="5" cy="5" r="5" fill="#4ade80"/></svg>
            Scholarship Requirements
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;color:#fff;line-height:1.2;margin:0 0 12px;">
            Submit Requirements
        </h1>
        <p style="color:rgba(255,255,255,0.75);font-size:0.95rem;line-height:1.7;margin:0;">
            Upload your documents at your own pace. You can submit partially and come back to complete the rest.
        </p>
    </div>
</section>

{{-- ── MAIN ── --}}
<section style="padding:40px 16px 64px;">
<div style="max-width:740px;margin:0 auto;">

    @if(session('success'))
        <div class="alert-success">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <circle cx="9" cy="9" r="9" fill="#1e8449"/>
                <path d="M5 9l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── No application yet ── --}}
    @if(!$applicant)
        <div class="card">
            <div style="text-align:center;padding:56px 40px;">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--green-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#145a32" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:var(--green-deep);margin:0 0 10px;">No Application Found</h2>
                <p style="font-size:0.875rem;color:var(--text-muted);max-width:380px;margin:0 auto 24px;line-height:1.7;">
                    You need to submit a scholarship application first before uploading requirements.
                </p>
                <a href="{{ route('application_new.get') }}"
                   style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;border-radius:10px;background:var(--green-mid);color:#fff;font-size:0.875rem;font-weight:700;text-decoration:none;">
                    Apply Now
                    <svg width="14" height="14" fill="none" viewBox="0 0 14 14"><path d="M5 12l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>

    @else

    {{-- ── Compute stats ── --}}
    @php
        $totalReqs      = $requirements->count();
        $submittedCount = count($submitted);
        $pendingCount   = $totalReqs - $submittedCount;
        $pct            = $totalReqs > 0 ? round(($submittedCount / $totalReqs) * 100) : 0;
        $initials       = strtoupper(substr($applicant->first_name, 0, 1) . substr($applicant->last_name, 0, 1));
        $appType        = $applicant->typeOfApplication->name ?? 'N/A';
        $schType        = $applicant->typeOfScholarship->name  ?? 'N/A';
        $program        = $applicant->program->name            ?? 'N/A';
        $statusColors   = [
            'pending'  => ['bg'=>'#fef3c7','border'=>'#fcd34d','color'=>'#92400e'],
            'approved' => ['bg'=>'#d1fae5','border'=>'#6ee7b7','color'=>'#065f46'],
            'rejected' => ['bg'=>'#fee2e2','border'=>'#fca5a5','color'=>'#991b1b'],
        ];
        $sc = $statusColors[$applicant->status] ?? $statusColors['pending'];
    @endphp

    {{-- ── STUDENT INFO CARD ── --}}
    <div class="card" style="margin-bottom:20px;animation-delay:0.05s;">
        <div class="card-header" style="display:flex;align-items:center;gap:12px;">
            <div style="width:32px;height:32px;border-radius:8px;background:var(--green-mid);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h3 style="font-family:'Playfair Display',serif;font-size:0.95rem;font-weight:700;color:var(--green-deep);margin:0;">Applicant Overview</h3>
                <p style="font-size:0.7rem;color:var(--text-muted);margin:0;">Requirements shown are based on your application type</p>
            </div>
        </div>

        {{-- Name + badges --}}
        <div style="padding:20px 32px;border-bottom:1px solid var(--slate-border);display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#145a32,#1e8449);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(20,90,50,0.25);">
                <span style="color:#fff;font-size:1.1rem;font-weight:700;">{{ $initials }}</span>
            </div>
            <div>
                <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--text-main);margin:0 0 6px;">
                    {{ $applicant->first_name }} {{ $applicant->last_name }}
                </h2>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span style="display:inline-flex;align-items:center;gap:4px;background:var(--green-soft);border:1px solid var(--green-muted);color:var(--green-mid);font-size:0.68rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;padding:3px 10px;border-radius:100px;">
                        🎓 {{ $appType }}
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:4px;background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;font-size:0.68rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;padding:3px 10px;border-radius:100px;">
                        ⭐ {{ $schType }}
                    </span>
                    <span style="display:inline-flex;align-items:center;background:{{ $sc['bg'] }};border:1px solid {{ $sc['border'] }};color:{{ $sc['color'] }};font-size:0.68rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;padding:3px 10px;border-radius:100px;">
                        {{ ucfirst($applicant->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Info grid --}}
        <div style="padding:18px 32px;display:grid;grid-template-columns:repeat(3,1fr);gap:16px;border-bottom:1px solid var(--slate-border);">
            <div>
                <p class="info-label">Program & Year</p>
                <p class="info-value">{{ $program }} {{ $applicant->year_level }}</p>
            </div>
            <div>
                <p class="info-label">Gender</p>
                <p class="info-value">{{ $applicant->gender->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="info-label">Contact</p>
                <p class="info-value">{{ $applicant->contact_no ?: '—' }}</p>
            </div>
        </div>

        {{-- Progress --}}
        <div style="padding:18px 32px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:8px;">
                <span style="font-size:0.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;">
                    Submission Progress
                </span>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-size:0.75rem;font-weight:700;color:var(--green-mid);">
                        {{ $submittedCount }} / {{ $totalReqs }} submitted
                    </span>
                    @if($pendingCount > 0)
                        <span style="font-size:0.72rem;color:#92400e;background:#fef3c7;border:1px solid #fcd34d;padding:2px 8px;border-radius:100px;font-weight:700;">
                            {{ $pendingCount }} pending
                        </span>
                    @else
                        <span style="font-size:0.72rem;color:#065f46;background:#d1fae5;border:1px solid #6ee7b7;padding:2px 8px;border-radius:100px;font-weight:700;">
                            All complete ✓
                        </span>
                    @endif
                </div>
            </div>
            <div class="progress-wrap">
                <div class="progress-bar" style="width:{{ $pct }}%;"></div>
            </div>
            <p style="font-size:0.7rem;color:var(--text-muted);margin-top:6px;">
                {{ $pct }}% of your required documents have been submitted.
            </p>
        </div>
    </div>

    {{-- ── FORM CARD ── --}}
    @if($requirements->isEmpty())
        <div class="card">
            <div style="text-align:center;padding:48px 40px;">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--green-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#145a32" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--green-deep);margin:0 0 8px;">No Requirements Configured</h2>
                <p style="font-size:0.875rem;color:var(--text-muted);max-width:360px;margin:0 auto;line-height:1.7;">
                    No active requirements have been set for your application type yet. Please check back later or contact the scholarship office.
                </p>
            </div>
        </div>

    @else

        <div class="card" style="animation-delay:0.1s;">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--green-deep);margin:0 0 4px;">
                        Requirements for <em>{{ $appType }}</em>
                    </h2>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin:0;">
                        Upload as many or as few as you have ready — you can return to complete the rest.
                    </p>
                </div>
                <span style="display:inline-flex;align-items:center;gap:6px;background:var(--green-soft);border:1px solid var(--green-muted);color:var(--green-mid);font-size:0.68rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;padding:5px 12px;border-radius:100px;">
                    <svg width="7" height="7" viewBox="0 0 7 7"><circle cx="3.5" cy="3.5" r="3.5" fill="#1e8449"/></svg>
                    PDF Only · Max 2 MB
                </span>
            </div>

            <form method="POST" action="{{ route('requirements_submission.post') }}"
                  enctype="multipart/form-data"
                  style="padding:32px 36px;display:flex;flex-direction:column;gap:28px;">
                @csrf

                {{-- ── Info notice ── --}}
                <div class="notice-banner">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>
                        <strong>Partial submission is allowed.</strong>
                        You don't need to upload everything at once. Leave any file field empty to skip it for now —
                        only the files you choose to upload will be saved. You can come back anytime to submit the remaining documents.
                    </span>
                </div>

                {{-- ── File Identifier ── --}}
                <div>
                    <div class="section-header">
                        <span class="section-num">1</span>
                        <span class="section-title">File Identifier</span>
                    </div>
                    <div>
                        <label class="field-label">File Name Prefix <span style="color:#e53e3e;">*</span></label>
                        <input type="text" name="file_name"
                               value="{{ old('file_name', 'GVC_' . strtoupper(substr($applicant->last_name, 0, 6)) . '_' . date('Y')) }}"
                               placeholder="e.g. GVC_DELACRUZ_2025"
                               class="field-input {{ $errors->has('file_name') ? 'is-invalid' : '' }}">
                        <p class="field-hint">This prefix will be added to all your uploaded file names.</p>
                        @error('file_name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <hr class="section-divider">

                {{-- ── Dynamic requirements list ── --}}
                <div>
                    <div class="section-header">
                        <span class="section-num">2</span>
                        <span class="section-title">Required Documents</span>
                    </div>

                    {{-- Quick legend --}}
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
                        <span style="font-size:0.72rem;color:var(--text-muted);">Legend:</span>
                        <span class="badge badge-done">
                            <svg width="9" height="9" fill="none" viewBox="0 0 9 9"><circle cx="4.5" cy="4.5" r="4.5" fill="#059669"/><path d="M2.2 4.5l1.6 1.6 2.9-2.9" stroke="#fff" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Submitted
                        </span>
                        <span class="badge badge-pending">Pending</span>
                        <span style="font-size:0.72rem;color:var(--text-muted);margin-left:4px;">— All fields are optional per save. Upload only what you have ready.</span>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($requirements as $index => $req)
                            @php
                                $fieldKey = 'req_' . $req->id;
                                $isDone   = in_array($req->id, $submitted);
                                $hasError = $errors->has($fieldKey);
                            @endphp

                            <div class="req-row {{ $isDone ? 'is-done' : '' }}">
                                {{-- Top row: name + badge --}}
                                <div class="req-row-top">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        {{-- Number circle --}}
                                        <span style="width:22px;height:22px;border-radius:50%;background:{{ $isDone ? '#059669' : '#e2e8f0' }};color:{{ $isDone ? '#fff' : 'var(--text-muted)' }};display:inline-flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;flex-shrink:0;">
                                            @if($isDone)
                                                <svg width="10" height="10" fill="none" viewBox="0 0 10 10"><path d="M2 5l2.5 2.5 3.5-3.5" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </span>
                                        <p class="req-name">{{ $req->name }}</p>
                                    </div>
                                    @if($isDone)
                                        <span class="badge badge-done">
                                            <svg width="9" height="9" fill="none" viewBox="0 0 9 9"><circle cx="4.5" cy="4.5" r="4.5" fill="#059669"/><path d="M2.2 4.5l1.6 1.6 2.9-2.9" stroke="#fff" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            Submitted
                                        </span>
                                    @else
                                        <span class="badge badge-pending">Pending</span>
                                    @endif
                                </div>

                                {{-- File input --}}
                                <div>
                                    <input type="file"
                                           name="{{ $fieldKey }}"
                                           accept="application/pdf"
                                           class="field-input {{ $hasError ? 'is-invalid' : '' }}">
                                    @if($hasError)
                                        <p class="field-error">{{ $errors->first($fieldKey) }}</p>
                                    @elseif($isDone)
                                        <p class="field-hint">✓ Already on file. Upload a new PDF here to replace it, or leave empty to keep the existing one.</p>
                                    @else
                                        <p class="field-hint">Optional for this save. PDF only, max 2 MB.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Actions ── --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--slate-border);flex-wrap:wrap;gap:12px;">
                    <a href="{{ url('/scholarship') }}" class="btn-back">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Back
                    </a>
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        @if($pendingCount > 0)
                            <span style="font-size:0.78rem;color:var(--text-muted);">
                                {{ $pendingCount }} document{{ $pendingCount > 1 ? 's' : '' }} still pending
                            </span>
                        @endif
                        <button type="submit" class="btn-submit">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Save Submitted Files
                        </button>
                    </div>
                </div>

            </form>
        </div>

    @endif {{-- requirements empty check --}}
    @endif {{-- applicant check --}}

    <p style="text-align:center;font-size:0.75rem;color:#94a3b8;margin-top:20px;">
        Your documents are kept confidential and secure.
    </p>
</div>
</section>

{{-- ── FOOTER ── --}}
<footer style="padding:24px;text-align:center;">
    <p style="font-size:0.8rem;color:rgba(255,255,255,0.4);margin:0;">
        &copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.
    </p>
</footer>

</body>
</html>