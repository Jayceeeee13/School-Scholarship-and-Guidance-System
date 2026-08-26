<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnecdotalsResource\Pages;
use App\Models\Anecdotals;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;

class AnecdotalsResource extends Resource
{
    protected static ?string $model = Anecdotals::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

    protected static ?string $navigationLabel = 'Anecdotals';

    protected static ?string $navigationGroup = 'Guidance Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Information')
                    ->schema([
                        // Read-only info pulled from logform → appointment
                        Forms\Components\Placeholder::make('name')
                            ->label('Full Name')
                            ->content(fn ($record) => $record?->logform?->appointment
                                ? trim(
                                    $record->logform->appointment->first_name . ' ' .
                                    $record->logform->appointment->middle_name . ' ' .
                                    $record->logform->appointment->last_name
                                )
                                : '—'
                            )
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('course_and_year')
                            ->label('Course & Year')
                            ->content(fn ($record) => $record?->logform?->appointment?->course_and_year ?? '—'),

                        Forms\Components\Placeholder::make('contact_no')
                            ->label('Contact Number')
                            ->content(fn ($record) => $record?->logform?->appointment?->contact_no ?? '—'),

                        Forms\Components\Placeholder::make('address')
                            ->label('Address')
                            ->content(fn ($record) => $record?->logform?->appointment?->present_address ?? '—')
                            ->columnSpanFull(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Student Information')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('full_name')
                            ->label('Full Name')
                            ->state(fn ($record) => $record?->logform?->appointment
                                ? trim(
                                    $record->logform->appointment->first_name . ' ' .
                                    $record->logform->appointment->middle_name . ' ' .
                                    $record->logform->appointment->last_name
                                )
                                : '—'
                            )
                            ->icon('heroicon-o-user-circle')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),

                        \Filament\Infolists\Components\TextEntry::make('course_and_year')
                            ->label('Course & Year')
                            ->state(fn ($record) => $record?->logform?->appointment?->course_and_year ?? '—')
                            ->icon('heroicon-o-academic-cap')
                            ->badge()
                            ->color('info'),

                        \Filament\Infolists\Components\TextEntry::make('contact_no')
                            ->label('Contact Number')
                            ->state(fn ($record) => $record?->logform?->appointment?->contact_no ?? '—')
                            ->icon('heroicon-o-phone')
                            ->placeholder('N/A')
                            ->copyable()
                            ->copyMessage('Contact number copied!')
                            ->copyMessageDuration(1500),

                        \Filament\Infolists\Components\TextEntry::make('address')
                            ->label('Address')
                            ->state(fn ($record) => $record?->logform?->appointment?->present_address ?? '—')
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull()
                            ->placeholder('No address provided'),
                    ])
                    ->icon('heroicon-o-identification')
                    ->columns(2),

                \Filament\Infolists\Components\Section::make('Observation Details')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('area_concern')
                            ->label('Area of Concern')
                            ->badge()
                            ->color('warning')
                            ->size('lg')
                            ->placeholder('Not specified')
                            ->columnSpanFull(),

                        \Filament\Infolists\Components\TextEntry::make('concern')
                            ->label('Specific Concern/Observation')
                            ->html()
                            ->placeholder('No concern documented')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-exclamation-triangle')
                    ->columns(1),

                \Filament\Infolists\Components\Section::make('Intervention & Action')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('intervention')
                            ->label('Intervention/Action Taken')
                            ->html()
                            ->placeholder('No intervention recorded')
                            ->columnSpanFull(),

                        \Filament\Infolists\Components\TextEntry::make('personnel_name')
                            ->label('Interviewed/Documented By')
                            ->state(function (Anecdotals $record): string {
                                if ($record->personnel) {
                                    return trim("{$record->personnel->first_name} {$record->personnel->middle_name} {$record->personnel->last_name}");
                                }
                                return 'Not specified';
                            })
                            ->icon('heroicon-o-user')
                            ->badge()
                            ->color('success'),
                    ])
                    ->icon('heroicon-o-clipboard-document-check')
                    ->columns(1),

                \Filament\Infolists\Components\Section::make('Record Information')
                    ->schema([
                        \Filament\Infolists\Components\Group::make()
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('F d, Y h:i A')
                                    ->icon('heroicon-o-clock'),
                                \Filament\Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('F d, Y h:i A')
                                    ->icon('heroicon-o-arrow-path')
                                    ->since(),
                            ])
                            ->columns(2),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Pull name from logform → appointment
                Tables\Columns\TextColumn::make('logform.appointment.first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('logform.appointment.last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('logform.appointment.course_and_year')
                    ->label('Course & Year')
                    ->searchable(),

                Tables\Columns\TextColumn::make('area_concern')
                    ->label('Area of Concern')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('personnel.full_name')
                    ->label('Interviewed By')
                    ->getStateUsing(function (Anecdotals $record) {
                        if ($record->personnel) {
                            return trim("{$record->personnel->first_name} {$record->personnel->middle_name} {$record->personnel->last_name}");
                        }
                        return 'N/A';
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('personnel', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('middle_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->join('personnels', 'anecdotals.personnel_id', '=', 'personnels.id')
                            ->orderBy('personnels.last_name', $direction);
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnecdotals::route('/'),
            'create' => Pages\CreateAnecdotals::route('/create'),
            'view'   => Pages\ViewAnecdotals::route('/{record}'),
            'edit'   => Pages\EditAnecdotals::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}