<?php

namespace App\Filament\Resources\CounselingLogformsResource\Pages;

use App\Filament\Resources\CounselingLogformsResource;
use App\Filament\Resources\AnecdotalsResource;
use App\Models\CounselingLogforms;
use App\Models\Anecdotals;
use App\Models\CounselingAppointments;
use App\Models\Students;
use App\Models\CounselingTimeSlot;
use App\Models\ModeOfCounseling;
use App\Models\SupportNeeded;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Forms;
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
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        if ($this->activeTab === 'anecdotals') {
            return Anecdotals::query()
                ->with([
                    'logform.appointment',
                    'logform.walkInStudent',
                    'personnel',
                ]);
        }

        return parent::getTableQuery()
            ->whereNull('archived_at')
            ->with([
                'appointment',
                'walkInStudent',
                'referral',
                'followUpAppointment',
            ]);
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
                ->badge(
                    fn () =>
                        CounselingLogforms::whereNull('archived_at')->count()
                ),

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
                        ->color(
                            fn (?string $state): string =>
                                $state === 'walk_in'
                                    ? 'warning'
                                    : 'info'
                        )
                        ->formatStateUsing(
                            fn (?string $state): string =>
                                $state === 'walk_in'
                                    ? 'Walk-in'
                                    : 'Scheduled'
                        ),

                    Tables\Columns\TextColumn::make('display_name')
                        ->label('Student Name')
                        ->getStateUsing(
                            fn (Anecdotals $record) =>
                                $record->logform?->display_name ?? '—'
                        )
                        ->searchable(query: function ($query, $search) {
                            return $query->whereHas(
                                'logform',
                                function ($q) use ($search) {
                                    $q->whereHas(
                                        'appointment',
                                        function ($a) use ($search) {
                                            $a->where(
                                                'first_name',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'last_name',
                                                'like',
                                                "%{$search}%"
                                            );
                                        }
                                    )->orWhereHas(
                                        'walkInStudent',
                                        function ($s) use ($search) {
                                            $s->where(
                                                'first_name',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'last_name',
                                                'like',
                                                "%{$search}%"
                                            );
                                        }
                                    );
                                }
                            );
                        }),

                    Tables\Columns\TextColumn::make('display_course')
                        ->label('Course & Year')
                        ->getStateUsing(
                            fn (Anecdotals $record) =>
                                $record->logform?->display_course ?? '—'
                        ),

                    Tables\Columns\TextColumn::make('area_concern')
                        ->label('Area of Concern')
                        ->limit(40)
                        ->wrap(),

                    Tables\Columns\TextColumn::make('personnel.full_name')
                        ->label('Interviewed By')
                        ->getStateUsing(
                            function (Anecdotals $record) {
                                if ($record->personnel) {
                                    return trim(
                                        "{$record->personnel->first_name} " .
                                        "{$record->personnel->middle_name} " .
                                        "{$record->personnel->last_name}"
                                    );
                                }

                                return 'N/A';
                            }
                        )
                        ->searchable(query: function ($query, $search) {
                            return $query->whereHas(
                                'personnel',
                                function ($q) use ($search) {
                                    $q->where(
                                        'first_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'middle_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'last_name',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                        })
                        ->sortable(query: function ($query, $direction) {
                            return $query
                                ->join(
                                    'personnels',
                                    'anecdotals.personnel_id',
                                    '=',
                                    'personnels.id'
                                )
                                ->orderBy(
                                    'personnels.last_name',
                                    $direction
                                );
                        }),
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make()
                            ->url(
                                fn (Anecdotals $record) =>
                                    AnecdotalsResource::getUrl(
                                        'view',
                                        ['record' => $record]
                                    )
                            ),

                        Tables\Actions\EditAction::make()
                            ->url(
                                fn (Anecdotals $record) =>
                                    AnecdotalsResource::getUrl(
                                        'edit',
                                        ['record' => $record]
                                    )
                            ),
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

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(
                        fn (string $state): string =>
                            $state === 'walk_in'
                                ? 'warning'
                                : 'info'
                    )
                    ->formatStateUsing(
                        fn (string $state): string =>
                            $state === 'walk_in'
                                ? 'Walk-in'
                                : 'Scheduled'
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Student Name')
                    ->getStateUsing(
                        fn (CounselingLogforms $record) =>
                            $record->display_name
                    ),

                Tables\Columns\TextColumn::make('display_course')
                    ->label('Course & Year')
                    ->getStateUsing(
                        fn (CounselingLogforms $record) =>
                            $record->display_course
                    ),

                Tables\Columns\TextColumn::make('display_contact')
                    ->label('Contact')
                    ->getStateUsing(
                        fn (CounselingLogforms $record) =>
                            $record->display_contact
                    ),

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

                Tables\Columns\IconColumn::make('follow_up_required')
                    ->label('Follow-up')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('followUpAppointment.counseling_date')
                    ->label('Follow-up Date')
                    ->date('M d, Y')
                    ->placeholder('—')
                    ->toggleable(),
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

                    Tables\Actions\Action::make('schedule_follow_up')
                        ->label('Schedule Follow-up')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(
                            fn (CounselingLogforms $record): bool =>
                                $record->follow_up_required &&
                                ! $record->followUpAppointment
                        )
                        ->form([
                            Forms\Components\DatePicker::make('counseling_date')
                                ->label('Follow-up Date')
                                ->required()
                                ->native(false)
                                ->minDate(today()),

                            Forms\Components\Select::make('time_slot_id')
                                ->label('Time Slot')
                                ->options(
                                    fn () =>
                                        CounselingTimeSlot::query()
                                            ->where('is_active', true)
                                            ->pluck('name', 'id')
                                            ->toArray()
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),

                            Forms\Components\Select::make('mode_of_counseling_id')
                                ->label('Mode of Counseling')
                                ->options(
                                    fn () =>
                                        ModeOfCounseling::query()
                                            ->pluck('name', 'id')
                                            ->toArray()
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),

                            Forms\Components\Select::make('support_needed_id')
                                ->label('Support Needed')
                                ->options(
                                    fn () =>
                                        SupportNeeded::active()
                                            ->pluck('name', 'id')
                                            ->toArray()
                                )
                                ->searchable()
                                ->preload()
                                ->native(false),

                            Forms\Components\Textarea::make('concern')
                                ->label('Concern')
                                ->rows(4)
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->modalHeading('Schedule Follow-up Counseling')
                        ->modalDescription(
                            'Create the next counseling appointment for this student.'
                        )
                        ->modalSubmitActionLabel('Schedule Follow-up')
                        ->action(
                            function (
                                CounselingLogforms $record,
                                array $data
                            ): void {
                                if ($record->followUpAppointment) {
                                    Notification::make()
                                        ->title('Follow-up Already Scheduled')
                                        ->body(
                                            'A follow-up appointment already exists for this counseling record.'
                                        )
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                $studentId = null;
                                $lastName = '';
                                $firstName = '';
                                $middleName = '';
                                $courseAndYear = '';
                                $contactNo = '';
                                $presentAddress = '';
                                $parentAppointmentId = null;

                                if ($record->isWalkIn()) {
                                    $student = $record->walkInStudent;

                                    if (! $student) {
                                        Notification::make()
                                            ->title('Student Not Found')
                                            ->body(
                                                'The student connected to this walk-in counseling record could not be found.'
                                            )
                                            ->danger()
                                            ->send();

                                        return;
                                    }

                                    $studentId = $student->id;
                                    $lastName = $student->last_name ?? '';
                                    $firstName = $student->first_name ?? '';
                                    $middleName = $student->middle_name ?? '';

                                    $courseAndYear = trim(
                                        ($student->program?->name ?? '') .
                                        ' ' .
                                        ($student->year_level ?? '')
                                    );

                                    $contactNo = $student->contact_no ?? '';
                                    $presentAddress = $student->address ?? '';
                                } elseif ($record->appointment) {
                                    $appointment = $record->appointment;

                                    $studentId = $appointment->student_id;
                                    $lastName = $appointment->last_name ?? '';
                                    $firstName = $appointment->first_name ?? '';
                                    $middleName = $appointment->middle_name ?? '';
                                    $courseAndYear = $appointment->course_and_year ?? '';
                                    $contactNo = $appointment->contact_no ?? '';
                                    $presentAddress = $appointment->present_address ?? '';

                                    $parentAppointmentId = $appointment->id;
                                } elseif ($record->referral) {
                                    $referral = $record->referral;

                                    $nameParts = preg_split(
                                        '/\s+/',
                                        trim($referral->name)
                                    );

                                    $lastName = '';
                                    $firstName = '';
                                    $middleName = '';

                                    if (count($nameParts) === 1) {
                                        $firstName = $nameParts[0];
                                    } elseif (count($nameParts) === 2) {
                                        $firstName = $nameParts[0];
                                        $lastName = $nameParts[1];
                                    } else {
                                        $lastName = array_pop($nameParts);
                                        $firstName = array_shift($nameParts);
                                        $middleName = implode(' ', $nameParts);
                                    }

                                    $courseAndYear =
                                        $referral->course_and_year ?? '';
                                } else {
                                    Notification::make()
                                        ->title('Student Information Missing')
                                        ->body(
                                            'This logform does not have enough student information to create a follow-up appointment.'
                                        )
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                $followUp = CounselingAppointments::create([
                                    'parent_appointment_id' => $parentAppointmentId,
                                    'source_logform_id' => $record->id,
                                    'student_id' => $studentId,
                                    'last_name' => $lastName,
                                    'first_name' => $firstName,
                                    'middle_name' => $middleName,
                                    'course_and_year' => $courseAndYear,
                                    'contact_no' => $contactNo,
                                    'present_address' => $presentAddress,
                                    'counseling_date' => $data['counseling_date'],
                                    'time_slot_id' => $data['time_slot_id'],
                                    'mode_of_counseling_id' => $data['mode_of_counseling_id'],
                                    'support_needed_id' => $data['support_needed_id'] ?? null,
                                    'concern' => $data['concern'],
                                    'status' => 'pending',
                                ]);

                                $record->update([
                                    'follow_up_required' => false,
                                ]);

                                try {
                                    $followUp->notifyAdmin('follow_up_scheduled');
                                } catch (\Throwable $e) {
                                }

                                $studentName = $record->display_name
                                    ?: "Logform #{$record->id}";

                                $this->logCustomActivity(
                                    $record,
                                    'logforms',
                                    'follow_up_scheduled',
                                    "Scheduled follow-up counseling for {$studentName} on " .
                                    $followUp->counseling_date->format('M d, Y')
                                );

                                Notification::make()
                                    ->title('Follow-up Scheduled')
                                    ->body(
                                        "A follow-up counseling appointment has been scheduled for {$studentName}."
                                    )
                                    ->success()
                                    ->send();
                            }
                        ),

                    Tables\Actions\Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Logform')
                        ->modalDescription(
                            'This will hide the logform from this list. You can restore it later from Settings → Archived Records.'
                        )
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(
                            function (
                                CounselingLogforms $record
                            ): void {
                                $record->update([
                                    'archived_at' => now(),
                                ]);

                                $studentName = $record->display_name
                                    ?: "Logform #{$record->id}";

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
                            }
                        ),
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
                            $ids = $records
                                ->pluck('id')
                                ->implode(',');

                            return redirect()->away(
                                route(
                                    'counseling-logforms.print',
                                    ['ids' => $ids]
                                )
                            );
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('archive_selected')
                        ->label('Archive Selected')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Selected Logforms')
                        ->modalDescription(
                            'This will hide the selected logforms from this list. You can restore them later from Settings → Archived Records.'
                        )
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function ($records) {
                            $records->each(
                                function (
                                    CounselingLogforms $record
                                ) {
                                    $record->update([
                                        'archived_at' => now(),
                                    ]);

                                    $studentName = $record->display_name
                                        ?: "Logform #{$record->id}";

                                    $this->logCustomActivity(
                                        $record,
                                        'logforms',
                                        'archived',
                                        "Archived logform for {$studentName}"
                                    );
                                }
                            );

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