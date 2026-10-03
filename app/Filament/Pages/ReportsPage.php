<?php

namespace App\Filament\Pages;

use App\Models\InstitutionalScholar;
use App\Models\Scholars;
use App\Models\Term;
use App\Models\TypeOfScholarship;
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

    // ── Tab state ────────────────────────────────────────────────────────────
    public string $activeTab = 'grantees';

    // ── Scholarship categories ──────────────────────────────────────────────
    public const SCHOLARSHIP_CATEGORIES = ['TES', 'TDP', 'CMSP'];

    // ── Filter state ────────────────────────────────────────────────────────
    public ?string $school_year_filter = null;

    public function mount(): void
    {
        $activeTerm = Term::where('is_active', true)->first();
        if ($activeTerm) {
            $this->school_year_filter = $activeTerm->school_year;
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
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

    // ── Resolve term pair ───────────────────────────────────────────────────
    public function getTermPair(): array
    {
        if (! $this->school_year_filter) {
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

    // ── Grantees stats builder ──────────────────────────────────────────────
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

    // ── Grantees report data ────────────────────────────────────────────────
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

    // ── Institutional scholars report data ──────────────────────────────────
    public function getInstitutionalReportData(): array
    {
        [$term1, $term2] = $this->getTermPair();

        $scholarshipTypes = TypeOfScholarship::active()
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $rows = [];

        foreach ($scholarshipTypes as $type) {
            $t1Count = $term1
                ? InstitutionalScholar::where('term_id', $term1->id)
                    ->where('status', '!=', 'revoked')
                    ->whereRaw('LOWER(type_of_scholarship) LIKE ?', ['%' . strtolower($type) . '%'])
                    ->count()
                : 0;

            $t2Count = $term2
                ? InstitutionalScholar::where('term_id', $term2->id)
                    ->where('status', '!=', 'revoked')
                    ->whereRaw('LOWER(type_of_scholarship) LIKE ?', ['%' . strtolower($type) . '%'])
                    ->count()
                : 0;

            $rows[] = [
                'type'     => $type,
                't1_count' => $t1Count,
                't2_count' => $t2Count,
            ];
        }

        $grandT1 = $term1
            ? InstitutionalScholar::where('term_id', $term1->id)
                ->where('status', '!=', 'revoked')
                ->count()
            : 0;

        $grandT2 = $term2
            ? InstitutionalScholar::where('term_id', $term2->id)
                ->where('status', '!=', 'revoked')
                ->count()
            : 0;

        return [
            'term1'    => $term1,
            'term2'    => $term2,
            'rows'     => $rows,
            'grand_t1' => $grandT1,
            'grand_t2' => $grandT2,
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