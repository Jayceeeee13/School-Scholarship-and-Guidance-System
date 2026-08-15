<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodResource\Pages;
use App\Models\InactiveDate;
use App\Models\Period;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Periods';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 10;

    // Hidden from sidebar — accessed via the Settings page links only
    protected static bool $shouldRegisterNavigation = false;

    // ── Form ─────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Period Settings')
                ->description('Control whether this period is currently open for submissions.')
                ->schema([

                    Forms\Components\TextInput::make('key')
                        ->label('Key')
                        ->disabled()
                        ->helperText('Internal identifier — cannot be changed.'),

                    Forms\Components\TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->maxLength(100)
                        ->helperText('Human-readable name shown in the admin UI.'),

                    Forms\Components\Toggle::make('is_open')
                        ->label('Currently Open (Manual Override)')
                        ->helperText('Used as fallback when no schedule dates are set.')
                        ->onColor('success')
                        ->offColor('danger')
                        ->live(),

                ])->columns(1),

            Forms\Components\Section::make('Schedule')
                ->description('Set the dates for this period. When dates are set, the period opens and closes automatically based on the schedule — the manual toggle above is ignored. Inactive/holiday dates are disabled.')
                ->schema([

                    Forms\Components\DateTimePicker::make('open_date')
                        ->label('Opening Date & Time')
                        ->helperText('Displayed to students when the period is closed. Period opens automatically at this time.')
                        ->native(false)
                        ->displayFormat('F d, Y h:i A')
                        ->nullable()
                        ->disabledDates(fn () => InactiveDate::getInactiveDates())
                        ->rules([
                            function () {
                                return function (string $attribute, $value, $fail) {
                                    if ($value && InactiveDate::isInactive(
                                        \Carbon\Carbon::parse($value)->toDateString()
                                    )) {
                                        $fail('The opening date falls on an inactive/holiday date. Please choose another date.');
                                    }
                                };
                            },
                        ]),

                    Forms\Components\DateTimePicker::make('close_date')
                        ->label('Closing Date & Time')
                        ->helperText('Period closes automatically at this time.')
                        ->native(false)
                        ->displayFormat('F d, Y h:i A')
                        ->nullable()
                        ->afterOrEqual('open_date')
                        ->disabledDates(fn () => InactiveDate::getInactiveDates())
                        ->rules([
                            function () {
                                return function (string $attribute, $value, $fail) {
                                    if ($value && InactiveDate::isInactive(
                                        \Carbon\Carbon::parse($value)->toDateString()
                                    )) {
                                        $fail('The closing date falls on an inactive/holiday date. Please choose another date.');
                                    }
                                };
                            },
                        ]),

                ])->columns(2),

        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('label')
                    ->label('Period')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_open')
                    ->label('Status')
                    ->boolean()
                    ->state(fn (Period $record): bool => $record->is_open)
                    ->trueIcon('heroicon-o-lock-open')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('open_date')
                    ->label('Opens')
                    ->dateTime('F d, Y h:i A')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('close_date')
                    ->label('Closes')
                    ->dateTime('F d, Y h:i A')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->paginated(false);
    }

    // ── Pages ─────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriods::route('/'),
            'edit'  => Pages\EditPeriod::route('/{record}/edit'),
        ];
    }
}