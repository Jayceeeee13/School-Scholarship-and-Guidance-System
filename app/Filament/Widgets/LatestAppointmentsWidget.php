<?php

namespace App\Filament\Widgets;

use App\Models\CounselingAppointments;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAppointmentsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = [
    'default' => 'full',
    'md'      => 2,
];
protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest 5 Appointments')
            ->description('Most recent appointments')
            ->query(
                CounselingAppointments::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('counseling_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('timeSlot.name')
                    ->label('Time')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->weight('bold')
                    ->getStateUsing(fn ($record) => $record->full_name),
                
                Tables\Columns\TextColumn::make('course_and_year')
                    ->label('Course & Year')
                    ->searchable(),
                
                // Tables\Columns\TextColumn::make('supportNeeded.name')
                //     ->label('Support Type')
                //     ->badge()
                //     ->color('success'),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                
                // Tables\Columns\TextColumn::make('created_at')
                //     ->label('Created')
                //     ->dateTime('M d, h:i A')
                //     ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.counseling-appointments.edit', ['record' => $record->id]))
                    ->color('primary'),
            ])
            ->paginated(false); // No pagination for just 5 records
    }

    public static function canView(): bool
{
    return auth()->user()->isAdmin() || auth()->user()->isGuidance();
}
}