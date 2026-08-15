<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship | Green Valley College Foundation</title>
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
                        'btn-glow':   '0 0 40px rgba(5,46,22,0.7), 0 10px 40px -10px rgba(20,83,45,0.6)'
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
        .fade-up  { animation: fadeUp 0.65s cubic-bezier(.22,1,.36,1) both; }
        .delay-1  { animation-delay: 0.10s; }
        .delay-2  { animation-delay: 0.22s; }
        .delay-3  { animation-delay: 0.34s; }
        .delay-4  { animation-delay: 0.46s; }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(12px); }
            to   { opacity: 1; transform: scale(1)    translateY(0); }
        }
        .modal-box { animation: modalIn 0.25s cubic-bezier(.22,1,.36,1) both; }

        @keyframes pulse-ring {
            0%   { transform: scale(1);   opacity: 0.6; }
            100% { transform: scale(2.2); opacity: 0; }
        }
        .pulse-dot::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: currentColor;
            animation: pulse-ring 1.4s ease-out infinite;
        }
        .pulse-dot { position: relative; display: inline-block; }

        .step-line {
            position: absolute;
            left: 11px;
            top: 24px;
            bottom: -8px;
            width: 2px;
            background: #e2e8f0;
        }
        .step-line.done { background: #16a34a; }
    </style>
</head>

<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

<?php
    $appPeriod = \App\Models\Period::scholarshipApplication();
    $reqPeriod = \App\Models\Period::scholarshipRequirement();
    $isStudent = auth()->user()->role && strtolower(auth()->user()->role->name) === 'student';

    // A student may already hold a Scholars record without ever having gone
    // through the Applicant flow (e.g. imported directly by the scholarship
    // office), so "already applied" alone isn't enough to gate re-applying.
    $scholarRecord    = \App\Models\Scholars::forUser(auth()->user());
    $isAlreadyScholar = (bool) $scholarRecord;
?>


<div id="already-applied-modal"
     style="display:none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeModal()">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="modal-box relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center z-10">
        <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-5">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none">
                <circle cx="15" cy="15" r="15" fill="#16a34a"/>
                <path d="M8 15l5 5 9-9" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alreadyApplied): ?>
            <h2 class="font-display text-xl font-bold text-slate-900 mb-2">Application Already Submitted</h2>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">
                Our records show that you have already submitted a scholarship application.
                Each account is allowed only <strong>one application</strong>. Please contact
                the scholarship office if you need to make changes.
            </p>
        <?php else: ?>
            <h2 class="font-display text-xl font-bold text-slate-900 mb-2">You're Already a Scholar</h2>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">
                Our records show you already hold an active scholarship
                (<?php echo e($scholarRecord->type_of_scholarship ?? 'Institutional'); ?>). There's no need to
                submit a new application. Please contact the scholarship office if you believe this is a mistake.
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <button onclick="closeModal()"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm px-6 py-3 transition-colors">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Got it, go back
        </button>
    </div>
</div>


<header class="bg-green-800 border-b border-white sticky top-0 z-40 shadow-sm shadow-green-900/5">
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
                    <button type="submit"
                            class="inline-flex items-center rounded-full border border-emerald-200/70 bg-emerald-900/40 px-4 py-1.5 text-xs sm:text-sm font-medium text-emerald-50 hover:bg-emerald-800/80 hover:border-emerald-200 transition">
                        Logout
                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
    </div>
</header>


<section class="relative overflow-hidden py-20 md:py-28">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.10]"
         style="background-image: url('<?php echo e(asset('images/gvc.png')); ?>');"></div>
    <div class="relative max-w-3xl mx-auto px-6 text-center fade-up">
        <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-900/60 border border-emerald-400/30 text-emerald-300 text-sm font-semibold mb-5 tracking-wide">
            Scholarship Office
        </span>
        <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-extrabold leading-tight mb-4 tracking-tight text-emerald-50 drop-shadow-md">
            Scholarship <br><span class="text-gvc-pale">Services</span>
        </h1>
        <p class="text-emerald-100/80 text-sm sm:text-base max-w-xl mx-auto">
            Submit your requirements and track your scholarship application status here.
        </p>
    </div>
