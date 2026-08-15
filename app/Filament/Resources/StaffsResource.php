<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffsResource\Pages;
// use App\Filament\Resources\StaffsResource\RelationManagers;
use App\Models\Staffs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffsResource extends Resource
{
    protected static ?string $model = Staffs::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('staff_number')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('first_number')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('middle_number')
                    ->maxLength(200)
                    ->default(null),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->maxLength(200),
                Forms\Components\DatePicker::make('birth_date')
                    ->required(),
                Forms\Components\TextInput::make('age')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('gender')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->maxLength(200),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListStaffs::route('/'),
            'create' => Pages\CreateStaffs::route('/create'),
            'edit' => Pages\EditStaffs::route('/{record}/edit'),
        ];
    }
}
