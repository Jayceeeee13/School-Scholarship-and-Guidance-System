<?php

namespace App\Filament\Resources\ReferralsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LogformsRelationManager extends RelationManager
{
    protected static string $relationship = 'logforms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(500),
                Forms\Components\TextInput::make('course_and_year')
                    ->required()
                    ->maxLength(500),
                Forms\Components\TextInput::make('contact_no')
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('concern')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('remarks')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('course_and_year'),
                Tables\Columns\TextColumn::make('contact_no'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}