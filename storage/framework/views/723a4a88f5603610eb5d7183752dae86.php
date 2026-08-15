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


<section style="padding:40px 16px 64px;">
    <div style="max-width:720px;margin:0 auto;">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('already_applied') || session('success')): ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="alert-success">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <circle cx="9" cy="9" r="9" fill="#1e8449"/>
                        <path d="M5 9l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="already-applied-card">
                
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
                    <a href="<?php echo e(url('/gvc')); ?>"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;border-radius:10px;background:var(--green-mid);color:#fff;font-size:0.875rem;font-weight:700;text-decoration:none;box-shadow:0 2px 8px rgba(20,90,50,0.25);transition:background 0.15s;"
                       onmouseover="this.style.background='var(--green-deep)'"
                       onmouseout="this.style.background='var(--green-mid)'">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Back to Home
                    </a>
                </div>
            </div>

        <?php else: ?>

        
        <div class="form-card">

            
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

            <form method="POST"
                  action="<?php echo e(route('application_new.post')); ?>"
                  enctype="multipart/form-data"
                  style="padding:32px 40px;display:flex;flex-direction:column;gap:32px;">
                <?php echo csrf_field(); ?>

                
                <div class="controls-box">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        
                        <div>
                            <label class="field-label">Applicant Type <span class="req">*</span></label>
                            <select name="type_of_application_id" class="field-input" required>
                                <option value="">Select Type</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $applicantTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>" <?php echo e(old('type_of_application_id') == $type->id ? 'selected' : ''); ?>>
                                        <?php echo e($type->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="field-label">Scholarship Type <span class="req">*</span></label>
                            <select name="type_of_scholarship_id" class="field-input" required>
                                <option value="">Select Scholarship</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $scholarshipTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($type->id); ?>"
                                        <?php echo e(old('type_of_scholarship_id') == $type->id ? 'selected' : ''); ?>

                                        <?php echo e($type->slots <= 0 ? 'disabled' : ''); ?>>
                                        <?php echo e($type->name); ?> — <?php echo e($type->slots); ?> slot<?php echo e($type->slots === 1 ? '' : 's'); ?> <?php echo e($type->slots <= 0 ? '(Full)' : 'left'); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        
                        <div style="grid-column:1/-1;" x-data="{ fileName: '' }">
                            <label class="field-label">2×2 ID Picture</label>
                            <label for="picture" class="file-upload-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2" style="flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="fileName || 'Click to upload .jpg, .jpeg, or .png'" style="font-size:0.85rem;"></span>
                            </label>
                            <input id="picture" name="picture" type="file"
                                   accept=".jpg,.jpeg,.png" style="display:none;"
                                   @change="fileName = $event.target.files[0]?.name || ''">
                        </div>

                    </div>
                </div>

                <hr class="section-divider">

                
                <div>
                    <div class="section-header">
                        <span class="section-num">1</span>
                        <span class="section-title">Personal Information</span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        <div>
                            <label class="field-label">First Name <span class="req">*</span></label>
                            <input type="text" name="first_name" required
                                   value="<?php echo e(old('first_name')); ?>"
                                   placeholder="e.g. Juan" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Middle Name</label>
                            <input type="text" name="middle_name"
                                   value="<?php echo e(old('middle_name')); ?>"
                                   placeholder="e.g. Santos" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name" required
                                   value="<?php echo e(old('last_name')); ?>"
                                   placeholder="e.g. Dela Cruz" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Extension Name</label>
                            <input type="text" name="extension_name"
                                   value="<?php echo e(old('extension_name')); ?>"
                                   placeholder="e.g. Jr., Sr., III" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Program <span class="req">*</span></label>
                            <select name="program_id" class="field-input" required>
                                <option value="">Select Program</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($program->id); ?>" <?php echo e(old('program_id') == $program->id ? 'selected' : ''); ?>>
                                        <?php echo e($program->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="field-label">Year Level <span class="req">*</span></label>
                            <select name="year_level" class="field-input" required>
                                <option value="">Select Year Level</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['1st Year','2nd Year','3rd Year','4th Year','5th Year']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($level); ?>" <?php echo e(old('year_level') === $level ? 'selected' : ''); ?>>
                                        <?php echo e($level); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="field-label">Gender <span class="req">*</span></label>
                            <select name="gender_id" class="field-input" required>
                                <option value="">Select Gender</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $genders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gender->id); ?>" <?php echo e(old('gender_id') == $gender->id ? 'selected' : ''); ?>>
                                        <?php echo e($gender->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="field-label">Contact Number</label>
                            <input type="tel" name="contact_no"
                                   value="<?php echo e(old('contact_no')); ?>"
                                   placeholder="09XX-XXX-XXXX" class="field-input">
                        </div>

                        
                        <div>
                            <label class="field-label">Birth Date</label>
                            <input type="date" name="birthdate"
                                   id="birthdate"
                                   value="<?php echo e(old('birthdate')); ?>"
                                   class="field-input" style="color:#4a5568;"
                                   onchange="computeAge()">
                        </div>

                        
                        <div>
                            <label class="field-label">
                                Age
                                <span style="font-size:0.65rem;font-weight:400;color:#94a3b8;text-transform:none;letter-spacing:0;margin-left:4px;">
                                    — auto-filled from birth date
                                </span>
                            </label>
                            <input type="number" name="age" id="age"
                                   min="1" max="99"
                                   value="<?php echo e(old('age')); ?>"
                                   placeholder="Auto-computed"
                                   class="field-input"
                                   readonly>
                        </div>

                        <div>
                            <label class="field-label">Religion</label>
                            <input type="text" name="religion"
                                   value="<?php echo e(old('religion')); ?>"
                                   placeholder="e.g. Roman Catholic" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Facebook Account</label>
                            <input type="text" name="facebook_account"
                                   value="<?php echo e(old('facebook_account')); ?>"
                                   placeholder="facebook.com/yourprofile" class="field-input">
                        </div>

                    </div>
                </div>

                <hr class="section-divider">

                
                <div>
                    <div class="section-header">
                        <span class="section-num">2</span>
                        <span class="section-title">Family Background</span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

                        <div>
                            <label class="field-label">Father's Name <span class="req">*</span></label>
                            <input type="text" name="fathers_name" required
                                   value="<?php echo e(old('fathers_name')); ?>"
                                   placeholder="Full name" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Father's Contact No.</label>
                            <input type="tel" name="fathers_contact_no"
                                   value="<?php echo e(old('fathers_contact_no')); ?>"
                                   placeholder="09XX-XXX-XXXX" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Mother's Name <span class="req">*</span></label>
                            <input type="text" name="mothers_name" required
                                   value="<?php echo e(old('mothers_name')); ?>"
                                   placeholder="Full name" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Mother's Contact No.</label>
                            <input type="tel" name="mothers_contact_no"
                                   value="<?php echo e(old('mothers_contact_no')); ?>"
                                   placeholder="09XX-XXX-XXXX" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Guardian</label>
                            <input type="text" name="guardian"
                                   value="<?php echo e(old('guardian')); ?>"
                                   placeholder="Full name (if applicable)" class="field-input">
                        </div>

                        <div>
                            <label class="field-label">Guardian Contact No.</label>
                            <input type="tel" name="guardian_contact_no"
                                   value="<?php echo e(old('guardian_contact_no')); ?>"
                                   placeholder="09XX-XXX-XXXX" class="field-input">
                        </div>

                    </div>
                </div>

                
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:8px;border-top:1px solid var(--slate-border);flex-wrap:wrap;gap:12px;">
                    <a href="<?php echo e(url('/gvc')); ?>" class="btn-back">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Back to Home
                    </a>
                    <button type="submit" class="btn-submit">
                        Submit Application
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M5 12l5-5-5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>

            </form>
        </div>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
    function computeAge() {
        const birthdateVal = document.getElementById('birthdate').value;
        const ageInput     = document.getElementById('age');

        if (!birthdateVal) {
            ageInput.value = '';
            return;
        }

        const birth   = new Date(birthdateVal);
        const today   = new Date();
        let   age     = today.getFullYear() - birth.getFullYear();
        const mDiff   = today.getMonth() - birth.getMonth();

        // Subtract 1 if birthday hasn't occurred yet this year
        if (mDiff < 0 || (mDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        ageInput.value = age > 0 ? age : '';
    }

    // Re-compute on page load to handle old('birthdate') after validation failure
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('birthdate') &&
            document.getElementById('birthdate').value) {
            computeAge();
        }
    });
</script>

</body>
</html><?php /**PATH C:\xampp\htdocs\sample\resources\views/application_new.blade.php ENDPATH**/ ?>