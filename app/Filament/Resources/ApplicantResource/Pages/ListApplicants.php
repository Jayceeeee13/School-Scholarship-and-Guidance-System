<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Filament\Resources\TypeOfScholarshipResource;
use App\Models\TypeOfScholarship;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListApplicants extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = ApplicantResource::class;

    public ?string $selectedSchoolYear = null;
    public ?string $selectedSemester = null;
    public bool $filterEnabled = false;

    public function mount(): void
    {
        parent::mount();

        // Don't apply filter by default
        $this->selectedSchoolYear = session('filter_school_year');
        $this->selectedSemester = session('filter_semester');
        $this->filterEnabled = session('filter_enabled', false);
    }

    public function updatedActiveTab(): void
    {
        // The 'scholarship_types' tab uses a completely different model,
        // columns, and filters than the applicants tabs. resetTable() clears
        // Filament's cached Table instance (columns/filters/actions) in
        // addition to filters/pagination — without it, the cached table
        // config from the previous tab can render against rows fetched for
        // the new tab, causing column closures to run against the wrong
        // model (e.g. calling an Applicant-only method on a TypeOfScholarship).
        $this->resetTable();
    }

    public function getTitle(): string
    {
        return $this->activeTab === 'scholarship_types'
            ? 'Scholarship Offers'
            : 'Applicants';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('newScholarshipType')
                ->label('New Scholarship Offers')
                ->icon('heroicon-o-plus-circle')
                ->color('gray')
                ->visible(fn (): bool => $this->activeTab === 'scholarship_types')
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('slots')
                        ->label('Slots')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Number of available slots for this scholarship type'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $record = TypeOfScholarship::create($data);

                    $this->logCustomActivity(
                        $record,
                        'scholarship_types',
                        'created',
                        "Added scholarship offer \"{$record->name}\" ({$record->slots} slots)"
                    );

                    \Filament\Notifications\Notification::make()
                        ->title('Scholarship offers added')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('New applicant')
                ->visible(fn (): bool => $this->activeTab !== 'scholarship_types'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Applicants')
                ->badge($this->getFilteredCount())
                ->badgeColor('gray'),

            'scholarship_types' => Tab::make('Scholarship Offers')
                ->icon('heroicon-o-rectangle-stack')
                ->badge(TypeOfScholarship::count())
                ->badgeColor('info'),
        ];
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'scholarship_types') {
            return $table
                ->query(TypeOfScholarship::query())
                ->columns([
                    \Filament\Tables\Columns\TextColumn::make('name')
                        ->label('Name')
                        ->searchable()
                        ->sortable(),

                    \Filament\Tables\Columns\TextColumn::make('slots')
                        ->label('Slots')
                        ->numeric()
                        ->sortable()
                        ->badge()
                        ->color(fn ($state): string => match (true) {
                            (int) $state === 0 => 'danger',
                            (int) $state <= 5  => 'warning',
                            default             => 'success',
                        }),

                    \Filament\Tables\Columns\ToggleColumn::make('is_active')
                        ->label('Active')
                        ->sortable()
                        ->onColor('success')
                        ->offColor('danger'),
                ])
                ->actions([
                    \Filament\Tables\Actions\Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn (TypeOfScholarship $record): string =>
                            TypeOfScholarshipResource::getUrl('edit', ['record' => $record])
                        ),
                ])
                ->bulkActions([
                    \Filament\Tables\Actions\BulkActionGroup::make([
                        \Filament\Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
        }

        return ApplicantResource::table($table);
    }

    protected function getTableQuery(): ?Builder
    {
        if ($this->activeTab === 'scholarship_types') {
            return TypeOfScholarship::query();
        }

        $query = parent::getTableQuery();

        // Only apply filter if enabled
        if ($this->filterEnabled && $this->selectedSchoolYear && $this->selectedSemester) {
            $dateRange = $this->getDateRangeForSemester($this->selectedSchoolYear, $this->selectedSemester);

            if ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }

        return $query;
    }

    protected function getDateRangeForSemester(string $schoolYear, string $semester): ?array
    {
        // Parse school year (e.g., "2024-2025")
        $years = explode('-', $schoolYear);

        if (count($years) !== 2) {
            return null;
        }

        $startYear = (int) $years[0];
        $endYear = (int) $years[1];

        if ($semester === '1st Semester') {
            // 1st Semester: July to December
            $start = Carbon::create($startYear, 7, 1)->startOfDay();
            $end = Carbon::create($startYear, 12, 31)->endOfDay();
        } else {
            // 2nd Semester: January to June
            $start = Carbon::create($endYear, 1, 1)->startOfDay();
            $end = Carbon::create($endYear, 6, 30)->endOfDay();
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    protected function getFilteredCount(?string $status = null): int
    {
        $query = \App\Models\Applicant::query();

        if ($status) {
            $query->where('status', $status);
        }

        // Only apply filter if enabled
        if ($this->filterEnabled && $this->selectedSchoolYear && $this->selectedSemester) {
            $dateRange = $this->getDateRangeForSemester($this->selectedSchoolYear, $this->selectedSemester);

            if ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }
        }

        return $query->count();
    }

    public function getSubheading(): ?string
    {
        if ($this->activeTab === 'scholarship_types') {
            return "🎓 Manage scholarship offers and slot availability";
        }

        if ($this->filterEnabled && $this->selectedSchoolYear && $this->selectedSemester) {
            $semesterLabel = $this->selectedSemester === '1st Semester'
                ? '1st Semester (Jul-Dec)'
                : '2nd Semester (Jan-Jun)';
            return "📅 Filtered by: {$this->selectedSchoolYear} • 📚 {$semesterLabel}";
        }

        return "📋 Showing all applicants";
    }
}