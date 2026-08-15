<?php

namespace App\Filament\Pages;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamCategory;
use App\Models\QuestionChoice;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageExamQuestions extends Page
{
    protected static ?string $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.manage-exam-questions';

    public int $examId;
    public string $examTitle = '';

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $modalMode = 'create';

    public ?int $exam_category_id = null;
    public int $points = 1;
    public int $order = 1;
    public string $question = '';
    public array $choices = [];

    public string $search = '';
    public string $filterCategory = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return route('filament.admin.pages.manage-exam-questions', $parameters);
    }

    public function mount(int $exam): void
    {
        $examModel       = Exam::findOrFail($exam);
        $this->examId    = $examModel->id;
        $this->examTitle = $examModel->title;
        $this->resetChoices();
        $this->order = ExamQuestion::where('exam_id', $this->examId)->max('order') + 1;
    }

    public function getTitle(): string
    {
        return 'Questions — ' . $this->examTitle;
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/exams')        => 'Exams',
            url('/admin/exams/' . $this->examId . '/edit') => $this->examTitle,
            ''                         => 'Questions',
        ];
    }

    public function getQuestions()
    {
        return ExamQuestion::with(['examCategory', 'choices'])
            ->where('exam_id', $this->examId)
            ->when($this->search, fn ($q) => $q->where('question', 'like', "%{$this->search}%"))
            ->when($this->filterCategory, fn ($q) => $q->where('exam_category_id', $this->filterCategory))
            ->orderBy('order')
            ->get();
    }

    public function getCategories()
    {
        return ExamCategory::where('is_active', true)->orderBy('name')->get();
    }

    public function getStats(): array
    {
        $questions = ExamQuestion::where('exam_id', $this->examId)->get();
        return [
            'total'       => $questions->count(),
            'totalPoints' => $questions->sum('points'),
            'categories'  => $questions->pluck('exam_category_id')->unique()->filter()->count(),
        ];
    }

    protected function resetChoices(): void
    {
        $this->choices = collect(['A', 'B', 'C', 'D'])->map(fn ($l) => [
            'choice_letter' => $l,
            'choice_text'   => '',
            'is_correct'    => false,
        ])->toArray();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'exam_category_id', 'question']);
        $this->points    = 1;
        $this->order     = ExamQuestion::where('exam_id', $this->examId)->max('order') + 1;
        $this->modalMode = 'create';
        $this->resetChoices();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $q = ExamQuestion::with('choices')->findOrFail($id);

        $this->editingId        = $id;
        $this->exam_category_id = $q->exam_category_id;
        $this->points           = $q->points;
        $this->order            = $q->order;
        $this->question         = $q->question;
        $this->choices          = $q->choices->map(fn ($c) => [
            'id'            => $c->id,
            'choice_letter' => $c->choice_letter,
            'choice_text'   => $c->choice_text,
            'is_correct'    => (bool) $c->is_correct,
        ])->toArray();

        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function addChoice(): void
{
    $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
    $used    = array_column($this->choices, 'choice_letter');
    $next    = collect($letters)->first(fn ($l) => !in_array($l, $used));

    if ($next && count($this->choices) < 7) {
        $choices   = $this->choices;
        $choices[] = [
            'choice_letter' => $next,
            'choice_text'   => '',
            'is_correct'    => false,
        ];
        $this->choices = $choices;
    }
}

    public function removeChoice(int $index): void
{
    if (count($this->choices) > 2) {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        array_splice($this->choices, $index, 1);
        $this->choices = array_values($this->choices);

        // Reassign letters in order so there are no gaps
        foreach ($this->choices as $i => $choice) {
            $this->choices[$i]['choice_letter'] = $letters[$i];
        }
    }
}

    public function toggleCorrect(int $index): void
{
    $choices = $this->choices;

    foreach ($choices as $i => $choice) {
        $choices[$i]['is_correct'] = ($i === $index) ? !$choices[$i]['is_correct'] : false;
    }

    $this->choices = [];          // force Livewire to detect the diff
    $this->choices = $choices;
}   

    public function save(): void
    {
        $this->validate([
            'question'                => 'required|string|min:5',
            'points'                  => 'required|integer|min:1|max:100',
            'order'                   => 'required|integer|min:1',
            'choices'                 => 'required|array|min:2',
            'choices.*.choice_letter' => 'required|string',
            'choices.*.choice_text'   => 'required|string|min:1',
        ]);

        if (!collect($this->choices)->contains('is_correct', true)) {
            Notification::make()->danger()
                ->title('No Correct Answer')
                ->body('Please mark at least one choice as correct.')
                ->send();
            return;
        }

        if ($this->modalMode === 'create') {
            $q = ExamQuestion::create([
                'exam_id'          => $this->examId,
                'exam_category_id' => $this->exam_category_id,
                'question'         => $this->question,
                'points'           => $this->points,
                'order'            => $this->order,
            ]);

            foreach ($this->choices as $choice) {
                $q->choices()->create($choice);
            }

            Notification::make()->success()
                ->title('Question Added')
                ->body('New question saved successfully.')
                ->send();
        } else {
            $q = ExamQuestion::findOrFail($this->editingId);
            $q->update([
                'exam_category_id' => $this->exam_category_id,
                'question'         => $this->question,
                'points'           => $this->points,
                'order'            => $this->order,
            ]);

            $updatedIds = [];
            foreach ($this->choices as $choice) {
                if (!empty($choice['id'])) {
                    QuestionChoice::find($choice['id'])?->update($choice);
                    $updatedIds[] = $choice['id'];
                } else {
                    $new          = $q->choices()->create($choice);
                    $updatedIds[] = $new->id;
                }
            }

            $q->choices()->whereNotIn('id', $updatedIds)->delete();

            Notification::make()->success()
                ->title('Question Updated')
                ->body('Changes saved successfully.')
                ->send();
        }

        $this->showModal = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId        = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteQuestion(): void
    {
        ExamQuestion::findOrFail($this->deletingId)->delete();
        $this->showDeleteConfirm = false;
        $this->deletingId        = null;

        Notification::make()->danger()
            ->title('Question Deleted')
            ->body('The question has been removed.')
            ->send();
    }

    public static function getRoutePath(): string
    {
        return 'exams/{exam}/questions';
    }
}