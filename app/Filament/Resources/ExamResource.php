<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

    protected static ?string $navigationGroup = 'Exam Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            'edit'      => Pages\EditExam::route('/{record}/edit'),
            'questions' => Pages\ManageExamQuestions::route('/{record}/questions'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }
}