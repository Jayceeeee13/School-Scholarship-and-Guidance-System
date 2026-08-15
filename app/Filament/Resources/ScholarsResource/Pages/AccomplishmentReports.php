<?php

namespace App\Filament\Resources\ScholarsResource\Pages;

use App\Filament\Resources\ScholarsResource;
use App\Models\AccomplishmentReport;
use App\Models\Scholars;
use App\Models\Term;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class AccomplishmentReports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ScholarsResource::class;

    protected static string $view = 'filament.resources.scholars-resource.pages.accomplishment-reports';

    protected static ?string $title = 'Accomplishment Reports';

    // Not a nav item — only reachable via the button on the Scholars list.
    protected static bool $shouldRegisterNavigation = false;

    public function getBreadcrumb(): string
    {
        return 'Accomplishment Reports';
    }

    protected function getTableQuery(): Builder
    {
        // Only Talents / Supreme Student Government / Sports scholars submit
        // these — same rule the portal enforces at submission time.
        return AccomplishmentReport::query()
            ->whereHas('scholar', fn (Builder $q) =>
                $q->whereIn('type_of_scholarship', Scholars::ACCOMPLISHMENT_ELIGIBLE_TYPES)
            )
            ->with(['scholar', 'term']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('scholar.full_name')
                ->label('Scholar')
                ->searchable(['scholar.first_name', 'scholar.last_name'])
                ->sortable(),

            Tables\Columns\TextColumn::make('scholar.type_of_scholarship')
                ->label('Scholarship')
                ->badge()
                ->color('info')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('term.school_year')
                ->label('Term')
                ->formatStateUsing(fn ($state, $record) => $record->term
                    ? "{$record->term->school_year} — {$record->term->semester}"
                    : '—')
                ->toggleable(),

            Tables\Columns\TextColumn::make('activities_count')
                ->label('Activities')
                ->counts('activities')
                ->badge()
                ->color('gray'),

            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default    => 'warning',
                })
                ->formatStateUsing(fn (string $state): string => ucfirst($state))
                ->sortable(),

            Tables\Columns\TextColumn::make('submitted_at')
                ->label('Submitted')
                ->dateTime('M d, Y h:i A')
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->placeholder('All Statuses'),

            Tables\Filters\SelectFilter::make('term_id')
                ->label('Term')
                ->options(fn () => Term::orderByDesc('is_active')
                    ->orderByDesc('id')
                    ->get()
                    ->mapWithKeys(fn ($term) => [
                        $term->id => $term->school_year . ' — ' . $term->semester,
                    ]))
                ->placeholder('All Terms'),

            Tables\Filters\SelectFilter::make('scholarship_type')
                ->label('Scholarship Type')
                ->options(array_combine(
                    Scholars::ACCOMPLISHMENT_ELIGIBLE_TYPES,
                    Scholars::ACCOMPLISHMENT_ELIGIBLE_TYPES
                ))
                ->query(fn (Builder $query, array $data) => $query->when(
                    $data['value'] ?? null,
                    fn (Builder $q, $value) => $q->whereHas(
                        'scholar',
                        fn (Builder $q) => $q->where('type_of_scholarship', $value)
                    )
                )),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view_activities')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->modalHeading('Submitted Activities')
                ->modalContent(fn (AccomplishmentReport $record) => view(
                    'filament.accomplishment-report-activities-modal',
                    ['record' => $record]
                ))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('Mark this accomplishment report as approved.')
                ->visible(fn (AccomplishmentReport $record): bool => $record->status === 'pending')
                ->action(function (AccomplishmentReport $record) {
                    $record->update([
                        'status'  => 'approved',
                        'remarks' => null,
                    ]);

                    Notification::make()
                        ->title('Report approved')
                        ->success()
                        ->send();
                }),

            Tables\Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalSubmitActionLabel('Yes, Reject')
                ->form([
                    Forms\Components\Textarea::make('remarks')
                        ->label('Reason for rejection')
                        ->required()
                        ->rows(3)
                        ->placeholder('Let the scholar know what needs to be fixed...'),
                ])
                ->visible(fn (AccomplishmentReport $record): bool => $record->status === 'pending')
                ->action(function (AccomplishmentReport $record, array $data) {
                    $record->update([
                        'status'  => 'rejected',
                        'remarks' => $data['remarks'],
                    ]);

                    Notification::make()
                        ->title('Report rejected')
                        ->danger()
                        ->send();
                }),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'submitted_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}