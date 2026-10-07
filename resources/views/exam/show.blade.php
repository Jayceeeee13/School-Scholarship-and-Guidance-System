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
        [x-cloak] { display: none !important; }

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
        #fs-prompt { font-family: 'Outfit', sans-serif; transition: opacity .35s ease; }

        /* Question cards lift slightly on hover */
        .q-card { transition: border-color .2s ease, box-shadow .25s ease, transform .25s ease; }
        .q-card:hover { box-shadow: 0 14px 30px -14px rgba(20,83,45,.25); }

        /* Only animate if the visitor allows motion */
        @media (prefers-reduced-motion: no-preference) {

            /* Entrance animations (staggered with --d). "backwards" releases the
               element afterwards so hover effects keep working. */
            @keyframes riseIn {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes dropIn {
                from { opacity: 0; transform: translateY(-100%); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes popIn {
                from { opacity: 0; transform: scale(.85); }
                to   { opacity: 1; transform: none; }
            }
            @keyframes slideInLeft {
                from { opacity: 0; transform: translateX(-28px); }
                to   { opacity: 1; transform: none; }
            }
            .rise    { animation: riseIn .75s cubic-bezier(.22,1,.36,1) backwards; animation-delay: var(--d, 0ms); }
            .pop     { animation: popIn .5s cubic-bezier(.34,1.56,.64,1) backwards; animation-delay: var(--d, 0ms); }
            .slide-l { animation: slideInLeft .7s cubic-bezier(.22,1,.36,1) backwards; animation-delay: var(--d, 0ms); }
            .drop    { animation: dropIn .7s cubic-bezier(.22,1,.36,1) backwards; }

            /* Small pop when a choice is selected */
            @keyframes choicePop {
                0%   { transform: scale(.97); }
                60%  { transform: scale(1.015); }
                100% { transform: scale(1); }
            }
            input:checked + .choice-label { animation: choicePop .3s ease-out; }

            /* "Answered" tag pops in each time it appears */
            .pop-in { animation: popIn .35s cubic-bezier(.34,1.56,.64,1) backwards; }

            /* Fullscreen prompt: floating logo + glowing start button */
            @keyframes bob {
                0%, 100% { translate: 0 0; }
                50%      { translate: 0 -8px; }
            }
            .bob { animation: bob 3.5s ease-in-out infinite; }

            @keyframes startGlow {
                0%, 100% { box-shadow: 0 0 0 0 rgba(187,247,208,.5); }
                50%      { box-shadow: 0 0 0 14px rgba(187,247,208,0); }
            }
            .start-glow { animation: startGlow 2s ease-in-out infinite; }

            /* Blurred glow behind the fullscreen prompt */
            @keyframes floatSlow {
                0%, 100% { translate: 0 0; }
                50%      { translate: 26px -30px; }
            }
            .float-a { animation: floatSlow 12s ease-in-out infinite; }
            .float-b { animation: floatSlow 16s ease-in-out infinite reverse; }

            /* Time's up icon pulse */
            @keyframes alertPulse {
                0%, 100% { transform: scale(1); }
                50%      { transform: scale(1.12); }
            }
            .alert-pulse { animation: alertPulse 1.2s ease-in-out infinite; }

            /* Submit modal check icon */
            .bob-soft { animation: bob 3s ease-in-out infinite; }

            /* Button shine sweep on hover */
            .btn-shine { position: relative; overflow: hidden; }
            .btn-shine::before {
                content: ""; position: absolute; top: 0; left: -75%; width: 50%; height: 100%;
                background: linear-gradient(120deg, transparent, rgba(255,255,255,.3), transparent);
                transform: skewX(-20deg);
            }
            .btn-shine:hover::before { left: 130%; transition: left .7s ease; }
            .btn-shine:active { transform: scale(.98); }

            /* Logo hover */
            .logo-img { transition: transform .3s ease; }
            a:hover > .logo-img { transform: rotate(6deg) scale(1.1); }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen"
      x-data="examApp()">

{{-- FULLSCREEN PROMPT --}}
<div id="fs-prompt"
     class="fixed inset-0 z-[9999] bg-green-900 flex flex-col items-center justify-center text-white px-6 text-center overflow-hidden">

    <div class="float-a absolute -left-24 top-10 h-96 w-96 rounded-full bg-emerald-400/20 blur-3xl"></div>
    <div class="float-b absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-lime-300/10 blur-3xl"></div>

    <img src="{{ asset('images/logo.png') }}" alt="GVCFI" class="pop bob relative w-20 h-20 rounded-2xl object-contain mb-6 shadow-lg" style="--d:100ms">

    <h1 class="rise relative text-2xl md:text-3xl font-bold mb-2" style="--d:250ms">{{ $exam->title }}</h1>
    <p class="rise relative text-green-200 text-sm mb-1" style="--d:350ms">Green Valley College Foundation Inc.</p>
    <p class="rise relative text-green-300 text-xs mb-8" style="--d:430ms">{{ $exam->duration_minutes }} minutes &bull; {{ $exam->questions->count() }} items</p>

    <div class="rise relative bg-white/10 border border-white/20 rounded-2xl p-5 max-w-sm w-full mb-8 text-sm text-green-100 space-y-2 text-left backdrop-blur" style="--d:520ms">
        <p class="font-semibold text-white text-base mb-3">📋 Before you begin:</p>
        <p class="slide-l" style="--d:650ms">✅ The exam will run in <strong>fullscreen mode</strong>.</p>
        <p class="slide-l" style="--d:730ms">✅ Do not close or reload the browser.</p>
        <p class="slide-l" style="--d:810ms">✅ Timer starts immediately after you click Start.</p>
        <p class="slide-l" style="--d:890ms">✅ Unanswered items are counted as incorrect.</p>
        <p class="slide-l" style="--d:970ms">⚠️ <strong>Switching tabs or windows will be recorded as a violation.</strong></p>
    </div>

    <button onclick="startExam()"
        class="rise start-glow btn-shine relative bg-white text-green-900 font-bold text-base px-10 py-3.5 rounded-2xl shadow-lg hover:bg-green-100 hover:-translate-y-0.5 transition active:scale-95"
        style="--d:1100ms">
        🚀 Start Exam
    </button>

    <p class="rise relative text-green-400 text-xs mt-4" style="--d:1250ms">Examinee: <strong class="text-white">{{ auth()->user()->name }}</strong></p>
</div>

<script>
    function startExam() {
        enterFullscreen();
        const prompt = document.getElementById('fs-prompt');
        prompt.style.opacity = '0';
        prompt.style.pointerEvents = 'none';
        setTimeout(() => { prompt.style.display = 'none'; }, 350);
    }
</script>

{{-- NAVBAR --}}
<header class="drop bg-green-800 border-b border-white sticky top-0 z-50 shadow-sm">
    <div class="max-w-8xl mx-auto px-6 py-3 flex justify-between items-center gap-4">
        <a href="#" onclick="return false;" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="GVCFI" class="logo-img w-10 h-10 rounded-lg object-contain">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight">
                Green Valley College Foundation Inc.
            </span>
        </a>
        {{-- Timer only — no violation counter shown to examinee --}}
        <div :class="timeLeft <= 300 ? 'timer-warning' : 'text-emerald-100'"
             class="flex items-center gap-2 font-display font-bold text-lg">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-text="formatTime(timeLeft)"></span>
        </div>
    </div>
</header>

{{-- MAIN LAYOUT --}}
<div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- LEFT SIDEBAR --}}
    <aside class="lg:col-span-1 space-y-4 lg:sticky lg:top-20 lg:self-start">

        {{-- Student Info --}}
        <div class="slide-l bg-white rounded-2xl border border-green-200/60 shadow-sm p-4" style="--d:150ms">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-sm text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">Examinee</p>
                </div>
            </div>
            <div class="text-xs text-slate-500 space-y-1">
                <p>Exam: <span class="font-medium text-slate-700">{{ $exam->title }}</span></p>
                <p>Duration: <span class="font-medium text-slate-700">{{ $exam->duration_minutes }} mins</span></p>
                <p>Total items: <span class="font-medium text-slate-700">{{ $exam->questions->count() }}</span></p>
            </div>
        </div>

        {{-- Overall Progress --}}
        <div class="slide-l bg-white rounded-2xl border border-green-200/60 shadow-sm p-4" style="--d:250ms">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-slate-600">Overall Progress</span>
                <span class="text-xs font-bold text-green-700" x-text="answeredCount() + '/' + totalQuestions()"></span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-400 h-2 rounded-full progress-bar transition-all duration-500"
                     :style="'width:' + progressPercent() + '%'"></div>
            </div>
        </div>

        {{-- Categories Jump Links --}}
        <div class="slide-l bg-white rounded-2xl border border-green-200/60 shadow-sm p-4" style="--d:350ms">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Categories</p>
            <div class="space-y-1">
                <template x-for="(cat, idx) in categories" :key="idx">
                    <button
                        @click="scrollToCategory(idx)"
                        :class="activeCategory === idx ? 'bg-green-800 text-white' : 'hover:bg-green-50 text-slate-700'"
                        class="w-full text-left px-3 py-2 rounded-xl text-xs font-medium transition-all duration-200 flex justify-between items-center hover:translate-x-1">
                        <span x-text="cat.name"></span>
                        <span :class="activeCategory === idx ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-500'"
                              class="text-xs px-1.5 py-0.5 rounded-full font-semibold transition-colors"
                              x-text="categoryAnswered(idx) + '/' + cat.questions.length"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Submit Button --}}
        <button
            @click="confirmSubmit()"
            class="slide-l btn-shine w-full bg-green-800 hover:bg-green-700 hover:-translate-y-0.5 text-white font-display font-bold py-3 px-4 rounded-2xl transition shadow-sm text-sm"
            style="--d:450ms">
            Submit Exam
        </button>

    </aside>

    {{-- MAIN EXAM AREA --}}
    <main class="lg:col-span-3 space-y-8">

        <template x-for="(cat, catIdx) in categories" :key="catIdx">
            <div :id="'category-' + catIdx">

                {{-- Category Header --}}
                <div class="rise bg-white rounded-2xl border border-green-200/60 shadow-sm p-5 mb-4" style="--d:200ms">
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
                    <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-400 h-1.5 rounded-full transition-all duration-500"
                             :style="'width:' + (categoryAnswered(catIdx) / cat.questions.length * 100) + '%'"></div>
                    </div>
                </div>

                {{-- Questions --}}
                <div class="space-y-4">
                    <template x-for="(q, qIdx) in cat.questions" :key="q.id">
                        <div class="q-card bg-white rounded-2xl border-2 shadow-sm p-6 transition-all duration-200"
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
                                <span class="pop-in text-xs text-green-600 font-semibold inline-flex items-center gap-1">
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

        {{-- Bottom Submit --}}
        <div class="flex justify-end pt-2 pb-8">
            <button @click="confirmSubmit()"
                class="btn-shine bg-green-800 hover:bg-green-700 hover:-translate-y-0.5 text-white font-display font-bold py-3 px-8 rounded-2xl transition shadow-sm text-sm">
                Submit Exam
            </button>
        </div>

    </main>
