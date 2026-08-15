<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamAttemptResource\Pages;
use App\Models\ExamAttempt;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Database\Eloquent\Builder;

class ExamAttemptResource extends Resource
{
    protected static ?string $model = ExamAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-s-identification';

    protected static ?string $navigationLabel = 'Examinees';

    protected static ?string $modelLabel = 'Examinee';

    protected static ?string $pluralModelLabel = 'Examinees';

    protected static ?string $navigationGroup = 'Exam Management';

    protected static ?int $navigationSort = 2;

    /**
     * Scholarship discount guidelines (Section 5.4.1):
     *
     *  95 – 100  →  100% Tuition Fee and Misc. Discount
     *  85 –  94  →  100% Tuition Fee Discount
     *  75 –  84  →  75%  Tuition Fee Discount
     *  65 –  74  →  50%  Tuition Fee Discount
     *  60 –  65  →  25%  Tuition Fee Discount
     *  50 –  59  →  10%  Tuition Fee Discount
     *   0 –  49  →  No Discount
     */
    public static function resolveDiscount(float $pct): array
    {
        return match (true) {
            $pct >= 95 => ['label' => '100% Tuition Fee and Misc. Discount', 'short' => '100% + Misc.', 'color' => 'success'],
            $pct >= 85 => ['label' => '100% Tuition Fee Discount',           'short' => '100% Tuition', 'color' => 'success'],
            $pct >= 75 => ['label' => '75% Tuition Fee Discount',            'short' => '75% Tuition',  'color' => 'info'],
            $pct >= 65 => ['label' => '50% Tuition Fee Discount',            'short' => '50% Tuition',  'color' => 'primary'],
            $pct >= 60 => ['label' => '25% Tuition Fee Discount',            'short' => '25% Tuition',  'color' => 'warning'],
            $pct >= 50 => ['label' => '10% Tuition Fee Discount',            'short' => '10% Tuition',  'color' => 'warning'],
            default    => ['label' => 'No Discount',                         'short' => 'No Discount',  'color' => 'danger'],
        };
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'answers.question.examCategory',
            'answers.question.choices',
            'answers.choice',
            'exam',
            'user',
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ViewEntry::make('answers')
                    ->label('')
                    ->view('filament.infolists.exam-answers'),
            ]);
    }

    /**
     * NOTE: Required by Filament's Resource base class, but this resource
     * no longer has its own navigation entry — its records are now listed
     * under ExamResource's "Examinees" tab. This table() is only a fallback;
     * see ExamResource\Pages\ListExams::table() for the live implementation.
     */
    public static function table(Table $table): Table
    {
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
                    ->getStateUsing(fn ($record) => static::resolveDiscount((float) $record->percentage)['short'])
                    ->color(fn ($record) => static::resolveDiscount((float) $record->percentage)['color'])
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
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (ExamAttempt $record): string =>
                        route('exam-attempts.print', $record)
                    )
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamAttempts::route('/'),
            'view'  => Pages\ViewExamAttempt::route('/{record}'),
            // No 'print' page — handled by ExamResultSlipController
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // now shown as the "Examinees" tab on ExamResource instead
    }
}   