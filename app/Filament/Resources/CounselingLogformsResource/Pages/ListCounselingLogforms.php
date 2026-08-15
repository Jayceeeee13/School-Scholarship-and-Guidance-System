<?php

namespace App\Filament\Resources\CounselingLogformsResource\Pages;

use App\Filament\Resources\CounselingLogformsResource;
use App\Filament\Resources\AnecdotalsResource;
use App\Models\CounselingLogforms;
use App\Models\Anecdotals;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListCounselingLogforms extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = CounselingLogformsResource::class;

    public function updatedActiveTab(): void
    {
        // The 'anecdotals' tab uses a completely different model and columns
        // than the 'logforms' tab. resetTable() clears Filament's cached
        // Table instance (columns/filters/actions) in addition to resetting
        // pagination/search — without it, the previous tab's column config
        // can render against rows fetched for the new tab.
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        if ($this->activeTab === 'anecdotals') {
            return Anecdotals::query()->with(['logform.appointment', 'logform.walkInStudent', 'personnel']);
        }

        return parent::getTableQuery()->whereNull('archived_at')->with(['appointment', 'walkInStudent']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Logform')
                ->visible(fn (): bool => $this->activeTab === 'logforms'),

            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('counseling-logforms.print'))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->activeTab === 'logforms'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'logforms' => Tab::make('Logforms')
                ->icon('heroicon-o-document-text')
                ->badge(fn () => CounselingLogforms::whereNull('archived_at')->count()),

            'anecdotals' => Tab::make('Anecdotals')
                ->icon('heroicon-o-clipboard-document-list')
                ->badgeColor('warning')
                ->badge(fn () => Anecdotals::count()),
        ];
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'anecdotals') {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('logform.type')
                        ->label('Type')
                        ->badge()
                        ->color(fn (?string $state): string => $state === 'walk_in' ? 'warning' : 'info')
                        ->formatStateUsing(fn (?string $state): string => $state === 'walk_in' ? 'Walk-in' : 'Scheduled'),

                    Tables\Columns\TextColumn::make('display_name')
                        ->label('Student Name')
                        ->getStateUsing(fn (Anecdotals $record) => $record->logform?->display_name ?? '—')
                        ->searchable(query: function ($query, $search) {
                            return $query->whereHas('logform', function ($q) use ($search) {
                                $q->whereHas('appointment', function ($a) use ($search) {
                                    $a->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%");
                                })->orWhereHas('walkInStudent', function ($s) use ($search) {
                                    $s->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%");
                                });
                            });
                        }),

                    Tables\Columns\TextColumn::make('display_course')
                        ->label('Course & Year')
                        ->getStateUsing(fn (Anecdotals $record) => $record->logform?->display_course ?? '—'),

                    Tables\Columns\TextColumn::make('area_concern')
                        ->label('Area of Concern')
                        ->limit(40)
                        ->wrap(),

                    Tables\Columns\TextColumn::make('personnel.full_name')
                        ->label('Interviewed By')
                        ->getStateUsing(function (Anecdotals $record) {
                            if ($record->personnel) {
                                return trim("{$record->personnel->first_name} {$record->personnel->middle_name} {$record->personnel->last_name}");
                            }
                            return 'N/A';
                        })
                        ->searchable(query: function ($query, $search) {
                            return $query->whereHas('personnel', function ($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('middle_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                            });
                        })
                        ->sortable(query: function ($query, $direction) {
                            return $query->join('personnels', 'anecdotals.personnel_id', '=', 'personnels.id')
                                ->orderBy('personnels.last_name', $direction);
                        }),
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make()
                            ->url(fn (Anecdotals $record) => AnecdotalsResource::getUrl('view', ['record' => $record])),
                        Tables\Actions\EditAction::make()
                            ->url(fn (Anecdotals $record) => AnecdotalsResource::getUrl('edit', ['record' => $record])),
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

        // 'logforms' tab
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'walk_in' ? 'warning' : 'info')
                    ->formatStateUsing(fn (string $state): string => $state === 'walk_in' ? 'Walk-in' : 'Scheduled')
                    ->sortable(),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Student Name')
                    ->getStateUsing(fn (CounselingLogforms $record) => $record->display_name),

                Tables\Columns\TextColumn::make('display_course')
                    ->label('Course & Year')
                    ->getStateUsing(fn (CounselingLogforms $record) => $record->display_course),

                Tables\Columns\TextColumn::make('display_contact')
                    ->label('Contact')
                    ->getStateUsing(fn (CounselingLogforms $record) => $record->display_contact),

                Tables\Columns\TextColumn::make('supportNeeded.name')
    ->label('Support Needed')
    ->badge()
    ->color('info')
    ->placeholder('—'),

                Tables\Columns\TextColumn::make('concern')
                    ->label('Concern')
                    ->searchable(),

                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'walk_in'   => 'Walk-in',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Logform')
                        ->modalDescription('This will hide the logform from this list. You can restore it later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function (CounselingLogforms $record): void {
                            $record->update(['archived_at' => now()]);

                            $studentName = $record->display_name ?: "Logform #{$record->id}";

                            $this->logCustomActivity(
                                $record,
                                'logforms',
                                'archived',
                                "Archived logform for {$studentName}"
                            );

                            Notification::make()
                                ->title('Logform archived')
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
                    Tables\Actions\BulkAction::make('print_selected')
                        ->label('Print Selected')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->implode(',');
                            return redirect()->away(route('counseling-logforms.print', ['ids' => $ids]));
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('archive_selected')
                        ->label('Archive Selected')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Selected Logforms')
                        ->modalDescription('This will hide the selected logforms from this list. You can restore them later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function ($records) {
                            $records->each(function (CounselingLogforms $record) {
                                $record->update(['archived_at' => now()]);

                                $studentName = $record->display_name ?: "Logform #{$record->id}";

                                $this->logCustomActivity(
                                    $record,
                                    'logforms',
                                    'archived',
                                    "Archived logform for {$studentName}"
                                );
                            });

                            Notification::make()
                                ->title('Logforms archived')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}