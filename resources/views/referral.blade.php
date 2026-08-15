<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Form | Green Valley College Foundation</title>
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
        .field-input.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 3px rgba(229,62,62,0.12);
        }

        .field-error {
            font-size: 0.75rem;
            color: #e53e3e;
            margin-top: 4px;
        }

        .field-hint {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 4px;
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
        .alert-error {
            background: #fee2e2;
            border: 1px solid #e53e3e;
            color: #c53030;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.875rem;
        }
        .alert-error ul {
            margin: 0;
            padding-left: 18px;
        }

        .verify-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fef9c3;
            border: 1px solid #fbbf24;
            color: #92400e;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 100px;
            margin-top: 6px;
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
            Guidance Office
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;color:#fff;line-height:1.2;margin:0 0 12px;">
            Referral Form
        </h1>
        <p style="color:rgba(255,255,255,0.75);font-size:0.95rem;line-height:1.7;margin:0;">
            Submit a referral for a student.
            @auth
                @if(auth()->user()->student)
                    The student's name and course must match our enrollment records.
                @endif
            @endauth
        </p>
    </div>
</section>

{{-- ── FORM ─────────────────────────────────────────────────────────── --}}
<section style="padding:40px 16px 64px;">
    <div style="max-width:720px;margin:0 auto;">

        {{-- SUCCESS FLASH --}}
        @if(session('success'))
            <div class="alert-success">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <circle cx="9" cy="9" r="9" fill="#1e8449"/>
                    <path d="M5 9l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="alert-error">
                <strong style="display:block;margin-bottom:6px;">Please fix the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">

            {{-- Card Header --}}
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 style="font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:700;color:var(--green-deep);margin:0 0 4px;">
                        Student Referral
                    </h2>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">
                        Fields marked <span class="req">*</span> are required.
                        @auth
                            @if(auth()->user()->student)
                                Student name and course/year will be verified against enrollment records.
                            @endif
                        @endauth
                    </p>
                </div>
                <span style="display:inline-flex;align-items:center;gap:6px;background:var(--green-soft);border:1px solid var(--green-muted);color:var(--green-mid);font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;padding:5px 12px;border-radius:100px;">
                    <svg width="7" height="7" viewBox="0 0 7 7"><circle cx="3.5" cy="3.5" r="3.5" fill="#1e8449"/></svg>
                    New Referral
                </span>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('guidance_referrals.post') }}"
                  style="padding:32px 40px;display:flex;flex-direction:column;gap:32px;">
                @csrf

                {{-- SECTION 1 · Referral Information --}}
                <div>
                    <div class="section-header">
                        <span class="section-num">1</span>
                        <span class="section-title">Referral Information</span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        {{-- Date --}}
                        <div>
                            <label class="field-label">Date <span class="req">*</span></label>
                            <input type="date" name="date"
                                   value="{{ old('date') }}"
                                   class="field-input {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                   style="color:#4a5568;">
                            @error('date')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Student Name --}}
                        <div>
                            <label class="field-label">
                                Student Name <span class="req">*</span>
                            </label>
                            <input type="text" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Juan Dela Cruz"
                                   class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                            @error('name')
                                <p class="field-error">{{ $message }}</p>
                            @else
                                @auth
                                    @if(auth()->user()->student)
                                        <p class="field-hint">Must match an enrolled student's name in the system.</p>
                                    @endif
                                @endauth
                            @enderror
                        </div>

                        {{-- Course & Year --}}
                        <div>
                            <label class="field-label">Course & Year</label>
                            <input type="text" name="course_and_year"
                                   value="{{ old('course_and_year') }}"
                                   placeholder="e.g. BSIT — 2nd Year"
                                   class="field-input {{ $errors->has('course_and_year') ? 'is-invalid' : '' }}">
                            @error('course_and_year')
                                <p class="field-error">{{ $message }}</p>
                            @else
                                @auth
                                    @if(auth()->user()->student)
                                        <p class="field-hint">Used to verify enrollment. Must match program and year level on record.</p>
                                    @endif
                                @endauth
                            @enderror
                        </div>

                        {{-- Age --}}
                        <div>
                            <label class="field-label">Age</label>
                            <input type="number" name="age"
                                   value="{{ old('age') }}"
                                   min="1" max="99"
                                   placeholder="e.g. 19"
                                   class="field-input {{ $errors->has('age') ? 'is-invalid' : '' }}">
                            @error('age')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Case Presented --}}
                        <div style="grid-column: span 2;">
                            <label class="field-label">Case Presented</label>
                            <textarea name="case_presented"
                                      placeholder="Describe the case"
                                      class="field-input {{ $errors->has('case_presented') ? 'is-invalid' : '' }}"
                                      rows="3"
                                      style="resize:vertical;">{{ old('case_presented') }}</textarea>
                            @error('case_presented')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Referred By --}}
                        <div>
                            <label class="field-label">Referred By</label>
                            <input type="text" name="referred_by"
                                   value="{{ old('referred_by') }}"
                                   placeholder="Name of referrer"
                                   class="field-input {{ $errors->has('referred_by') ? 'is-invalid' : '' }}">
                            @error('referred_by')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid var(--slate-border);flex-wrap:wrap;gap:12px;">

                    {{-- Back button: enrolled → /guidance, unenrolled → /gvc --}}
                    @auth
                        @if(auth()->user()->student)
                            <a href="{{ url('/guidance') }}" class="btn-back">
                        @else
                            <a href="{{ route('gvc') }}" class="btn-back">
                        @endif
                    @else
                        <a href="{{ url('/') }}" class="btn-back">
                    @endauth
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </a>

                    <button type="submit" class="btn-submit">
                        Submit Referral
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M5 12l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

            </form>
        </div>

        <p style="text-align:center;font-size:0.75rem;color:#94a3b8;margin-top:20px;">
            Only enrolled students can be referred. Your data is kept confidential.
        </p>
    </div>
</section>

{{-- FOOTER --}}
<footer style="padding:24px;text-align:center;">
    <p style="font-size:0.8rem;color:rgba(255,255,255,0.4);margin:0;">
        &copy; {{ date('Y') }} Green Valley College Foundation Inc. All rights reserved.
    </p>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>