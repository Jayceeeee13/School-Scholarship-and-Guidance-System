<?php

namespace App\Filament\Pages;

use App\Models\AccomplishmentReport;
use App\Models\Applicant;
use App\Models\CounselingAppointments;
use App\Models\CounselingLogforms;
use App\Models\DailyTimeRecord;
use App\Models\ExamAttempt;
use App\Models\InstitutionalScholar;
use App\Models\Personnels;
use App\Models\Referrals;
use App\Models\Scholars;
use App\Models\TypeOfScholarship;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ArchivedRecords extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-s-archive-box';

    protected static ?string $navigationLabel = 'Archived Records';

    protected static string $view = 'filament.pages.archived-records';

    protected static ?string $title = 'Archived Records';

    protected static ?string $slug = 'archived-records';

    public string $activeTab = 'users';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    /**
     * Shared password-confirmation field used on every restore action in
     * this page — requires the currently logged-in admin to re-confirm
     * their own password before any archived record is brought back.
     */
    protected function passwordConfirmationField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('confirm_password')
            ->label('Confirm your password to continue')
            ->password()
            ->revealable()
            ->required()
            ->rule(function () {
                return function (string $attribute, $value, $fail) {
                    if (! Hash::check($value, auth()->user()->password)) {
                        $fail('The password is incorrect.');
                    }
                };
            });
    }

    /**
     * Reverses cascadeArchiveUserRecords() from ListUsers.php. Only
     * un-revokes scholars whose revocation_reason exactly matches the
     * auto-generated cascade text, so manually-revoked scholars (revoked
     * for an unrelated reason) are never touched by accident.
     * Appointments/Referrals/DTR/Accomplishment Reports have no such
     * marker to check against, so those are restored based on the
     * scholar/student link alone — acceptable since the admin explicitly
     * opts in via the checkbox either way.
     */
    protected function cascadeRestoreUserRecords(User $user): array
    {
        $summary = [
            'personnel'              => 0,
            'scholars'               => 0,
            'appointments'           => 0,
            'referrals'              => 0,
            'dtr'                    => 0,
            'accomplishment_reports' => 0,
        ];

        // ── Linked Personnel profile — matched via users.personnel_id ──
        if ($user->personnel && $user->personnel->archived_at) {
            $user->personnel->update(['archived_at' => null]);
            $summary['personnel'] = 1;
        }

        $scholarIds = [
            Scholars::class             => [],
            InstitutionalScholar::class => [],
        ];

        foreach ([Scholars::class, InstitutionalScholar::class] as $scholarModel) {
            $scholars = $scholarModel::where('user_id', $user->id)
                ->where('status', 'revoked')
                ->where('revocation_reason', 'Associated user account was archived.')
                ->get();

            foreach ($scholars as $scholar) {
                $scholar->update([
                    'status'            => 'active',
                    'revocation_reason' => null,
                    'revoked_at'        => null,
                ]);

                TypeOfScholarship::whereRaw('LOWER(name) = ?', [strtolower(trim($scholar->type_of_scholarship ?? ''))])
                    ->decrement('slots');

                $scholarIds[$scholarModel][] = $scholar->id;
                $summary['scholars']++;
            }
        }

        // ── Daily Time Records — matched via the scholar(s) tied to this user ──
        foreach ($scholarIds as $ids) {
            if (empty($ids)) {
                continue;
            }

            $summary['dtr'] += DailyTimeRecord::whereIn('scholar_id', $ids)
                ->whereNotNull('archived_at')
                ->update(['archived_at' => null]);
        }

        if ($student = $user->student) {
            $summary['appointments'] = CounselingAppointments::where('student_id', $student->id)
                ->whereNotNull('archived_at')
                ->update(['archived_at' => null]);

            $fullName = trim("{$student->first_name} {$student->last_name}");

            if ($fullName !== '') {
                $summary['referrals'] = Referrals::where('name', $fullName)
                    ->whereNotNull('archived_at')
                    ->update(['archived_at' => null]);
            }
        }

        // ── Accomplishment Reports — polymorphic (scholar_type + scholar_id) ──
        foreach ($scholarIds as $scholarModel => $ids) {
            if (empty($ids)) {
                continue;
            }

            $summary['accomplishment_reports'] += AccomplishmentReport::where('scholar_type', $scholarModel)
                ->whereIn('scholar_id', $ids)
                ->whereNotNull('archived_at')
                ->update(['archived_at' => null]);
        }

        return $summary;
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'personnels') {
            return $table
                ->query(Personnels::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('first_name')
                        ->label('First Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('last_name')
                        ->label('Last Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('email')
                        ->label('Email')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('contact_no')
                        ->label('Contact No.')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Personnel')
                        ->modalDescription('This record will reappear in the main Personnels list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (Personnels $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Personnel restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        if ($this->activeTab === 'applicants') {
            return $table
                ->query(Applicant::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('first_name')
                        ->label('First Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('last_name')
                        ->label('Last Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('typeOfApplication.name')
                        ->label('Application Type')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Applicant')
                        ->modalDescription('This application will reappear in the main Applicants list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (Applicant $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Applicant restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        if ($this->activeTab === 'appointments') {
            return $table
                ->query(CounselingAppointments::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('full_name')
                        ->label('Student Name')
                        ->getStateUsing(fn ($record) => $record->full_name)
                        ->searchable(['first_name', 'last_name', 'middle_name']),

                    Tables\Columns\TextColumn::make('course_and_year')
                        ->label('Course & Year')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('counseling_date')
                        ->label('Date')
                        ->date('M d, Y')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected']),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Appointment')
                        ->modalDescription('This appointment will reappear in the main Counseling Appointments list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (CounselingAppointments $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Appointment restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        if ($this->activeTab === 'referrals') {
            return $table
                ->query(Referrals::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Student Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('course_and_year')
                        ->label('Course & Year')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('referred_by')
                        ->label('Referred By')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'warning',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Referral')
                        ->modalDescription('This referral will reappear in the main Referrals list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (Referrals $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Referral restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        if ($this->activeTab === 'logforms') {
            return $table
                ->query(CounselingLogforms::query()->whereNotNull('archived_at')->with('appointment'))
                ->columns([
                    Tables\Columns\TextColumn::make('appointment.first_name')
                        ->label('First Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('appointment.last_name')
                        ->label('Last Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('appointment.course_and_year')
                        ->label('Course & Year')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('remarks')
                        ->label('Remarks')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Logform')
                        ->modalDescription('This logform will reappear in the main Logforms list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (CounselingLogforms $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Logform restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        if ($this->activeTab === 'examinees') {
            return $table
                ->query(ExamAttempt::query()->whereNotNull('archived_at')->with(['exam', 'user']))
                ->columns([
                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('exam.title')
                        ->label('Exam')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('percentage')
                        ->label('Percentage')
                        ->formatStateUsing(fn ($state) => "{$state}%")
                        ->sortable(),

                    Tables\Columns\TextColumn::make('completed_at')
                        ->label('Completed')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Examinee Record')
                        ->modalDescription('This exam attempt will reappear in the main Examinees list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (ExamAttempt $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Examinee record restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        if ($this->activeTab === 'dtr') {
            return $table
                ->query(DailyTimeRecord::query()->whereNotNull('archived_at')->with('scholar'))
                ->columns([
                    Tables\Columns\TextColumn::make('scholar.full_name')
                        ->label('Scholar')
                        ->getStateUsing(fn (DailyTimeRecord $record) => $record->scholar
                            ? trim("{$record->scholar->first_name} {$record->scholar->last_name}")
                            : '—')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('office_assigned')
                        ->label('Office Assigned')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('date')
                        ->label('Date')
                        ->date('M d, Y')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore DTR Entry')
                        ->modalDescription('This DTR entry will reappear in the main DTR list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (DailyTimeRecord $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('DTR entry restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        if ($this->activeTab === 'accomplishment_reports') {
            return $table
                ->query(AccomplishmentReport::query()->whereNotNull('archived_at')->with(['scholar', 'term']))
                ->columns([
                    Tables\Columns\TextColumn::make('scholar.full_name')
                        ->label('Scholar')
                        ->getStateUsing(fn (AccomplishmentReport $record) => $record->scholar
                            ? trim("{$record->scholar->first_name} {$record->scholar->last_name}")
                            : '—'),

                    Tables\Columns\TextColumn::make('term.school_year')
                        ->label('Term')
                        ->formatStateUsing(fn ($state, $record) => $record->term
                            ? "{$record->term->school_year} — {$record->term->semester}"
                            : '—'),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'warning',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                    Tables\Columns\TextColumn::make('submitted_at')
                        ->label('Submitted')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Accomplishment Report')
                        ->modalDescription('This report will reappear in the main Accomplishment Reports list.')
                        ->form([
                            $this->passwordConfirmationField(),
                        ])
                        ->action(function (AccomplishmentReport $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Accomplishment report restored')
                                ->success()
                                ->send();
                        }),
                ]);
        }

        return $table
            ->query(User::query()->whereNotNull('archived_at')->with(['personnel', 'role', 'student']))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Archived On')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Restore User')
                    ->modalDescription('This user will regain panel access and reappear in the main Users list.')
                    ->modalSubmitActionLabel('Yes, Restore')
                    ->form([
                        Forms\Components\Checkbox::make('restore_records')
                            ->label('Also restore their related records')
                            ->helperText('Reverses their linked Personnel profile plus any scholar/DTR/appointment/referral/accomplishment report records that were archived alongside this user, if any.')
                            ->default(false),

                        $this->passwordConfirmationField(),
                    ])
                    ->action(function (User $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $record->update(['archived_at' => null]);

                            if ($data['restore_records'] ?? false) {
                                $this->cascadeRestoreUserRecords($record);
                            }
                        });

                        Notification::make()
                            ->title('User restored')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}