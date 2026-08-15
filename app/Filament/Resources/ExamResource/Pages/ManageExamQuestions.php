<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Models\ExamQuestion;
use App\Models\ExamCategory;
use App\Models\QuestionChoice;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ManageExamQuestions extends Page
{
    protected static string $resource = ExamResource::class;
    protected static string $view = 'filament.resources.exam-resource.pages.manage-exam-questions';

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

    public function mount(int|string $record): void
    {
        $exam = \App\Models\Exam::findOrFail($record);
        $this->examId    = $exam->id;
        $this->examTitle = $exam->title;
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
            url('/admin/exams') => 'Exams',
            '' => 'Questions',
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

    public function getStats()
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
        $used    = collect($this->choices)->pluck('choice_letter')->toArray();
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $next    = collect($letters)->first(fn ($l) => !in_array($l, $used));

        if ($next && count($this->choices) < 7) {
            $this->choices[] = [
                'choice_letter' => $next,
                'choice_text'   => '',
                'is_correct'    => false,
            ];
        }
    }

    public function removeChoice(int $index): void
    {
        if (count($this->choices) > 2) {
            array_splice($this->choices, $index, 1);
            $this->choices = array_values($this->choices);
        }
    }

    public function toggleCorrect(int $index): void
    {
        $this->choices[$index]['is_correct'] = !$this->choices[$index]['is_correct'];
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
            Notification::make()->danger()->title('No Correct Answer')
                ->body('Please mark at least one choice as correct.')->send();
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

            Notification::make()->success()->title('Question Added')
                ->body('New question saved successfully.')->send();
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

            Notification::make()->success()->title('Question Updated')
                ->body('Changes saved successfully.')->send();
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

        Notification::make()->danger()->title('Question Deleted')
            ->body('The question has been removed.')->send();
    }
}