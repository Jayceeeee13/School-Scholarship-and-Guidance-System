<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Role;
use App\Models\Personnels;
use App\Models\Students;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-circle';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationGroup = 'Generals';

    protected const DEFAULT_PASSWORD = 'GVCFI@2026';

    /**
     * True if ANY of the selected role IDs is "Department Head".
     */
    protected static function hasDepartmentHeadRole(?array $roleIds): bool
    {
        if (empty($roleIds)) {
            return false;
        }

        return Role::whereIn('id', $roleIds)
            ->where('name', 'Department Head')
            ->exists();
    }

    /**
     * True if ANY of the selected role IDs is "Student".
     */
    protected static function hasStudentRole(?array $roleIds): bool
    {
        if (empty($roleIds)) {
            return false;
        }

        return Role::whereIn('id', $roleIds)
            ->where('name', 'Student')
            ->exists();
    }

    protected static function departmentHeadRoleId(): ?int
    {
        return Role::where('name', 'Department Head')->value('id');
    }

    /**
     * Gender options, shared by the user and personnel forms.
     * ASSUMPTION: a `genders` table with `id` and `name` columns.
     * Adjust here if yours is different.
     */
    public static function genderOptions(): array
    {
        return DB::table('genders')->orderBy('name')->pluck('name', 'id')->all();
    }

    // ─────────────────────────────────────────────────────────────────
    //  Student helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Students who do not have a user account yet.
     *
     * The students table can hold the same person more than once (for example one
     * row per term), so this also:
     *  - hides a student number as soon as ANY of its rows is linked to an account, and
     *  - keeps only the latest row for each student number (no duplicate names).
     * (NULL or 0 in students.user_id means "no account".)
     */
    protected static function availableStudents()
    {
        $table = (new Students)->getTable();

        $query = Students::query();

        if (Schema::hasColumn($table, 'user_id')) {
            $unlinked = fn ($q) => $q->whereNull('user_id')->orWhere('user_id', 0);

            $query->where($unlinked);

            if (Schema::hasColumn($table, 'student_id')) {
                // Student numbers that already have an account on any row.
                $query->whereNotIn('student_id', function ($sub) use ($table) {
                    $sub->select('student_id')
                        ->from($table)
                        ->whereNotNull('student_id')
                        ->whereNotNull('user_id')
                        ->where('user_id', '!=', 0);
                });

                // One row per student number (the latest one). Rows without a
                // student number are kept as they are.
                $query->where(function ($q) use ($table, $unlinked) {
                    $q->whereNull('student_id')
                        ->orWhereIn('id', function ($sub) use ($table, $unlinked) {
                            $sub->selectRaw('MAX(id)')
                                ->from($table)
                                ->whereNotNull('student_id')
                                ->where($unlinked)
                                ->groupBy('student_id');
                        });
                });
            }
        }

        return $query->orderBy('last_name')->orderBy('first_name');
    }

    /**
     * Options for the "Select Student" dropdown.
     * Only students WITHOUT a user account are returned.
     * With no $search it returns the first 50 (so the list shows when the dropdown opens);
     * with a $search it filters by first name, last name or student ID.
     */
    protected static function studentOptions(?string $search = null): array
    {
        $table = (new Students)->getTable();

        return self::availableStudents()
            ->when($search, function ($query) use ($search, $table) {
                $query->where(function ($q) use ($search, $table) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");

                    if (Schema::hasColumn($table, 'student_id')) {
                        $q->orWhere('student_id', 'like', "%{$search}%");
                    }
                });
            })
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Students $s) => [$s->id => self::studentLabel($s)])
            ->all();
    }

    /** "Juan Dela Cruz (2024-0001)" */
    protected static function studentLabel(Students $student): string
    {
        $a     = $student->getAttributes();
        $label = trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''));

        if (! empty($a['student_id'])) {
            $label .= " ({$a['student_id']})";
        }

        return $label;
    }

    /** Clears every field that is auto-filled from a personnel / student record. */
    protected static function clearAutofill(Set $set): void
    {
        foreach (['name', 'last_name', 'email', 'contact_no', 'birthdate', 'address', 'avatar'] as $field) {
            $set($field, null);
        }
    }

    /** Fills the user form from the chosen student record. */
    protected static function fillFromStudent(Set $set, $studentId): void
    {
        if (! $studentId) {
            self::clearAutofill($set);

            return;
        }

        $student = Students::find($studentId);

        if (! $student) {
            return;
        }

        $a = $student->getAttributes();

        $pick = function (array $keys) use ($a): ?string {
            foreach ($keys as $key) {
                if (isset($a[$key]) && trim((string) $a[$key]) !== '') {
                    return trim((string) $a[$key]);
                }
            }

            return null;
        };

        $set('name', trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '')));
        $set('last_name', $a['last_name'] ?? null);
        $set('email', $pick(['email', 'email_address']));
        $set('contact_no', $pick(['contact_no']));
        $set('birthdate', $pick(['birth_date', 'birthdate']));
        $set('address', $pick(['address']));

        // FileUpload state is an array of uuid => file path.
        $profile = $pick(['profile', 'avatar']);
        $set('avatar', $profile ? [(string) Str::uuid() => $profile] : null);

        $set('password', self::DEFAULT_PASSWORD);
    }

    // ─────────────────────────────────────────────────────────────────
    //  Form
    // ─────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
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

                CheckboxList::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->columns(2)
                    ->required()
                    ->live()
                    ->disableOptionWhen(function (string $value, Get $get, string $operation): bool {
                        if ($operation !== 'create') {
                            return false;
                        }

                        $selectedIds = collect($get('roles') ?? [])->map(fn ($id) => (int) $id)->all();
                        $valueId     = (int) $value;

                        // A ticked role stays clickable so it can be unticked.
                        if (empty($selectedIds) || in_array($valueId, $selectedIds, true)) {
                            return false;
                        }

                        $exclusive = ['admin', 'department head', 'student'];

                        $nameOf = fn (int $id): string => strtolower((string) Role::whereKey($id)->value('name'));

                        // An exclusive role is already ticked: lock everything else.
                        foreach ($selectedIds as $id) {
                            if (in_array($nameOf($id), $exclusive, true)) {
                                return true;
                            }
                        }

                        // Only combinable roles are ticked: lock the exclusive ones.
                        return in_array($nameOf($valueId), $exclusive, true);
                    })
                    ->rule(function (string $operation) {
                        return function (string $attribute, $value, $fail) use ($operation) {
                            if ($operation !== 'create') {
                                return;
                            }

                            $ids = array_map('intval', (array) $value);

                            if (count($ids) <= 1) {
                                return;
                            }

                            $names = Role::whereIn('id', $ids)->pluck('name')
                                ->map(fn ($n) => strtolower($n));

                            if (! $names->every(fn ($n) => in_array($n, ['guidance', 'scholarship'], true))) {
                                $fail('Admin, Department Head and Student cannot be combined with other roles. Only Guidance and Scholarship can be combined.');
                            }
                        };
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        if (self::hasStudentRole($state)) {
                            // Switching to Student: drop any personnel that was picked.
                            if ($get('personnel_id')) {
                                $set('personnel_id', null);
                                self::clearAutofill($set);
                            }

                            $set('password', self::DEFAULT_PASSWORD);

                            return;
                        }

                        // Switching away from Student: drop any student that was picked.
                        if ($get('student_record_id')) {
                            $set('student_record_id', null);
                            self::clearAutofill($set);
                        }

                        if (self::hasDepartmentHeadRole($state)) {
                            $set('password', self::DEFAULT_PASSWORD);
                        }
                    }),

                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name', fn ($query) => $query->active())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(fn (Get $get) => self::hasDepartmentHeadRole($get('roles')))
                    ->visible(fn (Get $get) => self::hasDepartmentHeadRole($get('roles'))),

                // ── Personnel dropdown (staff roles) ──
                Select::make('personnel_id')
                    ->label('Select Personnel')
                    ->hiddenOn('edit')
                    ->options(
                        Personnels::whereDoesntHave('user') // only personnels without a user
                            ->get()
                            ->mapWithKeys(fn ($p) => [
                                $p->id => "{$p->first_name} {$p->last_name}",
                            ])
                    )
                    ->searchable()
                    ->required(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles')) && ! self::hasStudentRole($get('roles')))
                    ->visible(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles')) && ! self::hasStudentRole($get('roles')))
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (! $state) {
                            return;
                        }

                        $personnel = Personnels::find($state);
                        if (! $personnel) {
                            return;
                        }

                        $set('name', "{$personnel->first_name} {$personnel->last_name}");
                        $set('last_name', $personnel->last_name);
                        $set('email', $personnel->email);
                        $set('contact_no', $personnel->contact_no);
                        $set('birthdate', $personnel->birthdate);
                        $set('address', $personnel->address);

                        // Auto-fill profile picture from personnel.
                        // FileUpload state is an array of uuid => file path.
                        if ($personnel->profile) {
                            $set('avatar', [(string) Str::uuid() => $personnel->profile]);
                        } else {
                            $set('avatar', null);
                        }

                        // Pre-fill password, still editable
                        $set('password', self::DEFAULT_PASSWORD);
                    }),

                // ── Student dropdown (shown instead of Personnel when "Student" is ticked) ──
                Select::make('student_record_id')
                    ->label('Select Student')
                    ->hiddenOn('edit')
                    ->searchable()
                    // Shown as soon as the dropdown opens (students without an account only).
                    ->options(fn () => self::studentOptions())
                    // Typing in the search box queries the database.
                    ->getSearchResultsUsing(fn (string $search): array => self::studentOptions($search))
                    ->getOptionLabelUsing(function ($value): ?string {
                        $student = Students::find($value);

                        return $student ? self::studentLabel($student) : null;
                    })
                    ->noSearchResultsMessage('No students without an account found.')
                    ->required(fn (Get $get) => self::hasStudentRole($get('roles')))
                    ->visible(fn (Get $get) => self::hasStudentRole($get('roles')))
                    // Not a real column on users: only used to pick the student.
                    ->dehydrated(false)
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state) {
                        self::fillFromStudent($set, $state);
                    })
                    // After the user is created, link the student record to the new account.
                    ->saveRelationshipsUsing(function (User $record, $state) {
                        if (! $state) {
                            return;
                        }

                        $student = Students::find($state);

                        if (! $student) {
                            return;
                        }

                        $studentTable = $student->getTable();

                        if (Schema::hasColumn($studentTable, 'user_id')) {
                            $student->forceFill(['user_id' => $record->id])->save();
                        }

                        $studentNumber = $student->getAttributes()['student_id'] ?? null;

                        if ($studentNumber
                            && Schema::hasColumn($record->getTable(), 'student_id')) {
                            $record->forceFill(['student_id' => $studentNumber])->save();
                        }
                    }),

                // readOnly (not disabled) so the value is always submitted and saved.
                // It is filled automatically for staff and students; only Department
                // Heads type it in.
                TextInput::make('name')
                    ->required()
                    ->dehydrated()
                    ->readOnly(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles'))),

                // Filled from the personnel / student record (used to match student accounts).
                Hidden::make('last_name')
                    ->dehydrated(),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100)
                    ->dehydrated()
                    // Editable for Department Head and Student accounts (a student record
                    // may not have an email on file); disabled for other roles.
                    ->disabled(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles'))
                        && ! self::hasStudentRole($get('roles')))
                    // Unique among ALL users, including archived ones, so a new user can
                    // never take an email that already belongs to an existing account.
                    ->unique('users', 'email', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'This email is already taken by another user account.',
                    ])
                    // The email is synced to the personnel record, so it must not belong to
                    // a DIFFERENT personnel. The personnel being linked is excluded.
                    ->rule(function (Get $get, ?User $record) {
                        return function (string $attribute, $value, $fail) use ($get, $record) {
                            $ownPersonnelId = $record?->personnel_id ?? $get('personnel_id');

                            $exists = Personnels::where('email', $value)
                                ->when($ownPersonnelId, fn ($q) => $q->where('id', '!=', $ownPersonnelId))
                                ->exists();

                            if ($exists) {
                                $fail('This email is already used by another personnel record.');
                            }
                        };
                    })
                    ->dehydrateStateUsing(fn ($state) => $state ? strtolower(trim($state)) : $state),

                TextInput::make('contact_no')
                    ->label('Contact Number')
                    ->tel()
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

                DatePicker::make('birthdate')
                    ->label('Date of Birth')
                    ->native(false)
                    ->displayFormat('M d, Y')
                    // Staff must be 18+, students may be younger.
                    ->maxDate(fn (Get $get) => self::hasStudentRole($get('roles'))
                        ? now()
                        : now()->subYears(18))
                    ->validationMessages([
                        'before_or_equal' => 'Must be at least 18 years old.',
                    ]),

                TextInput::make('address')
                    ->maxLength(100),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->disabled()
                    ->required()
                    ->hiddenOn('edit')
                    ->default(self::DEFAULT_PASSWORD)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
            ]);
    }

    /**
     * Read-only "View User" page.
     * (Used instead of the form so the password hash is never displayed.)
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Account')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('avatar')
                            ->label('Profile Picture')
                            ->disk('public')
                            ->circular()
                            ->defaultImageUrl(fn (User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=random')
                            ->columnSpanFull(),

                        TextEntry::make('name')
                            ->label('Name'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),

                        TextEntry::make('roles.name')
                            ->label('Roles')
                            ->badge()
                            ->placeholder('—'),

                        TextEntry::make('department.name')
                            ->label('Department')
                            ->placeholder('—'),
                    ]),

                Section::make('Personal Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('personnel.position')
                            ->label('Position')
                            ->placeholder('—'),

                        TextEntry::make('contact_no')
                            ->label('Contact No.')
                            ->placeholder('—'),

                        TextEntry::make('birthdate')
                            ->label('Date of Birth')
                            ->date('M d, Y')
                            ->placeholder('—'),

                        TextEntry::make('address')
                            ->label('Address')
                            ->placeholder('—'),
                    ]),

                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime('M d, Y h:i A'),

                        TextEntry::make('archived_at')
                            ->label('Archived At')
                            ->dateTime('M d, Y h:i A')
                            ->placeholder('Active'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'name')
                    ->multiple()
                    ->preload()
                    ->placeholder('All Roles'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canView($record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}