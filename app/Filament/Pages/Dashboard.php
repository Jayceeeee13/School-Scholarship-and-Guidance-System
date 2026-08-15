<?php

namespace App\Filament\Pages;

use App\Models\Term;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    /**
     * Point to our fully custom blade instead of Filament's widget grid.
     */
    protected static string $view = 'filament.pages.dashboard';

    /**
     * No widgets needed — everything is rendered directly in the blade.
     */
    public function getWidgets(): array
    {
        return [];
    }

    // ── School Year / Semester filter state ────────────────────────────

    public ?string $schoolYear = null;

    public ?string $semester = null;

    public function getSchoolYearOptions(): array
    {
        return Term::query()
            ->select('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year', 'school_year')
            ->toArray();
    }

    public function getSemesterOptions(): array
    {
        return [
            '1st Semester' => '1st Semester',
            '2nd Semester' => '2nd Semester',
            'Summer'       => 'Summer',
        ];
    }

    public function resetFilters(): void
    {
        $this->schoolYear = null;
        $this->semester = null;
    }

    /**
     * Resolves the currently selected school year + semester into a
     * [start, end] datetime range for filtering `created_at` columns.
     *
     * None of Scholars / Applicant / CounselingAppointments have a
     * term_id — so this is derived purely from created_at against a
     * standard Aug 1 – Jul 31 academic year, split into:
     *   1st Semester → Aug 1 – Dec 31 (start year)
     *   2nd Semester → Jan 1 – May 31 (end year)
     *   Summer       → Jun 1 – Jul 31 (end year)
     *   (no semester) → full Aug 1 – Jul 31 academic year
     *
     * Returns null when no school year is selected (no filter applied).
     */
    public function getDateRange(): ?array
    {
        if (! $this->schoolYear || ! str_contains($this->schoolYear, '-')) {
            return null;
        }

        [$startYear, $endYear] = explode('-', $this->schoolYear);

        return match ($this->semester) {
            '1st Semester' => ["{$startYear}-08-01 00:00:00", "{$startYear}-12-31 23:59:59"],
            '2nd Semester' => ["{$endYear}-01-01 00:00:00", "{$endYear}-05-31 23:59:59"],
            'Summer'       => ["{$endYear}-06-01 00:00:00", "{$endYear}-07-31 23:59:59"],
            default        => ["{$startYear}-08-01 00:00:00", "{$endYear}-07-31 23:59:59"],
        };
    }
}