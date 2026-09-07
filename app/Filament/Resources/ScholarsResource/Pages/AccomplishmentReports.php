<?php

namespace App\Filament\Resources\ScholarsResource\Pages;

use App\Filament\Resources\ScholarsResource;
use App\Models\AccomplishmentReport;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AccomplishmentReports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ScholarsResource::class;

    protected static string $view = 'filament.resources.scholars-resource.pages.accomplishment-reports';

    protected static ?string $title = 'Accomplishment Reports';

    public function table(Table $table): Table
    {
        return $table
            // AccomplishmentReport::scholar() is polymorphic (morphTo), so
            // this single query naturally covers reports belonging to
            // BOTH Scholars and InstitutionalScholar — no need to union
            // two separate queries.
            ->query(AccomplishmentReport::query()->with(['scholar', 'term', 'activities']))
            ->columns([
                Tables\Columns\TextColumn::make('scholar.full_name')
                    ->label('Scholar')
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHasMorph('scholar', ['App\Models\Scholars', 'App\Models\InstitutionalScholar'], function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('scholar_type')
                    ->label('Scholar Type')
                    ->formatStateUsing(fn (string $state) => str_contains($state, 'InstitutionalScholar')
                        ? 'Institutional'
                        : 'Regular')
                    ->badge()
                    ->color(fn (string $state) => str_contains($state, 'InstitutionalScholar') ? 'info' : 'gray'),

                Tables\Columns\TextColumn::make('scholar.type_of_scholarship')
                    ->label('Scholarship')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('term.school_year')
                    ->label('Term')
                    ->formatStateUsing(fn ($state, $record) => $record->term
                        ? "{$record->term->school_year} — {$record->term->semester}"
                        : '—'),

                Tables\Columns\TextColumn::make('activities_count')
                    ->label('Activities')
                    ->getStateUsing(fn ($record) => $record->activities->count()),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->infolist([
                        \Filament\Infolists\Components\Section::make('Scholar')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('scholar.full_name')
                                    ->label('Name'),
                                \Filament\Infolists\Components\TextEntry::make('scholar.type_of_scholarship')
                                    ->label('Scholarship'),
                                \Filament\Infolists\Components\TextEntry::make('term.school_year')
                                    ->label('Term')
                                    ->formatStateUsing(fn ($state, $record) => $record->term
                                        ? "{$record->term->school_year} — {$record->term->semester}"
                                        : '—'),
                            ])
                            ->columns(3),

                        \Filament\Infolists\Components\Section::make('Activities')
                            ->schema([
                                \Filament\Infolists\Components\RepeatableEntry::make('activities')
                                    ->label('')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('activity_date')
                                            ->label('Date')
                                            ->date('M d, Y'),
                                        \Filament\Infolists\Components\TextEntry::make('venue')
                                            ->label('Venue'),
                                        \Filament\Infolists\Components\TextEntry::make('activity')
                                            ->label('Activity'),
                                    ])
                                    ->columns(3),
                            ]),

                        \Filament\Infolists\Components\Section::make('Office Remarks')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('remarks')
                                    ->label('')
                                    ->placeholder('No remarks yet.'),
                            ])
                            ->visible(fn ($record) => $record->status !== 'pending'),
                    ]),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AccomplishmentReport $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (AccomplishmentReport $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Report approved')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (AccomplishmentReport $record) => $record->status === 'pending')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('remarks')
                            ->label('Reason for rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (AccomplishmentReport $record, array $data) {
                        $record->update(['status' => 'rejected', 'remarks' => $data['remarks']]);
                        Notification::make()->title('Report rejected')->danger()->send();
                    }),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}