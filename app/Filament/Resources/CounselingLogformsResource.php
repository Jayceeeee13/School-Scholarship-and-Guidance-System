<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CounselingLogformsResource\Pages;
use App\Models\CounselingLogforms;
use App\Models\Students;
use App\Models\CounselingAppointments;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CounselingLogformsResource extends Resource
{
    protected static ?string $model = CounselingLogforms::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationLabel = 'Logforms';

    protected static ?string $navigationGroup = 'Guidance Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Type of Appointment')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Appointment Type')
                            ->options([
                                'scheduled' => 'Scheduled Appointment',
                                'walk_in'   => 'Walk-in',
                            ])
                            ->default('scheduled')
                            ->required()
                            ->live()
                            ->native(false)
                            ->columnSpanFull(),
                    ]),

                // ── SCHEDULED: pick an existing appointment ─────────────────
                Forms\Components\Section::make('Student Information')
                    ->schema([
                        Forms\Components\Select::make('counseling_appointments_id')
                            ->label('Student Appointment')
                            ->relationship('appointment', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                $record->first_name . ' ' . $record->last_name . ' — ' . $record->course_and_year
                            )
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get) => $get('type') === 'scheduled')
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('course_and_year')
                            ->label('Course & Year')
                            ->content(fn ($record) => $record?->appointment?->course_and_year ?? '—'),

                        Forms\Components\Placeholder::make('contact_no')
                            ->label('Contact Number')
                            ->content(fn ($record) => $record?->appointment?->contact_no ?? '—'),

                        Forms\Components\Placeholder::make('present_address')
                            ->label('Address')
                            ->content(fn ($record) => $record?->appointment?->present_address ?? '—'),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'scheduled'),

                // ── WALK-IN: search for existing student ────────────────────
                Forms\Components\Section::make('Walk-in Student')
                    ->description('Search for the enrolled student by name or student ID.')
                    ->schema([
                        Forms\Components\Select::make('walkin_student_id')
                            ->label('Student')
                            ->options(fn () => Students::query()
                                ->with('program')
                                ->get()
                                ->mapWithKeys(fn ($s) => [
                                    $s->id => trim("{$s->first_name} {$s->last_name}")
                                        . ($s->student_id ? " ({$s->student_id})" : '')
                                        . ($s->program ? " — {$s->program->name}" : ''),
                                ]))
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get) => $get('type') === 'walk_in')
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('walkin_course_display')
                            ->label('Program / Year Level')
                            ->content(function (Get $get) {
                                $student = Students::with('program')->find($get('walkin_student_id'));
                                if (! $student) return '—';
                                return trim(($student->program?->name ?? '') . ' ' . ($student->year_level ?? '')) ?: '—';
                            })
                            ->visible(fn (Get $get) => (bool) $get('walkin_student_id')),

                        Forms\Components\Placeholder::make('walkin_contact_display')
                            ->label('Contact Number')
                            ->content(fn (Get $get) => Students::find($get('walkin_student_id'))?->contact_no ?? '—')
                            ->visible(fn (Get $get) => (bool) $get('walkin_student_id')),

                        Forms\Components\Placeholder::make('walkin_address_display')
                            ->label('Address')
                            ->content(fn (Get $get) => Students::find($get('walkin_student_id'))?->address ?? '—')
                            ->visible(fn (Get $get) => (bool) $get('walkin_student_id')),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'walk_in'),

                Forms\Components\Section::make('Counseling Details')
    ->schema([
        Forms\Components\Select::make('support_needed_id')
            ->label('Support Needed')
            ->options(\App\Models\SupportNeeded::active()->pluck('name', 'id'))
            ->searchable()
            ->native(false)
            ->placeholder('Select support type')
            ->columnSpanFull(),

        Forms\Components\Textarea::make('concern')
            ->label('Concern')
            ->rows(4)
            ->columnSpanFull(),

        Forms\Components\Textarea::make('remarks')
            ->label('Remarks')
            ->rows(4)
            ->columnSpanFull(),
    ]),

                // ── ANECDOTAL RECORDS — nested repeater ─────────────────────
                Forms\Components\Section::make('Anecdotal Records')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Forms\Components\Repeater::make('anecdotals')
                            ->relationship('anecdotals')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('display_name')
                                            ->label('Name')
                                            ->required()
                                            ->maxLength(500)
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(function (Get $get) {
                                                $type = $get('../../type');

                                                if ($type === 'walk_in') {
                                                    $student = Students::find($get('../../walkin_student_id'));
                                                    return $student
                                                        ? trim("{$student->first_name} {$student->middle_name} {$student->last_name}")
                                                        : '';
                                                }

                                                $appointment = CounselingAppointments::find($get('../../counseling_appointments_id'));
                                                return $appointment
                                                    ? trim("{$appointment->first_name} {$appointment->middle_name} {$appointment->last_name}")
                                                    : '';
                                            })
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if (!empty($state)) return;
                                                if (! $record || ! $record->logform) return;

                                                $logform = $record->logform;

                                                if ($logform->isWalkIn()) {
                                                    $student = $logform->walkInStudent;
                                                    if ($student) {
                                                        $component->state(trim("{$student->first_name} {$student->middle_name} {$student->last_name}"));
                                                    }
                                                    return;
                                                }

                                                $appointment = $logform->appointment;
                                                if ($appointment) {
                                                    $component->state(trim("{$appointment->first_name} {$appointment->middle_name} {$appointment->last_name}"));
                                                }
                                            })
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('display_course')
                                            ->label('Course')
                                            ->maxLength(500)
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(function (Get $get) {
                                                $type = $get('../../type');

                                                if ($type === 'walk_in') {
                                                    $student = Students::with('program')->find($get('../../walkin_student_id'));
                                                    if (! $student) return '';
                                                    return trim(($student->program?->name ?? '') . ' ' . ($student->year_level ?? ''));
                                                }

                                                $appointment = CounselingAppointments::find($get('../../counseling_appointments_id'));
                                                return $appointment?->course_and_year ?? '';
                                            })
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if (!empty($state)) return;
                                                if (! $record || ! $record->logform) return;

                                                $logform = $record->logform;

                                                if ($logform->isWalkIn()) {
                                                    $student = $logform->walkInStudent;
                                                    if ($student) {
                                                        $component->state(trim(($student->program?->name ?? '') . ' ' . ($student->year_level ?? '')));
                                                    }
                                                    return;
                                                }

                                                if ($logform->appointment) {
                                                    $component->state($logform->appointment->course_and_year);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('display_contact')
                                            ->label('Contact')
                                            ->tel()
                                            ->maxLength(20)
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(function (Get $get) {
                                                $type = $get('../../type');

                                                if ($type === 'walk_in') {
                                                    return Students::find($get('../../walkin_student_id'))?->contact_no ?? '';
                                                }

                                                return CounselingAppointments::find($get('../../counseling_appointments_id'))?->contact_no ?? '';
                                            })
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if (!empty($state)) return;
                                                if (! $record || ! $record->logform) return;

                                                $logform = $record->logform;

                                                if ($logform->isWalkIn()) {
                                                    $component->state($logform->walkInStudent?->contact_no ?? '');
                                                    return;
                                                }

                                                $component->state($logform->appointment?->contact_no ?? '');
                                            }),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Textarea::make('display_address')
                                            ->label('Address')
                                            ->maxLength(500)
                                            ->rows(1)
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(function (Get $get) {
                                                $type = $get('../../type');

                                                if ($type === 'walk_in') {
                                                    return Students::find($get('../../walkin_student_id'))?->address ?? '';
                                                }

                                                return CounselingAppointments::find($get('../../counseling_appointments_id'))?->present_address ?? '';
                                            })
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if (!empty($state)) return;
                                                if (! $record || ! $record->logform) return;

                                                $logform = $record->logform;

                                                if ($logform->isWalkIn()) {
                                                    $component->state($logform->walkInStudent?->address ?? '');
                                                    return;
                                                }

                                                $component->state($logform->appointment?->present_address ?? '');
                                            }),

                                        Forms\Components\TextInput::make('area_concern')
                                            ->label('Area of Concern')
                                            ->placeholder('e.g., Academic, Social')
                                            ->maxLength(500),
                                    ]),

                                Forms\Components\RichEditor::make('concern')
                                    ->label('Observation')
                                    ->placeholder('Describe observation...')
                                    ->columnSpanFull()
                                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                    ->disableToolbarButtons(['attachFiles']),

                                Forms\Components\RichEditor::make('intervention')
                                    ->label('Intervention')
                                    ->placeholder('Action taken...')
                                    ->columnSpanFull()
                                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                    ->disableToolbarButtons(['attachFiles']),

                                Forms\Components\Select::make('personnel_id')
                                    ->label('Interviewed By')
                                    ->relationship('personnel', 'last_name', fn ($query) => $query->selectRaw("*, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name"))
                                    ->getOptionLabelFromRecordUsing(fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->last_name}"))
                                    ->searchable(['first_name', 'middle_name', 'last_name'])
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select counselor/interviewer')
                                    ->columnSpanFull()
                                    ->native(false),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['display_name'] ?? 'New Record')
                            ->addActionLabel('+ Anecdotal')
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->cloneable()
                            ->collapsible()
                            ->deleteAction(fn ($action) => $action->requiresConfirmation()->size('sm'))
                            ->addAction(fn ($action) => $action->size('sm')),
                    ])
                    ->visible(fn ($operation) => in_array($operation, ['edit', 'create']))
                    ->collapsible(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Student Name'),
            ]);
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
            'index'  => Pages\ListCounselingLogforms::route('/'),
            'create' => Pages\CreateCounselingLogforms::route('/create'),
            'edit'   => Pages\EditCounselingLogforms::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }
}