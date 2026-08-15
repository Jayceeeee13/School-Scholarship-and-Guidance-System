<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\Pages;

use App\Filament\Resources\CounselingAppointmentsResource;
use App\Models\CounselingAppointments;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ListCounselingAppointments extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = CounselingAppointmentsResource::class;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery()->whereNull('archived_at');

        if ($this->activeTab === 'endorsed') {
            $query->whereHas('endorsement')->with(['endorsement.personnel']);
        }

        if ($this->activeTab === 'follow_ups') {
            $query->whereNotNull('parent_appointment_id');
        }

        if ($this->activeTab === 'all') {
            $query->whereNull('parent_appointment_id');
        }

        return $query
            ->withCount('followUps')
            ->orderBy('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Actions\Action::make('calendar')
            //     ->label('Calendar View')
            //     ->icon('heroicon-o-calendar')
            //     ->color('info')
            //     ->url('/admin-calendar'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Appointments')
                ->icon('heroicon-o-calendar-days'),

            'endorsed' => Tab::make('Endorsements')
                ->icon('heroicon-o-paper-airplane')
                ->badgeColor('info')
                ->badge(fn () => CounselingAppointments::whereNull('archived_at')->whereHas('endorsement')->count()),

            'follow_ups' => Tab::make('Follow-ups')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->badgeColor('info')
                ->badge(fn () => CounselingAppointments::whereNull('archived_at')->whereNotNull('parent_appointment_id')->count()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                // Shared by all tabs
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name', 'middle_name'])
                    ->sortable(['last_name', 'first_name'])
                    ->getStateUsing(fn ($record) => $record->full_name)
                    ->tooltip(fn ($record) =>
                        $record->isFollowUp() && $record->parentAppointment
                            ? 'Follow-up of ' . \Carbon\Carbon::parse($record->parentAppointment->counseling_date)->format('M d, Y')
                            : null
                    )
                    ->color(fn ($record) => $record->isFollowUp() ? 'info' : null),

                Tables\Columns\TextColumn::make('course_and_year')
                    ->label('Course & Year')
                    ->searchable()
                    ->sortable(),

                // ── Appointments / Follow-ups columns ──────────────────
                Tables\Columns\TextColumn::make('counseling_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->hidden(fn () => $this->activeTab === 'endorsed'),

                Tables\Columns\TextColumn::make('timeSlot.name')
                    ->label('Time Slot')
                    ->sortable()
                    ->hidden(fn () => $this->activeTab === 'endorsed'),

                Tables\Columns\TextColumn::make('modeOfCounseling.name')
                    ->label('Mode')
                    ->badge()
                    ->hidden(fn () => $this->activeTab === 'endorsed'),

                Tables\Columns\TextColumn::make('supportNeeded.name')
                    ->label('Support Needed')
                    ->wrap()
                    ->hidden(fn () => $this->activeTab === 'endorsed'),

                Tables\Columns\TextColumn::make('follow_ups_count')
                    ->label('Follow-ups')
                    ->counts('followUps')
                    ->badge()
                    ->color('info')
                    ->hidden(fn () => $this->activeTab === 'endorsed'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected'])
                    ->hidden(fn () => $this->activeTab === 'endorsed'),

                // ── Endorsements-only columns ──────────────────────────
                Tables\Columns\TextColumn::make('endorsement.to_where')
                    ->label('Endorsed To')
                    ->default('-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),

                Tables\Columns\TextColumn::make('endorsement.date')
                    ->label('Endorse Date')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('M d, Y') : '-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),

                Tables\Columns\TextColumn::make('endorsement.issue')
                    ->label('Issue')
                    ->limit(50)
                    ->default('-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),

                Tables\Columns\TextColumn::make('endorsed_by')
                    ->label('Endorsed By')
                    ->getStateUsing(fn ($record) => $record->endorsement?->personnel
                        ? trim("{$record->endorsement->personnel->first_name} {$record->endorsement->personnel->middle_name} {$record->endorsement->personnel->last_name}")
                        : '-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mode_of_couseling_id')
                    ->label('Mode of Counseling')
                    ->relationship('modeOfCounseling', 'name'),
                Tables\Filters\SelectFilter::make('support_needed_id')
                    ->label('Support Needed')
                    ->relationship('supportNeeded', 'name'),
                Tables\Filters\Filter::make('counseling_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('date_from')->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('date_until')->label('Until Date'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['date_from'] ?? null, fn ($q) => $q->whereDate('counseling_date', '>=', $data['date_from']))
                        ->when($data['date_until'] ?? null, fn ($q) => $q->whereDate('counseling_date', '<=', $data['date_until']))),
            ])
            ->actions([
                // ── Appointments / Follow-ups actions ──────────────────
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Appointment')
                        ->modalDescription('Are you sure you want to approve this appointment?')
                        ->modalSubmitActionLabel('Yes, Approve')
                        ->visible(fn ($record): bool => !in_array($record->status, ['approved', 'cancelled']))
                        ->action(function ($record) {
                            $record->update(['status' => 'approved']);
                            $record->notifyStudent('approved');

                            $this->logCustomActivity(
                                $record,
                                'appointments',
                                'approved',
                                "Approved counseling appointment for {$record->full_name}"
                            );

                            Notification::make()->title('Appointment Approved')->success()->send();
                        }),

                    // ── Reschedule (replaces Reject) ────────────────────
                    Tables\Actions\Action::make('reschedule')
                        ->label('Reschedule')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn ($record): bool => !in_array($record->status, ['cancelled']))
                        ->form([
                            \Filament\Forms\Components\DatePicker::make('counseling_date')
                                ->label('New Date')
                                ->required()
                                ->native(false)
                                ->displayFormat('F d, Y')
                                ->minDate(now())
                                ->disabledDates(fn () => \App\Models\InactiveDate::getInactiveDates())
                                ->live()
                                ->afterStateUpdated(fn (callable $set) => $set('time_slot_id', null)),

                            \Filament\Forms\Components\Select::make('time_slot_id')
                                ->label('New Time Slot')
                                ->options(function (callable $get, $record) {
                                    $selectedDate = $get('counseling_date');
                                    $timeSlots = \App\Models\CounselingTimeSlot::where('is_active', true)->orderBy('name')->get();

                                    if (!$selectedDate) {
                                        return $timeSlots->pluck('name', 'id');
                                    }

                                    $reserved = CounselingAppointments::whereDate('counseling_date', $selectedDate)
                                        ->where('id', '!=', $record->id)
                                        ->pluck('time_slot_id')->toArray();

                                    return $timeSlots->mapWithKeys(fn ($s) => [
                                        $s->id => $s->name . (in_array($s->id, $reserved) ? ' 🔴 Reserved' : ' ✅ Available'),
                                    ]);
                                })
                                ->disableOptionWhen(function ($value, callable $get, $record) {
                                    $selectedDate = $get('counseling_date');
                                    if (!$selectedDate) return false;
                                    return CounselingAppointments::whereDate('counseling_date', $selectedDate)
                                        ->where('id', '!=', $record->id)
                                        ->where('time_slot_id', $value)
                                        ->exists();
                                })
                                ->required()
                                ->searchable()
                                ->native(false)
                                ->disabled(fn (callable $get) => !$get('counseling_date')),

                            \Filament\Forms\Components\Textarea::make('reschedule_reason')
                                ->label('Reason for Rescheduling (Optional)')
                                ->placeholder('e.g., Counselor unavailable, room conflict...')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            $oldDate = \Carbon\Carbon::parse($record->counseling_date)->format('M d, Y');
                            $oldSlot = $record->timeSlot?->name;

                            $record->update([
                                'counseling_date' => $data['counseling_date'],
                                'time_slot_id'     => $data['time_slot_id'],
                                'status'           => 'pending',
                            ]);

                            $record->notifyStudent('rescheduled');

                            $newDate = \Carbon\Carbon::parse($data['counseling_date'])->format('M d, Y');

                            $this->logCustomActivity(
                                $record,
                                'appointments',
                                'rescheduled',
                                "Rescheduled appointment for {$record->full_name}",
                                [
                                    'from_date' => $oldDate,
                                    'to_date'   => $newDate,
                                    'reason'    => $data['reschedule_reason'] ?? null,
                                ]
                            );

                            Notification::make()
                                ->title('Appointment Rescheduled')
                                ->success()
                                ->body("New date: {$newDate}.")
                                ->send();
                        }),

                    // ── Schedule Follow-up ─────────────────────────────
                    Tables\Actions\Action::make('schedule_followup')
                        ->label('Schedule Follow-up')
                        ->icon('heroicon-o-arrow-path-rounded-square')
                        ->color('info')
                        ->visible(fn ($record): bool => $record->status === 'approved')
                        ->form([
                            \Filament\Forms\Components\DatePicker::make('counseling_date')
                                ->label('Follow-up Date')
                                ->required()
                                ->native(false)
                                ->displayFormat('F d, Y')
                                ->minDate(now())
                                ->disabledDates(fn () => \App\Models\InactiveDate::getInactiveDates())
                                ->live()
                                ->afterStateUpdated(fn (callable $set) => $set('time_slot_id', null)),

                            \Filament\Forms\Components\Select::make('time_slot_id')
                                ->label('Time Slot')
                                ->options(function (callable $get) {
                                    $selectedDate = $get('counseling_date');
                                    $timeSlots = \App\Models\CounselingTimeSlot::where('is_active', true)->orderBy('name')->get();

                                    if (!$selectedDate) {
                                        return $timeSlots->pluck('name', 'id');
                                    }

                                    $reserved = CounselingAppointments::whereDate('counseling_date', $selectedDate)
                                        ->pluck('time_slot_id')->toArray();

                                    return $timeSlots->mapWithKeys(fn ($s) => [
                                        $s->id => $s->name . (in_array($s->id, $reserved) ? ' 🔴 Reserved' : ' ✅ Available'),
                                    ]);
                                })
                                ->disableOptionWhen(function ($value, callable $get) {
                                    $selectedDate = $get('counseling_date');
                                    if (!$selectedDate) return false;
                                    return CounselingAppointments::whereDate('counseling_date', $selectedDate)
                                        ->where('time_slot_id', $value)
                                        ->exists();
                                })
                                ->required()
                                ->searchable()
                                ->native(false)
                                ->disabled(fn (callable $get) => !$get('counseling_date')),

                            \Filament\Forms\Components\Select::make('mode_of_counseling_id')
                                ->label('Mode of Counseling')
                                ->options(\App\Models\ModeOfCounseling::active()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            \Filament\Forms\Components\Select::make('support_needed_id')
                                ->label('Support Needed')
                                ->options(\App\Models\SupportNeeded::active()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            \Filament\Forms\Components\Textarea::make('concern')
                                ->label('Follow-up Concern/Notes')
                                ->placeholder('What should be addressed in this follow-up session?')
                                ->rows(3),
                        ])
                        ->action(function ($record, array $data) {
                            $followUp = CounselingAppointments::create([
                                'parent_appointment_id'  => $record->id,
                                'first_name'             => $record->first_name,
                                'middle_name'            => $record->middle_name,
                                'last_name'              => $record->last_name,
                                'course_and_year'        => $record->course_and_year,
                                'contact_no'             => $record->contact_no,
                                'present_address'        => $record->present_address,
                                'counseling_date'        => $data['counseling_date'],
                                'time_slot_id'           => $data['time_slot_id'],
                                'mode_of_counseling_id'  => $data['mode_of_counseling_id'],
                                'support_needed_id'      => $data['support_needed_id'],
                                'concern'                => $data['concern'] ?? null,
                                'status'                 => 'pending',
                            ]);

                            $followUp->notifyAdmin('follow_up_scheduled');

                            $this->logCustomActivity(
                                $followUp,
                                'appointments',
                                'follow_up_scheduled',
                                "Scheduled a follow-up for {$record->full_name}",
                                ['parent_appointment_id' => $record->id]
                            );

                            Notification::make()
                                ->title('Follow-up Scheduled')
                                ->success()
                                ->body("Taking you to the Records tab to document this session for {$record->first_name} {$record->last_name}.")
                                ->send();

                            return redirect(CounselingAppointmentsResource::getUrl('edit', [
                                'record' => $followUp->id,
                                'tab'    => '-records-tab',
                            ]));
                        }),

                    // ── View Original (only on follow-up rows) ─────────
                    Tables\Actions\Action::make('view_original')
                        ->label('View Original')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->visible(fn ($record): bool => $record->isFollowUp() && $record->parentAppointment)
                        ->url(fn ($record) => CounselingAppointmentsResource::getUrl('edit', [
                            'record' => $record->parent_appointment_id,
                            'tab'    => '-personal-information-tab',
                        ])),

                    Tables\Actions\EditAction::make()
                        ->url(fn ($record) => CounselingAppointmentsResource::getUrl('edit', [
                            'record' => $record->id,
                            'tab'    => '-personal-information-tab',
                        ])),

                    Tables\Actions\Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Appointment')
                        ->modalDescription('This will hide the appointment from this list. You can restore it later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function ($record): void {
                            $record->update(['archived_at' => now()]);

                            $this->logCustomActivity(
                                $record,
                                'appointments',
                                'archived',
                                "Archived appointment for {$record->full_name}"
                            );

                            Notification::make()
                                ->title('Appointment archived')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\ViewAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
                ->hidden(fn () => $this->activeTab === 'endorsed'),

                // ── Endorsements actions ───────────────────────────────
                // NOTE: action names below are suffixed with "_endorsement"
                // to avoid colliding with the identically-named actions
                // (edit, archive, view) in the Appointments/Follow-ups
                // ActionGroup above. Filament requires unique action names
                // across the whole table — even if only one group is
                // visible at a time via ->hidden(), both are still
                // registered, so duplicate names silently break the
                // "losing" action (button does nothing on click).
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('print')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->url(fn ($record) => route('endorsement.print', $record->id))
                        ->openUrlInNewTab(),

                    Tables\Actions\EditAction::make('edit_endorsement')
                        ->url(fn ($record) => CounselingAppointmentsResource::getUrl('edit', [
                            'record' => $record->id,
                            'tab'    => '-endorsement-tab',
                        ])),

                    Tables\Actions\Action::make('archive_endorsement')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Appointment')
                        ->modalDescription('This will hide the appointment from this list. You can restore it later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function ($record): void {
                            $record->update(['archived_at' => now()]);

                            $this->logCustomActivity(
                                $record,
                                'appointments',
                                'archived',
                                "Archived appointment for {$record->full_name}"
                            );

                            Notification::make()
                                ->title('Appointment archived')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\ViewAction::make('view_endorsement')
                        ->infolist([
                            \Filament\Infolists\Components\Section::make('Student Information')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('full_name')
                                        ->label('Student Name')
                                        ->getStateUsing(fn ($record) => $record->full_name),

                                    \Filament\Infolists\Components\TextEntry::make('course_and_year')
                                        ->label('Course & Year'),
                                ]),

                            \Filament\Infolists\Components\Section::make('Endorsement Details')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('endorsement.to_where')
                                        ->label('Endorsed To')
                                        ->default('-'),

                                    \Filament\Infolists\Components\TextEntry::make('endorsement.date')
                                        ->label('Endorse Date')
                                        ->formatStateUsing(fn ($state) => $state
                                            ? \Carbon\Carbon::parse($state)->format('M d, Y')
                                            : '-'),

                                    \Filament\Infolists\Components\TextEntry::make('endorsement.issue')
                                        ->label('Issue')
                                        ->default('-')
                                        ->columnSpanFull(),

                                    \Filament\Infolists\Components\TextEntry::make('endorsed_by')
                                        ->label('Endorsed By')
                                        ->getStateUsing(fn ($record) => $record->endorsement?->personnel
                                            ? trim("{$record->endorsement->personnel->first_name} {$record->endorsement->personnel->middle_name} {$record->endorsement->personnel->last_name}")
                                            : '-'),
                                ])
                                ->columns(2),
                        ]),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
                ->hidden(fn () => $this->activeTab !== 'endorsed'),
            ]);
    }
}