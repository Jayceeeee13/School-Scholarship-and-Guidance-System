<?php

namespace App\Filament\Pages;

use App\Models\Term;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public ?string $schoolYear = null;

    public ?string $semester = null;

    public function getWidgets(): array
    {
        return [];
    }

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

    public function getDateRange(): ?array
    {
        if (! $this->schoolYear || ! str_contains($this->schoolYear, '-')) {
            return null;
        }

        [$startYear, $endYear] = explode('-', $this->schoolYear);

        return match ($this->semester) {
            '1st Semester' => [
                "{$startYear}-08-01 00:00:00",
                "{$startYear}-12-31 23:59:59",
            ],

            '2nd Semester' => [
                "{$endYear}-01-01 00:00:00",
                "{$endYear}-05-31 23:59:59",
            ],

            'Summer' => [
                "{$endYear}-06-01 00:00:00",
                "{$endYear}-07-31 23:59:59",
            ],

            default => [
                "{$startYear}-08-01 00:00:00",
                "{$endYear}-07-31 23:59:59",
            ],
        };
    }

    public function getSelectedTermIds(): array
    {
        if (! $this->schoolYear) {
            return [];
        }

        $query = Term::query()
            ->where('school_year', $this->schoolYear);

        if ($this->semester) {
            $query->where('semester', $this->semester);
        }

        return $query->pluck('id')->toArray();
    }
}