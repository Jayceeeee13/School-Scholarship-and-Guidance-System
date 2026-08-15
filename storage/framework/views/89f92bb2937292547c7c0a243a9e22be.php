<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission & Scholarship Test | Green Valley College Foundation</title>
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
                    }
                }
            }
        }
    </script>

    <script>
        function enterFullscreen() {
            const el = document.documentElement;
            if (el.requestFullscreen) el.requestFullscreen();
            else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
            else if (el.mozRequestFullScreen) el.mozRequestFullScreen();
            else if (el.msRequestFullscreen) el.msRequestFullscreen();
        }

        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) setTimeout(() => enterFullscreen(), 300);
        });
        document.addEventListener('webkitfullscreenchange', () => {
            if (!document.webkitFullscreenElement) setTimeout(() => enterFullscreen(), 300);
        });

        document.addEventListener('keydown', (e) => {
            if (
                e.key === 'F11' ||
                (e.altKey && e.key === 'F4') ||
                (e.ctrlKey && e.key === 'w') ||
                (e.metaKey && e.key === 'w') ||
                (e.ctrlKey && e.key === 'l') ||
                e.key === 'Escape'
            ) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);

        document.addEventListener('contextmenu', e => e.preventDefault());
    </script>

    <style>
        @keyframes progress-fill { from { width: 0%; } }
        .progress-bar { animation: progress-fill 0.5s ease-out; }

        .choice-label {
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .choice-label:hover { transform: translateX(4px); }

        @keyframes pulse-red {
            0%, 100% { color: #ef4444; }
            50%       { color: #dc2626; }
        }
        .timer-warning { animation: pulse-red 1s infinite; }

        html { scroll-behavior: smooth; }
        #fs-prompt { font-family: 'Outfit', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen"
      x-data="examApp()">


<div id="fs-prompt"
     class="fixed inset-0 z-[9999] bg-green-900 flex flex-col items-center justify-center text-white px-6 text-center">

    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="GVCFI" class="w-20 h-20 rounded-2xl object-contain mb-6 shadow-lg">

    <h1 class="text-2xl md:text-3xl font-bold mb-2"><?php echo e($exam->title); ?></h1>
    <p class="text-green-200 text-sm mb-1">Green Valley College Foundation Inc.</p>
    <p class="text-green-300 text-xs mb-8"><?php echo e($exam->duration_minutes); ?> minutes &bull; <?php echo e($exam->questions->count()); ?> items</p>

    <div class="bg-white/10 border border-white/20 rounded-2xl p-5 max-w-sm w-full mb-8 text-sm text-green-100 space-y-2 text-left">
        <p class="font-semibold text-white text-base mb-3">📋 Before you begin:</p>
        <p>✅ The exam will run in <strong>fullscreen mode</strong>.</p>
        <p>✅ Do not close or reload the browser.</p>
        <p>✅ Timer starts immediately after you click Start.</p>
        <p>✅ Unanswered items are counted as incorrect.</p>
        <p>⚠️ <strong>Switching tabs or windows will be recorded as a violation.</strong></p>
    </div>

    <button onclick="startExam()"
        class="bg-white text-green-900 font-bold text-base px-10 py-3.5 rounded-2xl shadow-lg hover:bg-green-100 transition active:scale-95">
        🚀 Start Exam
    </button>

    <p class="text-green-400 text-xs mt-4">Examinee: <strong class="text-white"><?php echo e(auth()->user()->name); ?></strong></p>
</div>

<script>
    function startExam() {
        enterFullscreen();
        document.getElementById('fs-prompt').style.display = 'none';
    }
</script>


<header class="bg-green-800 border-b border-white sticky top-0 z-50 shadow-sm">
    <div class="max-w-8xl mx-auto px-6 py-3 flex justify-between items-center gap-4">
        <a href="#" onclick="return false;" class="flex items-center gap-2">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="GVCFI" class="w-10 h-10 rounded-lg object-contain">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight">
                Green Valley College Foundation Inc.
            </span>
        </a>
        
        <div :class="timeLeft <= 300 ? 'timer-warning' : 'text-emerald-100'"
             class="flex items-center gap-2 font-display font-bold text-lg">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="formatTime(timeLeft)"></span>
        </div>
    </div>
</header>


<div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-4 gap-6">

    
    <aside class="lg:col-span-1 space-y-4 lg:sticky lg:top-20 lg:self-start">

        
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-sm text-slate-800"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-slate-400">Examinee</p>
                </div>
            </div>
            <div class="text-xs text-slate-500 space-y-1">
                <p>Exam: <span class="font-medium text-slate-700"><?php echo e($exam->title); ?></span></p>
                <p>Duration: <span class="font-medium text-slate-700"><?php echo e($exam->duration_minutes); ?> mins</span></p>
                <p>Total items: <span class="font-medium text-slate-700"><?php echo e($exam->questions->count()); ?></span></p>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-slate-600">Overall Progress</span>
                <span class="text-xs font-bold text-green-700" x-text="answeredCount() + '/' + totalQuestions()"></span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full progress-bar transition-all duration-300"
                     :style="'width:' + progressPercent() + '%'"></div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Categories</p>
            <div class="space-y-1">
                <template x-for="(cat, idx) in categories" :key="idx">
                    <button
                        @click="scrollToCategory(idx)"
                        :class="activeCategory === idx ? 'bg-green-800 text-white' : 'hover:bg-green-50 text-slate-700'"
                        class="w-full text-left px-3 py-2 rounded-xl text-xs font-medium transition flex justify-between items-center">
                        <span x-text="cat.name"></span>
                        <span :class="activeCategory === idx ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-500'"
                              class="text-xs px-1.5 py-0.5 rounded-full font-semibold"
                              x-text="categoryAnswered(idx) + '/' + cat.questions.length"></span>
                    </button>
                </template>
            </div>
        </div>

        
        <button
            @click="confirmSubmit()"
            class="w-full bg-green-800 hover:bg-green-700 text-white font-display font-bold py-3 px-4 rounded-2xl transition shadow-sm text-sm">
            Submit Exam
        </button>

    </aside>

    
    <main class="lg:col-span-3 space-y-8">

        <template x-for="(cat, catIdx) in categories" :key="catIdx">
            <div :id="'category-' + catIdx">

                
                <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-5 mb-4">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1"
                               x-text="'Category ' + (catIdx + 1) + ' of ' + categories.length"></p>
                            <h2 class="font-display text-xl font-bold text-slate-800" x-text="cat.name"></h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-green-100 text-green-700 font-semibold px-3 py-1 rounded-full"
                                  x-text="cat.questions.length + ' items'"></span>
                            <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full"
                                  x-text="categoryAnswered(catIdx) + ' answered'"></span>
                        </div>
                    </div>
                    <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300"
                             :style="'width:' + (categoryAnswered(catIdx) / cat.questions.length * 100) + '%'"></div>
                    </div>
                </div>

                
                <div class="space-y-4">
                    <template x-for="(q, qIdx) in cat.questions" :key="q.id">
                        <div class="bg-white rounded-2xl border-2 shadow-sm p-6 transition-all duration-200"
                             :class="isAnswered(q.id) ? 'border-green-300' : 'border-slate-200'">

                            <div class="flex gap-4 mb-5">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl font-display font-bold text-sm flex items-center justify-center transition-all"
                                     :class="isAnswered(q.id) ? 'bg-green-600 text-white' : 'bg-green-800 text-white'"
                                     x-text="qIdx + 1"></div>
                                <p class="text-slate-800 font-medium leading-relaxed pt-1.5" x-text="q.question"></p>
                            </div>

                            <div class="space-y-2 ml-14">
                                <template x-for="choice in q.choices" :key="choice.id">
                                    <div class="relative">
                                        <input
                                            type="radio"
                                            :name="'question_' + q.id"
                                            :id="'choice_' + choice.id"
                                            :value="choice.id"
                                            class="sr-only"
                                            x-model="answers[q.id]"
                                        >
                                        <label
                                            :for="'choice_' + choice.id"
                                            :class="answers[q.id] == choice.id
                                                ? 'bg-green-800 text-white border-green-800'
                                                : 'border-slate-200 hover:border-green-300 hover:bg-green-50'"
                                            class="choice-label flex items-center gap-3 p-3 rounded-xl border-2 w-full">
                                            <span
                                                :class="answers[q.id] == choice.id
                                                    ? 'bg-green-400 text-green-950'
                                                    : 'bg-slate-100 text-slate-600'"
                                                class="flex-shrink-0 w-8 h-8 rounded-lg font-display font-bold text-sm flex items-center justify-center transition"
                                                x-text="choice.choice_letter">
                                            </span>
                                            <span class="text-sm font-medium" x-text="choice.choice_text"></span>
                                        </label>
                                    </div>
                                </template>
                            </div>

                            <div class="ml-14 mt-3" x-show="isAnswered(q.id)">
                                <span class="text-xs text-green-600 font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Answered
                                </span>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </template>

        
        <div class="flex justify-end pt-2 pb-8">
            <button @click="confirmSubmit()"
                class="bg-green-800 hover:bg-green-700 text-white font-display font-bold py-3 px-8 rounded-2xl transition shadow-sm text-sm">
                Submit Exam
            </button>
        </div>

    </main>
</div>


<div x-show="showSubmitModal"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="text-center mb-5">
            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-display text-xl font-bold text-slate-800 mb-2">Submit Exam?</h3>
            <p class="text-sm text-slate-500">
                You have answered
                <span class="font-bold text-green-700" x-text="answeredCount()"></span>
                out of
                <span class="font-bold" x-text="totalQuestions()"></span>
                questions.
            </p>

            <template x-if="answeredCount() < totalQuestions()">
                <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700">
                    ⚠️ You have <span x-text="totalQuestions() - answeredCount()"></span> unanswered question(s). These will be marked as incorrect.
                </div>
            </template>
        </div>

        <div class="flex gap-3">
            <button @click="showSubmitModal = false"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                Continue Exam
            </button>
            <form method="POST" action="<?php echo e(route('exam.submit', $exam->id)); ?>" id="submit-form" class="flex-1">
                <?php echo csrf_field(); ?>
                <div id="answers-container"></div>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-green-800 hover:bg-green-700 text-white font-semibold text-sm transition">
                    Submit Now
                </button>
            </form>
        </div>
    </div>
</div>


<div x-show="timeUp"
     class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
     style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="font-display text-xl font-bold text-slate-800 mb-2">Time's Up!</h3>
        <p class="text-sm text-slate-500 mb-5">Your exam is being submitted automatically.</p>
        <form method="POST" action="<?php echo e(route('exam.submit', $exam->id)); ?>" id="auto-submit-form">
            <?php echo csrf_field(); ?>
            <div id="auto-answers-container"></div>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 text-white font-semibold text-sm">
                Submitting...
            </button>
        </form>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function examApp() {
    return {
        categories: <?php echo json_encode($categories, 15, 512) ?>,
        answers: {},
        timeLeft: <?php echo e($exam->duration_minutes * 60); ?>,
        showSubmitModal: false,
        timeUp: false,
        timer: null,
        activeCategory: 0,

        // Silent violation tracking — nothing shown to examinee
        lastViolationTime: {},

        init() {
            // Timer
            this.timer = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    clearInterval(this.timer);
                    this.timeUp = true;
                    this.buildAnswerInputs('auto-answers-container');
                    this.$nextTick(() => {
                        document.getElementById('auto-submit-form').submit();
                    });
                }
            }, 1000);

            // Tab switch — fires when tab becomes hidden
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) this.trackViolation('tab_switch');
            });

            // Window blur — skip if tab_switch just fired (they always fire together)
            window.addEventListener('blur', () => {
                if (!this.recentViolation('tab_switch')) {
                    this.trackViolation('window_blur');
                }
            });

            // Clipboard attempts
            document.addEventListener('copy',  () => this.trackViolation('copy_attempt'));
            document.addEventListener('paste', () => this.trackViolation('paste_attempt'));
            document.addEventListener('cut',   () => this.trackViolation('cut_attempt'));

            // Scroll-based active category tracking
            this.$nextTick(() => {
                this.categories.forEach((cat, idx) => {
                    const el = document.getElementById('category-' + idx);
                    if (!el) return;
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) this.activeCategory = idx;
                        });
                    }, { threshold: 0.2 });
                    observer.observe(el);
                });
            });
        },

        // Returns true if the same violation type fired within the last 1500ms
        recentViolation(type) {
            const last = this.lastViolationTime[type] ?? 0;
            return (Date.now() - last) < 1500;
        },

        trackViolation(type) {
            // Deduplicate: ignore if same type fired within last 1500ms
            if (this.recentViolation(type)) return;
            this.lastViolationTime[type] = Date.now();

            const entry = {
                type:     type,
                time:     new Date().toISOString(),
                timeLeft: this.timeLeft,
            };

            // Send silently to server — no UI feedback shown to examinee
            fetch('<?php echo e(route("exam.violation", $exam->id)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: JSON.stringify({ violation: entry }),
            }).catch(() => {
                // Fail silently
            });
        },

        formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) return `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        },

        isAnswered(questionId) {
            return this.answers[questionId] !== undefined && this.answers[questionId] !== null;
        },

        answeredCount() {
            return Object.keys(this.answers).filter(k => this.answers[k] !== null && this.answers[k] !== undefined).length;
        },

        totalQuestions() {
            return this.categories.reduce((sum, cat) => sum + cat.questions.length, 0);
        },

        progressPercent() {
            return Math.round((this.answeredCount() / this.totalQuestions()) * 100);
        },

        categoryAnswered(idx) {
            return this.categories[idx].questions.filter(q => this.isAnswered(q.id)).length;
        },

        scrollToCategory(idx) {
            this.activeCategory = idx;
            const el = document.getElementById('category-' + idx);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        buildAnswerInputs(containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            Object.entries(this.answers).forEach(([questionId, choiceId]) => {
                if (choiceId !== null && choiceId !== undefined) {
                    const input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = `answers[${questionId}]`;
                    input.value = choiceId;
                    container.appendChild(input);
                }
            });
        },

        confirmSubmit() {
            this.buildAnswerInputs('answers-container');
            this.showSubmitModal = true;
        },
    };
}
</script>

</body>
</html><?php /**PATH /home/u476045238/domains/gvcfiguidancesc.com/public_html/resources/views/exam/show.blade.php ENDPATH**/ ?>