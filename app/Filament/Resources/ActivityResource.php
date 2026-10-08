<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Activities';

    // Hidden from the sidebar; reached through the Settings page instead.
    protected static bool $shouldRegisterNavigation = false;

    // ── Permissions: admin only ─────────────────────────────────────

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    // ── Form ────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(150)
                ->columnSpanFull(),

            Forms\Components\DatePicker::make('activity_date')
                ->label('Date')
                ->default(now())
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('images')
                ->label('Photos')
                ->helperText('Up to 10 photos. The first photo is the cover. Drag to reorder.')
                ->image()
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->maxFiles(10)
                ->maxSize(2048)
                ->disk('public')
                ->directory('activities')
                ->imageEditor()
                ->panelLayout('grid')
                ->columnSpanFull(),

            Forms\Components\Textarea::make('description')
                ->rows(6)
                ->maxLength(2000)
                ->columnSpanFull(),
        ]);
    }

    // ── Table ───────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Photos')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('activity_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('activity_date', 'desc')
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
            'index' => Pages\ManageActivities::route('/'),
        ];
    }
}