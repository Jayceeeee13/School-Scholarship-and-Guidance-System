<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Personnels;
use App\Models\User;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ListUsers extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = UserResource::class;

    /**
     * Each tab here queries a completely different model (User, Personnels,
     * Activity) with its own columns/actions. resetTable() clears Filament's
     * cached Table instance (columns/filters/actions) in addition to
     * pagination/search — without it, the previous tab's cached table config
     * renders against the new tab's rows on the first click, requiring a
     * second click to "catch up." See the same fix in ListApplicants,
     * ListExams, and ListCounselingLogforms.
     */
    public function updatedActiveTab(): void
    {
        $this->resetTable();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('newPersonnel')
                ->label('New Personnel')
                ->icon('heroicon-o-user-plus')
                ->color('gray')
                ->visible(fn (): bool => $this->activeTab === 'personnels')
                ->form([
                    Forms\Components\TextInput::make('first_name')
                        ->label('First Name')
                        ->required()
                        ->maxLength(100)
                        ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                        ->validationMessages([
                            'regex' => 'First name can only contain letters, spaces, hyphens, and periods.',
                        ])
                        ->extraInputAttributes([
                            'style'      => 'text-transform: capitalize;',
                            'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                        ])
                        ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                    Forms\Components\TextInput::make('middle_name')
                        ->label('Middle Name')
                        ->maxLength(100)
                        ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                        ->validationMessages([
                            'regex' => 'Middle name can only contain letters, spaces, hyphens, and periods.',
                        ])
                        ->extraInputAttributes([
                            'style'      => 'text-transform: capitalize;',
                            'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                        ])
                        ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                    Forms\Components\TextInput::make('last_name')
                        ->label('Last Name')
                        ->required()
                        ->maxLength(100)
                        ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                        ->validationMessages([
                            'regex' => 'Last name can only contain letters, spaces, hyphens, and periods.',
                        ])
                        ->extraInputAttributes([
                            'style'      => 'text-transform: capitalize;',
                            'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                        ])
                        ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                    Forms\Components\DatePicker::make('birthdate')
                        ->label('Date of Birth')
                        ->required()
                        ->native(false)
                        ->displayFormat('M d, Y')
                        ->maxDate(now())
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $set('age', \Carbon\Carbon::parse($state)->age);
                            } else {
                                $set('age', null);
                            }
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
                        ->unique('personnels', 'email'),
                ])
                ->action(function (array $data): void {
                    $data['age'] = \Carbon\Carbon::parse($data['birthdate'])->age;

                    $record = Personnels::create($data);

                    $this->logCustomActivity(
                        $record,
                        'personnels',
                        'created',
                        "Added personnel {$record->first_name} {$record->last_name}"
                    );

                    \Filament\Notifications\Notification::make()
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
        if ($this->activeTab === 'personnels') {
            return $table
                ->query(Personnels::query()->whereNull('archived_at'))
                ->columns([
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

                    TextColumn::make('user.role.name')
                        ->label('Role')
                        ->badge()
                        ->toggleable()
                        ->placeholder('—'),
                ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        \Filament\Tables\Actions\Action::make('editPersonnel')
                            ->label('Edit')
                            ->icon('heroicon-o-pencil-square')
                            ->fillForm(fn (Personnels $record): array => [
                                'first_name'  => $record->first_name,
                                'middle_name' => $record->middle_name,
                                'last_name'   => $record->last_name,
                                'birthdate'   => $record->birthdate,
                                'age'         => $record->age,
                                'contact_no'  => $record->contact_no,
                                'address'     => $record->address,
                                'email'       => $record->email,
                            ])
                            ->form([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                    ->validationMessages([
                                        'regex' => 'First name can only contain letters, spaces, hyphens, and periods.',
                                    ])
                                    ->extraInputAttributes([
                                        'style'      => 'text-transform: capitalize;',
                                        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                                Forms\Components\TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(100)
                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                    ->validationMessages([
                                        'regex' => 'Middle name can only contain letters, spaces, hyphens, and periods.',
                                    ])
                                    ->extraInputAttributes([
                                        'style'      => 'text-transform: capitalize;',
                                        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                    ->validationMessages([
                                        'regex' => 'Last name can only contain letters, spaces, hyphens, and periods.',
                                    ])
                                    ->extraInputAttributes([
                                        'style'      => 'text-transform: capitalize;',
                                        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                                Forms\Components\DatePicker::make('birthdate')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('M d, Y')
                                    ->maxDate(now())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('age', \Carbon\Carbon::parse($state)->age);
                                        } else {
                                            $set('age', null);
                                        }
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
                                    ->unique('personnels', 'email', ignoreRecord: true),
                            ])
                            ->action(function (Personnels $record, array $data): void {
                                $data['age'] = \Carbon\Carbon::parse($data['birthdate'])->age;

                                $record->update($data);

                                \Filament\Notifications\Notification::make()
                                    ->title('Personnel updated')
                                    ->success()
                                    ->send();
                            }),

                        \Filament\Tables\Actions\Action::make('viewUserAccount')
                            ->label('View User')
                            ->icon('heroicon-o-user')
                            ->color('gray')
                            ->url(fn (Personnels $record): ?string =>
                                $record->user
                                    ? UserResource::getUrl('edit', ['record' => $record->user])
                                    : null
                            )
                            ->visible(fn (Personnels $record): bool => (bool) $record->user),

                        \Filament\Tables\Actions\Action::make('archivePersonnel')
                            ->label('Archive')
                            ->icon('heroicon-o-archive-box')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Archive Personnel')
                            ->modalDescription('This will hide the record from this list. You can restore it later from Settings → Archived Records.')
                            ->modalSubmitActionLabel('Yes, Archive')
                            ->action(function (Personnels $record): void {
                                $record->update(['archived_at' => now()]);

                                $this->logCustomActivity(
                                    $record,
                                    'personnels',
                                    'archived',
                                    "Archived personnel {$record->first_name} {$record->last_name}"
                                );

                                \Filament\Notifications\Notification::make()
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
                ->columns([
                    TextColumn::make('created_at')
                        ->label('When')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),

                    // ── Log (module) column ─────────────────────────────
                    // Grouped by which part of the system the event came from.
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

                    // ── Event column ────────────────────────────────────
                    // Grouped by the general nature of the action, regardless
                    // of which module it came from.
                    TextColumn::make('event')
                        ->label('Event')
                        ->badge()
                        ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '—')
                        ->color(fn (?string $state): string => match ($state) {
                            // Positive / success outcomes
                            'created',
                            'login',
                            'approved',
                            'invitation_accepted' => 'success',

                            // Neutral / in-progress changes
                            'updated',
                            'rescheduled',
                            'follow_up_scheduled' => 'warning',

                            // Restorative
                            'restored' => 'info',

                            // Negative / terminal outcomes
                            'deleted',
                            'logout',
                            'failed_login',
                            'rejected',
                            'archived',
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
                    \Filament\Tables\Actions\Action::make('viewChanges')
                        ->label('View Changes')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Activity Details')
                        ->modalContent(fn (Activity $record) => view(
                            'filament.activity-log-modal',
                            ['properties' => $record->properties]
                        ))
                        ->visible(fn (Activity $record): bool => $record->properties->isNotEmpty())
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                ]);
        }

        // 'users' tab — shows EVERY non-archived user account, regardless
        // of whether it has a linked Personnels record.
        return $table
            ->query(User::query()->whereNull('archived_at')->with(['personnel', 'role']))
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

                TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—'),
            ])
            ->actions([
                // \Filament\Tables\Actions\Action::make('edit')
                //     ->label('Edit')
                //     ->icon('heroicon-o-pencil-square')
                //     ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record])),

                \Filament\Tables\Actions\Action::make('archiveUser')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Archive User')
                    ->modalDescription('This user will lose panel access and be hidden from this list. You can restore it later from Settings → Archived Records.')
                    ->modalSubmitActionLabel('Yes, Archive')
                    ->visible(fn (User $record): bool => $record->id !== auth()->id())
                    ->action(function (User $record): void {
                        $record->update(['archived_at' => now()]);

                        $this->logCustomActivity(
                            $record,
                            'user',
                            'archived',
                            "Archived user {$record->name}"
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('User archived')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}