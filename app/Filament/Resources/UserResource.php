<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\Personnels; 
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

protected static ?string $navigationIcon = 'heroicon-s-user-circle';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationGroup = 'Generals';

    protected static function isDepartmentHeadRole(?int $roleId): bool
    {
        if (! $roleId) return false;

        return Role::find($roleId)?->name === 'Department Head';
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
                // ->circular()
                ->nullable(),

                Select::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if (self::isDepartmentHeadRole($state)) {
                            $set('password', 'GVCFI@2026');
                        }
                    }),

                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name', fn ($query) => $query->active())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(fn (Get $get) => self::isDepartmentHeadRole($get('role_id')))
                    ->visible(fn (Get $get) => self::isDepartmentHeadRole($get('role_id'))),

                 Select::make('personnel_id')
            ->label('Select Personnel')
            ->options(
                Personnels::whereDoesntHave('user') // ✅ only show personnels without a user
            ->get()
            ->mapWithKeys(fn ($p) => [
                $p->id => "{$p->first_name} {$p->last_name}"
            ])
            )
            ->searchable()
            ->required(fn (Get $get) => ! self::isDepartmentHeadRole($get('role_id')))
            ->visible(fn (Get $get) => ! self::isDepartmentHeadRole($get('role_id')))
            ->reactive()
            ->afterStateUpdated(function (Set $set, $state) {
                if (!$state) return;

                $personnel = Personnels::find($state);
                if (!$personnel) return;

                $set('name', "{$personnel->first_name} {$personnel->last_name}");
                $set('email', $personnel->email);
                $set('contact_no', $personnel->contact_no);
                $set('birthdate', $personnel->birthdate);
                $set('address', $personnel->address);
                $set('gender_id', $personnel->gender_id);

                // Pre-fill password, still editable
                $set('password', 'GVCFI@2026');
            }),

                TextInput::make('name')
                    ->required()
                    ->dehydrated()
                    ->disabled(fn (Get $get) => ! self::isDepartmentHeadRole($get('role_id'))),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->disabled(fn (Get $get) => ! self::isDepartmentHeadRole($get('role_id'))),

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
                TextColumn::make('role.name')
                ->label('Role')
                ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('—')
                    ->toggleable(),
            //     IconColumn::make('is_online')
            // ->label('Status')
            // ->boolean()
            // ->trueIcon('heroicon-o-check-circle')
            // ->falseIcon('heroicon-o-x-circle')
            // ->trueColor('success')  // 🟢 green
            // ->falseColor('danger'), // 🔴 red
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
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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