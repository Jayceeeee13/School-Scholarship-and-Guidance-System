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
    <style>
        /* Progress bar animation */
        @keyframes progress-fill {
            from { width: 0%; }
        }
        .progress-bar { animation: progress-fill 0.5s ease-out; }

        /* Smooth answer selection */
        .choice-label {
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .choice-label:hover {
            transform: translateX(4px);
        }
        input[type="radio"]:checked + .choice-label {
            background-color: #14532d;
            color: white;
            border-color: #14532d;
        }
        input[type="radio"]:checked + .choice-label .choice-letter {
            background-color: #4ade80;
            color: #052e16;
        }

        /* Category tab active */
        .cat-tab.active {
            background-color: #14532d;
            color: white;
            border-color: #14532d;
        }

        /* Timer warning */
        @keyframes pulse-red {
            0%, 100% { color: #ef4444; }
            50% { color: #dc2626; }
        }
        .timer-warning { animation: pulse-red 1s infinite; }

        /* Fade in questions */
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .question-animate { animation: fadeSlide 0.25s ease-out; }

        /* Answered indicator dot */
        .q-dot.answered { background-color: #14532d; }
        .q-dot.current { ring: 2px solid #14532d; background-color: #bbf7d0; }
        .q-dot { transition: all 0.2s; }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen" x-data="examApp()" x-init="init()">

{{-- ── NAVBAR ── --}}
<header class="bg-green-800 border-b border-white sticky top-0 z-50 shadow-sm">
    <div class="max-w-8xl mx-auto px-6 py-3 flex justify-between items-center gap-4">
        <a href="{{ url('/gvc') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="GVCFI" class="w-10 h-10 rounded-lg object-contain">
            <span class="font-display text-base md:text-lg font-bold text-white tracking-tight">
                Green Valley College Foundation Inc.
            </span>
        </a>

        {{-- Timer --}}
        <div class="flex items-center gap-3">
            <div :class="timeLeft <= 300 ? 'timer-warning' : 'text-emerald-100'"
                 class="flex items-center gap-2 font-display font-bold text-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-text="formatTime(timeLeft)"></span>
            </div>
        </div>
    </div>
</header>

{{-- ── MAIN LAYOUT ── --}}
<div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- ── LEFT SIDEBAR ── --}}
    <aside class="lg:col-span-1 space-y-4">

        {{-- Student Info --}}
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-4">
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

        {{-- Categories --}}
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Categories</p>
            <div class="space-y-1">
                <template x-for="(cat, idx) in categories" :key="idx">
                    <button
                        @click="goToCategory(idx)"
                        :class="currentCategoryIndex === idx ? 'bg-green-800 text-white' : 'hover:bg-green-50 text-slate-700'"
                        class="w-full text-left px-3 py-2 rounded-xl text-xs font-medium transition flex justify-between items-center">
                        <span x-text="cat.name"></span>
                        <span :class="currentCategoryIndex === idx ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-500'"
                              class="text-xs px-1.5 py-0.5 rounded-full font-semibold"
                              x-text="categoryAnswered(idx) + '/' + cat.questions.length"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Question navigator --}}
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Questions</p>
            <div class="flex flex-wrap gap-1.5">
                <template x-for="(q, idx) in currentCategory().questions" :key="q.id">
                    <button
                        @click="currentQuestionIndex = idx"
                        :class="{
                            'bg-green-800 text-white': currentQuestionIndex === idx,
                            'bg-green-100 text-green-800': currentQuestionIndex !== idx && isAnswered(q.id),
                            'bg-slate-100 text-slate-500': currentQuestionIndex !== idx && !isAnswered(q.id)
                        }"
                        class="w-8 h-8 rounded-lg text-xs font-semibold transition hover:opacity-80"
                        x-text="idx + 1">
                    </button>
                </template>
            </div>
            <div class="flex gap-3 mt-3 text-xs text-slate-500">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 inline-block"></span> Answered</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-slate-100 inline-block"></span> Unanswered</span>
            </div>
        </div>

        {{-- Submit --}}
        <button
            @click="confirmSubmit()"
            class="w-full bg-green-800 hover:bg-green-700 text-white font-display font-bold py-3 px-4 rounded-2xl transition shadow-sm text-sm">
            Submit Exam
        </button>

    </aside>

    {{-- ── MAIN EXAM AREA ── --}}
    <main class="lg:col-span-3">

        {{-- Category Header --}}
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-5 mb-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-1"
                       x-text="'Category ' + (currentCategoryIndex + 1) + ' of ' + categories.length"></p>
                    <h2 class="font-display text-xl font-bold text-slate-800"
                        x-text="currentCategory().name"></h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-green-100 text-green-700 font-semibold px-3 py-1 rounded-full"
                          x-text="currentCategory().questions.length + ' items'"></span>
                    <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-3 py-1 rounded-full"
                          x-text="categoryAnswered(currentCategoryIndex) + ' answered'"></span>
                </div>
            </div>

            {{-- Category progress --}}
            <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300"
                     :style="'width:' + (categoryAnswered(currentCategoryIndex) / currentCategory().questions.length * 100) + '%'"></div>
            </div>
        </div>

        {{-- Question Card --}}
        <div class="bg-white rounded-2xl border border-green-200/60 shadow-sm p-6 mb-4 question-animate"
             :key="currentQuestionIndex + '-' + currentCategoryIndex">

            {{-- Question number + text --}}
            <div class="flex gap-4 mb-6">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-green-800 text-white font-display font-bold text-sm flex items-center justify-center"
                     x-text="currentQuestionIndex + 1"></div>
                <p class="text-slate-800 font-medium leading-relaxed pt-1.5"
                   x-text="currentQuestion().question"></p>
            </div>

            {{-- Choices --}}
            <div class="space-y-3">
                <template x-for="choice in currentQuestion().choices" :key="choice.id">
                    <div class="relative">
                        <input
                            type="radio"
                            :name="'question_' + currentQuestion().id"
                            :id="'choice_' + choice.id"
                            :value="choice.id"
                            class="sr-only"
                            x-model="answers[currentQuestion().id]"
                        >
                        <label
                            :for="'choice_' + choice.id"
                            :class="answers[currentQuestion().id] == choice.id
                                ? 'bg-green-800 text-white border-green-800'
                                : 'border-slate-200 hover:border-green-300 hover:bg-green-50'"
                            class="choice-label flex items-center gap-3 p-3.5 rounded-xl border-2 w-full">
                            <span
                                :class="answers[currentQuestion().id] == choice.id
                                    ? 'bg-green-400 text-green-950'
                                    : 'bg-slate-100 text-slate-600'"
                                class="choice-letter flex-shrink-0 w-8 h-8 rounded-lg font-display font-bold text-sm flex items-center justify-center transition"
                                x-text="choice.choice_letter">
                            </span>
                            <span class="text-sm font-medium" x-text="choice.choice_text"></span>
                        </label>
                    </div>
                </template>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="flex justify-between items-center">
            <button
                @click="prevQuestion()"
                :disabled="currentQuestionIndex === 0 && currentCategoryIndex === 0"
                :class="(currentQuestionIndex === 0 && currentCategoryIndex === 0) ? 'opacity-30 cursor-not-allowed' : 'hover:bg-green-50'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-green-200 text-green-800 font-semibold text-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Previous
            </button>

            <span class="text-xs text-slate-400 font-medium"
                  x-text="'Question ' + (currentQuestionIndex + 1) + ' of ' + currentCategory().questions.length"></span>

            <button
                @click="nextQuestion()"
                :class="isLastQuestion() ? 'bg-amber-500 hover:bg-amber-400 text-white' : 'bg-green-800 hover:bg-green-700 text-white'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition">
                <span x-text="isLastQuestion() ? 'Finish Category' : 'Next'"></span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

    </main>
