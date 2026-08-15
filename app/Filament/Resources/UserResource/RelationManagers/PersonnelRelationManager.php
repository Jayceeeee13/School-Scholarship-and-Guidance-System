<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PersonnelRelationManager extends RelationManager
{
    protected static string $relationship = 'personnel';

    protected static ?string $title = 'Personnel Info';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('middle_name')
                    ->label('Middle Name')
                    ->maxLength(100),

                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(100),

                Forms\Components\DatePicker::make('birthdate')
                    ->label('Date of Birth')
                    ->required()
                    ->native(false)
                    ->displayFormat('M d, Y')
                    ->maxDate(now()),

                Forms\Components\TextInput::make('age')
                    ->label('Age')
                    ->numeric()
                    ->readOnly(),

                Forms\Components\TextInput::make('contact_no')
                    ->label('Contact Number')
                    ->tel()
                    ->required()
                    ->maxLength(11),

                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('birthdate')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('contact_no')
                    ->label('Contact No.')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }
}