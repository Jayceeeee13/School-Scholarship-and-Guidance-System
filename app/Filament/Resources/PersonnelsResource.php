<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonnelsResource\Pages;
use App\Filament\Resources\PersonnelsResource\RelationManagers;
use App\Models\Personnels;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonnelsResource extends Resource
{
    protected static ?string $model = Personnels::class;

protected static ?string $navigationIcon = 'heroicon-s-identification';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Generals';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            $age = \Carbon\Carbon::parse($state)->age;
            $set('age', $age);
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
        'regex'     => 'Contact number can only contain digits.',
        'min'       => 'Contact number must be exactly 11 digits.',
        'max'       => 'Contact number must be exactly 11 digits.',
    ])
    ->extraInputAttributes([
        'onkeypress' => "return /^[0-9]$/.test(event.key)",
        'maxlength'  => '11',
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : $state),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->extraInputAttributes([
                    'style' => 'text-transform: capitalize;',
                    ])
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthdate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPersonnels::route('/'),
            'create' => Pages\CreatePersonnels::route('/create'),
            'edit' => Pages\EditPersonnels::route('/{record}/edit'),
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
    return false;
}
}
