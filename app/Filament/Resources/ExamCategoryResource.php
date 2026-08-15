<?php
// app/Filament/Resources/ExamCategoryResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamCategoryResource\Pages;
use App\Models\ExamCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ExamCategoryResource extends Resource
{
    protected static ?string $model = ExamCategory::class;
    protected static ?string $navigationIcon = 'heroicon-s-tag';
    protected static ?string $navigationGroup = 'Exam Management';
    // protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Category Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Questions')
                    ->counts('questions')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExamCategories::route('/'),
            'create' => Pages\CreateExamCategory::route('/create'),
            'edit'   => Pages\EditExamCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}