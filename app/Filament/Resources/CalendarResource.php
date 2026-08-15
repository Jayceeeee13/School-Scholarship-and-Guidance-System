<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarResource\Pages;
use App\Models\CounselingAppointments;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CalendarResource extends Resource
{
    protected static ?string $model = CounselingAppointments::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Calendars';
    protected static ?string $navigationGroup = 'Generals';

    protected static ?string $modelLabel = 'Calendar';
    protected static ?string $pluralModelLabel = 'Calendars';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'       => Pages\CalendarAppointments::route('/'),
            'manage-date' => Pages\ManageDatePage::route('/date/{date}'),
        ];
    }
}