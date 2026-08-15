<x-filament-panels::page>
    <div class="space-y-6">

        {{-- STATS BAR --}}
        @php $stats = $this->getStats(); @endphp
        <div class="grid grid-cols-3 gap-4">
            @foreach ([
                ['label' => 'Total Questions', 'value' => $stats['total'],       'icon' => 'heroicon-o-document-text', 'color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
                ['label' => 'Total Points',    'value' => $stats['totalPoints'], 'icon' => 'heroicon-o-star',          'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50'],
                ['label' => 'Categories',      'value' => $stats['categories'],  'icon' => 'heroicon-o-tag',           'color' => 'text-green-600',  'bg' => 'bg-green-50'],
            ] as $stat)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm flex items-center gap-4">
                <div class="rounded-lg p-3 {{ $stat['bg'] }}">
                    @svg($stat['icon'], 'w-6 h-6 ' . $stat['color'])
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- TOOLBAR --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-72">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                        @svg('heroicon-o-magnifying-glass', 'w-4 h-4')
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search questions..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                </div>

                <select wire:model.live="filterCategory"
                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Categories</option>
                    @foreach ($this->getCategories() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 transition">
                @svg('heroicon-o-plus-circle', 'w-4 h-4')
                Add Question
            </button>
        </div>

        {{-- QUESTIONS LIST --}}
        <div class="space-y-3">
            @forelse ($this->getQuestions() as $q)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                <div class="flex items-start gap-4 p-4">
                    <div class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600">
                        {{ $q->order }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            @if ($q->examCategory)
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                    @svg('heroicon-o-tag', 'w-3 h-3')
                                    {{ $q->examCategory->name }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                @svg('heroicon-o-star', 'w-3 h-3')
                                {{ $q->points }} {{ Str::plural('pt', $q->points) }}
                            </span>
                        </div>

                        <p class="text-sm font-semibold text-gray-800 leading-snug mb-3">{{ $q->question }}</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                            @foreach ($q->choices->sortBy('choice_letter') as $choice)
                            <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs
                                {{ $choice->is_correct ? 'bg-green-50 border border-green-200 text-green-800 font-semibold' : 'bg-gray-50 border border-gray-100 text-gray-600' }}">
                                <span class="font-bold">{{ $choice->choice_letter }}.</span>
                                <span class="truncate">{{ $choice->choice_text }}</span>
                                @if ($choice->is_correct)
                                    @svg('heroicon-o-check-circle', 'w-3.5 h-3.5 ml-auto text-green-600 shrink-0')
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <button wire:click="openEdit({{ $q->id }})"
                            class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition" title="Edit">
                            @svg('heroicon-o-pencil-square', 'w-4 h-4')
                        </button>
                        <button wire:click="confirmDelete({{ $q->id }})"
                            class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 transition" title="Delete">
                            @svg('heroicon-o-trash', 'w-4 h-4')
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 bg-white py-16 text-center">
                @svg('heroicon-o-document-text', 'w-12 h-12 text-gray-300 mb-3')
                <p class="text-base font-semibold text-gray-500">No questions found</p>
                <p class="mt-1 text-sm text-gray-400">Add your first question to get started.</p>
                <button wire:click="openCreate"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">
                    @svg('heroicon-o-plus-circle', 'w-4 h-4')
                    Add First Question
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- CREATE / EDIT MODAL --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl" wire:key="modal-{{ implode('-', array_column($choices, 'is_correct')) }}">  

            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-2">
                        @svg('heroicon-o-document-text', 'w-5 h-5 text-green-600')
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">
                            {{ $modalMode === 'create' ? 'Add New Question' : 'Edit Question' }}
                        </h2>
                        <p class="text-xs text-gray-500">{{ $examTitle }}</p>
                    </div>
                </div>
                <button wire:click="$set('showModal', false)" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 transition">
                    @svg('heroicon-o-x-mark', 'w-5 h-5')
                </button>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Category</label>
                        <select wire:model="exam_category_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">— None —</option>
                            @foreach ($this->getCategories() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Points</label>
                        <input type="number" wire:model="points" min="1" max="100"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Order</label>
                        <input type="number" wire:model="order" min="1"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        Question <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="question" rows="4"
                        placeholder="Write a clear and concise question..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none">
                    </textarea>
                    @error('question') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                            Answer Choices <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs text-gray-400">Mark at least one as correct</span>
                    </div>

                    <div class="space-y-2">
    @foreach ($choices as $index => $choice)
    <div
        wire:key="choice-{{ $index }}"
        style="{{ $choice['is_correct'] ? 'border: 2px solid #4ade80; background-color: #f0fdf4; box-shadow: 0 0 0 1px #86efac;' : 'border: 2px solid #e5e7eb; background-color: #f9fafb;' }}"
        class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all duration-200">

        <span
            style="{{ $choice['is_correct'] ? 'background-color: #22c55e; color: #ffffff;' : 'background-color: #e5e7eb; color: #4b5563;' }}"
            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-all duration-200">
            {{ $choice['choice_letter'] }}
        </span>

        <input
            type="text"
            wire:model="choices.{{ $index }}.choice_text"
            placeholder="Enter choice text..."
            style="{{ $choice['is_correct'] ? 'color: #166534; font-weight: 500;' : 'color: #374151;' }}"
            class="flex-1 bg-transparent text-sm focus:outline-none placeholder-gray-400" />

        <button
            type="button"
            wire:click="toggleCorrect({{ $index }})"
            title="{{ $choice['is_correct'] ? 'Marked as correct' : 'Mark as correct' }}"
            style="{{ $choice['is_correct'] ? 'color: #22c55e;' : 'color: #d1d5db;' }}"
            class="shrink-0 rounded-full p-1 transition-all duration-200 hover:text-green-500">
            @svg('heroicon-s-check-circle', 'w-5 h-5')
        </button>

        @if (count($choices) > 2)
        <button type="button" wire:click="removeChoice({{ $index }})"
            class="shrink-0 text-gray-300 hover:text-red-500 transition">
            @svg('heroicon-o-x-circle', 'w-5 h-5')
        </button>
        @endif
    </div>
    @endforeach
</div> 

                    @if (count($choices) < 7)
                    <button type="button" wire:click="addChoice"
                        class="mt-3 inline-flex items-center gap-1.5 text-sm text-green-600 hover:text-green-700 font-medium transition">
                        @svg('heroicon-o-plus-circle', 'w-4 h-4')
                        Add Choice
                    </button>
                    @endif
                    @error('choices') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="sticky bottom-0 flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4">
                <button wire:click="$set('showModal', false)"
                    class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="save"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">
                    @svg('heroicon-o-check', 'w-4 h-4')
                    {{ $modalMode === 'create' ? 'Save Question' : 'Save Changes' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- DELETE CONFIRM MODAL --}}
    @if ($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showDeleteConfirm', false)"></div>
        <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                @svg('heroicon-o-exclamation-triangle', 'w-7 h-7 text-red-600')
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1">Delete Question?</h3>
            <p class="text-sm text-gray-500 mb-6">This will permanently remove the question and all its choices. This cannot be undone.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteConfirm', false)"
                    class="flex-1 rounded-lg border border-gray-300 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="deleteQuestion"
                    class="flex-1 rounded-lg bg-red-600 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
    @endif

</x-filament-panels::page>