</section>


<section class="flex-1 py-16 md:py-24 bg-gradient-to-b from-green-50/80 to-white">
    <div class="max-w-4xl mx-auto px-6">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alreadyApplied && $applicant): ?>
        <?php
            $status = $applicant->status ?? 'pending';

            $statusConfig = [
                'pending'  => [
                    'label'   => 'Under Review',
                    'desc'    => 'Your application has been received and is currently being reviewed by the scholarship office.',
                    'bg'      => 'bg-amber-50',
                    'border'  => 'border-amber-200',
                    'pill_bg' => 'bg-amber-100',
                    'pill_tx' => 'text-amber-800',
                    'pill_br' => 'border-amber-300',
                    'dot'     => 'bg-amber-400',
                    'icon_bg' => 'bg-amber-100',
                    'icon_tx' => 'text-amber-600',
                    'pulse'   => true,
                ],
                'approved' => [
                    'label'   => 'Approved',
                    'desc'    => 'Congratulations! Your scholarship application has been approved.',
                    'bg'      => 'bg-emerald-50',
                    'border'  => 'border-emerald-200',
                    'pill_bg' => 'bg-emerald-100',
                    'pill_tx' => 'text-emerald-800',
                    'pill_br' => 'border-emerald-300',
                    'dot'     => 'bg-emerald-500',
                    'icon_bg' => 'bg-emerald-100',
                    'icon_tx' => 'text-emerald-600',
                    'pulse'   => false,
                ],
                'rejected' => [
                    'label'   => 'Not Approved',
                    'desc'    => 'Unfortunately your application was not approved. Please contact the scholarship office for more information.',
                    'bg'      => 'bg-red-50',
                    'border'  => 'border-red-200',
                    'pill_bg' => 'bg-red-100',
                    'pill_tx' => 'text-red-800',
                    'pill_br' => 'border-red-300',
                    'dot'     => 'bg-red-500',
                    'icon_bg' => 'bg-red-100',
                    'icon_tx' => 'text-red-500',
                    'pulse'   => false,
                ],
            ];
            $cfg = $statusConfig[$status] ?? $statusConfig['pending'];

            $appType  = $applicant->typeOfApplication->name ?? 'N/A';
            $schType  = $applicant->typeOfScholarship->name ?? 'N/A';
            $program  = $applicant->program->name           ?? 'N/A';
            $initials = strtoupper(substr($applicant->first_name,0,1).substr($applicant->last_name,0,1));

            $steps = [
                ['label' => 'Application Submitted', 'done' => true],
                ['label' => 'Under Review',           'done' => in_array($status, ['pending','approved','rejected'])],
                ['label' => 'Decision Released',      'done' => in_array($status, ['approved','rejected'])],
                ['label' => 'Scholarship Granted',    'done' => $status === 'approved'],
            ];
        ?>

        <div class="fade-up mb-8 rounded-2xl border <?php echo e($cfg['border']); ?> <?php echo e($cfg['bg']); ?> shadow-sm overflow-hidden">

            
            <div class="flex items-center justify-between gap-4 px-6 py-4 border-b <?php echo e($cfg['border']); ?> flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl <?php echo e($cfg['icon_bg']); ?> flex items-center justify-center flex-shrink-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status === 'approved'): ?>
                            <svg class="w-5 h-5 <?php echo e($cfg['icon_tx']); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        <?php elseif($status === 'rejected'): ?>
                            <svg class="w-5 h-5 <?php echo e($cfg['icon_tx']); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 <?php echo e($cfg['icon_tx']); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Application Status</p>
                        <p class="font-display text-sm font-bold text-slate-800"><?php echo e($applicant->first_name); ?> <?php echo e($applicant->last_name); ?></p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-2 rounded-full <?php echo e($cfg['pill_bg']); ?> border <?php echo e($cfg['pill_br']); ?> px-4 py-1.5 text-xs font-bold <?php echo e($cfg['pill_tx']); ?> tracking-wide">
                    <span class="relative inline-flex w-2 h-2 rounded-full <?php echo e($cfg['dot']); ?> <?php echo e($cfg['pulse'] ? 'pulse-dot' : ''); ?>"></span>
                    <?php echo e($cfg['label']); ?>

                </span>
            </div>

            
            <div class="p-6 grid md:grid-cols-3 gap-6">

                <div class="md:col-span-2 flex flex-col gap-5">
                    <p class="text-sm text-slate-600 leading-relaxed"><?php echo e($cfg['desc']); ?></p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="bg-white/70 rounded-xl p-3 border border-white">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Application Type</p>
                            <p class="text-sm font-semibold text-slate-700"><?php echo e($appType); ?></p>
                        </div>
                        <div class="bg-white/70 rounded-xl p-3 border border-white">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Scholarship Type</p>
                            <p class="text-sm font-semibold text-slate-700"><?php echo e($schType); ?></p>
                        </div>
                        <div class="bg-white/70 rounded-xl p-3 border border-white">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Program</p>
                            <p class="text-sm font-semibold text-slate-700"><?php echo e($program); ?> <?php echo e($applicant->year_level); ?></p>
                        </div>
                        <div class="bg-white/70 rounded-xl p-3 border border-white">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Submitted On</p>
                            <p class="text-sm font-semibold text-slate-700">
                                <?php echo e($applicant->created_at ? $applicant->created_at->format('M d, Y') : '—'); ?>

                            </p>
                        </div>
                        <div class="bg-white/70 rounded-xl p-3 border border-white">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Last Updated</p>
                            <p class="text-sm font-semibold text-slate-700">
                                <?php echo e($applicant->updated_at ? $applicant->updated_at->format('M d, Y') : '—'); ?>

                            </p>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($applicant->benefit): ?>
                        <div class="bg-white/70 rounded-xl p-3 border border-white">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Benefit</p>
                            <p class="text-sm font-semibold text-slate-700"><?php echo e($applicant->benefit); ?></p>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="flex flex-col gap-0">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Progress</p>
                    <div class="flex flex-col gap-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isLast = $i === count($steps) - 1; ?>
                        <div class="flex items-start gap-3 relative" style="padding-bottom: <?php echo e($isLast ? '0' : '20px'); ?>;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isLast): ?>
                                <div class="absolute left-[11px] top-6 bottom-0 w-0.5 <?php echo e($step['done'] ? 'bg-emerald-400' : 'bg-slate-200'); ?>"></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center z-10
                                <?php echo e($step['done'] ? 'bg-emerald-500' : 'bg-white border-2 border-slate-300'); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step['done']): ?>
                                    <svg width="10" height="10" fill="none" viewBox="0 0 10 10">
                                        <path d="M2 5l2.2 2.2 3.8-3.8" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="pt-0.5">
                                <p class="text-xs font-semibold <?php echo e($step['done'] ? 'text-emerald-700' : 'text-slate-400'); ?>">
                                    <?php echo e($step['label']); ?>

                                </p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alreadyApplied || $isAlreadyScholar): ?>
                <div onclick="openModal()"
                     class="group relative bg-white rounded-2xl border border-green-200/60 shadow-sm p-10 flex flex-col items-center text-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-card-hover hover:border-emerald-300/60 fade-up delay-2 cursor-pointer">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="font-display text-xl font-bold text-slate-900 mb-2 group-hover:text-emerald-800 transition-colors">Apply Scholarship</h2>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alreadyApplied): ?>
                            Your application has been submitted. Contact the scholarship office for any changes.
                        <?php else: ?>
                            You're already an active scholar. Contact the scholarship office if you need to make changes.
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-100 border border-emerald-300 px-5 py-2.5 text-sm font-semibold text-emerald-700">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <circle cx="7" cy="7" r="7" fill="#16a34a"/>
                            <path d="M3.5 7l2.5 2.5 4.5-4.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alreadyApplied): ?>
                            Already Submitted
                        <?php else: ?>
                            Already a Scholar
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
            <?php elseif($appPeriod->is_open): ?>
                <a href="<?php echo e(route('application_new.get')); ?>"
                   class="group relative bg-white rounded-2xl border border-green-200/60 shadow-sm p-10 flex flex-col items-center text-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-card-hover hover:border-emerald-300/60 fade-up delay-2">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h2 class="font-display text-xl font-bold text-slate-900 mb-2 group-hover:text-emerald-800 transition-colors">Apply Scholarship</h2>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Fill out and submit your scholarship application form to get started.
                    </p>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-900/80 border border-emerald-200/50 px-5 py-2.5 text-sm font-semibold text-emerald-50 group-hover:bg-emerald-800 transition-colors">
                        Apply Now
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            <?php else: ?>
                <div class="group relative bg-white rounded-2xl border border-gray-200 shadow-sm p-10 flex flex-col items-center text-center fade-up delay-2 opacity-60 cursor-not-allowed">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h2 class="font-display text-xl font-bold text-slate-900 mb-2">Apply Scholarship</h2>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        The application period is currently closed.
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appPeriod->open_date): ?>
                            Opens on <?php echo e($appPeriod->opensOnLabel()); ?>.
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-gray-100 border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-500">
                        Period Closed
                    </span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reqPeriod->is_open): ?>
                <a href="<?php echo e(route('requirements_submission.get')); ?>"
                   class="group relative bg-white rounded-2xl border border-green-200/60 shadow-sm p-10 flex flex-col items-center text-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-card-hover hover:border-emerald-300/60 fade-up delay-1">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="font-display text-xl font-bold text-slate-900 mb-2 group-hover:text-emerald-800 transition-colors">
                        Submit Requirements
                    </h2>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Upload and submit your scholarship requirements and letter of intent for processing.
                    </p>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-btn-glow group-hover:bg-emerald-400 transition-colors">
                        Submit Now
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>
            <?php else: ?>
                <div class="group relative bg-white rounded-2xl border border-gray-200 shadow-sm p-10 flex flex-col items-center text-center fade-up delay-1 opacity-60 cursor-not-allowed">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="font-display text-xl font-bold text-slate-900 mb-2">Submit Requirements</h2>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        The submission period is currently closed.
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reqPeriod->open_date): ?>
                            Opens on <?php echo e($reqPeriod->opensOnLabel()); ?>.
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                    <span class="inline-flex items-center gap-2 rounded-xl bg-gray-100 border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-500">
                        Period Closed
                    </span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isStudent): ?>
            <a href="<?php echo e(route('accomplishment_reports.get')); ?>"
               class="group relative bg-white rounded-2xl border border-green-200/60 shadow-sm p-10 flex flex-col items-center text-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-card-hover hover:border-emerald-300/60 fade-up delay-3">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 group-hover:bg-emerald-200 transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h4m-4-4l4 4-4 4M3 21h18a2 2 0 002-2V7a2 2 0 00-2-2h-5.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 0014.586 3H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-2 group-hover:text-emerald-800 transition-colors">
                    Accomplishment Reports
                </h2>
                <p class="text-sm text-slate-500 leading-relaxed mb-6">
                    For Talents, SSG, and Sports scholars — submit proof of accomplishments to maintain your scholarship.
                </p>
                <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-900/80 border border-emerald-200/50 px-5 py-2.5 text-sm font-semibold text-emerald-50 group-hover:bg-emerald-800 transition-colors">
                    Go to Reports
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>

        
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


<footer class="py-10 bg-slate-800 border-t border-green-300/20">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <p class="text-slate-400 text-sm">© <?php echo e(date('Y')); ?> Green Valley College Foundation Inc. All rights reserved.</p>
    </div>
</footer>

<script>
    function openModal() {
        document.getElementById('already-applied-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('already-applied-modal').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

</body>
</html><?php /**PATH C:\xampp\htdocs\sample\resources\views/scholarship.blade.php ENDPATH**/ ?>