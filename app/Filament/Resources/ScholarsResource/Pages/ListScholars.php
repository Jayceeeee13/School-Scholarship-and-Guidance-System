<?php

namespace App\Filament\Resources\ScholarsResource\Pages;

use App\Filament\Resources\ScholarsResource;
use App\Models\DailyTimeRecord;
use App\Models\Department;
use App\Models\Scholars;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListScholars extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = ScholarsResource::class;

    public function updatedActiveTab(): void
    {
        $this->resetTable();
    }

    protected function scopeDtrQueryToRole(Builder $query): Builder
    {
        $user = auth()->user();

        if ($user->isDepartmentHead()) {
            return $query->whereHas('scholar', fn ($q) => $q->where('department_head_id', $user->id));
        }

        if ($user->hasAnyRole(['admin', 'scholarship'])) {
            return $query->whereIn('status', ['submitted', 'received']);
        }

        return $query;
    }

    protected function recalculateTotalHours(callable $get, callable $set): void
    {
        $total = DailyTimeRecord::calculateTotalHours(
            $get('am_in'),
            $get('am_out'),
            $get('pm_in'),
            $get('pm_out'),
        );

        $set('total_hours', $total);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => $this->activeTab !== 'dtr'),

            Actions\Action::make('newDtr')
                ->label('New DTR Entry')
                ->icon('heroicon-o-plus-circle')
                ->color('gray')
                ->visible(fn (): bool => $this->activeTab === 'dtr')
                ->modalHeading('New Daily Time Record')
                ->modalDescription('Log a scholar\'s time-in/out for a single day. This entry starts as Pending until it\'s approved.')
                ->modalSubmitActionLabel('Save Entry')
                ->form([
                    Forms\Components\Section::make('Who & Where')
                        ->description('Select the scholar and confirm their assigned office.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Select::make('scholar_id')
                                ->label('Scholar (Student Representatives)')
                                ->options(function () {
                                    $query = Scholars::where('type_of_scholarship', 'Student Representatives');

                                    if (auth()->user()->isDepartmentHead()) {
                                        $query->where('department_head_id', auth()->id());
                                    }

                                    return $query->get()->mapWithKeys(fn ($s) => [
                                        $s->id => "{$s->first_name} {$s->last_name}",
                                    ]);
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->native(false)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) {
                                        $set('office_assigned', null);
                                        return;
                                    }

                                    $scholar = Scholars::with('departmentHead.department')->find($state);

                                    if ($scholar?->departmentHead?->department?->name) {
                                        $set('office_assigned', $scholar->departmentHead->department->name);
                                        return;
                                    }

                                    $lastOffice = DailyTimeRecord::where('scholar_id', $state)
                                        ->whereNotNull('office_assigned')
                                        ->latest('date')
                                        ->value('office_assigned');

                                    $set('office_assigned', $lastOffice);
                                }),

                            Forms\Components\Select::make('office_assigned')
                                ->label('Office Assigned')
                                ->helperText('Auto-filled from the scholar\'s department head — adjust if needed.')
                                ->options(fn () => Department::active()->pluck('name', 'name'))
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->required(),
                        ])
                        ->compact(),

                    Forms\Components\Section::make('When')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Forms\Components\DatePicker::make('date')
                                ->label('Date')
                                ->required()
                                ->native(false)
                                ->displayFormat('M d, Y')
                                ->maxDate(now())
                                ->helperText('Month/year for reporting is taken automatically from this date.'),
                        ])
                        ->compact(),

                    Forms\Components\Section::make('Time Log')
                        ->description('Total Hours below updates automatically as you fill these in.')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('am_in')
                                        ->label('AM In')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('am_in_location')
                                        ->label('AM In Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('am_out')
                                        ->label('AM Out')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('am_out_location')
                                        ->label('AM Out Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('pm_in')
                                        ->label('PM In')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('pm_in_location')
                                        ->label('PM In Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('pm_out')
                                        ->label('PM Out')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('pm_out_location')
                                        ->label('PM Out Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\TextInput::make('total_hours')
                                ->label('Total Hours')
                                ->numeric()
                                ->step(0.01)
                                ->readOnly()
                                ->default(0)
                                ->suffix('hrs')
                                ->helperText('Automatically computed — you don\'t need to fill this in.')
                                ->dehydrated(true)
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->collapsible(),

                    Forms\Components\Textarea::make('remarks')
                        ->label('Remarks')
                        ->placeholder('Optional notes for this entry...')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $data['total_hours'] = DailyTimeRecord::calculateTotalHours(
                        $data['am_in'] ?? null,
                        $data['am_out'] ?? null,
                        $data['pm_in'] ?? null,
                        $data['pm_out'] ?? null,
                    );

                    $record = DailyTimeRecord::create($data + ['status' => 'pending']);

                    $this->logCustomActivity(
                        $record,
                        'dtr',
                        'created',
                        "Added DTR entry for {$record->date?->format('M d, Y')} ({$record->office_assigned})"
                    );

                    Notification::make()
                        ->title('DTR entry added')
                        ->body('This entry is now Pending and awaiting approval.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ScholarsResource::getTabs();

        $tabs['dtr'] = Tab::make('DTR')
            ->icon('heroicon-o-clock')
            ->badge(function () {
                $user = auth()->user();

                if (! $user->isDepartmentHead() && $user->hasAnyRole(['admin', 'scholarship'])) {
                    return DailyTimeRecord::where('status', 'submitted')->count();
                }

                $query = DailyTimeRecord::where('status', 'pending');

                if ($user->isDepartmentHead()) {
                    $query->whereHas('scholar', fn ($q) => $q->where('department_head_id', $user->id));
                }

                return $query->count();
            })
            ->badgeColor('warning');

        return $tabs;
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'dtr') {
            return $table
                ->query(function () {
                    $query = DailyTimeRecord::query()->with(['scholar', 'approvedBy', 'receivedBy']);

                    return $this->scopeDtrQueryToRole($query);
                })
                ->columns([
                    Tables\Columns\TextColumn::make('scholar.full_name')
                        ->label('Name')
                        ->getStateUsing(fn (DailyTimeRecord $record) => $record->scholar
                            ? trim("{$record->scholar->first_name} {$record->scholar->last_name}")
                            : '—')
                        ->searchable(query: function (Builder $query, string $search) {
                            return $query->whereHas('scholar', function ($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                            });
                        })
                        ->sortable(),

                    Tables\Columns\TextColumn::make('scholar_course_year')
                        ->label('Course & Year')
                        ->getStateUsing(function (DailyTimeRecord $record) {
                            if (! $record->scholar) {
                                return '—';
                            }

                            $yearLabel = match ((string) $record->scholar->year_level) {
                                '1' => '1st Year',
                                '2' => '2nd Year',
                                '3' => '3rd Year',
                                '4' => '4th Year',
                                '5' => '5th Year',
                                default => $record->scholar->year_level,
                            };

                            return trim("{$record->scholar->program} - {$yearLabel}", ' -');
                        })
                        ->toggleable(),

                    Tables\Columns\TextColumn::make('office_assigned')
                        ->label('Office Assigned')
                        ->searchable()
                        ->sortable()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('month_label')
                        ->label('Month')
                        ->getStateUsing(fn (DailyTimeRecord $record) => $record->month_label)
                        ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('date', $direction))
                        ->toggleable(),

                    Tables\Columns\TextColumn::make('date')
                        ->label('Date')
                        ->date('M d, Y')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('am_in')
                        ->label('AM In')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_in, $record->am_in_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('am_out')
                        ->label('AM Out')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_out, $record->am_out_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('pm_in')
                        ->label('PM In')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_in, $record->pm_in_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('pm_out')
                        ->label('PM Out')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_out, $record->pm_out_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('total_hours')
                        ->label('Total Hrs')
                        ->numeric(2)
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'   => 'warning',
                            'approved'  => 'info',
                            'submitted' => 'primary',
                            'received'  => 'success',
                            'rejected'  => 'danger',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                        ->tooltip(fn (string $state): string => match ($state) {
                            'pending'   => 'Awaiting Department Head approval.',
                            'approved'  => 'Approved by Department Head — not yet sent to Admin/Scholarship.',
                            'submitted' => 'Sent to Admin/Scholarship, awaiting receipt.',
                            'received'  => 'Received and finalized by Admin/Scholarship.',
                            'rejected'  => 'Rejected — see remarks for the reason.',
                            default     => ucfirst($state),
                        })
                        ->sortable(),

                    Tables\Columns\TextColumn::make('approvedBy.name')
                        ->label('Approved By')
                        ->placeholder('—')
                        ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('receivedBy.name')
                        ->label('Received By')
                        ->placeholder('—')
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
                ->filters([
                    Tables\Filters\SelectFilter::make('status')
                        ->options(function () {
                            $user = auth()->user();

                            if (! $user->isDepartmentHead() && $user->hasAnyRole(['admin', 'scholarship'])) {
                                return [
                                    'submitted' => 'Submitted',
                                    'received'  => 'Received',
                                ];
                            }

                            return [
                                'pending'   => 'Pending',
                                'approved'  => 'Approved',
                                'submitted' => 'Submitted',
                                'received'  => 'Received',
                                'rejected'  => 'Rejected',
                            ];
                        }),

                    Tables\Filters\SelectFilter::make('month')
                        ->label('Month')
                        ->options(fn () => DailyTimeRecord::query()
                            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym, DATE_FORMAT(date, '%M %Y') as label")
                            ->distinct()
                            ->orderByDesc('ym')
                            ->pluck('label', 'ym'))
                        ->query(function (Builder $query, array $data) {
                            if (! $data['value']) {
                                return;
                            }

                            [$year, $month] = explode('-', $data['value']);

                            $query->whereYear('date', $year)->whereMonth('date', $month);
                        }),

                    Tables\Filters\SelectFilter::make('office_assigned')
                        ->label('Office Assigned')
                        ->options(fn () => Department::active()->pluck('name', 'name')),
                ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make()
                            ->infolist([
                                \Filament\Infolists\Components\Section::make('Scholar Information')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('scholar_name')
                                            ->label('Name')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => $record->scholar
                                                ? trim("{$record->scholar->first_name} {$record->scholar->last_name}")
                                                : '—'),

                                        \Filament\Infolists\Components\TextEntry::make('scholar_course_year')
                                            ->label('Course & Year')
                                            ->getStateUsing(function (DailyTimeRecord $record) {
                                                if (! $record->scholar) {
                                                    return '—';
                                                }

                                                $yearLabel = match ((string) $record->scholar->year_level) {
                                                    '1' => '1st Year',
                                                    '2' => '2nd Year',
                                                    '3' => '3rd Year',
                                                    '4' => '4th Year',
                                                    '5' => '5th Year',
                                                    default => $record->scholar->year_level,
                                                };

                                                return trim("{$record->scholar->program} - {$yearLabel}", ' -');
                                            }),

                                        \Filament\Infolists\Components\TextEntry::make('office_assigned')
                                            ->label('Office Assigned')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('month_label')
                                            ->label('Month')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => $record->month_label),
                                    ])
                                    ->columns(2),

                                \Filament\Infolists\Components\Section::make('Time & Location Log')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('date')
                                            ->label('Date')
                                            ->date('M d, Y'),

                                        \Filament\Infolists\Components\TextEntry::make('total_hours')
                                            ->label('Total Hours')
                                            ->numeric(2)
                                            ->suffix(' hrs')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('am_in')
                                            ->label('AM In')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_in, $record->am_in_location))
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('am_out')
                                            ->label('AM Out')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_out, $record->am_out_location))
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('pm_in')
                                            ->label('PM In')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_in, $record->pm_in_location))
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('pm_out')
                                            ->label('PM Out')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_out, $record->pm_out_location))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(3),

                                \Filament\Infolists\Components\Section::make('Status & Routing')
                                    ->icon('heroicon-o-arrow-path-rounded-square')
                                    ->description('Where this entry currently sits in the approval chain.')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'pending'   => 'warning',
                                                'approved'  => 'info',
                                                'submitted' => 'primary',
                                                'received'  => 'success',
                                                'rejected'  => 'danger',
                                                default     => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                                        \Filament\Infolists\Components\TextEntry::make('approvedBy.name')
                                            ->label('Approved By')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('approved_at')
                                            ->label('Approved At')
                                            ->dateTime('M d, Y h:i A')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('receivedBy.name')
                                            ->label('Received By')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('received_at')
                                            ->label('Received At')
                                            ->dateTime('M d, Y h:i A')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('remarks')
                                            ->label('Remarks')
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tables\Actions\EditAction::make()
                            ->mutateFormDataUsing(function (array $data): array {
                                $data['total_hours'] = DailyTimeRecord::calculateTotalHours(
                                    $data['am_in'] ?? null,
                                    $data['am_out'] ?? null,
                                    $data['pm_in'] ?? null,
                                    $data['pm_out'] ?? null,
                                );

                                return $data;
                            }),

                        Tables\Actions\Action::make('approve')
                            ->label('Approve')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->requiresConfirmation()
                            ->modalHeading('Approve DTR')
                            ->modalDescription('This marks the entry as Approved. It will then need to be Submitted to Admin/Scholarship separately.')
                            ->modalSubmitActionLabel('Yes, Approve')
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'pending'
                                && (auth()->user()->isDepartmentHead() || auth()->user()->isAdmin())
                            )
                            ->action(function (DailyTimeRecord $record): void {
                                $record->update([
                                    'status'         => 'approved',
                                    'approved_by_id' => auth()->id(),
                                    'approved_at'    => now(),
                                ]);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'approved',
                                    "Approved DTR for {$record->scholar?->first_name} {$record->scholar?->last_name} ({$record->date?->format('M d, Y')})"
                                );

                                Notification::make()
                                    ->title('DTR Approved')
                                    ->body('Next step: Submit this entry to Admin/Scholarship.')
                                    ->success()
                                    ->send();
                            }),

                        Tables\Actions\Action::make('submit')
                            ->label('Submit')
                            ->icon('heroicon-o-paper-airplane')
                            ->color('primary')
                            ->requiresConfirmation()
                            ->modalHeading('Submit DTR to Admin/Scholarship')
                            ->modalDescription('This sends the approved entry onward. It will show as "Submitted" until Admin/Scholarship marks it Received.')
                            ->modalSubmitActionLabel('Yes, Submit')
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'approved'
                                && (auth()->user()->isDepartmentHead() || auth()->user()->isAdmin())
                            )
                            ->action(function (DailyTimeRecord $record): void {
                                $record->update(['status' => 'submitted']);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'submitted',
                                    "Submitted DTR for {$record->scholar?->first_name} {$record->scholar?->last_name} to Admin/Scholarship ({$record->date?->format('M d, Y')})"
                                );

                                Notification::make()
                                    ->title('DTR Submitted')
                                    ->success()
                                    ->body('The DTR has been sent to Admin and Scholarship for receiving.')
                                    ->send();
                            }),

                        Tables\Actions\Action::make('reject')
                            ->label('Reject')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Reject DTR')
                            ->modalDescription('The scholar/department head will need to correct and re-add this entry.')
                            ->form([
                                Forms\Components\Textarea::make('remarks')
                                    ->label('Reason for Rejection')
                                    ->placeholder('e.g., Missing PM Out time, incorrect location...')
                                    ->required()
                                    ->rows(2),
                            ])
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'pending'
                                && (auth()->user()->isDepartmentHead() || auth()->user()->isAdmin())
                            )
                            ->action(function (DailyTimeRecord $record, array $data): void {
                                $record->update([
                                    'status'  => 'rejected',
                                    'remarks' => $data['remarks'],
                                ]);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'rejected',
                                    "Rejected DTR for {$record->scholar?->first_name} {$record->scholar?->last_name} ({$record->date?->format('M d, Y')})",
                                    ['reason' => $data['remarks']]
                                );

                                Notification::make()
                                    ->title('DTR Rejected')
                                    ->danger()
                                    ->send();
                            }),

                        Tables\Actions\Action::make('receive')
                            ->label('Mark Received')
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->color('info')
                            ->requiresConfirmation()
                            ->modalHeading('Mark DTR as Received')
                            ->modalDescription('This finalizes the entry — confirms Admin/Scholarship has it on file.')
                            ->modalSubmitActionLabel('Yes, Mark Received')
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'submitted'
                                && auth()->user()->hasAnyRole(['admin', 'scholarship'])
                            )
                            ->action(function (DailyTimeRecord $record): void {
                                $record->update([
                                    'status'         => 'received',
                                    'received_by_id' => auth()->id(),
                                    'received_at'    => now(),
                                ]);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'received',
                                    "Marked DTR as received for {$record->scholar?->first_name} {$record->scholar?->last_name} ({$record->date?->format('M d, Y')})"
                                );

                                Notification::make()
                                    ->title('DTR Marked as Received')
                                    ->success()
                                    ->send();
                            }),

                        Tables\Actions\DeleteAction::make(),
                    ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
                ])
                ->defaultSort('date', 'desc');
        }

        return ScholarsResource::table($table);
    }

    protected static function formatPunch(?string $time, ?string $location): ?string
    {
        if (! $time) {
            return null;
        }

        $formatted = \Carbon\Carbon::parse($time)->format('h:i A');

        return $location ? "{$formatted} — {$location}" : $formatted;
    }
}