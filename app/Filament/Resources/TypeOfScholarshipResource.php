<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeOfScholarshipResource\Pages;
use App\Filament\Resources\TypeOfScholarshipResource\RelationManagers;
use App\Models\TypeOfScholarship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeOfScholarshipResource extends Resource
{
    protected static ?string $model = TypeOfScholarship::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false; // Hides from sidebar

    protected static ?string $modelLabel = 'Scholarship Offer';
    protected static ?string $pluralModelLabel = 'Scholarship Offers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('slots')
                    ->label('Slots')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Number of available slots for this scholarship type'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive items will not appear in dropdowns'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('slots')
                    ->label('Slots')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5  => 'warning',
                        default      => 'success',
                    }),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTypeOfScholarships::route('/'),
            'create' => Pages\CreateTypeOfScholarship::route('/create'),
            'edit' => Pages\EditTypeOfScholarship::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
{
    return auth()->user()->hasAnyRole(['admin', 'scholarship']);
}

public static function canCreate(): bool
{
    return auth()->user()->hasAnyRole(['admin', 'scholarship']);
}

public static function canEdit($record): bool
{
    return auth()->user()->hasAnyRole(['admin', 'scholarship']);
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