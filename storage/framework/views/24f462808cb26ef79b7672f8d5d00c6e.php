<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guidance | Green Valley College Foundation</title>
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
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.65s cubic-bezier(.22,1,.36,1) both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.22s; }
        .delay-3 { animation-delay: 0.34s; }
        .delay-4 { animation-delay: 0.46s; }
    </style>
</head>

<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

<!-- NAVBAR -->
<header class="bg-green-800 border-b border-white sticky top-0 z-50 shadow-sm shadow-green-900/5">
    <div class="max-w-8xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">
        <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2 group flex-shrink-0">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Green Valley College Foundation" class="w-10 h-10 rounded-lg object-contain flex-shrink-0">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>
        <nav class="flex items-center gap-2 sm:gap-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
            <?php else: ?>
                <span class="hidden sm:inline text-xs sm:text-sm text-emerald-100 mr-2">
                    Hello, <span class="font-semibold"><?php echo e(auth()->user()->name); ?></span>
                </span>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-full border border-emerald-200/70 bg-emerald-900/40 px-4 py-1.5 text-xs sm:text-sm font-medium text-emerald-50 hover:bg-emerald-800/80 hover:border-emerald-200 transition"
                    >
                        Logout
                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
    </div>
</header>

<!-- HERO BANNER -->
<section class="relative overflow-hidden py-20 md:py-28">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.10]" style="background-image: url('<?php echo e(asset('images/gvc.png')); ?>');"></div>

    <div class="relative max-w-3xl mx-auto px-6 text-center fade-up">
        <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-900/60 border border-emerald-400/30 text-emerald-300 text-sm font-semibold mb-5 tracking-wide">
            Guidance Office
        </span>
        <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold leading-tight mb-4 tracking-tight text-emerald-50 drop-shadow-md">
            How Can We <br><span class="text-gvc-pale">Help You Today?</span>
        </h1>
        <p class="text-emerald-100/80 text-sm sm:text-base max-w-xl mx-auto">
            The Guidance Office is here to support your academic and personal well-being. Choose an option below to get started.
        </p>
    </div>
</section>

