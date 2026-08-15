<?php

namespace App\Filament\Resources\ExamAttemptResource\Pages;

use App\Filament\Resources\ExamAttemptResource;
use App\Models\ExamAttempt;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class ViewExamAttempt extends ViewRecord
{
    protected static string $resource = ExamAttemptResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([

                Section::make('Examinee Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Name'),

                        TextEntry::make('user.email')
                            ->label('Email'),

                        TextEntry::make('exam.title')
                            ->label('Exam'),

                        TextEntry::make('score')
                            ->label('Score')
                            ->formatStateUsing(fn ($record) => "{$record->score} / {$record->total_points}"),

                        TextEntry::make('percentage')
                            ->label('Percentage')
                            ->formatStateUsing(fn ($state) => "{$state}%")
                            ->badge()
                            ->color(fn ($state) => match (true) {
                                $state >= 75 => 'success',
                                $state >= 50 => 'warning',
                                default      => 'danger',
                            }),

                        TextEntry::make('completed_at')
                            ->label('Completed At')
                            ->dateTime('M d, Y h:i A'),
                    ]),

                Section::make('Scholarship Grant')
                    ->description('Based on Section 5.4.1 of the scholarship guidelines.')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('scholarship_discount')
                            ->label('Discount Tier')
                            ->badge()
                            ->getStateUsing(fn ($record) => ExamAttemptResource::resolveDiscount((float) $record->percentage)['label'])
                            ->color(fn ($record) => ExamAttemptResource::resolveDiscount((float) $record->percentage)['color']),

                        TextEntry::make('scholarship_range')
                            ->label('Qualifying Score Range')
                            ->getStateUsing(fn ($record) => match (true) {
                                (float) $record->percentage >= 95 => '95 – 100  (100% Tuition Fee and Misc. Discount)',
                                (float) $record->percentage >= 85 => '85 – 94   (100% Tuition Fee Discount)',
                                (float) $record->percentage >= 75 => '75 – 84   (75% Tuition Fee Discount)',
                                (float) $record->percentage >= 65 => '65 – 74   (50% Tuition Fee Discount)',
                                (float) $record->percentage >= 60 => '60 – 65   (25% Tuition Fee Discount)',
                                (float) $record->percentage >= 50 => '50 – 59   (10% Tuition Fee Discount)',
                                default                           => 'Below 50  (No discount qualifed)',
                            }),
                    ]),

                Section::make('Answer Breakdown by Category')
                    ->schema([
                        ViewEntry::make('answers')
                            ->label('')
                            ->view('filament.infolists.exam-answers'),
                    ]),
            ]);
    }

    protected function resolveRecord(int | string $key): ExamAttempt
    {
        return ExamAttempt::with([
            'exam',
            'user',
            'answers.question.examCategory',
            'answers.question.choices',
            'answers.choice',
        ])->findOrFail($key);
    }
}