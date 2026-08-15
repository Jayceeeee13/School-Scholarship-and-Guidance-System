<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Green Valley College Foundation</title>
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
</head>
<body class="bg-emerald-950/5 text-slate-800 font-sans antialiased">

<!-- NAVBAR -->
<header class="bg-green-800 border-b border-white sticky top-0 z-40 shadow-sm shadow-green-900/5">
    <div class="max-w-8xl mx-auto px-6 py-3 flex flex-wrap justify-between items-center gap-4">
        <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2 group flex-shrink-0">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Green Valley College Foundation" class="w-10 h-10 rounded-lg object-contain flex-shrink-0">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight whitespace-nowrap">
                Green Valley College Foundation Inc.
            </span>
        </a>
        <nav class="flex items-center gap-2 sm:gap-3">
            <a href="<?php echo e(route('register')); ?>"
               class="inline-flex items-center rounded-full bg-emerald-400 px-4 py-1.5 text-xs sm:text-sm font-semibold text-emerald-950 shadow-btn-glow hover:bg-emerald-300 transition">
                Register
            </a>
        </nav>
    </div>
</header>

<!-- HERO BACKGROUND -->
<section class="relative min-h-[calc(100vh-64px)] flex items-center">
    <div class="absolute inset-0 bg-hero-gradient"></div>
    <div class="absolute inset-0 bg-hero-pattern bg-repeat opacity-60"></div>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-[0.08]" style="background-image: url('<?php echo e(asset('images/gvc.png')); ?>');"></div>

    <div class="relative max-w-6xl mx-auto px-6 py-12 grid gap-10 lg:grid-cols-[minmax(0,1.2fr),minmax(0,1fr)] items-center">
        <div class="text-emerald-50 space-y-4 max-w-xl">
            <p class="inline-flex items-center rounded-full bg-emerald-900/40 border border-emerald-300/40 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide">
                Student Portal
            </p>
            <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight drop-shadow-md">
                Login to your<br class="hidden sm:block"> Scholarship & Guidance account
            </h1>
            <p class="text-sm sm:text-base text-emerald-100/90">
                Access your scholarship applications, renewal status, and guidance appointments all in one place.
            </p>
        </div>

        <div class="w-full max-w-md ml-auto bg-white/95 backdrop-blur rounded-2xl shadow-xl shadow-emerald-950/40 border border-emerald-200/70 p-6 md:p-8">
            <h2 class="font-display text-xl md:text-2xl font-semibold mb-1 text-slate-900">Login</h2>
            <p class="text-sm text-slate-600 mb-5">Sign in using your registered email and password.</p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-3">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm p-3">
                    <ul class="list-disc list-inside space-y-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form method="POST" action="<?php echo e(route('login.post')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="<?php echo e(old('email')); ?>"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"
                >
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 pr-10 text-sm shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"
                    >
                    <button
                        type="button"
                        onclick="togglePassword('password', this)"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-emerald-700 transition-colors"
                        aria-label="Toggle password visibility"
                    >
                        <!-- Eye icon (show) -->
                        <svg class="eye-icon w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <!-- Eye-slash icon (hide) — hidden by default -->
                        <svg class="eye-slash-icon w-4 h-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="w-full inline-flex items-center justify-center rounded-lg bg-emerald-700 hover:bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white shadow-btn-glow transition"
            >
                Login
            </button>
            <p class="mt-3 text-xs text-slate-500 text-center">
                Don't have an account?
                <a href="<?php echo e(route('register')); ?>" class="text-emerald-700 font-semibold hover:text-emerald-500">Register</a>
            </p>
        </form>
        </div>
    </div>
</section>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const eyeIcon = btn.querySelector('.eye-icon');
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
</html><?php /**PATH /home/u476045238/domains/gvcfiguidancesc.com/public_html/resources/views/login.blade.php ENDPATH**/ ?>