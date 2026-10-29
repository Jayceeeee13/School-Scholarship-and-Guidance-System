<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Role;
use App\Models\Personnels;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-circle';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationGroup = 'Generals';

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
     * Gender options, shared by the user and personnel forms.
     * ASSUMPTION: a `genders` table with `id` and `name` columns.
     * Adjust here if yours is different.
     */
    public static function genderOptions(): array
    {
        return DB::table('genders')->orderBy('name')->pluck('name', 'id')->all();
    }

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
    ->relationship(
        'roles',
        'name',
        fn ($query, string $operation) => $operation === 'create'
            ? $query->where('name', '!=', 'Student')
            : $query
    )
    ->columns(2)
    ->required()
    ->live()
    ->afterStateUpdated(function (Set $set, $state) {
        if (self::hasDepartmentHeadRole($state)) {
            $set('password', 'GVCFI@2026');
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
                    ->required(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles')))
                    ->visible(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles')))
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
                        $set('password', 'GVCFI@2026');
                    }),

                TextInput::make('name')
                    ->required()
                    ->dehydrated()
                    ->disabled(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles'))),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100)
                    ->dehydrated()
                    ->disabled(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles')))
                    ->unique('users', 'email', ignoreRecord: true)
                    // The email is synced to the personnel record, so it must
                    // not collide with a DIFFERENT personnel's email.
                    ->rule(function (?User $record) {
                        return function (string $attribute, $value, $fail) use ($record) {
                            $exists = Personnels::where('email', $value)
                                ->when(
                                    $record?->personnel_id,
                                    fn ($q) => $q->where('id', '!=', $record->personnel_id)
                                )
                                ->exists();

                            if ($exists) {
                                $fail('This email is already used by another personnel record.');
                            }
                        };
                    }),

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
                    ->maxDate(now()),

                TextInput::make('address')
                    ->maxLength(100),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->disabled()
                    ->required()
                    ->hiddenOn('edit')
                    ->default('GVCFI@2026')
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
                //
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