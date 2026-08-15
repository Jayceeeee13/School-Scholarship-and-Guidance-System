<?php

namespace App\Filament\Resources\CounselingAppointmentsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogformsRelationManager extends RelationManager
{
    protected static string $relationship = 'logforms';

    protected static ?string $title = 'Counseling Logforms';

    protected static ?string $icon = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('course_and_year')
                                ->label('Course & Year')
                                ->maxLength(500),
                            Forms\Components\TextInput::make('contact_no')
                                ->label('Contact Number')
                                ->tel()
                                ->maxLength(20),
                        ]),
                    Forms\Components\Textarea::make('address')
                        ->maxLength(500)
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Concern Details')
                ->schema([
                    Forms\Components\Textarea::make('concern')
                        ->maxLength(500)
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course_and_year')
                    ->label('Course & Year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_no')
                    ->label('Contact')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                        // Automatically set the counseling_appointment_id
                        $data['counseling_appointment_id'] = $this->ownerRecord->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_anecdotals')
                    ->label('View Anecdotals')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->url(fn ($record) => 
                        CounselingLogformResource::getUrl('view', ['record' => $record->id])
                    )
                    ->color('info'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