<!-- MAIN CONTENT -->
<section class="flex-1 py-20 md:py-28 bg-gradient-to-b from-green-50/80 to-white">
    <div class="max-w-4xl mx-auto px-6">

        
        <?php
            $student = auth()->user()->student ?? null;

            $invitations = collect();
            if ($student) {
                $invitations = \App\Models\ReferralInvitation::whereHas('referral', function ($q) use ($student) {
                    $q->whereRaw("LOWER(name) LIKE ?", ['%' . strtolower($student->first_name) . '%'])
                      ->orWhereRaw("LOWER(name) LIKE ?", ['%' . strtolower($student->last_name) . '%']);
                })
                ->with(['referral', 'timeSlot', 'personnel'])
                ->latest()
                ->get();
            }
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invitations->isNotEmpty()): ?>
        <div class="mb-10 fade-up">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-display text-lg font-bold text-slate-800">Counseling Invitations</h2>
                    <p class="text-xs text-slate-500">You have been invited for a counseling session. Please respond below.</p>
                </div>
                <?php $pendingCount = $invitations->where('status', 'pending')->count(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingCount > 0): ?>
                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                        <?php echo e($pendingCount); ?> Pending
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="mb-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $invitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-xl border shadow-sm px-4 py-3 flex items-center gap-3 flex-wrap
                            <?php echo e($inv->status === 'pending' ? 'border-amber-200' : ($inv->status === 'accepted' ? 'border-green-200' : 'border-red-200')); ?>">

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            <?php echo e($inv->session_date?->format('F d, Y') ?? 'Date TBD'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inv->timeSlot): ?>
                                <span class="text-slate-400 font-normal">&mdash; <?php echo e($inv->timeSlot->name); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                        <p class="text-xs text-slate-400 truncate">
                            <?php echo e($inv->personnel ? trim($inv->personnel->first_name . ' ' . $inv->personnel->last_name) : 'Counselor TBA'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inv->purpose): ?> &mdash; <?php echo e(Str::limit($inv->purpose, 60)); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0
                                 <?php echo e($inv->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($inv->status === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600')); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inv->status === 'pending'): ?>
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <?php elseif($inv->status === 'accepted'): ?>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <?php else: ?>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php echo e(ucfirst($inv->status)); ?>

                    </span>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inv->status === 'pending'): ?>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <form method="POST" action="<?php echo e(route('referral.invitation.respond', $inv->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700 active:scale-95 transition">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Accept
                            </button>
                        </form>
                        <form method="POST" action="<?php echo e(route('referral.invitation.respond', $inv->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="status" value="declined">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 active:scale-95 transition">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Decline
                            </button>
                        </form>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        


        
        <?php
            $appointments = collect();
            if (auth()->check()) {
                $portalStudent = auth()->user()->student ?? null;
                if ($portalStudent) {
                    $appointments = \App\Models\CounselingAppointments::where('student_id', $portalStudent->id)
                        ->with(['timeSlot', 'modeOfCounseling', 'supportNeeded'])
                        ->orderBy('counseling_date', 'desc')
                        ->get();
                }
            }
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appointments->isNotEmpty()): ?>
        <div class="mb-10 fade-up delay-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-display text-lg font-bold text-slate-800">My Appointments</h2>
                    <p class="text-xs text-slate-500">Your scheduled counseling sessions.</p>
                </div>
                <?php $upcomingCount = $appointments->whereIn('status', ['pending', 'approved'])->count(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingCount > 0): ?>
                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
                        <?php echo e($upcomingCount); ?> Upcoming
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('appointment_cancelled')): ?>
                <div class="mb-3 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <?php echo e(session('appointment_cancelled')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statusColor = match($appt->status) {
                        'approved'  => ['border' => 'border-green-200',  'badge' => 'bg-green-100 text-green-700',  'dot' => null],
                        'pending'   => ['border' => 'border-amber-200',  'badge' => 'bg-amber-100 text-amber-700',  'dot' => 'bg-amber-500'],
                        'cancelled' => ['border' => 'border-slate-200',  'badge' => 'bg-slate-100 text-slate-500',  'dot' => null],
                        'completed' => ['border' => 'border-blue-200',   'badge' => 'bg-blue-100 text-blue-700',    'dot' => null],
                        'rejected'  => ['border' => 'border-red-200',    'badge' => 'bg-red-100 text-red-600',      'dot' => null],
                        default     => ['border' => 'border-slate-200',  'badge' => 'bg-slate-100 text-slate-500',  'dot' => null],
                    };
                ?>

                <div class="bg-white rounded-xl border shadow-sm px-4 py-3 flex items-center gap-3 flex-wrap <?php echo e($statusColor['border']); ?>">

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">
                            <?php echo e($appt->counseling_date->format('F d, Y')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appt->timeSlot): ?>
                                <span class="text-slate-400 font-normal">&mdash; <?php echo e($appt->timeSlot->name); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                        <p class="text-xs text-slate-400 truncate">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appt->modeOfCounseling): ?><?php echo e($appt->modeOfCounseling->name); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appt->supportNeeded): ?> &mdash; <?php echo e($appt->supportNeeded->name); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($appt->concern)): ?> &mdash; <?php echo e(Str::limit($appt->concern, 50)); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0 <?php echo e($statusColor['badge']); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appt->status === 'pending' && $statusColor['dot']): ?>
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($statusColor['dot']); ?> animate-pulse"></span>
                        <?php elseif($appt->status === 'approved'): ?>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <?php elseif(in_array($appt->status, ['cancelled', 'rejected'])): ?>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <?php elseif($appt->status === 'completed'): ?>
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php echo e(ucfirst($appt->status)); ?>

                    </span>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($appt->status, ['pending', 'approved'])): ?>
                    <button
                        type="button"
                        onclick="document.getElementById('cancel-modal-<?php echo e($appt->id); ?>').classList.remove('hidden')"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 active:scale-95 transition flex-shrink-0"
                    >
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </button>

                    
                    <div id="cancel-modal-<?php echo e($appt->id); ?>"
                         class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
                         style="background: rgba(15,23,42,0.45); backdrop-filter: blur(4px);">
                        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-sm p-6">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
                                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-display text-lg font-bold text-slate-900 text-center mb-1">Cancel Appointment?</h3>
                            <p class="text-sm text-slate-500 text-center mb-1">
                                <?php echo e($appt->counseling_date->format('F d, Y')); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appt->timeSlot): ?> &mdash; <?php echo e($appt->timeSlot->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p class="text-xs text-slate-400 text-center mb-6">
                                This action cannot be undone. You can book a new appointment afterwards.
                            </p>
                            <div class="flex gap-3">
                                <button
                                    type="button"
                                    onclick="document.getElementById('cancel-modal-<?php echo e($appt->id); ?>').classList.add('hidden')"
                                    class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 active:scale-95 transition"
                                >
                                    Keep It
                                </button>
                                <form method="POST" action="<?php echo e(route('guidance.appointment.cancel', $appt->id)); ?>" class="flex-1">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 active:scale-95 transition">
                                        Yes, Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        


        
        <div class="grid gap-6 sm:grid-cols-2">

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->role?->name !== 'guest'): ?>
            <a href="<?php echo e(route('guidance.appointment')); ?>"
               class="group relative bg-white rounded-2xl border border-green-200/60 shadow-sm p-10 flex flex-col items-center text-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-card-hover hover:border-emerald-300/60 fade-up delay-1">

                <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>

                <h2 class="font-display text-xl font-bold text-slate-900 mb-2 group-hover:text-emerald-800 transition-colors">
                    Book an Appointment
                </h2>
                <p class="text-sm text-slate-500 leading-relaxed mb-6">
                    Schedule a one-on-one session with a guidance counselor for academic, personal, or career concerns.
                </p>

                <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-btn-glow group-hover:bg-emerald-400 transition-colors">
                    Book Now
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <a href="<?php echo e(route('guidance_referrals.get')); ?>"
               class="group relative bg-white rounded-2xl border border-green-200/60 shadow-sm p-10 flex flex-col items-center text-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-card-hover hover:border-emerald-300/60 fade-up delay-2">

                <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <circle cx="18" cy="5" r="3"/>
                        <circle cx="6" cy="12" r="3"/>
                        <circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                </div>

                <h2 class="font-display text-xl font-bold text-slate-900 mb-2 group-hover:text-emerald-800 transition-colors">
                    Referrals
                </h2>
                <p class="text-sm text-slate-500 leading-relaxed mb-6">
                    Submit or view referral requests from faculty or staff for students who may need guidance support.
                </p>

                <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-900/80 border border-emerald-200/50 px-5 py-2.5 text-sm font-semibold text-emerald-50 group-hover:bg-emerald-800 transition-colors">
                    View Referrals
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>

        </div>

        <!-- Back to Home -->
        <div class="text-center mt-12 fade-up delay-4">
            <a href="<?php echo e(url('/gvc')); ?>" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-emerald-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Home
            </a>
        </div>

    </div>
</section>

<!-- FOOTER -->
<footer class="py-10 bg-slate-800 border-t border-green-300/20">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="text-slate-400 text-sm">© <?php echo e(date('Y')); ?> Green Valley College Foundation Inc. All rights reserved.</p>
    </div>
</footer>

</body>
</html><?php /**PATH /home/u476045238/domains/gvcfiguidancesc.com/public_html/resources/views/guidance.blade.php ENDPATH**/ ?>