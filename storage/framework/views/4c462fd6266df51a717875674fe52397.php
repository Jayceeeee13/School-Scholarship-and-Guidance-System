<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Green Valley College Foundation</title>
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
    <style>
        .fields-section.hidden { display: none; }
    </style>
</head>
<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased">

<!-- NAVBAR -->
<header class="bg-green-800 border-b border-white sticky top-0 z-40 shadow-sm shadow-green-900/5">
    <div class="max-w-8xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">
        <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2 flex-shrink-0">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Green Valley College Foundation" class="w-10 h-10 rounded-lg object-contain flex-shrink-0">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>
        <nav class="flex items-center gap-2 sm:gap-3">
            <a href="<?php echo e(route('login')); ?>"
               class="inline-flex items-center rounded-full border border-emerald-200/70 bg-emerald-900/40 px-4 py-1.5 text-xs sm:text-sm font-medium text-emerald-50 hover:bg-emerald-800/80 hover:border-emerald-200 transition">
                Login
            </a>
        </nav>
    </div>
</header>

<!-- HERO BACKGROUND -->
<section class="relative min-h-[calc(100vh-64px)] flex items-center">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat opacity-60"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.08]"
         style="background-image: url('<?php echo e(asset('images/gvc.png')); ?>');"></div>

    <div class="relative max-w-6xl mx-auto px-6 py-12 grid gap-10 lg:grid-cols-[minmax(0,1.2fr),minmax(0,1fr)] items-start">

        
        <div class="text-emerald-50 space-y-4 max-w-xl">
            <p class="inline-flex items-center rounded-full bg-emerald-900/40 border border-emerald-300/40 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide">
                Student Registration
            </p>
            <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight drop-shadow-md">
                Create your<br class="hidden sm:block"> Scholarship &amp; Guidance account
            </h1>
            <p class="text-sm sm:text-base text-emerald-100/90">
                Tell us a bit about yourself so we can match you with the right scholarship and guidance services.
            </p>
            <div class="space-y-3 pt-2">
                <div class="flex items-start gap-3 bg-emerald-900/40 border border-emerald-300/20 rounded-xl px-4 py-3">
                    <div class="mt-0.5 w-7 h-7 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-emerald-200">Enrolled Student</p>
                        <p class="text-xs text-emerald-100/70 mt-0.5">Use your Student ID, last name, and birthdate to verify your enrollment and instantly link your account.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-emerald-900/40 border border-emerald-300/20 rounded-xl px-4 py-3">
                    <div class="mt-0.5 w-7 h-7 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-emerald-200">Not Yet Enrolled</p>
                        <p class="text-xs text-emerald-100/70 mt-0.5">You can still register to take the scholarship qualifying exam. Apply for enrollment afterward to access full scholarship benefits.</p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="w-full max-w-md ml-auto bg-white/95 backdrop-blur rounded-2xl shadow-xl shadow-emerald-950/40 border border-emerald-200/70 p-6 md:p-8">
            <h1 class="font-display text-xl md:text-2xl font-semibold mb-1 text-slate-900">Create an account</h1>
            <p class="text-sm text-slate-600 mb-5">Register as a student to access the portal.</p>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm p-3">
                    <ul class="list-disc list-inside space-y-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="mb-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">I am...</p>
                <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                    <button type="button" id="tab-enrolled" onclick="switchType('enrolled')"
                        class="tab-btn rounded-lg px-3 py-2 text-sm font-semibold text-white bg-emerald-700 shadow-sm transition">
                        ✅ Enrolled Student
                    </button>
                    <button type="button" id="tab-unenrolled" onclick="switchType('unenrolled')"
                        class="tab-btn rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">
                        📋 Not Yet Enrolled
                    </button>
                </div>
                <p id="type-hint-enrolled" class="mt-2 text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                    Verify your identity using your Student ID, last name, and birthdate.
                </p>
                <p id="type-hint-unenrolled" class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 hidden">
                    You can take the scholarship exam now. You'll need to enroll first before applying for a scholarship.
                </p>
            </div>

            <form method="POST" action="<?php echo e(route('register.post')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="enrollment_type" id="enrollment_type" value="<?php echo e(old('enrollment_type', 'enrolled')); ?>">

                
                <div id="fields-enrolled" class="fields-section space-y-4">

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Student ID <span class="text-red-400">*</span></label>
                        <input type="text" name="student_id" value="<?php echo e(old('student_id')); ?>"
                               placeholder="e.g. 2024-00001"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Last Name <span class="text-red-400">*</span></label>
                        <input type="text" name="last_name_enrolled" value="<?php echo e(old('last_name_enrolled')); ?>"
                               placeholder="e.g. Dela Cruz"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Birthdate <span class="text-red-400">*</span></label>
                        <input type="date" name="birthdate_enrolled" value="<?php echo e(old('birthdate_enrolled')); ?>"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>

                    <div class="flex items-center gap-2 py-1">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="text-xs text-slate-400 font-medium">Account credentials</span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="password_enrolled"
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-10 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                            <button type="button" onclick="togglePassword('password_enrolled', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-emerald-700 transition-colors">
                                <svg class="eye-icon w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg class="eye-slash-icon w-4 h-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Confirm Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirm_enrolled"
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-10 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                            <button type="button" onclick="togglePassword('password_confirm_enrolled', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-emerald-700 transition-colors">
                                <svg class="eye-icon w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg class="eye-slash-icon w-4 h-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>

                </div>

                
                <div id="fields-unenrolled" class="fields-section space-y-4 hidden">

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-slate-700">First Name <span class="text-red-400">*</span></label>
                            <input type="text" name="first_name" value="<?php echo e(old('first_name')); ?>"
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-slate-700">Last Name <span class="text-red-400">*</span></label>
                            <input type="text" name="last_name" value="<?php echo e(old('last_name')); ?>"
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Birthdate <span class="text-red-400">*</span></label>
                        <input type="date" name="birthdate" value="<?php echo e(old('birthdate')); ?>"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Gender</label>
                        <select name="gender_id"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                            <option value="">Select gender (optional)</option>
                            <option value="1" <?php if(old('gender_id') == 1): ?> selected <?php endif; ?>>Male</option>
                            <option value="2" <?php if(old('gender_id') == 2): ?> selected <?php endif; ?>>Female</option>
                        </select>
                    </div>

                    
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">
                            Contact Number <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               name="contact_no"
                               value="<?php echo e(old('contact_no')); ?>"
                               placeholder="e.g. 09XX XXX XXXX"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none <?php $__errorArgs = ['contact_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contact_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">
                            Address <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               name="address"
                               value="<?php echo e(old('address')); ?>"
                               placeholder="House no., street, barangay, city"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2 py-1">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="text-xs text-slate-400 font-medium">Account credentials</span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email_unenrolled" value="<?php echo e(old('email_unenrolled')); ?>"
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_unenrolled" id="password_unenrolled"
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-10 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                            <button type="button" onclick="togglePassword('password_unenrolled', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-emerald-700 transition-colors">
                                <svg class="eye-icon w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg class="eye-slash-icon w-4 h-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Confirm Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_confirmation_unenrolled" id="password_confirm_unenrolled"
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-10 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                            <button type="button" onclick="togglePassword('password_confirm_unenrolled', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-emerald-700 transition-colors">
                                <svg class="eye-icon w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg class="eye-slash-icon w-4 h-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>

                </div>

                
                <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-lg bg-emerald-700 hover:bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white shadow-btn-glow transition mt-2">
                    Create Account
                </button>

                <p class="mt-3 text-xs text-slate-500 text-center">
                    Already have an account?
                    <a href="<?php echo e(route('login')); ?>" class="text-emerald-700 font-semibold hover:text-emerald-500">Login</a>
                </p>
                <!-- <p class="mt-1 text-xs text-slate-500 text-center">
                    Sign up as
                    <a href="<?php echo e(route('guest.form')); ?>" class="text-emerald-700 font-semibold hover:text-emerald-500">Guest</a>
                </p> -->

            </form>
        </div>
    </div>
</section>

<script>
    // Restore tab state after a validation error redirect
    const oldType = '<?php echo e(old('enrollment_type', 'enrolled')); ?>';
    if (oldType) switchType(oldType);

    function switchType(type) {
        document.getElementById('enrollment_type').value = type;

        const enrolledSection   = document.getElementById('fields-enrolled');
        const unenrolledSection = document.getElementById('fields-unenrolled');
        const hintEnrolled      = document.getElementById('type-hint-enrolled');
        const hintUnenrolled    = document.getElementById('type-hint-unenrolled');
        const tabEnrolled       = document.getElementById('tab-enrolled');
        const tabUnenrolled     = document.getElementById('tab-unenrolled');

        if (type === 'enrolled') {
            enrolledSection.classList.remove('hidden');
            unenrolledSection.classList.add('hidden');
            hintEnrolled.classList.remove('hidden');
            hintUnenrolled.classList.add('hidden');

            tabEnrolled.classList.add('bg-emerald-700', 'text-white', 'shadow-sm');
            tabEnrolled.classList.remove('text-slate-600');
            tabUnenrolled.classList.remove('bg-emerald-700', 'text-white', 'shadow-sm');
            tabUnenrolled.classList.add('text-slate-600');
        } else {
            unenrolledSection.classList.remove('hidden');
            enrolledSection.classList.add('hidden');
            hintUnenrolled.classList.remove('hidden');
            hintEnrolled.classList.add('hidden');

            tabUnenrolled.classList.add('bg-emerald-700', 'text-white', 'shadow-sm');
            tabUnenrolled.classList.remove('text-slate-600');
            tabEnrolled.classList.remove('bg-emerald-700', 'text-white', 'shadow-sm');
            tabEnrolled.classList.add('text-slate-600');
        }
    }

    function togglePassword(inputId, btn) {
        const input        = document.getElementById(inputId);
        const eyeIcon      = btn.querySelector('.eye-icon');
        const eyeSlashIcon = btn.querySelector('.eye-slash-icon');
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }
</script>

</body>
</html><?php /**PATH C:\xampp\htdocs\sample\resources\views/register.blade.php ENDPATH**/ ?>