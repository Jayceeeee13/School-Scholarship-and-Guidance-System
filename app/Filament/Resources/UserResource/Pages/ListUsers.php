<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\AccomplishmentReport;
use App\Models\CounselingAppointments;
use App\Models\DailyTimeRecord;
use App\Models\InstitutionalScholar;
use App\Models\Personnels;
use App\Models\Referrals;
use App\Models\Scholars;
use App\Models\TypeOfScholarship;
use App\Models\User;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

class ListUsers extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = UserResource::class;

    /**
     * Each tab queries a completely different model (User, Personnels,
     * Activity) with its own columns/actions. resetTable() clears Filament's
     * cached Table instance so the previous tab's config doesn't render
     * against the new tab's rows.
     */
    public function updatedActiveTab(): void
    {
        $this->resetTable();
    }

    // ─────────────────────────────────────────────────────────────────
    //  Personnel → User sync
    // ─────────────────────────────────────────────────────────────────

    /**
     * Pushes shared personnel info onto the linked user account.
     * Returns true if a linked user existed and was updated.
     *
     * personnel field  →  user field
     *   first + last   →  name
     *   email          →  email
     *   contact_no     →  contact_no
     *   birthdate      →  birthdate
     *   address        →  address
     *   profile        →  avatar
     */
    protected function syncPersonnelToUser(Personnels $personnel): bool
    {
        $user = $personnel->user;

        if (! $user) {
            return false;
        }

        $user->update([
            'name'       => trim("{$personnel->first_name} {$personnel->last_name}"),
            'email'      => $personnel->email,
            'contact_no' => $personnel->contact_no,
            'birthdate'  => $personnel->birthdate,
            'address'    => $personnel->address,
            'avatar'     => $personnel->profile,
        ]);

        $this->logCustomActivity(
            $user,
            'user',
            'updated',
            "Updated user {$user->name} (synced from personnel profile)"
        );

        return true;
    }

    /**
     * Shared form schema for creating / editing a personnel.
     * Pass the record when editing so email-uniqueness ignores it
     * (and ignores its linked user account).
     */
    protected function personnelFormSchema(?Personnels $record = null): array
    {
        $nameRule   = 'regex:/^[a-zA-Z\s\-\.]+$/';
        $capitalize = fn ($state) => $state ? ucwords(strtolower($state)) : $state;

        $nameAttributes = [
            'style'      => 'text-transform: capitalize;',
            'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
        ];

        return [
            Forms\Components\FileUpload::make('profile')
                ->label('Profile Picture')
                ->image()
                ->disk('public')
                ->directory('avatars')
                ->visibility('public')
                ->imageEditor()
                ->imageCropAspectRatio('1:1')
                ->imageResizeTargetWidth('200')
                ->imageResizeTargetHeight('200')
                ->maxSize(1024)
                ->nullable(),

            Forms\Components\TextInput::make('first_name')
                ->label('First Name')
                ->required()
                ->maxLength(100)
                ->rule($nameRule)
                ->validationMessages([
                    'regex' => 'First name can only contain letters, spaces, hyphens, and periods.',
                ])
                ->extraInputAttributes($nameAttributes)
                ->dehydrateStateUsing($capitalize),

            Forms\Components\TextInput::make('middle_name')
                ->label('Middle Name')
                ->maxLength(100)
                ->rule($nameRule)
                ->validationMessages([
                    'regex' => 'Middle name can only contain letters, spaces, hyphens, and periods.',
                ])
                ->extraInputAttributes($nameAttributes)
                ->dehydrateStateUsing($capitalize),

            Forms\Components\TextInput::make('last_name')
                ->label('Last Name')
                ->required()
                ->maxLength(100)
                ->rule($nameRule)
                ->validationMessages([
                    'regex' => 'Last name can only contain letters, spaces, hyphens, and periods.',
                ])
                ->extraInputAttributes($nameAttributes)
                ->dehydrateStateUsing($capitalize),

            Forms\Components\TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(100)
                ->extraInputAttributes([
                    'style' => 'text-transform: capitalize;',
                ]),

            Forms\Components\DatePicker::make('birthdate')
                ->label('Date of Birth')
                ->required()
                ->native(false)
                ->displayFormat('M d, Y')
                ->maxDate(now())
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('age', $state ? \Carbon\Carbon::parse($state)->age : null);
                }),

            Forms\Components\TextInput::make('age')
                ->label('Age')
                ->numeric()
                ->readOnly()
                ->suffix('years old')
                ->dehydrated(true),

            Forms\Components\TextInput::make('contact_no')
                ->label('Contact Number')
                ->tel()
                ->required()
                ->maxLength(11)
                ->minLength(11)
                ->rule('regex:/^[0-9]+$/')
                ->validationMessages([
                    'regex' => 'Contact number can only contain digits.',
                    'min'   => 'Contact number must be exactly 11 digits.',
                    'max'   => 'Contact number must be exactly 11 digits.',
                ])
                ->extraInputAttributes([
                    'onkeypress' => "return /^[0-9]$/.test(event.key)",
                    'maxlength'  => '11',
                ])
                ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : $state),

            Forms\Components\TextInput::make('address')
                ->required()
                ->maxLength(100)
                ->extraInputAttributes([
                    'style' => 'text-transform: capitalize;',
                ]),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(100)
                ->unique('personnels', 'email', ignoreRecord: true)
                // The email is also pushed to the linked user account, so it
                // must not collide with another user's email.
                ->rule(function () use ($record) {
                    return function (string $attribute, $value, $fail) use ($record) {
                        $exists = User::where('email', $value)
                            ->when(
                                $record?->user,
                                fn ($q) => $q->where('id', '!=', $record->user->id)
                            )
                            ->exists();

                        if ($exists) {
                            $fail('This email is already used by another user account.');
                        }
                    };
                }),
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    //  Archive cascade
    // ─────────────────────────────────────────────────────────────────

    /**
     * Cascade-archives all data tied to a user's identity when their
     * account is archived. Each model uses its own real archival
     * mechanism: Scholars/InstitutionalScholar use status='revoked',
     * while CounselingAppointments, Referrals, Personnels, DailyTimeRecord
     * and AccomplishmentReport use archived_at.
     *
     * Returns a summary of what was archived.
     */
    protected function cascadeArchiveUserRecords(User $user): array
    {
        $summary = [
            'personnel'              => 0,
            'scholars'               => 0,
            'appointments'           => 0,
            'referrals'              => 0,
            'dtr'                    => 0,
            'accomplishment_reports' => 0,
        ];

        // ── Linked Personnel profile, matched via users.personnel_id ──
        if ($user->personnel && ! $user->personnel->archived_at) {
            $user->personnel->update(['archived_at' => now()]);

            $this->logCustomActivity(
                $user->personnel,
                'personnels',
                'archived',
                "Archived personnel {$user->personnel->first_name} {$user->personnel->last_name} (cascaded from user archive)"
            );

            $summary['personnel'] = 1;
        }

        // ── Scholars / Institutional Scholars, matched directly by user_id ──
        $scholarIds = [
            Scholars::class             => [],
            InstitutionalScholar::class => [],
        ];

        foreach ([Scholars::class, InstitutionalScholar::class] as $scholarModel) {
            $scholars = $scholarModel::where('user_id', $user->id)
                ->where('status', '!=', 'revoked')
                ->get();

            foreach ($scholars as $scholar) {
                $scholar->update([
                    'status'            => 'revoked',
                    'revocation_reason' => 'Associated user account was archived.',
                    'revoked_at'        => now(),
                ]);

                // Case-insensitive match so a casing mismatch doesn't
                // silently skip restoring the slot.
                TypeOfScholarship::whereRaw('LOWER(name) = ?', [strtolower(trim($scholar->type_of_scholarship ?? ''))])
                    ->increment('slots');

                $scholarIds[$scholarModel][] = $scholar->id;
                $summary['scholars']++;
            }
        }

        // ── Daily Time Records ──
        foreach ($scholarIds as $ids) {
            if (empty($ids)) {
                continue;
            }

            $summary['dtr'] += DailyTimeRecord::whereIn('scholar_id', $ids)
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);
        }

        // ── Counseling Appointments ──
        if ($student = $user->student) {
            $summary['appointments'] = CounselingAppointments::where('student_id', $student->id)
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);

            // ── Referrals, matched by full name ──
            $fullName = trim("{$student->first_name} {$student->last_name}");

            if ($fullName !== '') {
                $summary['referrals'] = Referrals::where('name', $fullName)
                    ->whereNull('archived_at')
                    ->update(['archived_at' => now()]);
            }
        }

        // ── Accomplishment Reports ──
        foreach ($scholarIds as $scholarModel => $ids) {
            if (empty($ids)) {
                continue;
            }

            $summary['accomplishment_reports'] += AccomplishmentReport::where('scholar_type', $scholarModel)
                ->whereIn('scholar_id', $ids)
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);
        }

        return $summary;
    }

    /**
     * Turns the cascade summary into a readable string,
     * listing only what was actually archived.
     */
    protected function formatArchiveSummary(array $summary): string
    {
        $labels = [
            'personnel'              => 'personnel profile',
            'scholars'               => 'scholarship(s) revoked',
            'appointments'           => 'counseling appointment(s)',
            'referrals'              => 'referral(s)',
            'dtr'                    => 'daily time record(s)',
            'accomplishment_reports' => 'accomplishment report(s)',
        ];

        $parts = [];

        foreach ($labels as $key => $label) {
            if (($summary[$key] ?? 0) > 0) {
                $parts[] = "{$summary[$key]} {$label}";
            }
        }

        return $parts ? implode(', ', $parts) : 'no related records';
    }

    // ─────────────────────────────────────────────────────────────────
    //  Header actions / tabs
    // ─────────────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('newPersonnel')
                ->label('New Personnel')
                ->icon('heroicon-o-user-plus')
                ->color('gray')
                ->visible(fn (): bool => $this->activeTab === 'personnels')
                ->form($this->personnelFormSchema())
                ->action(function (array $data): void {
                    $data['age'] = \Carbon\Carbon::parse($data['birthdate'])->age;

                    $record = Personnels::create($data);

                    $this->logCustomActivity(
                        $record,
                        'personnels',
                        'created',
                        "Added personnel {$record->first_name} {$record->last_name}"
                    );

                    Notification::make()
                        ->title('Personnel added')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('New user')
                ->visible(fn (): bool => $this->activeTab === 'users'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'users' => Tab::make('Users')
                ->badge(User::whereNull('archived_at')->count()),

            'personnels' => Tab::make('Personnels')
                ->badge(Personnels::whereNull('archived_at')->count()),

            'activity' => Tab::make('Activity Log')
                ->icon('heroicon-o-clock')
                ->badge(
                    auth()->user()->isAdmin()
                        ? Activity::count()
                        : Activity::where('causer_id', auth()->id())->count()
                ),
        ];
    }

    public function table(Table $table): Table
    {
        // ═════════════════════════════════════════════════════════════
        //  PERSONNELS TAB
        // ═════════════════════════════════════════════════════════════
        if ($this->activeTab === 'personnels') {
            return $table
                ->query(Personnels::query()->whereNull('archived_at')->with(['user.roles']))
                ->columns([
                    ImageColumn::make('profile')
                        ->label('Profile')
                        ->disk('public')
                        ->circular()
                        ->toggleable(),

                    TextColumn::make('first_name')
                        ->label('First Name')
                        ->searchable()
                        ->sortable()
                        ->toggleable(),

                    TextColumn::make('middle_name')
                        ->label('Middle Name')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('last_name')
                        ->label('Last Name')
                        ->searchable()
                        ->sortable()
                        ->toggleable(),

                    TextColumn::make('position')
                        ->label('Position')
                        ->searchable()
                        ->sortable()
                        ->toggleable()
                        ->placeholder('—'),

                    TextColumn::make('age')
                        ->label('Age')
                        ->numeric()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('birthdate')
                        ->label('Date of Birth')
                        ->date()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('contact_no')
                        ->label('Contact No.')
                        ->searchable()
                        ->toggleable(),

                    TextColumn::make('address')
                        ->label('Address')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('email')
                        ->label('Personnel Email')
                        ->searchable()
                        ->toggleable(),

                    TextColumn::make('user.email')
                        ->label('User Email')
                        ->searchable()
                        ->toggleable()
                        ->placeholder('— no account —'),

                    TextColumn::make('user.roles.name')
                        ->label('Roles')
                        ->badge()
                        ->toggleable()
                        ->placeholder('—'),
                ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('editPersonnel')
                            ->label('Edit')
                            ->icon('heroicon-o-pencil-square')
                            ->fillForm(fn (Personnels $record): array => [
                                'profile'     => $record->profile,
                                'first_name'  => $record->first_name,
                                'middle_name' => $record->middle_name,
                                'last_name'   => $record->last_name,
                                'position'    => $record->position,
                                'birthdate'   => $record->birthdate,
                                'age'         => $record->age,
                                'contact_no'  => $record->contact_no,
                                'address'     => $record->address,
                                'email'       => $record->email,
                            ])
                            ->form(fn (Personnels $record): array => $this->personnelFormSchema($record))
                            ->action(function (Personnels $record, array $data): void {
                                $data['age'] = \Carbon\Carbon::parse($data['birthdate'])->age;

                                $synced = false;

                                DB::transaction(function () use ($record, $data, &$synced) {
                                    $record->update($data);
                                    $record->refresh();

                                    // Reflect the changes on the linked user account.
                                    $synced = $this->syncPersonnelToUser($record);
                                });

                                $this->logCustomActivity(
                                    $record,
                                    'personnels',
                                    'updated',
                                    "Updated personnel {$record->first_name} {$record->last_name}"
                                );

                                Notification::make()
                                    ->title('Personnel updated')
                                    ->body($synced
                                        ? 'The linked user account was updated too.'
                                        : 'No linked user account to update.')
                                    ->success()
                                    ->send();
                            }),

                        // Goes to the read-only View User page
                        Tables\Actions\Action::make('viewUserAccount')
                            ->label('View User')
                            ->icon('heroicon-o-user')
                            ->color('gray')
                            ->url(fn (Personnels $record): ?string =>
                                $record->user
                                    ? UserResource::getUrl('view', ['record' => $record->user])
                                    : null
                            )
                            ->visible(fn (Personnels $record): bool => (bool) $record->user),

                        Tables\Actions\Action::make('archivePersonnel')
                            ->label('Archive')
                            ->icon('heroicon-o-archive-box')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Archive Personnel')
                            ->modalDescription('This will hide the record from this list. You can restore it later from Settings → Archived Records.')
                            ->modalSubmitActionLabel('Yes, Archive')
                            ->form([
                                Forms\Components\TextInput::make('confirm_password')
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
                                    }),
                            ])
                            ->action(function (Personnels $record): void {
                                $record->update(['archived_at' => now()]);

                                $this->logCustomActivity(
                                    $record,
                                    'personnels',
                                    'archived',
                                    "Archived personnel {$record->first_name} {$record->last_name}"
                                );

                                Notification::make()
                                    ->title('Personnel archived')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
                ]);
        }

        // ═════════════════════════════════════════════════════════════
        //  ACTIVITY LOG TAB
        // ═════════════════════════════════════════════════════════════
        if ($this->activeTab === 'activity') {
            return $table
                ->query(
                    Activity::query()
                        ->with('causer')
                        ->when(
                            ! auth()->user()->isAdmin(),
                            fn (Builder $query) => $query->where('causer_id', auth()->id())
                        )
                        ->latest()
                )
                ->recordUrl(null)
                ->filters([
                    Tables\Filters\SelectFilter::make('quick_range')
                        ->label('Quick Range')
                        ->options([
                            '1h'    => 'Last 1 Hour',
                            '6h'    => 'Last 6 Hours',
                            '24h'   => 'Last 24 Hours',
                            'today' => 'Today',
                            '7d'    => 'Last 7 Days',
                            '30d'   => 'Last 30 Days',
                        ])
                        ->placeholder('Any time')
                        ->query(function (Builder $query, array $data): Builder {
                            if (blank($data['value'] ?? null)) {
                                return $query;
                            }

                            return match ($data['value']) {
                                '1h'    => $query->where('created_at', '>=', now()->subHour()),
                                '6h'    => $query->where('created_at', '>=', now()->subHours(6)),
                                '24h'   => $query->where('created_at', '>=', now()->subDay()),
                                'today' => $query->whereDate('created_at', today()),
                                '7d'    => $query->where('created_at', '>=', now()->subDays(7)),
                                '30d'   => $query->where('created_at', '>=', now()->subDays(30)),
                                default => $query,
                            };
                        })
                        ->indicateUsing(function (array $data): ?string {
                            if (blank($data['value'] ?? null)) {
                                return null;
                            }

                            return match ($data['value']) {
                                '1h'    => 'Last 1 Hour',
                                '6h'    => 'Last 6 Hours',
                                '24h'   => 'Last 24 Hours',
                                'today' => 'Today',
                                '7d'    => 'Last 7 Days',
                                '30d'   => 'Last 30 Days',
                                default => null,
                            };
                        }),

                    Tables\Filters\Filter::make('created_at')
                        ->label('Date Range')
                        ->form([
                            Forms\Components\DatePicker::make('date_from')
                                ->label('From Date')
                                ->native(false)
                                ->displayFormat('M d, Y'),

                            Forms\Components\DatePicker::make('date_until')
                                ->label('Until Date')
                                ->native(false)
                                ->displayFormat('M d, Y'),
                        ])
                        ->query(function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['date_from'] ?? null,
                                    fn ($q) => $q->whereDate('created_at', '>=', $data['date_from'])
                                )
                                ->when(
                                    $data['date_until'] ?? null,
                                    fn ($q) => $q->whereDate('created_at', '<=', $data['date_until'])
                                );
                        })
                        ->indicateUsing(function (array $data): array {
                            $indicators = [];

                            if ($data['date_from'] ?? null) {
                                $indicators[] = 'From ' . \Carbon\Carbon::parse($data['date_from'])->format('M d, Y');
                            }

                            if ($data['date_until'] ?? null) {
                                $indicators[] = 'Until ' . \Carbon\Carbon::parse($data['date_until'])->format('M d, Y');
                            }

                            return $indicators;
                        }),
                ])
                ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
                ->filtersFormColumns(2)
                ->columns([
                    TextColumn::make('created_at')
                        ->label('When')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),

                    TextColumn::make('log_name')
                        ->label('Log')
                        ->badge()
                        ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '—')
                        ->color(fn (?string $state): string => match ($state) {
                            'auth'              => 'info',
                            'user'              => 'warning',
                            'personnels'        => 'success',
                            'applicants'        => 'primary',
                            'appointments'      => 'info',
                            'logforms'          => 'purple',
                            'exam_attempts'     => 'amber',
                            'referrals'         => 'cyan',
                            'scholarship_types' => 'indigo',
                            default             => 'gray',
                        })
                        ->sortable(),

                    TextColumn::make('event')
                        ->label('Event')
                        ->badge()
                        ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '—')
                        ->color(fn (?string $state): string => match ($state) {
                            'created',
                            'login',
                            'approved',
                            'invitation_accepted' => 'success',

                            'updated',
                            'rescheduled',
                            'follow_up_scheduled',
                            'attendance_noted' => 'warning',

                            'restored' => 'info',

                            'deleted',
                            'logout',
                            'failed_login',
                            'rejected',
                            'archived',
                            'archived_records',
                            'invitation_declined' => 'danger',

                            default => 'gray',
                        })
                        ->placeholder('—'),

                    TextColumn::make('description')
                        ->label('Description')
                        ->searchable()
                        ->wrap(),

                    TextColumn::make('causer.name')
                        ->label('Performed By')
                        ->searchable()
                        ->placeholder('System'),
                ])
                ->actions([
                    Tables\Actions\Action::make('viewChanges')
                        ->label('View Changes')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Activity Details')
                        ->modalContent(fn (Activity $record) => view(
                            'filament.activity-log-modal',
                            ['properties' => $record->properties]
                        ))
                        ->visible(fn (Activity $record): bool => $record->properties?->isNotEmpty() ?? false)
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                ]);
        }

        // ═════════════════════════════════════════════════════════════
        //  USERS TAB, shows EVERY non-archived user account
        // ═════════════════════════════════════════════════════════════
        return $table
            ->query(User::query()->whereNull('archived_at')->with(['personnel', 'roles', 'student']))
            ->recordUrl(fn (User $record): string => UserResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('viewUser')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->url(fn (User $record): string => UserResource::getUrl('view', ['record' => $record])),

                    Tables\Actions\Action::make('editUser')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record]))
                        ->visible(fn (User $record): bool => UserResource::canEdit($record)),

                    // Archive with an optional cascade (checkbox)
                    Tables\Actions\Action::make('archiveUser')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive User')
                        ->modalDescription('Are you sure you want to archive this user? They will immediately lose access.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->visible(fn (User $record): bool => $record->id !== auth()->id())
                        ->form([
                            Forms\Components\Checkbox::make('archive_records')
                                ->label('Also archive all related records')
                                ->helperText('Includes their linked Personnel profile (if any), scholar records, DTR entries, counseling appointments, referrals, and accomplishment reports tied to their student profile.')
                                ->default(false),

                            Forms\Components\TextInput::make('confirm_password')
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
                                }),
                        ])
                        ->action(function (User $record, array $data): void {
                            $summary = null;

                            // All-or-nothing: if any step fails, nothing is archived.
                            DB::transaction(function () use ($record, $data, &$summary) {
                                $record->update(['archived_at' => now()]);

                                if ($data['archive_records'] ?? false) {
                                    $summary = $this->cascadeArchiveUserRecords($record);
                                }
                            });

                            if ($summary !== null) {
                                $details = $this->formatArchiveSummary($summary);

                                $this->logCustomActivity(
                                    $record,
                                    'user',
                                    'archived',
                                    "Archived user {$record->name} (cascaded: {$details})"
                                );

                                Notification::make()
                                    ->title('User archived')
                                    ->body("Also archived: {$details}.")
                                    ->success()
                                    ->send();

                                return;
                            }

                            $this->logCustomActivity(
                                $record,
                                'user',
                                'archived',
                                "Archived user {$record->name}"
                            );

                            Notification::make()
                                ->title('User archived')
                                ->success()
                                ->send();
                        }),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ]);
    }
}