</div>

{{-- ── SUBMIT CONFIRM MODAL ── --}}
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
            <p class="text-sm text-slate-500">You have answered <span class="font-bold text-green-700" x-text="answeredCount()"></span> out of <span class="font-bold" x-text="totalQuestions()"></span> questions.</p>

            {{-- Unanswered warning --}}
            <template x-if="answeredCount() < totalQuestions()">
                <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700">
                    ⚠️ You have <span x-text="totalQuestions() - answeredCount()"></span> unanswered question(s). Unanswered items will be marked as incorrect.
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
                <template x-for="(choiceId, questionId) in answers" :key="questionId">
                    <input type="hidden" :name="'answers[' + questionId + ']'" :value="choiceId">
                </template>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-green-800 hover:bg-green-700 text-white font-semibold text-sm transition">
                    Submit Now
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── TIME UP MODAL ── --}}
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
        <form method="POST" action="{{ route('exam.submit', $exam->id) }}" id="auto-submit-form">
            @csrf
            <template x-for="(choiceId, questionId) in answers" :key="questionId">
                <input type="hidden" :name="'answers[' + questionId + ']'" :value="choiceId">
            </template>
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
        // Exam data injected from Laravel
        categories: @json($categories),
        durationSeconds: {{ $exam->duration_minutes * 60 }},

        // State
        currentCategoryIndex: 0,
        currentQuestionIndex: 0,
        answers: {},
        timeLeft: {{ $exam->duration_minutes * 60 }},
        showSubmitModal: false,
        timeUp: false,
        timer: null,

        init() {
            // Start countdown timer
            this.timer = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    clearInterval(this.timer);
                    this.timeUp = true;
                    this.$nextTick(() => {
                        document.getElementById('auto-submit-form').submit();
                    });
                }
            }, 1000);
        },

        formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) return `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        },

        currentCategory() {
            return this.categories[this.currentCategoryIndex];
        },

        currentQuestion() {
            return this.currentCategory().questions[this.currentQuestionIndex];
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

        isLastQuestion() {
            return this.currentQuestionIndex === this.currentCategory().questions.length - 1;
        },

        nextQuestion() {
            if (!this.isLastQuestion()) {
                this.currentQuestionIndex++;
            } else if (this.currentCategoryIndex < this.categories.length - 1) {
                this.currentCategoryIndex++;
                this.currentQuestionIndex = 0;
            }
        },

        prevQuestion() {
            if (this.currentQuestionIndex > 0) {
                this.currentQuestionIndex--;
            } else if (this.currentCategoryIndex > 0) {
                this.currentCategoryIndex--;
                this.currentQuestionIndex = this.currentCategory().questions.length - 1;
            }
        },

        goToCategory(idx) {
            this.currentCategoryIndex = idx;
            this.currentQuestionIndex = 0;
        },

        confirmSubmit() {
            this.showSubmitModal = true;
        },
    };
}
</script>

</body>
</html>