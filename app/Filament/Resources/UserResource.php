<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Role;
use App\Models\Personnels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
                        $set('gender_id', $personnel->gender_id);

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
                    ->disabled(fn (Get $get) => ! self::hasDepartmentHeadRole($get('roles'))),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->disabled()
                    ->required()
                    ->default('GVCFI@2026')
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
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
                // Tables\Actions\EditAction::make(),
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
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
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