</div>

{{-- SUBMIT CONFIRM MODAL --}}
<div x-show="showSubmitModal"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     style="display:none;">
    <div x-show="showSubmitModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">

        <div class="text-center mb-5">
            <div class="bob-soft w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
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
                <div class="pop-in mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700">
                    ⚠️ You have <span x-text="totalQuestions() - answeredCount()"></span> unanswered question(s). These will be marked as incorrect.
                </div>
            </template>
        </div>

        <div class="flex gap-3">
            <button @click="showSubmitModal = false"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                Continue Exam
            </button>
            <form method="POST" action="{{ route('exam.submit', $exam->id) }}" id="submit-form" class="flex-1">
                @csrf
                <div id="answers-container"></div>
                <button type="submit"
                        class="btn-shine w-full py-2.5 rounded-xl bg-green-800 hover:bg-green-700 text-white font-semibold text-sm transition">
                    Submit Now
                </button>
            </form>
        </div>
    </div>
</div>

{{-- TIME UP MODAL --}}
<div x-show="timeUp"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     style="display:none;">
    <div class="pop bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
        <div class="alert-pulse w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="font-display text-xl font-bold text-slate-800 mb-2">Time's Up!</h3>
        <p class="text-sm text-slate-500 mb-5">Your exam is being submitted automatically.</p>
        <form method="POST" action="{{ route('exam.submit', $exam->id) }}" id="auto-submit-form">
            @csrf
            <div id="auto-answers-container"></div>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 text-white font-semibold text-sm">
                Submitting...
            </button>
        </form>
    </div>
</div>

@php
    // Shuffle the questions WITHIN each category — category order itself is untouched.
    // Runs fresh on every page load, so each student gets a different question order per category.
    $shuffledCategories = collect($categories)->map(function ($cat) {
        $cat = (array) $cat;
        $cat['questions'] = collect($cat['questions'])->shuffle()->values()->all();
        return $cat;
    })->values()->all();
@endphp

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function examApp() {
    return {
        categories: @json($shuffledCategories),
        answers: {},
        timeLeft: {{ $exam->duration_minutes * 60 }},
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
            fetch('{{ route("exam.violation", $exam->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
</html>