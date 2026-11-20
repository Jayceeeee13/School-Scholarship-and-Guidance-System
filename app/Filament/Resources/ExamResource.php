<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

    protected static ?string $navigationGroup = 'Exam Management';

    // Only one exam is managed, so the sidebar item is singular.
    protected static ?string $navigationLabel = 'Exam';

    protected static ?int $navigationSort = 1;

    /**
     * The sidebar item opens the exam itself instead of a table with one row.
     * (If no exam exists yet, it falls back to the normal list / create page.)
     */
    public static function getNavigationUrl(): string
    {
        $exam = Exam::query()->first();

        return $exam
            ? static::getUrl('edit', ['record' => $exam])
            : static::getUrl('index');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Overview (only when the exam already exists) ──
                Forms\Components\Section::make('Overview')
                    ->icon('heroicon-o-chart-bar')
                    ->visible(fn (?Exam $record) => filled($record))
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('questions_summary')
                                    ->label('Questions')
                                    ->content(function (?Exam $record): string {
                                        if (! $record) {
                                            return '—';
                                        }

                                        $count = $record->questions()->count();

                                        return $count . ' question' . ($count === 1 ? '' : 's');
                                    }),

                                Forms\Components\Placeholder::make('points_summary')
                                    ->label('Total points')
                                    ->content(fn (?Exam $record): string => $record
                                        ? (string) $record->questions()->sum('points')
                                        : '—'),

                                Forms\Components\Placeholder::make('examinees_summary')
                                    ->label('Examinees')
                                    ->content(fn (?Exam $record): string => $record
                                        ? (string) $record->attempts()->whereNull('archived_at')->count()
                                        : '—'),
                            ]),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('manageQuestions')
                                ->label('Manage Questions')
                                ->icon('heroicon-o-document-text')
                                ->color('info')
                                ->url(fn (?Exam $record) => $record
                                    ? url("/admin/exams/{$record->id}/questions")
                                    : null),

                            Forms\Components\Actions\Action::make('viewExaminees')
                                ->label('View Examinees')
                                ->icon('heroicon-o-identification')
                                ->color('gray')
                                ->url(fn () => static::getUrl('index')),
                        ]),
                    ]),

                // ── Exam details ──
                Forms\Components\Section::make('Exam Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Admission and Scholarship Test 2025'),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Brief description of the exam...'),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('duration_minutes')
                                    ->label('Duration (minutes)')
                                    ->required()
                                    ->numeric()
                                    ->default(120)
                                    ->suffix('minutes'),

                                Forms\Components\TextInput::make('passing_score')
                                    ->label('Passing Score (%)')
                                    ->required()
                                    ->numeric()
                                    ->default(50)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Only active exams can be taken by applicants'),
                            ]),

                        Forms\Components\TextInput::make('access_code')
                            ->label('Exam Access Code')
                            ->helperText('Students must enter this code before they can open the exam. Leave empty for no code. Click the arrows to generate a new one, then save.')
                            ->maxLength(12)
                            ->unique(ignoreRecord: true)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase; letter-spacing: .15em;'])
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? strtoupper(trim($state)) : null)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generateCode')
                                    ->icon('heroicon-m-arrow-path')
                                    ->tooltip('Generate a new code')
                                    ->action(fn (Set $set) => $set('access_code', Exam::generateAccessCode()))
                            ),
                    ]),
            ]);
    }

    /**
     * NOTE: Required by Filament's Resource base class, but not what
     * renders the index page. Pages\ListExams::table() overrides this
     * with the real tabbed (Exams / Examinees) table — see that class
     * for the live implementation.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions')
                    ->badge(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable(),

                Tables\Columns\TextColumn::make('passing_score')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('access_code')
                    ->label('Access Code')
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('Code copied')
                    ->placeholder('No code'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
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
                Tables\Actions\Action::make('questions')
                    ->label('Questions')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (Exam $record) => url("/admin/exams/{$record->id}/questions")),

                Tables\Actions\Action::make('generate_code')
                    ->label('Generate New Code')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Students who already entered the old code will have to enter the new one.')
                    ->action(function (Exam $record): void {
                        $record->update(['access_code' => Exam::generateAccessCode()]);

                        Notification::make()
                            ->title('New code: ' . $record->access_code)
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListExams::route('/'),
            'create'    => Pages\CreateExam::route('/create'),
            // "Exam Settings" page: EditExam with a different title and breadcrumb.
            'edit'      => Pages\ExamSettings::route('/{record}/edit'),
            'questions' => Pages\ManageExamQuestions::route('/{record}/questions'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    /**
     * Only one exam is ever managed, so "New Exam" disappears once it exists.
     */
    public static function canCreate(): bool
    {
        return ! Exam::query()->exists();
    }

    /**
     * The one exam can't be deleted from the panel.
     */
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }
}