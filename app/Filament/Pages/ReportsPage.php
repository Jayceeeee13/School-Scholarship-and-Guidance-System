<?php

namespace App\Filament\Pages;

use App\Models\Scholars;
use App\Models\Term;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class ReportsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-s-chart-bar-square';
    protected static ?string $navigationLabel = 'Scholarships Reports';
    protected static ?string $navigationGroup = 'Scholarship Management';
    protected static ?string $title           = 'Grantees Report';
    protected static ?int    $navigationSort  = 99;
    protected static string  $view            = 'filament.pages.reports-page';

    // ── Scholarship categories ──────────────────────────────────────────────
    public const SCHOLARSHIP_CATEGORIES = ['TES', 'TDP', 'CMSP'];

    // ── Filter state ────────────────────────────────────────────────────────
    /** Selected school year string e.g. "2025-2026" */
    public ?string $school_year_filter = null;

    public function mount(): void
    {
        // Default to the active term's school year
        $activeTerm = Term::where('is_active', true)->first();
        if ($activeTerm) {
            $this->school_year_filter = $activeTerm->school_year;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('school_year_filter')
                                    ->label('Filter by School Year')
                                    ->options(function () {
                                        // Get distinct school years, sorted newest first
                                        return Term::query()
                                            ->select('school_year')
                                            ->distinct()
                                            ->orderByDesc('school_year')
                                            ->pluck('school_year', 'school_year')
                                            ->mapWithKeys(function ($year) {
                                                $isActive = Term::where('school_year', $year)
                                                    ->where('is_active', true)
                                                    ->exists();
                                                return [$year => $year . ($isActive ? ' (Active)' : '')];
                                            });
                                    })
                                    ->placeholder('Select School Year')
                                    ->native(false)
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn () => null),
                            ]),
                    ])
                    ->compact(),
            ])
            ->statePath('');
    }

    // ── Resolve term pair from selected school year ─────────────────────────

    /**
     * Returns [term1, term2] where:
     *   term1 = 1st Semester of the selected school year
     *   term2 = 2nd Semester of the selected school year
     *
     * Falls back gracefully if either semester doesn't exist.
     */
    public function getTermPair(): array
    {
        if (! $this->school_year_filter) {
            // No filter — use active school year
            $activeYear = Term::where('is_active', true)->value('school_year');
            if (! $activeYear) {
                return [null, null];
            }
            $this->school_year_filter = $activeYear;
        }

        $year = $this->school_year_filter;

        $term1 = Term::where('school_year', $year)
            ->where(fn ($q) => $q
                ->whereRaw("LOWER(semester) LIKE '%1st%'")
                ->orWhereRaw("LOWER(semester) LIKE '%first%'")
            )
            ->first();

        $term2 = Term::where('school_year', $year)
            ->where(fn ($q) => $q
                ->whereRaw("LOWER(semester) LIKE '%2nd%'")
                ->orWhereRaw("LOWER(semester) LIKE '%second%'")
            )
            ->first();

        return [$term1, $term2];
    }

    // ── Stats builder ───────────────────────────────────────────────────────

    /**
     * Build stats for a single term, optionally filtered by scholarship category keyword.
     */
    public function getTermStats(?Term $term, ?string $scholarshipCategory = null): array
    {
        $empty = [
            'total_male' => 0, 'total_female' => 0, 'total' => 0,
            'pwd_male'   => 0, 'pwd_female'   => 0,
            'ip_male'    => 0, 'ip_female'    => 0,
            'none_board_male'  => 0, 'none_board_female'  => 0,
            'with_board_male'  => 0, 'with_board_female'  => 0,
        ];

        if (! $term) return $empty;

        $query = Scholars::where('term_id', $term->id)
            ->where('status', 'active');

        if ($scholarshipCategory) {
            $query->whereRaw(
                'LOWER(type_of_scholarship) LIKE ?',
                ['%' . strtolower($scholarshipCategory) . '%']
            );
        }

        $scholars = $query->get();
        $male     = $scholars->where('sex', 'Male');
        $female   = $scholars->where('sex', 'Female');

        $pwdMale   = $male->filter(fn ($s) => strtolower($s->pwd ?? '') === 'yes');
        $pwdFemale = $female->filter(fn ($s) => strtolower($s->pwd ?? '') === 'yes');

        $ipMale   = $male->filter(fn ($s) => ! empty($s->ip_group));
        $ipFemale = $female->filter(fn ($s) => ! empty($s->ip_group));

        $withBoardMale   = $male->filter(fn ($s) => str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'));
        $withBoardFemale = $female->filter(fn ($s) => str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'));
        $noneBoardMale   = $male->filter(fn ($s) => ! str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'));
        $noneBoardFemale = $female->filter(fn ($s) => ! str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'));

        return [
            'total_male'        => $male->count(),
            'total_female'      => $female->count(),
            'total'             => $scholars->count(),
            'pwd_male'          => $pwdMale->count(),
            'pwd_female'        => $pwdFemale->count(),
            'ip_male'           => $ipMale->count(),
            'ip_female'         => $ipFemale->count(),
            'none_board_male'   => $noneBoardMale->count(),
            'none_board_female' => $noneBoardFemale->count(),
            'with_board_male'   => $withBoardMale->count(),
            'with_board_female' => $withBoardFemale->count(),
        ];
    }

    // ── Main data builder ───────────────────────────────────────────────────

    public function getReportData(): array
    {
        [$term1, $term2] = $this->getTermPair();

        $categories = self::SCHOLARSHIP_CATEGORIES;

        $rows = [];
        foreach ($categories as $cat) {
            $rows[$cat] = [
                'term1' => $this->getTermStats($term1, $cat),
                'term2' => $this->getTermStats($term2, $cat),
            ];
        }

        return [
            'term1'      => $term1,
            'term2'      => $term2,
            'categories' => $categories,
            'rows'       => $rows,
            'grand_t1'   => $this->getTermStats($term1),
            'grand_t2'   => $this->getTermStats($term2),
        ];
    }

    // ── Navigation visibility ───────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }
}