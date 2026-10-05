<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Requirements | Green Valley College Foundation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-deep: #0a3d20;
            --green-mid: #145a32;
            --green-accent: #1e8449;
            --green-soft: #d5f5e3;
            --green-muted: #a9dfbf;
            --surface: #f8fafb;
            --border: #e2e8f0;
            --text: #1a202c;
            --muted: #64748b;
            --danger: #e53e3e;
            --radius-sm: 10px;
            --radius-md: 12px;
            --radius-lg: 20px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh;
            font-family: 'Lato', sans-serif; color: var(--text);
            background: #f0f4f1;
        }
        h1, h2, h3, .serif { font-family: 'Playfair Display', serif; margin: 0; }
        p { margin: 0; }
        a { text-decoration: none; }

        /* ── Icons ── */
        .icon { width: 1em; height: 1em; flex-shrink: 0; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .icon-sm { width: 14px; height: 14px; }
        .icon-md { width: 18px; height: 18px; }
        .icon-lg { width: 28px; height: 28px; }

        /* ── Layout ── */
        .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
        .main { max-width: 740px; margin: 0 auto; padding: 40px 16px 64px; display: flex; flex-direction: column; gap: 20px; }
        .stack { display: flex; flex-direction: column; }
        .row { display: flex; align-items: center; flex-wrap: wrap; }
        .gap-2 { gap: 8px; } .gap-3 { gap: 12px; } .gap-4 { gap: 16px; }
        .between { justify-content: space-between; }

        /* ── Header / hero / footer ── */
        .navbar { position: sticky; top: 0; z-index: 50; background: var(--green-deep); border-bottom: 1px solid rgba(255,255,255,.08); }
        .navbar .container { height: 56px; display: flex; align-items: center; }
        .brand { display: flex; align-items: center; gap: 10px; color: #fff; font-size: 1rem; font-weight: 700; }
        .brand img { width: 34px; height: 34px; border-radius: 8px; object-fit: contain; }

        .hero { padding: 56px 24px; text-align: center; background: linear-gradient(160deg, var(--green-deep), var(--green-mid) 55%, var(--green-accent)); }
        .hero-inner { max-width: 640px; margin: 0 auto; }
        .hero h1 { font-size: clamp(1.8rem, 4vw, 2.8rem); line-height: 1.2; color: #fff; margin: 16px 0 12px; }
        .hero p { color: rgba(255,255,255,.75); font-size: .95rem; line-height: 1.7; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 100px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); color: #a7f3c0; font-size: .7rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }

        .footer { padding: 24px; text-align: center; background: var(--green-deep); color: rgba(255,255,255,.4); font-size: .8rem; }

        /* ── Card ── */
        .card { background: #fff; border: 1px solid rgba(20,90,50,.08); border-radius: var(--radius-lg); box-shadow: 0 10px 40px -5px rgba(10,61,32,.08); overflow: hidden; }
        .card-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding: 20px 32px; background: #f4fcf7; border-bottom: 1px solid var(--green-soft); }
        .card-header h2, .card-header h3 { color: var(--green-deep); font-size: 1.05rem; }
        .card-section { padding: 18px 32px; border-bottom: 1px solid var(--border); }
        .card-section:last-child { border-bottom: 0; }
        .card-empty { padding: 48px 40px; text-align: center; }
        .card-empty h2 { color: var(--green-deep); font-size: 1.25rem; margin: 16px 0 8px; }
        .card-empty p { max-width: 380px; margin: 0 auto 24px; color: var(--muted); font-size: .875rem; line-height: 1.7; }
        .card-empty p:last-child { margin-bottom: 0; }
        .icon-circle { width: 56px; height: 56px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--green-soft); color: var(--green-mid); }
        .icon-square { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: var(--green-mid); color: #fff; }
        .muted { color: var(--muted); }
        .caption { font-size: .75rem; color: var(--muted); }

        /* ── Applicant ── */
        .avatar { width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, var(--green-mid), var(--green-accent)); color: #fff; font-weight: 700; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(20,90,50,.25); }
        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .info-label { font-size: .68rem; font-weight: 700; color: var(--muted); letter-spacing: .08em; text-transform: uppercase; }
        .info-value { margin-top: 3px; font-size: .875rem; font-weight: 700; }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border: 1px solid; border-radius: 100px; font-size: .68rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; white-space: nowrap; }
        .badge-green    { background: var(--green-soft); border-color: var(--green-muted); color: var(--green-mid); }
        .badge-blue     { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
        .badge-pending  { background: #fef3c7; border-color: #fcd34d; color: #92400e; }
        .badge-approved, .badge-done { background: #d1fae5; border-color: #6ee7b7; color: #065f46; }
        .badge-rejected { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }

        /* ── Progress ── */
        .progress { height: 8px; margin: 8px 0 6px; border-radius: 100px; background: #e5e7eb; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--green-mid), var(--green-accent)); transition: width .4s ease; }

        /* ── Alerts ── */
        .alert { display: flex; gap: 10px; padding: 14px 20px; border: 1px solid; border-radius: var(--radius-md); font-size: .875rem; }
        .alert-success { align-items: center; background: var(--green-soft); border-color: var(--green-accent); color: var(--green-mid); font-weight: 700; }
        .alert-error { flex-direction: column; background: #fee2e2; border-color: var(--danger); color: #c53030; }
        .alert-error ul { margin: 0; padding-left: 18px; }
        .alert-info { align-items: flex-start; background: #eff6ff; border-color: #bfdbfe; color: #1e40af; font-size: .82rem; line-height: 1.6; }
        .alert-info .icon { margin-top: 2px; }

        /* ── Form ── */
        .form { display: flex; flex-direction: column; gap: 28px; padding: 32px 36px; }
        .form-section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
        .form-section-header span { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; background: var(--green-mid); color: #fff; font-size: .75rem; font-weight: 700; }
        .form-section-header h3 { color: var(--green-deep); font-size: 1rem; }
        .form-actions { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding-top: 20px; border-top: 1px solid var(--border); }

        .field-label { display: block; margin-bottom: 6px; font-size: .72rem; font-weight: 700; color: var(--muted); letter-spacing: .06em; text-transform: uppercase; }
        .field-input { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); background: #fff; color: var(--text); font: inherit; font-size: .875rem; outline: none; transition: border-color .15s, box-shadow .15s; }
        .field-input::placeholder { color: #b0bec5; }
        .field-input:focus { border-color: var(--green-accent); box-shadow: 0 0 0 3px rgba(30,132,73,.12); }
        .field-input.is-invalid { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(229,62,62,.12); }
        .field-input[type="file"] { padding: 7px 12px; font-size: .82rem; cursor: pointer; }
        .field-input[type="file"]::file-selector-button { margin-right: 10px; padding: 4px 10px; border: 1px solid var(--green-muted); border-radius: 6px; background: var(--green-soft); color: var(--green-mid); font: inherit; font-size: .75rem; font-weight: 700; cursor: pointer; }
        .field-hint, .field-error { margin-top: 4px; font-size: .72rem; }
        .field-hint { display: flex; align-items: center; gap: 4px; color: var(--muted); }
        .field-error { color: var(--danger); }

        /* ── Requirement rows ── */
        .req { display: flex; flex-direction: column; gap: 10px; padding: 16px 18px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--radius-md); }
        .req.is-done { background: #f0fdf4; border-color: var(--green-muted); }
        .req-title { display: flex; align-items: center; gap: 8px; font-size: .85rem; font-weight: 700; line-height: 1.4; }
        .req-num { width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--border); color: var(--muted); font-size: .65rem; font-weight: 700; flex-shrink: 0; }
        .req.is-done .req-num { background: #059669; color: #fff; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border: 1.5px solid transparent; border-radius: var(--radius-sm); font: inherit; font-size: .875rem; font-weight: 700; cursor: pointer; transition: background .15s, box-shadow .15s, border-color .15s; }
        .btn-primary { background: var(--green-mid); color: #fff; box-shadow: 0 2px 8px rgba(20,90,50,.25); }
        .btn-primary:hover { background: var(--green-deep); }
        .btn-secondary { background: #fff; border-color: var(--border); color: var(--muted); }
        .btn-secondary:hover { background: var(--surface); color: var(--text); }
        .btn:focus-visible { outline: 3px solid rgba(30,132,73,.4); outline-offset: 2px; }

        @media (max-width: 600px) {
            .form, .card-header, .card-section { padding-left: 20px; padding-right: 20px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- Icon sprite: reference with <svg class="icon"><use href="#i-name"/></svg> --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></symbol>
    <symbol id="i-check-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.7 2.7L16 9.8"/></symbol>
    <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0114 0z"/></symbol>
    <symbol id="i-file" viewBox="0 0 24 24"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></symbol>
    <symbol id="i-cap" viewBox="0 0 24 24"><path d="M2 9l10-5 10 5-10 5L2 9z"/><path d="M6 11.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5M22 9v6"/></symbol>
    <symbol id="i-award" viewBox="0 0 24 24"><circle cx="12" cy="9" r="6"/><path d="M8.5 14L7 22l5-3 5 3-1.5-8"/></symbol>
    <symbol id="i-upload" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M16 8l-4-4-4 4M12 4v12"/></symbol>
    <symbol id="i-arrow-left" viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6"/></symbol>
    <symbol id="i-arrow-right" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></symbol>
</svg>

<header class="navbar">
    <div class="container">
        <a href="{{ url('/') }}" class="brand serif">
            <img src="{{ asset('images/logo.png') }}" alt="GVC">
            Green Valley College Foundation Inc.
        </a>
    </div>
</header>

<section class="hero">
    <div class="hero-inner">
        <span class="hero-badge">
            <svg class="icon" width="10" height="10" style="fill:#4ade80;stroke:none"><use href="#i-check-circle"/></svg>
            Scholarship Requirements
        </span>
        <h1>Submit Requirements</h1>
        <p>Upload your documents at your own pace. You can submit partially and come back to complete the rest.</p>
    </div>
</section>

<main class="main">

    @if(session('success'))
        <div class="alert alert-success">
            <svg class="icon icon-md"><use href="#i-check-circle"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!$applicant)
        {{-- No application yet --}}
        <div class="card card-empty">
            <div class="icon-circle"><svg class="icon icon-lg"><use href="#i-file"/></svg></div>
            <h2>No Application Found</h2>
            <p>You need to submit a scholarship application first before uploading requirements.</p>
            <a href="{{ route('application_new.get') }}" class="btn btn-primary">
                Apply now
                <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg>
            </a>
        </div>
    @else
        @php
            $totalReqs      = $requirements->count();
            $submittedCount = count($submitted);
            $pendingCount   = $totalReqs - $submittedCount;
            $pct            = $totalReqs > 0 ? round(($submittedCount / $totalReqs) * 100) : 0;
            $initials       = strtoupper(substr($applicant->first_name, 0, 1) . substr($applicant->last_name, 0, 1));
            $appType        = $applicant->typeOfApplication->name ?? 'N/A';
            $schType        = $applicant->typeOfScholarship->name ?? 'N/A';
            $program        = $applicant->program->name ?? 'N/A';
            $status         = in_array($applicant->status, ['pending', 'approved', 'rejected']) ? $applicant->status : 'pending';
        @endphp

        {{-- Applicant overview --}}
        <div class="card">
            <div class="card-header" style="justify-content:flex-start">
                <div class="icon-square"><svg class="icon icon-md"><use href="#i-user"/></svg></div>
                <div>
                    <h3>Applicant overview</h3>
                    <p class="caption">Requirements shown are based on your application type</p>
                </div>
            </div>

            <div class="card-section row gap-4">
                <div class="avatar">{{ $initials }}</div>
                <div class="stack gap-2">
                    <h2 style="font-size:1.1rem">{{ $applicant->first_name }} {{ $applicant->last_name }}</h2>
                    <div class="row gap-2">
                        <span class="badge badge-green"><svg class="icon icon-sm"><use href="#i-cap"/></svg>{{ $appType }}</span>
                        <span class="badge badge-blue"><svg class="icon icon-sm"><use href="#i-award"/></svg>{{ $schType }}</span>
                        <span class="badge badge-{{ $status }}">{{ ucfirst($status) }}</span>
                    </div>
                </div>
            </div>

            <div class="card-section info-grid">
                <div>
                    <p class="info-label">Program &amp; year</p>
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

            <div class="card-section">
                <div class="row between gap-2">
                    <span class="info-label">Submission progress</span>
                    <div class="row gap-3">
                        <span class="info-label" style="color:var(--green-mid)">{{ $submittedCount }} / {{ $totalReqs }} submitted</span>
                        @if($pendingCount > 0)
                            <span class="badge badge-pending">{{ $pendingCount }} pending</span>
                        @else
                            <span class="badge badge-done"><svg class="icon icon-sm"><use href="#i-check"/></svg>All complete</span>
                        @endif
                    </div>
                </div>
                <div class="progress" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width:{{ $pct }}%"></div>
                </div>
                <p class="caption">{{ $pct }}% of your required documents have been submitted.</p>
            </div>
        </div>

        @if($requirements->isEmpty())
            <div class="card card-empty">
                <div class="icon-circle"><svg class="icon icon-lg"><use href="#i-check"/></svg></div>
                <h2>No Requirements Configured</h2>
                <p>No active requirements have been set for your application type yet. Please check back later or contact the scholarship office.</p>
            </div>
        @else
            {{-- Upload form --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2>Requirements for {{ $appType }}</h2>
                        <p class="caption">Upload as many or as few as you have ready. You can return to complete the rest.</p>
                    </div>
                    <span class="badge badge-green">PDF only, max 2 MB</span>
                </div>

                <form method="POST" action="{{ route('requirements_submission.post') }}" enctype="multipart/form-data" class="form">
                    @csrf

                    <div class="alert alert-info">
                        <svg class="icon icon-md"><use href="#i-info"/></svg>
                        <span>
                            <strong>Partial submission is allowed.</strong>
                            Leave any file field empty to skip it for now. Only the files you choose to upload will be saved.
                        </span>
                    </div>

                    {{-- 1. File identifier --}}
                    <div>
                        <div class="form-section-header"><span>1</span><h3>File identifier</h3></div>
                        <label class="field-label" for="file_name">File name prefix <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="file_name" name="file_name"
                               value="{{ old('file_name', 'GVC_' . strtoupper(substr($applicant->last_name, 0, 6)) . '_' . date('Y')) }}"
                               placeholder="e.g. GVC_DELACRUZ_2025"
                               class="field-input {{ $errors->has('file_name') ? 'is-invalid' : '' }}">
                        <p class="field-hint">This prefix will be added to all your uploaded file names.</p>
                        @error('file_name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- 2. Required documents --}}
                    <div>
                        <div class="form-section-header"><span>2</span><h3>Required documents</h3></div>

                        <div class="row gap-3" style="margin-bottom:16px">
                            <span class="caption">Legend:</span>
                            <span class="badge badge-done"><svg class="icon icon-sm"><use href="#i-check"/></svg>Submitted</span>
                            <span class="badge badge-pending">Pending</span>
                        </div>

                        <div class="stack gap-3">
                            @foreach($requirements as $index => $req)
                                @php
                                    $fieldKey = 'req_' . $req->id;
                                    $isDone   = in_array($req->id, $submitted);
                                    $hasError = $errors->has($fieldKey);
                                @endphp

                                <div class="req {{ $isDone ? 'is-done' : '' }}">
                                    <div class="row between gap-3" style="flex-wrap:nowrap">
                                        <div class="req-title">
                                            <span class="req-num">
                                                @if($isDone)
                                                    <svg class="icon" width="10" height="10" style="stroke-width:3"><use href="#i-check"/></svg>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </span>
                                            {{ $req->name }}
                                        </div>
                                        @if($isDone)
                                            <span class="badge badge-done"><svg class="icon icon-sm"><use href="#i-check"/></svg>Submitted</span>
                                        @else
                                            <span class="badge badge-pending">Pending</span>
                                        @endif
                                    </div>

                                    <div>
                                        <input type="file" name="{{ $fieldKey }}" accept="application/pdf"
                                               aria-label="Upload {{ $req->name }}"
                                               class="field-input {{ $hasError ? 'is-invalid' : '' }}">
                                        @if($hasError)
                                            <p class="field-error">{{ $errors->first($fieldKey) }}</p>
                                        @elseif($isDone)
                                            <p class="field-hint">
                                                <svg class="icon icon-sm" style="color:#059669"><use href="#i-check"/></svg>
                                                Already on file. Upload a new PDF to replace it, or leave empty to keep it.
                                            </p>
                                        @else
                                            <p class="field-hint">Optional for this save. PDF only, max 2 MB.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ url('/scholarship') }}" class="btn btn-secondary">
                            <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
                            Back
                        </a>
                        <div class="row gap-3">
                            @if($pendingCount > 0)
                                <span class="caption">{{ $pendingCount }} {{ \Illuminate\Support\Str::plural('document', $pendingCount) }} still pending</span>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <svg class="icon icon-sm"><use href="#i-upload"/></svg>
                                Save submitted files
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    @endif

    <p class="caption" style="text-align:center">Your documents are kept confidential and secure.</p>
</main>

<footer class="footer">
    &copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.
</footer>

</body>
</html>