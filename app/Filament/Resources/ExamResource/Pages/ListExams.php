<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use App\Filament\Resources\ExamAttemptResource;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Term;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ListExams extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = ExamResource::class;

    /**
     * Prevents stale table state (sort column, filters, toggled columns,
     * cached query/columns) from one tab bleeding into the other. Without
     * this, switching tabs can apply columns built for one model against
     * a query built for the other (e.g. Exam's `questions_count` column
     * running against the ExamAttempt query), causing errors like
     * "Call to undefined method ExamAttempt::questions()".
     */
    public function updatedActiveTab(): void
    {
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        if ($this->activeTab === 'examinees') {
            return ExamAttempt::query()->whereNull('archived_at')->with([
                'answers.question.examCategory',
                'answers.question.choices',
                'answers.choice',
                'exam',
                'user',
            ]);
        }

        return parent::getTableQuery();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Exam')
                ->visible(fn (): bool => $this->activeTab === 'exams'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'exams' => Tab::make('Exams')
                ->icon('heroicon-o-clipboard-document-list')
                ->badge(fn () => Exam::count()),

            'examinees' => Tab::make('Examinees')
                ->icon('heroicon-o-identification')
                ->badgeColor('info')
                ->badge(fn () => ExamAttempt::whereNull('archived_at')->count()),
        ];
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'examinees') {
            return $table
                ->columns([
                    TextColumn::make('user.name')
                        ->label('Name')
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('user.email')
                        ->label('Email')
                        ->searchable()
                        ->toggleable(),

                    TextColumn::make('exam.title')
                        ->label('Exam')
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('score')
                        ->label('Score')
                        ->formatStateUsing(fn ($record) => "{$record->score} / {$record->total_points}"),

                    TextColumn::make('percentage')
                        ->label('Percentage')
                        ->formatStateUsing(fn ($state) => "{$state}%")
                        ->sortable()
                        ->badge()
                        ->color(fn ($state) => match (true) {
                            $state >= 75 => 'success',
                            $state >= 50 => 'warning',
                            default      => 'danger',
                        }),

                    TextColumn::make('status')
                        ->label('Result')
                        ->badge()
                        ->getStateUsing(fn ($record) => $record->percentage >= 75 ? 'Passed' : 'Failed')
                        ->color(fn ($state) => match ($state) {
                            'Passed' => 'success',
                            'Failed' => 'danger',
                            default  => 'gray',
                        }),

                    TextColumn::make('scholarship_discount')
                        ->label('Scholarship')
                        ->badge()
                        ->getStateUsing(fn ($record) => ExamAttemptResource::resolveDiscount((float) $record->percentage)['short'])
                        ->color(fn ($record) => ExamAttemptResource::resolveDiscount((float) $record->percentage)['color'])
                        ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('percentage', $direction)),

                    TextColumn::make('violation_count')
                        ->label('Violations')
                        ->sortable()
                        ->badge()
                        ->getStateUsing(fn ($record) => (int) $record->getRawOriginal('violation_count') ?? (int) $record->violation_count)
                        ->color(fn ($state) => match (true) {
                            $state === 0 => 'success',
                            $state <= 3  => 'warning',
                            default      => 'danger',
                        })
                        ->formatStateUsing(fn ($state) => $state === 0 ? '✓ Clean' : "⚠ {$state} violation(s)"),

                    TextColumn::make('completed_at')
                        ->label('Completed')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->defaultSort('completed_at', 'desc')
                ->filters([
                    SelectFilter::make('exam')
                        ->relationship('exam', 'title')
                        ->label('Exam'),

                    SelectFilter::make('school_year')
                        ->label('School Year')
                        ->options(
                            Term::query()
                                ->select('school_year')
                                ->distinct()
                                ->orderByDesc('school_year')
                                ->pluck('school_year', 'school_year')
                        )
                        ->query(function (Builder $query, array $data) {
                            if (! $data['value']) {
                                return;
                            }

                            // school_year format: "YYYY-YYYY+1" — treated as an
                            // academic year running Aug 1 (start year) – Jul 31 (end year).
                            // ExamAttempt has no term_id/school_year column, so this is
                            // derived purely from completed_at.
                            [$startYear, $endYear] = explode('-', $data['value']);

                            $query->whereBetween('completed_at', [
                                "{$startYear}-08-01 00:00:00",
                                "{$endYear}-07-31 23:59:59",
                            ]);
                        }),

                    SelectFilter::make('result')
                        ->label('Result')
                        ->options([
                            'passed' => 'Passed',
                            'failed' => 'Failed',
                        ])
                        ->query(function (Builder $query, array $data) {
                            match ($data['value'] ?? null) {
                                'passed' => $query->where('percentage', '>=', 75),
                                'failed' => $query->where('percentage', '<', 75),
                                default  => null,
                            };
                        }),

                    SelectFilter::make('scholarship')
                        ->label('Scholarship')
                        ->options([
                            '95' => '100% + Misc. Discount (95–100)',
                            '85' => '100% Tuition Discount (85–94)',
                            '75' => '75% Tuition Discount (75–84)',
                            '65' => '50% Tuition Discount (65–74)',
                            '60' => '25% Tuition Discount (60–65)',
                            '50' => '10% Tuition Discount (50–59)',
                            '0'  => 'No Discount (Below 50)',
                        ])
                        ->query(function (Builder $query, array $data) {
                            match ($data['value'] ?? null) {
                                '95' => $query->whereBetween('percentage', [95, 100]),
                                '85' => $query->whereBetween('percentage', [85, 94.99]),
                                '75' => $query->whereBetween('percentage', [75, 84.99]),
                                '65' => $query->whereBetween('percentage', [65, 74.99]),
                                '60' => $query->whereBetween('percentage', [60, 64.99]),
                                '50' => $query->whereBetween('percentage', [50, 59.99]),
                                '0'  => $query->where('percentage', '<', 50),
                                default => null,
                            };
                        }),

                    SelectFilter::make('violations')
                        ->label('Violations')
                        ->options([
                            'clean'   => '✓ No Violations',
                            'flagged' => '⚠ Has Violations',
                        ])
                        ->query(function (Builder $query, array $data) {
                            match ($data['value'] ?? null) {
                                'clean'   => $query->where('violation_count', 0),
                                'flagged' => $query->where('violation_count', '>', 0),
                                default   => null,
                            };
                        }),

                    Filter::make('date_range')
                        ->label('Date Range')
                        ->form([
                            Forms\Components\DatePicker::make('from')->label('From'),
                            Forms\Components\DatePicker::make('until')->label('Until'),
                        ])
                        ->query(function (Builder $query, array $data) {
                            $query
                                ->when($data['from'],  fn ($q) => $q->whereDate('completed_at', '>=', $data['from']))
                                ->when($data['until'], fn ($q) => $q->whereDate('completed_at', '<=', $data['until']));
                        }),
                ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make()
                            ->url(fn (ExamAttempt $record) => ExamAttemptResource::getUrl('view', ['record' => $record])),

                        Tables\Actions\Action::make('print')
                            ->label('Print')
                            ->icon('heroicon-o-printer')
                            ->url(fn (ExamAttempt $record): string => route('exam-attempts.print', $record))
                            ->openUrlInNewTab(),

                        Tables\Actions\Action::make('archive')
                            ->label('Archive')
                            ->icon('heroicon-o-archive-box')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Archive Examinee Record')
                            ->modalDescription('This will hide the exam attempt from this list. You can restore it later from Settings → Archived Records.')
                            ->modalSubmitActionLabel('Yes, Archive')
                            ->action(function (ExamAttempt $record): void {
                                $record->update(['archived_at' => now()]);

                                $this->logCustomActivity(
                                    $record,
                                    'exam_attempts',
                                    'archived',
                                    "Archived exam attempt for {$record->user?->name} ({$record->exam?->title})"
                                );

                                Notification::make()
                                    ->title('Examinee record archived')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\BulkAction::make('archive_selected')
                            ->label('Archive Selected')
                            ->icon('heroicon-o-archive-box')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Archive Selected Records')
                            ->modalDescription('This will hide the selected exam attempts from this list. You can restore them later from Settings → Archived Records.')
                            ->modalSubmitActionLabel('Yes, Archive')
                            ->action(function ($records) {
                                $records->each(function (ExamAttempt $record) {
                                    $record->update(['archived_at' => now()]);

                                    $this->logCustomActivity(
                                        $record,
                                        'exam_attempts',
                                        'archived',
                                        "Archived exam attempt for {$record->user?->name} ({$record->exam?->title})"
                                    );
                                });

                                Notification::make()
                                    ->title('Examinee records archived')
                                    ->success()
                                    ->send();
                            })
                            ->deselectRecordsAfterCompletion(),
                    ]),
                ]);
        }

        // 'exams' tab (default) — unchanged, no archive here
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions')
                    ->badge(),

                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable(),

                TextColumn::make('passing_score')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All exams')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('questions')
                        ->label('Questions')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->url(fn (Exam $record) => url("/admin/exams/{$record->id}/questions")),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}