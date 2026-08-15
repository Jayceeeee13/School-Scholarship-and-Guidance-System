<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Form | Green Valley College Foundation</title>
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
        .form-card {
            background: #fff; border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 10px 40px -5px rgba(10,61,32,0.08);
            border: 1px solid rgba(20,90,50,0.08); overflow: hidden;
        }
        .card-header {
            background: linear-gradient(to right, #f7fdf9, #edfbf2);
            border-bottom: 1px solid var(--green-soft); padding: 24px 40px;
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
            display: block; font-size: 0.75rem; font-weight: 700;
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
        .field-input.is-readonly {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
        }
        .field-input.is-disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }
        select.field-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px 12px; /* ← FIX: prevents repeating arrow pattern */
            padding-right: 36px;
            cursor: pointer;
        }
        select.field-input:disabled {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px 12px; /* ← FIX: prevents repeating arrow pattern */
            cursor: not-allowed;
        }
        .field-error { font-size: 0.75rem; color: #e53e3e; margin-top: 4px; }
        .field-hint  { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; line-height: 1.4; }
        .field-hint.hint-warning { color: #b45309; }
        .field-hint.hint-danger  { color: #dc2626; font-weight: 600; }
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
        .req { color: #e53e3e; }
        footer { background: var(--green-deep); border-top: 1px solid rgba(255,255,255,0.06); }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .form-card { animation: fadeUp 0.4s ease both; }
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
        .alert-error ul { margin: 0; padding-left: 18px; }

        /* Calendar styles */
        .calendar-wrapper { position: relative; }
        .calendar-input-btn {
            width: 100%; border: 1.5px solid var(--slate-border);
            border-radius: 10px; padding: 10px 14px; font-size: 0.875rem;
            font-family: 'Lato', sans-serif; color: var(--text-main);
            background: #fff; cursor: pointer; text-align: left;
            display: flex; align-items: center; justify-content: space-between;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .calendar-input-btn:focus, .calendar-input-btn.open {
            border-color: var(--green-accent);
            box-shadow: 0 0 0 3px rgba(30,132,73,0.12);
            outline: none;
        }
        .calendar-input-btn.is-invalid { border-color: #e53e3e; box-shadow: 0 0 0 3px rgba(229,62,62,0.12); }
        .calendar-dropdown {
            position: absolute; top: calc(100% + 6px); left: 0; z-index: 100;
            background: #fff; border: 1.5px solid var(--slate-border);
            border-radius: 14px; padding: 16px;
            box-shadow: 0 10px 40px -5px rgba(0,0,0,0.15);
            width: 300px; display: none;
        }
        .calendar-dropdown.open { display: block; }
        .cal-nav {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 12px;
        }
        .cal-nav button {
            background: none; border: none; cursor: pointer; padding: 4px 8px;
            border-radius: 6px; color: var(--green-mid); font-size: 1rem; font-weight: 700;
            transition: background 0.1s;
        }
        .cal-nav button:hover { background: var(--green-soft); }
        .cal-nav-label { font-size: 0.875rem; font-weight: 700; color: var(--green-deep); }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
        .cal-dow {
            text-align: center; font-size: 0.65rem; font-weight: 700;
            color: var(--text-muted); padding: 4px 0; text-transform: uppercase;
        }
        .cal-day {
            text-align: center; font-size: 0.8rem; padding: 7px 4px;
            border-radius: 8px; cursor: pointer; transition: background 0.1s, color 0.1s;
            border: none; background: none; width: 100%;
        }
        .cal-day:hover:not(:disabled) { background: var(--green-soft); color: var(--green-deep); }
        .cal-day.today { font-weight: 700; color: var(--green-accent); }
        .cal-day.selected { background: var(--green-mid); color: #fff; font-weight: 700; }
        .cal-day.selected:hover { background: var(--green-deep); }
        .cal-day:disabled { color: #cbd5e1; cursor: not-allowed; text-decoration: line-through; }
        .cal-day.inactive { color: #fca5a5; cursor: not-allowed; text-decoration: line-through; background: #fef2f2; }
        .cal-day.inactive:hover { background: #fef2f2; color: #fca5a5; }
        .cal-day.other-month { color: #d1d5db; }
    </style>
</head>
<body>


<header class="navbar shadow-sm">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between;">
        <a href="<?php echo e(url('/')); ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="GVC" style="width:34px;height:34px;border-radius:8px;object-fit:contain;">
            <span style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;letter-spacing:-0.01em;">
                Green Valley College Foundation Inc.
            </span>
        </a>
    </div>
</header>


<section class="hero" style="padding:56px 24px;">
    <div style="position:relative;max-width:640px;margin:0 auto;text-align:center;">
        <div class="hero-badge" style="margin-bottom:16px;display:inline-flex;">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><circle cx="5" cy="5" r="5" fill="#4ade80"/></svg>
            Counseling Appointment
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem);font-weight:700;color:#fff;line-height:1.2;margin:0 0 12px;">
            Appointment Form
        </h1>
        <p style="color:rgba(255,255,255,0.75);font-size:0.95rem;line-height:1.7;margin:0;">
            Fill in your details and select a preferred time slot.
            No payment is required as part of this process.
        </p>
    </div>
</section>


<section style="padding:40px 16px 64px;">
    <div style="max-width:720px;margin:0 auto;">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert-success">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <circle cx="9" cy="9" r="9" fill="#1e8449"/>
                    <path d="M5 9l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert-error">
                <strong style="display:block;margin-bottom:6px;">Please fix the following errors:</strong>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php
            $program        = $student?->program?->name ?? null;
            $yearLevel      = $student?->year_level ?? null;
            $courseAndYear  = old('course_and_year',
                collect([$program, $yearLevel ? $yearLevel . ' Year' : null])
                    ->filter()->implode(' — ')
            );
            $contactNo      = old('contact_no',     $student?->contact_no  ?? auth()->user()->contact_no);
            $presentAddress = old('present_address', $student?->address     ?? auth()->user()->address);

            // Pass inactive dates to JS
            $inactiveDates = \App\Models\InactiveDate::getInactiveDates();
        ?>

        <div class="form-card">

            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 style="font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:700;color:var(--green-deep);margin:0 0 4px;">
                        Appointment Form
                    </h2>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">
                        Fields marked <span class="req">*</span> are required.
                    </p>
                </div>
                <span style="display:inline-flex;align-items:center;gap:6px;background:var(--green-soft);border:1px solid var(--green-muted);color:var(--green-mid);font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;padding:5px 12px;border-radius:100px;">
                    <svg width="7" height="7" viewBox="0 0 7 7"><circle cx="3.5" cy="3.5" r="3.5" fill="#1e8449"/></svg>
                    New Appointment
                </span>
            </div>

            <form method="POST" action="<?php echo e(route('appointments.post')); ?>"
                  style="padding:32px 40px;display:flex;flex-direction:column;gap:32px;">
                <?php echo csrf_field(); ?>

                
                <div>
                    <div class="section-header">
                        <span class="section-num">1</span>
                        <span class="section-title">Personal Information</span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student): ?>
                    <div style="display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:10px 14px;margin-bottom:16px;">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" style="flex-shrink:0;">
                            <circle cx="7.5" cy="7.5" r="7.5" fill="#16a34a"/>
                            <path d="M4 7.5l2.5 2.5 4.5-4.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p style="font-size:0.75rem;color:#15803d;margin:0;font-weight:600;">
                            Personal information has been pre-filled from your student record. Locked fields cannot be edited.
                        </p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        
                        <div>
                            <label class="field-label">Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name"
                                   value="<?php echo e(old('last_name', $student?->last_name)); ?>"
                                   placeholder="e.g. Dela Cruz"
                                   <?php echo e($student?->last_name ? 'readonly' : ''); ?>

                                   class="field-input <?php echo e($errors->has('last_name') ? 'is-invalid' : ''); ?> <?php echo e($student?->last_name ? 'is-readonly' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label class="field-label">First Name</label>
                            <input type="text" name="first_name"
                                   value="<?php echo e(old('first_name', $student?->first_name)); ?>"
                                   placeholder="e.g. Juan"
                                   <?php echo e($student?->first_name ? 'readonly' : ''); ?>

                                   class="field-input <?php echo e($errors->has('first_name') ? 'is-invalid' : ''); ?> <?php echo e($student?->first_name ? 'is-readonly' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label class="field-label">Middle Name</label>
                            <input type="text" name="middle_name"
                                   value="<?php echo e(old('middle_name', $student?->middle_name)); ?>"
                                   placeholder="e.g. Santos"
                                   <?php echo e($student?->middle_name ? 'readonly' : ''); ?>

                                   class="field-input <?php echo e($errors->has('middle_name') ? 'is-invalid' : ''); ?> <?php echo e($student?->middle_name ? 'is-readonly' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label class="field-label">Course & Year</label>
                            <input type="text" name="course_and_year"
                                   value="<?php echo e($courseAndYear); ?>"
                                   placeholder="e.g. BSIT — 2nd Year"
                                   <?php echo e($courseAndYear ? 'readonly' : ''); ?>

                                   class="field-input <?php echo e($errors->has('course_and_year') ? 'is-invalid' : ''); ?> <?php echo e($courseAndYear ? 'is-readonly' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['course_and_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label class="field-label">Contact Number</label>
                            <input type="tel" name="contact_no"
                                   value="<?php echo e($contactNo); ?>"
                                   placeholder="09XX-XXX-XXXX"
                                   class="field-input <?php echo e($errors->has('contact_no') ? 'is-invalid' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contact_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label class="field-label">Present Address</label>
                            <input type="text" name="present_address"
                                   value="<?php echo e($presentAddress); ?>"
                                   placeholder="Barangay, City/Municipality"
                                   class="field-input <?php echo e($errors->has('present_address') ? 'is-invalid' : ''); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['present_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div style="grid-column:1/-1;" x-data="calendarPicker()" @click.outside="close()">
                            <label class="field-label">Counseling Date <span class="req">*</span></label>

                            <div class="calendar-wrapper">
                                
                                <input type="hidden" name="counseling_date" :value="selectedValue">

                                
                                <button type="button"
                                        @click="toggle()"
                                        :class="['calendar-input-btn', open ? 'open' : '', '<?php echo e($errors->has('counseling_date') ? 'is-invalid' : ''); ?>']">
                                    <span :style="selectedLabel ? 'color:#1a202c' : 'color:#b0bec5'"
                                          x-text="selectedLabel || 'Select a date'"></span>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <rect x="1" y="2" width="14" height="13" rx="2" stroke="#64748b" stroke-width="1.4"/>
                                        <line x1="11" y1="1" x2="11" y2="4" stroke="#64748b" stroke-width="1.4" stroke-linecap="round"/>
                                        <line x1="5"  y1="1" x2="5"  y2="4" stroke="#64748b" stroke-width="1.4" stroke-linecap="round"/>
                                        <line x1="1"  y1="7" x2="15" y2="7" stroke="#64748b" stroke-width="1.4"/>
                                    </svg>
                                </button>

                                
                                <div class="calendar-dropdown" :class="{ open: open }">
                                    <div class="cal-nav">
                                        <button type="button" @click="prevMonth()">&#8249;</button>
                                        <span class="cal-nav-label" x-text="monthLabel"></span>
                                        <button type="button" @click="nextMonth()">&#8250;</button>
                                    </div>
                                    <div class="cal-grid">
                                        <div class="cal-dow">Su</div>
                                        <div class="cal-dow">Mo</div>
                                        <div class="cal-dow">Tu</div>
                                        <div class="cal-dow">We</div>
                                        <div class="cal-dow">Th</div>
                                        <div class="cal-dow">Fr</div>
                                        <div class="cal-dow">Sa</div>

                                        <template x-for="(day, i) in calDays" :key="i">
                                            <button
                                                type="button"
                                                :disabled="day.disabled"
                                                :class="[
                                                    'cal-day',
                                                    day.otherMonth  ? 'other-month' : '',
                                                    day.isToday     ? 'today'       : '',
                                                    day.isSelected  ? 'selected'    : '',
                                                    day.isInactive  ? 'inactive'    : '',
                                                ]"
                                                @click="selectDay(day)"
                                                x-text="day.date"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <p class="field-hint" style="margin-top:6px;">
                                Select your preferred counseling date. Unavailable dates are marked in red.
                            </p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['counseling_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>
                </div>

                <hr class="section-divider">

                
                <div x-data="timeSlotPicker()" x-init="init()">
                    <div class="section-header">
                        <span class="section-num">2</span>
                        <span class="section-title">Appointment Details</span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">

                        
                        <div>
                            <label class="field-label">Time Slot <span class="req">*</span></label>
                            <select name="time_slot_id"
                                    :disabled="!selectedDate"
                                    @change="onSlotChange($event)"
                                    :class="['field-input', !selectedDate ? 'is-disabled' : '', '<?php echo e($errors->has('time_slot_id') ? 'is-invalid' : ''); ?>']"
                                    x-ref="slotSelect">
                                <option value="" x-text="selectedDate ? 'Choose an available time slot' : 'Select a date first'"></option>
                                <template x-for="slot in slots" :key="slot.id">
                                    <option
                                        :value="slot.id"
                                        :disabled="slot.reserved"
                                        x-text="slot.label"
                                        :selected="slot.id == <?php echo e(old('time_slot_id', 'null')); ?>"
                                    ></option>
                                </template>
                            </select>
                            <p class="field-hint" :class="slotHintClass" x-text="slotHint"></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['time_slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label class="field-label">Support Needed</label>
                            <select name="support_needed_id"
                                    class="field-input <?php echo e($errors->has('support_needed_id') ? 'is-invalid' : ''); ?>">
                                <option value="">Select Support Needed</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $supportNeeds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $support): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($support->id); ?>"
                                        <?php echo e(old('support_needed_id') == $support->id ? 'selected' : ''); ?>>
                                        <?php echo e($support->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['support_needed_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div>
                            <label class="field-label">Mode of Counseling</label>
                            <select name="mode_of_counseling_id"
                                    class="field-input <?php echo e($errors->has('mode_of_counseling_id') ? 'is-invalid' : ''); ?>">
                                <option value="">Select Mode</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modeOfCounselings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($mode->id); ?>"
                                        <?php echo e(old('mode_of_counseling_id') == $mode->id ? 'selected' : ''); ?>>
                                        <?php echo e($mode->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mode_of_counseling_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>

                    
                    <div style="margin-top:16px;"
                         x-data="notesField('<?php echo e(old('concern')); ?>', 9999)">
                        <label class="field-label">Concern / Remarks</label>
                        <div style="position:relative;">
                            <textarea
                                name="concern"
                                rows="5"
                                maxlength="9999"
                                placeholder="Briefly describe your concern or remarks…"
                                x-model="content"
                                :class="{ 'border-amber-400': isNearLimit, 'border-red-400 border-2': isAtLimit }"
                                class="field-input <?php echo e($errors->has('concern') ? 'is-invalid' : ''); ?>"
                                style="resize:vertical;padding-bottom:28px;line-height:1.6;"
                            ><?php echo e(old('concern')); ?></textarea>
                            <span style="position:absolute;bottom:10px;right:12px;font-size:0.7rem;pointer-events:none;font-family:'Lato',sans-serif;"
                                  :class="{
                                      'text-amber-500 font-bold': isNearLimit && !isAtLimit,
                                      'text-red-500 font-bold':   isAtLimit,
                                      'text-slate-400':           !isNearLimit
                                  }">
                                <span x-text="content.length"></span>&hairsp;/&hairsp;9,999
                            </span>
                        </div>
                        <div style="margin-top:6px;height:3px;width:100%;border-radius:100px;background:#e2e8f0;overflow:hidden;">
                            <div style="height:100%;border-radius:100px;transition:width 0.2s,background-color 0.2s;"
                                 :style="`width:${pct}%;background-color:${barColor}`"></div>
                        </div>
                        <p style="margin-top:4px;font-size:0.7rem;"
                           :class="{
                               'text-amber-500': isNearLimit && !isAtLimit,
                               'text-red-500 font-semibold': isAtLimit,
                               'text-slate-400': !isNearLimit
                           }"
                           x-text="hint"></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['concern'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid var(--slate-border);flex-wrap:wrap;gap:12px;">
                    <a href="<?php echo e(route('guidance')); ?>" class="btn-back">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Back
                    </a>
                    <button type="submit" class="btn-submit">
                        Submit Appointment
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M5 12l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

            </form>
        </div>

        <p style="text-align:center;font-size:0.75rem;color:#94a3b8;margin-top:20px;">
            No payment is required. Your data is kept confidential.
        </p>
    </div>
</section>


<footer style="padding:24px;text-align:center;">
    <p style="font-size:0.8rem;color:rgba(255,255,255,0.4);margin:0;">
        &copy; <?php echo e(date('Y')); ?> Green Valley College Foundation Inc. All rights reserved.
    </p>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    // ── Inactive dates from server ───────────────────────────────────
    const INACTIVE_DATES = <?php echo json_encode($inactiveDates, 15, 512) ?>;

    // ── All time slots from server ───────────────────────────────────
    const ALL_SLOTS = <?php echo json_encode($timeSlots->map(fn($s) => ['id' => $s->id, 'name' => $s->name]), 512) ?>;

    // ── Shared selected date (bridges calendar → time slot picker) ───
    let sharedDate = null;

    // ── Calendar Picker ──────────────────────────────────────────────
    function calendarPicker() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        return {
            open: false,
            viewYear:  today.getFullYear(),
            viewMonth: today.getMonth(),
            selectedValue: '<?php echo e(old('counseling_date', '')); ?>',
            selectedLabel: <?php if(old('counseling_date')): ?> '<?php echo e(\Carbon\Carbon::parse(old('counseling_date'))->format('F d, Y')); ?>' <?php else: ?> null <?php endif; ?>,

            get monthLabel() {
                return new Date(this.viewYear, this.viewMonth, 1)
                    .toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            },

            get calDays() {
                const firstDay = new Date(this.viewYear, this.viewMonth, 1).getDay();
                const daysInMonth = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
                const daysInPrev  = new Date(this.viewYear, this.viewMonth, 0).getDate();
                const days = [];

                // Prev month padding
                for (let i = firstDay - 1; i >= 0; i--) {
                    days.push({ date: daysInPrev - i, otherMonth: true, disabled: true });
                }

                // Current month
                for (let d = 1; d <= daysInMonth; d++) {
                    const dateObj = new Date(this.viewYear, this.viewMonth, d);
                    const iso     = this.toISO(dateObj);
                    const isPast  = dateObj < today;
                    const isInactive = INACTIVE_DATES.includes(iso);

                    days.push({
                        date:       d,
                        iso:        iso,
                        otherMonth: false,
                        isToday:    iso === this.toISO(today),
                        isSelected: iso === this.selectedValue,
                        isInactive: isInactive,
                        disabled:   isPast || isInactive,
                    });
                }

                // Next month padding
                const remaining = 42 - days.length;
                for (let d = 1; d <= remaining; d++) {
                    days.push({ date: d, otherMonth: true, disabled: true });
                }

                return days;
            },

            toISO(dateObj) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            },

            formatLabel(iso) {
                const [y, m, d] = iso.split('-').map(Number);
                return new Date(y, m - 1, d).toLocaleDateString('en-US', {
                    month: 'long', day: 'numeric', year: 'numeric'
                });
            },

            selectDay(day) {
                if (day.disabled || day.otherMonth) return;
                this.selectedValue = day.iso;
                this.selectedLabel = this.formatLabel(day.iso);
                this.open = false;

                // Notify time slot picker
                sharedDate = day.iso;
                window.dispatchEvent(new CustomEvent('date-selected', { detail: day.iso }));
            },

            prevMonth() {
                if (this.viewMonth === 0) { this.viewMonth = 11; this.viewYear--; }
                else { this.viewMonth--; }
            },

            nextMonth() {
                if (this.viewMonth === 11) { this.viewMonth = 0; this.viewYear++; }
                else { this.viewMonth++; }
            },

            toggle() { this.open = !this.open; },
            close()  { this.open = false; },
        };
    }

    // ── Time Slot Picker ─────────────────────────────────────────────
    function timeSlotPicker() {
        return {
            selectedDate: null,
            slots: [],
            loading: false,

            get slotHint() {
                if (!this.selectedDate) return 'Please select a date first to see time slot availability.';
                if (this.loading)       return 'Loading availability…';
                const total    = this.slots.length;
                const reserved = this.slots.filter(s => s.reserved).length;
                const available = total - reserved;
                if (available === 0) return '🔴 All time slots are reserved for this date. Please choose another date.';
                return `✅ ${available} of ${total} slots available | 🔴 Reserved slots are disabled`;
            },

            get slotHintClass() {
                if (!this.selectedDate) return 'field-hint';
                const allReserved = this.slots.length > 0 && this.slots.every(s => s.reserved);
                return allReserved ? 'field-hint hint-danger' : 'field-hint';
            },

            init() {
                // If there's an old date value (after validation error), load it
                const oldDate = '<?php echo e(old('counseling_date', '')); ?>';
                if (oldDate) {
                    this.selectedDate = oldDate;
                    this.fetchSlots(oldDate);
                }

                // Listen for date-selected events from the calendar
                window.addEventListener('date-selected', (e) => {
                    this.selectedDate = e.detail;
                    this.fetchSlots(e.detail);
                });
            },

            fetchSlots(date) {
                this.loading = true;
                this.slots = [];

                fetch(`/guidance/appointment/slots?date=${date}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    this.slots = data.map(s => ({
                        id:       s.id,
                        name:     s.name,
                        reserved: s.reserved,
                        label:    s.name + (s.reserved ? ' 🔴 Reserved' : ' ✅ Available'),
                    }));
                    this.loading = false;
                })
                .catch(() => {
                    // Fallback: show all slots as available if fetch fails
                    this.slots = ALL_SLOTS.map(s => ({
                        id: s.id, name: s.name, reserved: false,
                        label: s.name + ' ✅ Available',
                    }));
                    this.loading = false;
                });
            },

            onSlotChange(e) {
                // Reset if user picks reserved (shouldn't happen but safety net)
                const slot = this.slots.find(s => s.id == e.target.value);
                if (slot && slot.reserved) e.target.value = '';
            },
        };
    }

    // ── Notes / Concern field ────────────────────────────────────────
    function notesField(initial, max) {
        return {
            content: initial,
            max,
            get pct()         { return Math.min((this.content.length / this.max) * 100, 100); },
            get isNearLimit() { return this.content.length >= Math.floor(this.max * 0.9); },
            get isAtLimit()   { return this.content.length >= this.max; },
            get barColor() {
                if (this.isAtLimit)   return '#ef4444';
                if (this.isNearLimit) return '#f59e0b';
                if (this.pct > 50)    return '#3b82f6';
                return '#22c55e';
            },
            get hint() {
                const rem = this.max - this.content.length;
                if (this.isAtLimit)   return 'Maximum of 9,999 characters reached.';
                if (this.isNearLimit) return `Only ${rem} characters remaining.`;
                return `${rem} characters remaining.`;
            }
        };
    }
</script>
</body>
</html><?php /**PATH /home/u476045238/domains/gvcfiguidancesc.com/public_html/resources/views/appointment.blade.php ENDPATH**/ ?>