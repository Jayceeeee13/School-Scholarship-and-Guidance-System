<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenderResource\Pages;
use App\Filament\Resources\GenderResource\RelationManagers;
use App\Models\Gender;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GenderResource extends Resource
{
    protected static ?string $model = Gender::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false; // Hides from sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
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
            'index' => Pages\ListGenders::route('/'),
            'create' => Pages\CreateGender::route('/create'),
            'edit' => Pages\EditGender::route('/{record}/edit'),
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
