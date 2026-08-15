<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TermResource\Pages;
use App\Models\Term;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Terms';

    protected static ?string $navigationGroup = 'Settings';

    protected static bool $shouldRegisterNavigation = false; // Hides from sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Term Information')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('school_year')
                            ->label('School Year')
                            ->options(
                                collect(range(2000, 2049))
                                    ->mapWithKeys(fn ($y) => ["{$y}-" . ($y + 1) => "{$y}-" . ($y + 1)])
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('semester')
                            ->label('Semester')
                            ->options([
                                '1st Semester' => '1st Semester',
                                '2nd Semester' => '2nd Semester',
                                'Summer'       => 'Summer',
                            ])
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Set as Active Term')
                            ->helperText('Only one term can be active at a time. Enabling this will deactivate all others.')
                            ->default(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_year')
                    ->label('School Year')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('semester')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Students')
                    ->counts('students')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('school_year', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Terms')
                    ->placeholder('All Terms')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->actions([
                // Quick toggle active status
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (Term $record) => $record->is_active ? 'Deactivate' : 'Set Active')
                    ->icon(fn (Term $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Term $record) => $record->is_active ? 'danger' : 'success')
                    ->action(function (Term $record) {
                        if (! $record->is_active) {
                            // Deactivate all others first
                            Term::where('id', '!=', $record->id)->update(['is_active' => false]);
                        }
                        $record->update(['is_active' => ! $record->is_active]);

                        Notification::make()
                            ->title($record->is_active ? 'Term activated' : 'Term deactivated')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (Term $record) {
                            if ($record->students()->exists()) {
                                Notification::make()
                                    ->title('Cannot delete')
                                    ->body('This term has students assigned to it.')
                                    ->danger()
                                    ->send();

                                throw new \Filament\Exceptions\Halt();
                            }
                        }),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->students()->exists()) {
                                    Notification::make()
                                        ->title('Cannot delete')
                                        ->body("Term \"{$record->school_year} — {$record->semester}\" has students assigned and cannot be deleted.")
                                        ->danger()
                                        ->send();

                                    throw new \Filament\Exceptions\Halt();
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTerms::route('/'),
            'create' => Pages\CreateTerm::route('/create'),
            'edit'   => Pages\EditTerm::route('/{record}/edit'),
        ];
    }
}