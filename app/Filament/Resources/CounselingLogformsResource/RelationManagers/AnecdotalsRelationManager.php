<?php

namespace App\Filament\Resources\CounselingLogformsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AnecdotalsRelationManager extends RelationManager
{
    protected static string $relationship = 'anecdotals';

    public function form(Form $form): Form
    {
        $appointment = $this->getOwnerRecord()?->appointment;

        $fullName = $appointment
            ? trim($appointment->first_name . ' ' . $appointment->middle_name . ' ' . $appointment->last_name)
            : '—';

        return $form
            ->schema([
                // Student info as hidden fields (auto-populated from appointment)
                Forms\Components\Hidden::make('name')
                    ->default($fullName)
                    ->dehydrated(),

                Forms\Components\Hidden::make('course_and_year')
                    ->default($appointment?->course_and_year)
                    ->dehydrated(),

                Forms\Components\Hidden::make('contact_no')
                    ->default($appointment?->contact_no)
                    ->dehydrated(),

                Forms\Components\Hidden::make('address')
                    ->default($appointment?->present_address)
                    ->dehydrated(),

                // Student info display section
                Forms\Components\Section::make('Student Information')
                    ->schema([
                        Forms\Components\TextInput::make('student_full_name')
                            ->label('Full Name')
                            ->default($fullName)
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('student_course')
                            ->label('Course & Year')
                            ->default($appointment?->course_and_year ?? '—')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('student_contact')
                            ->label('Contact')
                            ->default($appointment?->contact_no ?? '—')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('student_address')
                            ->label('Address')
                            ->default($appointment?->present_address ?? '—')
                            ->disabled()
                            ->columnSpan(1),
                    ]),

                Forms\Components\Section::make('Anecdotal Details')
                    ->schema([
                        Forms\Components\Textarea::make('area_concern')
                            ->label('Area of Concern')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('concern')
                            ->label('Specific Concern')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('intervention')
                            ->label('Intervention/Action Taken')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('personnel_id')
                            ->label('Interviewed By')
                            ->relationship(
                                'personnel',
                                'last_name',
                                fn ($query) => $query->selectRaw("*, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name")
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->last_name}"))
                            ->searchable(['first_name', 'middle_name', 'last_name'])
                            ->preload()
                            ->required()
                            ->placeholder('Select counselor/interviewer')
                            ->columnSpanFull()
                            ->native(false),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area_concern')
                    ->label('Area of Concern')
                    ->limit(40),

                Tables\Columns\TextColumn::make('concern')
                    ->label('Concern')
                    ->limit(40)
                    ->html(),

                Tables\Columns\TextColumn::make('intervention')
                    ->label('Intervention')
                    ->limit(40)
                    ->html(),

                Tables\Columns\TextColumn::make('personnel.first_name')
                    ->label('Interviewed By')
                    ->formatStateUsing(fn ($record) => trim("{$record?->personnel?->first_name} {$record?->personnel?->middle_name} {$record?->personnel?->last_name}")),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}