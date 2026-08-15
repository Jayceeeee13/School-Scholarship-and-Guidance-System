<?php

namespace App\Filament\Widgets;

use App\Models\Applicant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestApplicantsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = [
    'default' => 'full',
    'md'      => 2,
];
protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest 5 Applicants')
            ->description('Most recently submitted applications')
            ->query(
                Applicant::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Submitted')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student Name')
                    ->weight('bold')
                    ->getStateUsing(fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->last_name}")),

                Tables\Columns\TextColumn::make('typeOfScholarship.name')
                    ->label('Scholarship Type')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.applicants.edit', [
                        'record' => $record->id
                    ])),
            ])
            ->paginated(false);
    }

    public static function canView(): bool
{
    return auth()->user()->isAdmin() || auth()->user()->isScholarship();
}
}