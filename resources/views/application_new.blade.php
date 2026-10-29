<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Scholarship Application | Green Valley College Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Lato', 'sans-serif'],
                        display: ['Playfair Display', 'serif']
                    }
                }
            }
        }
    </script>
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
            --error:        #dc2626;
            --error-soft:   #fef2f2;
        }

        * { box-sizing: border-box; }

        body {
            background-color: #f0f4f1;
            background-image:
                radial-gradient(circle at 10% 20%, rgba(20,90,50,0.06) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(20,90,50,0.04) 0%, transparent 50%);
            min-height: 100vh;
        }

        .navbar {
            background: var(--green-deep);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .hero {
            background: linear-gradient(160deg, var(--green-deep) 0%, var(--green-mid) 55%, var(--green-accent) 100%);
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(ellipse at 80% 50%, rgba(255,255,255,0.04) 0%, transparent 60%),
                radial-gradient(ellipse at 20% 80%, rgba(0,0,0,0.15) 0%, transparent 60%);
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            color: #a7f3c0;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 100px;
        }

        .form-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 10px 40px -5px rgba(10,61,32,0.08);
            border: 1px solid rgba(20,90,50,0.08);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(to right, #f7fdf9, #edfbf2);
            border-bottom: 1px solid var(--green-soft);
            padding: 24px 40px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--slate-border);
        }
        .section-num {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--green-mid);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--green-deep);
        }

        .field-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
        }

        .field-input {
            width: 100%;
            border: 1.5px solid var(--slate-border);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.875rem;
            font-family: 'Lato', sans-serif;
            color: var(--text-main);
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
            appearance: none;
        }
        .field-input::placeholder { color: #b0bec5; }
        .field-input:focus {
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(30,132,73,0.12);
        }
        select.field-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px 12px;
            padding-right: 36px;
            cursor: pointer;
        }

        /* Readonly age field styling */
        .field-input[readonly] {
            background: #f8fafb;
            color: var(--text-muted);
            cursor: default;
        }

        /* Locked scholarship type (exam takers → Academic) */
        .field-input.is-locked,
        select.field-input:disabled {
            background-color: #f8fafb;
            color: var(--text-muted);
            cursor: not-allowed;
            opacity: 1;
        }
        .lock-note {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin-top: 6px;
            font-size: 0.72rem;
            line-height: 1.4;
            color: var(--green-mid);
        }

        /* ── Validation states ── */
        .field-input.has-error {
            border-color: var(--error);
            background-color: var(--error-soft);
        }
        .field-input.has-error:focus {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.12);
        }
        .field-error {
            display: none;
            margin: 6px 0 0;
            font-size: 0.72rem;
            line-height: 1.4;
            font-weight: 600;
            color: var(--error);
        }
        .field-error:not(:empty) { display: block; }

        .hint {
            margin: 6px 0 0;
            font-size: 0.68rem;
            color: #94a3b8;
        }

        .error-summary {
            background: var(--error-soft);
            border: 1px solid #fecaca;
            border-left: 4px solid var(--error);
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.82rem;
            color: #991b1b;
        }
        .error-summary strong { display: block; margin-bottom: 6px; }
        .error-summary ul { margin: 0; padding-left: 18px; }
        .error-summary li { margin-bottom: 2px; }

        .controls-box {
            background: var(--slate-light);
            border: 1.5px solid var(--slate-border);
            border-radius: 14px;
            padding: 20px;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            border: 2px dashed #c8d8cf;
            border-radius: 10px;
            background: #fff;
            padding: 12px 16px;
            font-size: 0.875rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }
        .file-upload-label:hover {
            border-color: var(--green-accent);
            background: #f0fdf4;
        }

        .section-divider {
            border: none;
            border-top: 1px solid var(--slate-border);
            margin: 0;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 10px;
            border: 1.5px solid var(--slate-border);
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 600;
            background: #fff;
            text-decoration: none;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }
        .btn-back:hover {
            background: var(--slate-light);
            border-color: #cbd5e1;
            color: var(--text-main);
        }
        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 28px;
            border-radius: 10px;
            background: var(--green-mid);
            color: #fff;
            font-size: 0.875rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow: 0 2px 8px rgba(20,90,50,0.25);
        }
        .btn-submit:hover {
            background: var(--green-deep);
            box-shadow: 0 4px 14px rgba(20,90,50,0.3);
        }
        .btn-submit:active { transform: scale(0.98); }
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .req { color: #e53e3e; }

        footer {
            background: var(--green-deep);
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .form-card { animation: fadeUp 0.4s ease both; }

        /* Already-applied banner */
        .already-applied-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 10px 40px -5px rgba(10,61,32,0.08);
            border: 1px solid rgba(20,90,50,0.08);
            overflow: hidden;
            animation: fadeUp 0.4s ease both;
            text-align: center;
            padding: 56px 40px;
        }

        /* Success alert */
        .alert-success {
            background: #d5f5e3;
            border: 1px solid #1e8449;
            color: #145a32;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        @media (max-width: 640px) {
            .card-header { padding: 20px; }
            form.app-form { padding: 24px 20px !important; }
            .grid-2 { grid-template-columns: 1fr !important; }
        }
    </style>
</head>

<body>

{{-- ── NAVBAR ──────────────────────────────────────────────────────── --}}
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

{{-- ── HERO ────────────────────────────────────────────────────────── --}}
<section class="hero" style="padding:56px 24px;">
    <div style="position:relative;max-width:640px;margin:0 auto;text-align:center;">
        <div class="hero-badge" style="margin-bottom:16px;display:inline-flex;">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><circle cx="5" cy="5" r="5" fill="#4ade80"/></svg>
            Apply Scholarship
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;color:#fff;line-height:1.2;margin:0 0 12px;">
            Scholarship Application
        </h1>
        <p style="color:rgba(255,255,255,0.75);font-size:0.95rem;line-height:1.7;margin:0;">
            Provide your personal information below. No payment is required as part of this application.
        </p>
    </div>
</section>

{{-- ── MAIN CONTENT ─────────────────────────────────────────────────── --}}
<section style="padding:40px 16px 64px;">
    <div style="max-width:720px;margin:0 auto;">

        {{-- ── ALREADY APPLIED STATE ────────────────────────────────── --}}
        @if(session('already_applied') || session('success'))

            @if(session('success'))
                <div class="alert-success">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <circle cx="9" cy="9" r="9" fill="#1e8449"/>
                        <path d="M5 9l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="already-applied-card">
                {{-- Icon --}}
                <div style="width:72px;height:72px;border-radius:50%;background:var(--green-soft);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <circle cx="16" cy="16" r="16" fill="#1e8449"/>
                        <path d="M9 16l5 5 9-9" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h2 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--green-deep);margin:0 0 10px;">
                    Application Already Submitted
                </h2>
                <p style="font-size:0.9rem;color:var(--text-muted);line-height:1.7;max-width:420px;margin:0 auto 28px;">
                    Our records show that you have already submitted a scholarship application.
                    Each account is allowed only one application. Please contact the scholarship
                    office if you need to make changes.
                </p>

                <div style="display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;">
                    <a href="{{ url('/gvc') }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;border-radius:10px;background:var(--green-mid);color:#fff;font-size:0.875rem;font-weight:700;text-decoration:none;box-shadow:0 2px 8px rgba(20,90,50,0.25);transition:background 0.15s;"
                       onmouseover="this.style.background='var(--green-deep)'"
                       onmouseout="this.style.background='var(--green-mid)'">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Back to Home
                    </a>
                </div>
            </div>

        @else

        {{-- ── FORM ────────────────────────────────────────────────── --}}
        <div class="form-card">

            {{-- Card Header --}}
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 style="font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:700;color:var(--green-deep);margin:0 0 4px;">
                        Application Form
                    </h2>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">
                        Fields marked <span class="req">*</span> are required.
                    </p>
                </div>
                <span style="display:inline-flex;align-items:center;gap:6px;background:var(--green-soft);border:1px solid var(--green-muted);color:var(--green-mid);font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;padding:5px 12px;border-radius:100px;">
                    <svg width="7" height="7" viewBox="0 0 7 7"><circle cx="3.5" cy="3.5" r="3.5" fill="#1e8449"/></svg>
                    New Application
                </span>
            </div>

            <form id="applicationForm"
                  class="app-form"
                  method="POST"
                  action="{{ route('application_new.post') }}"
                  enctype="multipart/form-data"
                  novalidate
                  style="padding:32px 40px;display:flex;flex-direction:column;gap:32px;">
                @csrf

                {{-- ── SERVER-SIDE ERROR SUMMARY ─────────────────── --}}
                @if ($errors->any())
                    <div class="error-summary" role="alert">
                        <strong>Please fix the following before submitting:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ── TOP CONTROLS ──────────────────────────────── --}}
                <div class="controls-box">
                    <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        {{-- Applicant Type --}}
                        <div>
                            <label class="field-label">Applicant Type <span class="req">*</span></label>
                            <select name="type_of_application_id"
                                    class="field-input {{ $errors->has('type_of_application_id') ? 'has-error' : '' }}"
                                    required>
                                <option value="">Select Type</option>
                                @foreach($applicantTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('type_of_application_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="field-error" data-error-for="type_of_application_id">{{ $errors->first('type_of_application_id') }}</p>
                        </div>

                        {{-- Scholarship Type --}}
                        <div>
                            <label class="field-label">Scholarship Type <span class="req">*</span></label>

                            @if(!empty($academicId))
                                {{-- Exam takers are automatically Academic. The disabled select is
                                     display-only; the hidden input is what actually gets submitted. --}}
                                <select class="field-input is-locked" disabled>
                                    @foreach($scholarshipTypes as $type)
                                        @if($type->id == $academicId)
                                            <option value="{{ $type->id }}" selected>
                                                {{ $type->name }} — {{ $type->slots }} slot{{ $type->slots === 1 ? '' : 's' }} {{ $type->slots <= 0 ? '(Full)' : 'left' }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type="hidden" name="type_of_scholarship_id" value="{{ $academicId }}">
                                <p class="lock-note">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="flex-shrink:0;margin-top:1px;">
                                        <rect x="5" y="11" width="14" height="9" rx="2"/>
                                        <path stroke-linecap="round" d="M8 11V8a4 4 0 018 0v3"/>
                                    </svg>
                                    Set automatically to Academic because you have taken the scholarship exam.
                                </p>
                            @else
                                <select name="type_of_scholarship_id"
                                        class="field-input {{ $errors->has('type_of_scholarship_id') ? 'has-error' : '' }}"
                                        required>
                                    <option value="">Select Scholarship</option>
                                    @foreach($scholarshipTypes as $type)
                                        <option
                                            value="{{ $type->id }}"
                                            {{ old('type_of_scholarship_id') == $type->id ? 'selected' : '' }}
                                            {{ $type->slots <= 0 ? 'disabled' : '' }}>
                                            {{ $type->name }} — {{ $type->slots }} slot{{ $type->slots === 1 ? '' : 's' }} {{ $type->slots <= 0 ? '(Full)' : 'left' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <p class="field-error" data-error-for="type_of_scholarship_id">{{ $errors->first('type_of_scholarship_id') }}</p>
                        </div>

                        {{-- 2×2 Picture --}}
                        <div style="grid-column:1/-1;" x-data="{ fileName: '' }">
                            <label class="field-label">2×2 ID Picture</label>
                            <label for="picture" class="file-upload-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2" style="flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="fileName || 'Click to upload .jpg, .jpeg, or .png (max 5 MB)'" style="font-size:0.85rem;"></span>
                            </label>
                            <input id="picture" name="picture" type="file"
                                   accept=".jpg,.jpeg,.png,image/jpeg,image/png" style="display:none;"
                                   @change="fileName = validatePicture($event.target) ? ($event.target.files[0]?.name || '') : ''">
                            <p class="field-error" data-error-for="picture">{{ $errors->first('picture') }}</p>
                        </div>

                    </div>
                </div>

                <hr class="section-divider">

                {{-- ── SECTION 1 · Personal Information ─────────── --}}
                <div>
                    <div class="section-header">
                        <span class="section-num">1</span>
                        <span class="section-title">Personal Information</span>
                    </div>

                    <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        <div>
                            <label class="field-label">First Name <span class="req">*</span></label>
                            <input type="text" name="first_name" required maxlength="100"
                                   autocomplete="given-name"
                                   value="{{ old('first_name') }}"
                                   placeholder="e.g. Juan"
                                   class="field-input {{ $errors->has('first_name') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="first_name">{{ $errors->first('first_name') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Middle Name</label>
                            <input type="text" name="middle_name" maxlength="100"
                                   autocomplete="additional-name"
                                   value="{{ old('middle_name') }}"
                                   placeholder="e.g. Santos"
                                   class="field-input {{ $errors->has('middle_name') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="middle_name">{{ $errors->first('middle_name') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name" required maxlength="100"
                                   autocomplete="family-name"
                                   value="{{ old('last_name') }}"
                                   placeholder="e.g. Dela Cruz"
                                   class="field-input {{ $errors->has('last_name') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="last_name">{{ $errors->first('last_name') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Extension Name</label>
                            <input type="text" name="extension_name" maxlength="20"
                                   value="{{ old('extension_name') }}"
                                   placeholder="e.g. Jr, Sr, III"
                                   class="field-input {{ $errors->has('extension_name') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="extension_name">{{ $errors->first('extension_name') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Program <span class="req">*</span></label>
                            <select name="program_id"
                                    class="field-input {{ $errors->has('program_id') ? 'has-error' : '' }}"
                                    required>
                                <option value="">Select Program</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="field-error" data-error-for="program_id">{{ $errors->first('program_id') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Year Level <span class="req">*</span></label>
                            <select name="year_level"
                                    class="field-input {{ $errors->has('year_level') ? 'has-error' : '' }}"
                                    required>
                                <option value="">Select Year Level</option>
                                @foreach(['1st Year','2nd Year','3rd Year','4th Year','5th Year'] as $level)
                                    <option value="{{ $level }}" {{ old('year_level') === $level ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="field-error" data-error-for="year_level">{{ $errors->first('year_level') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Gender <span class="req">*</span></label>
                            <select name="gender_id"
                                    class="field-input {{ $errors->has('gender_id') ? 'has-error' : '' }}"
                                    required>
                                <option value="">Select Gender</option>
                                @foreach($genders as $gender)
                                    <option value="{{ $gender->id }}" {{ old('gender_id') == $gender->id ? 'selected' : '' }}>
                                        {{ $gender->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="field-error" data-error-for="gender_id">{{ $errors->first('gender_id') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Contact Number</label>
                            <input type="tel" name="contact_no" maxlength="11" inputmode="numeric" autocomplete="off"
                                   autocomplete="tel"
                                   value="{{ old('contact_no') }}"
                                   placeholder="09XXXXXXXXX"
                                   class="field-input {{ $errors->has('contact_no') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="contact_no">{{ $errors->first('contact_no') }}</p>
                        </div>

                        {{-- Birth Date --}}
                        <div>
                            <label class="field-label">Birth Date</label>
                            <input type="date" name="birthdate"
                                   id="birthdate"
                                   min="1930-01-02"
                                   max="{{ now()->subDay()->toDateString() }}"
                                   value="{{ old('birthdate') }}"
                                   class="field-input {{ $errors->has('birthdate') ? 'has-error' : '' }}"
                                   style="color:#4a5568;">
                            <p class="field-error" data-error-for="birthdate">{{ $errors->first('birthdate') }}</p>
                        </div>

                        {{-- Age (auto-computed) --}}
                        <div>
                            <label class="field-label">
                                Age
                                <span style="font-size:0.65rem;font-weight:400;color:#94a3b8;text-transform:none;letter-spacing:0;margin-left:4px;">
                                    — auto-filled from birth date
                                </span>
                            </label>
                            <input type="number" name="age" id="age"
                                   min="1" max="99"
                                   value="{{ old('age') }}"
                                   placeholder="Auto-computed"
                                   class="field-input"
                                   readonly>
                        </div>

                        <div>
                            <label class="field-label">Religion</label>
                            <input type="text" name="religion" maxlength="100"
                                   value="{{ old('religion') }}"
                                   placeholder="e.g. Roman Catholic"
                                   class="field-input {{ $errors->has('religion') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="religion">{{ $errors->first('religion') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Facebook Account</label>
                            <input type="text" name="facebook_account" maxlength="100"
                                   value="{{ old('facebook_account') }}"
                                   placeholder="facebook.com/yourprofile"
                                   class="field-input {{ $errors->has('facebook_account') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="facebook_account">{{ $errors->first('facebook_account') }}</p>
                        </div>

                    </div>
                </div>

                <hr class="section-divider">

                {{-- ── SECTION 2 · Family Background ────────────── --}}
                <div>
                    <div class="section-header">
                        <span class="section-num">2</span>
                        <span class="section-title">Family Background</span>
                    </div>

                    <div class="grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        <div>
                            <label class="field-label">Father's Name <span class="req">*</span></label>
                            <input type="text" name="fathers_name" required maxlength="100"
                                   value="{{ old('fathers_name') }}"
                                   placeholder="Full name"
                                   class="field-input {{ $errors->has('fathers_name') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="fathers_name">{{ $errors->first('fathers_name') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Father's Contact No.</label>
                            <input type="tel" name="fathers_contact_no" maxlength="11" inputmode="numeric" autocomplete="off"
                                   value="{{ old('fathers_contact_no') }}"
                                   placeholder="09XXXXXXXXX"
                                   class="field-input {{ $errors->has('fathers_contact_no') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="fathers_contact_no">{{ $errors->first('fathers_contact_no') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Mother's Name <span class="req">*</span></label>
                            <input type="text" name="mothers_name" required maxlength="100"
                                   value="{{ old('mothers_name') }}"
                                   placeholder="Full name"
                                   class="field-input {{ $errors->has('mothers_name') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="mothers_name">{{ $errors->first('mothers_name') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Mother's Contact No.</label>
                            <input type="tel" name="mothers_contact_no" maxlength="11" inputmode="numeric" autocomplete="off"
                                   value="{{ old('mothers_contact_no') }}"
                                   placeholder="09XXXXXXXXX"
                                   class="field-input {{ $errors->has('mothers_contact_no') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="mothers_contact_no">{{ $errors->first('mothers_contact_no') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Guardian</label>
                            <input type="text" name="guardian" maxlength="100"
                                   value="{{ old('guardian') }}"
                                   placeholder="Full name (if applicable)"
                                   class="field-input {{ $errors->has('guardian') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="guardian">{{ $errors->first('guardian') }}</p>
                        </div>

                        <div>
                            <label class="field-label">Guardian Contact No.</label>
                            <input type="tel" name="guardian_contact_no" maxlength="11" inputmode="numeric" autocomplete="off"
                                   value="{{ old('guardian_contact_no') }}"
                                   placeholder="09XXXXXXXXX"
                                   class="field-input {{ $errors->has('guardian_contact_no') ? 'has-error' : '' }}">
                            <p class="field-error" data-error-for="guardian_contact_no">{{ $errors->first('guardian_contact_no') }}</p>
                        </div>

                    </div>
                </div>

                {{-- ── ACTION BUTTONS ───────────────────────────── --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid var(--slate-border);flex-wrap:wrap;gap:12px;">
                    <a href="{{ url('/gvc') }}" class="btn-back">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Back to Home
                    </a>
                    <button type="submit" id="submitBtn" class="btn-submit">
                        <span id="submitLabel">Submit Application</span>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M5 12l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

            </form>
        </div>

        @endif

        <p style="text-align:center;font-size:0.75rem;color:#94a3b8;margin-top:20px;">
            No payment is required. Your data is kept confidential.
        </p>
    </div>
</section>

{{-- ── FOOTER ──────────────────────────────────────────────────────── --}}
<footer style="padding:24px;text-align:center;">
    <p style="font-size:0.8rem;color:rgba(255,255,255,0.4);margin:0;">
        &copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.
    </p>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    /* ═══════════════════════════════════════════════════════════════
       Age auto-compute
    ═══════════════════════════════════════════════════════════════ */
    function computeAge() {
        const birthdateVal = document.getElementById('birthdate').value;
        const ageInput     = document.getElementById('age');

        if (!birthdateVal) {
            ageInput.value = '';
            return;
        }

        const birth = new Date(birthdateVal);
        const today = new Date();
        let   age   = today.getFullYear() - birth.getFullYear();
        const mDiff = today.getMonth() - birth.getMonth();

        // Subtract 1 if birthday hasn't occurred yet this year
        if (mDiff < 0 || (mDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        ageInput.value = age > 0 ? age : '';
    }

    /* ═══════════════════════════════════════════════════════════════
       Client-side validation — mirrors ApplicationController rules.
       (The server still re-validates everything on submit.)
    ═══════════════════════════════════════════════════════════════ */
    const NAME_RE  = /^[\p{L}\s]+$/u;   // letters and spaces only
    const EXT_RE   = /^[A-Za-z\s]+$/;   // letters and spaces only
    const PHONE_RE = /^09\d{9}$/;       // exactly 11 digits, starting with 09

    const NAME_MSG  = 'may only contain letters and spaces.';
    const PHONE_MSG = 'must be exactly 11 digits and start with 09 (e.g. 09171234567).';

    // Remove anything that is not a letter/space, trim leading space, collapse double spaces.
    function cleanName(value) {
        return value
            .replace(/[^\p{L}\s]/gu, '')
            .replace(/^\s+/, '')
            .replace(/\s{2,}/g, ' ');
    }

    // "juan DELA cruz" -> "Juan Dela Cruz"
    function toTitleCase(value) {
        return value
            .toLowerCase()
            .replace(/(^|\s)(\p{L})/gu, function (m, space, letter) {
                return space + letter.toUpperCase();
            });
    }

    const NAME_FIELDS  = ['first_name', 'middle_name', 'last_name', 'fathers_name', 'mothers_name', 'guardian'];
    const PHONE_FIELDS = ['contact_no', 'fathers_contact_no', 'mothers_contact_no', 'guardian_contact_no'];

    function nameRule(value, label, required) {
        const v = value.trim();
        if (!v)             return required ? label + ' is required.' : '';
        if (v.length > 100) return label + ' must not exceed 100 characters.';
        if (!NAME_RE.test(v)) return label + ' ' + NAME_MSG;
        return '';
    }

    function phoneRule(value, label) {
        const v = value.replace(/[\s\-]+/g, '');
        if (!v) return '';
        return PHONE_RE.test(v) ? '' : label + ' ' + PHONE_MSG;
    }

    function maxRule(value, label, max) {
        return value.trim().length > max ? label + ' must not exceed ' + max + ' characters.' : '';
    }

    const RULES = {
        type_of_application_id: (v) => v ? '' : 'Please select an applicant type.',
        type_of_scholarship_id: (v) => v ? '' : 'Please select a scholarship type.',

        first_name:  (v) => nameRule(v, 'First name', true),
        middle_name: (v) => nameRule(v, 'Middle name', false),
        last_name:   (v) => nameRule(v, 'Last name', true),

        extension_name: (v) => {
            const t = v.trim();
            if (!t) return '';
            if (t.length > 20) return 'Extension name must not exceed 20 characters.';
            return EXT_RE.test(t) ? '' : 'Extension name may only contain letters (e.g. Jr, Sr, III).';
        },

        program_id: (v) => v ? '' : 'Please select a program.',
        year_level: (v) => v ? '' : 'Please select a year level.',
        gender_id:  (v) => v ? '' : 'Please select a gender.',

        contact_no: (v) => phoneRule(v, 'Contact number'),

        birthdate: (v) => {
            if (!v) return '';
            const today = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD (local)
            if (v >= today)       return 'Birth date must be a past date.';
            if (v <= '1930-01-01') return 'Please enter a valid birth date.';
            return '';
        },

        religion:         (v) => maxRule(v, 'Religion', 100),
        facebook_account: (v) => maxRule(v, 'Facebook account', 100),

        fathers_name:       (v) => nameRule(v, "Father's name", true),
        fathers_contact_no: (v) => phoneRule(v, "Father's contact number"),
        mothers_name:       (v) => nameRule(v, "Mother's name", true),
        mothers_contact_no: (v) => phoneRule(v, "Mother's contact number"),

        guardian: (v) => {
            const contact = document.querySelector('[name="guardian_contact_no"]').value.trim();
            if (!v.trim() && contact) {
                return 'Please enter the guardian name since a guardian contact number was provided.';
            }
            return nameRule(v, 'Guardian name', false);
        },
        guardian_contact_no: (v) => phoneRule(v, 'Guardian contact number'),
    };

    function setError(el, message) {
        const box = document.querySelector('[data-error-for="' + el.name + '"]');
        if (box) box.textContent = message;
        el.classList.toggle('has-error', Boolean(message));
        el.setAttribute('aria-invalid', message ? 'true' : 'false');
    }

    function validateField(el) {
        const rule = RULES[el.name];
        if (!rule || el.disabled) return true;
        const message = rule(el.value);
        setError(el, message);
        return !message;
    }

    // Used by the picture input's Alpine @change handler.
    function validatePicture(input) {
        const box  = document.querySelector('[data-error-for="picture"]');
        const file = input.files && input.files[0];
        let message = '';

        if (file) {
            const okType = ['image/jpeg', 'image/png'].includes(file.type) || /\.(jpe?g|png)$/i.test(file.name);
            if (!okType) {
                message = 'The ID picture must be a .jpg, .jpeg, or .png file.';
            } else if (file.size > 5 * 1024 * 1024) {
                message = 'The ID picture must not be larger than 5 MB.';
            }
        }

        if (box) box.textContent = message;
        if (message) input.value = ''; // discard the bad file
        return !message;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('applicationForm');
        if (!form) return;

        const birth = document.getElementById('birthdate');
        if (birth) {
            birth.addEventListener('change', computeAge);
            if (birth.value) computeAge(); // after a failed server validation
        }

        // ── Block invalid characters while typing ──────────────────
        form.addEventListener('input', function (e) {
            const el = e.target;

            if (NAME_FIELDS.includes(el.name)) {
                // Letters + spaces only, first letter of every word capitalised.
                const before = el.value;
                const pos    = el.selectionStart;
                const after  = toTitleCase(cleanName(before));

                if (after !== before) {
                    const newPos = toTitleCase(cleanName(before.slice(0, pos))).length;
                    el.value = after;
                    try { el.setSelectionRange(newPos, newPos); } catch (err) {}
                }
            } else if (el.name === 'extension_name') {
                // Letters + spaces only (kept exactly as typed, e.g. "III").
                const cleaned = el.value
                    .replace(/[^A-Za-z\s]/g, '')
                    .replace(/^\s+/, '')
                    .replace(/\s{2,}/g, ' ');
                if (cleaned !== el.value) el.value = cleaned;
            } else if (PHONE_FIELDS.includes(el.name)) {
                // Digits only, maximum 11.
                const digits = el.value.replace(/\D/g, '').slice(0, 11);
                if (digits !== el.value) el.value = digits;
            }

            // Once a field shows an error, re-check live so it clears as soon as it's fixed.
            if (el.classList.contains('has-error')) validateField(el);

            // Guardian name depends on guardian contact.
            if (el.name === 'guardian_contact_no') {
                validateField(form.elements['guardian']);
            }
        });

        // ── Validate when leaving a field / changing a select or date ──
        form.addEventListener('focusout', function (e) { validateField(e.target); });
        form.addEventListener('change',   function (e) { validateField(e.target); });

        // ── Validate everything on submit ──────────────────────────
        form.addEventListener('submit', function (e) {
            let firstInvalid = null;

            Array.from(form.elements).forEach(function (el) {
                if (!RULES[el.name]) return;
                if (!validateField(el) && !firstInvalid) firstInvalid = el;
            });

            const picture = document.getElementById('picture');
            if (picture && !validatePicture(picture) && !firstInvalid) firstInvalid = picture;

            if (firstInvalid) {
                e.preventDefault();
                const target = firstInvalid.type === 'file'
                    ? document.querySelector('label[for="picture"]')
                    : firstInvalid;
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (firstInvalid.type !== 'file') firstInvalid.focus({ preventScroll: true });
                return;
            }

            // Prevent double submissions.
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('submitLabel').textContent = 'Submitting…';
        });
    });
</script>

</body>
</html>