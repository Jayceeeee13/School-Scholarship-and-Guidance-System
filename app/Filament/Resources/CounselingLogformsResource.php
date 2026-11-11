<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CounselingLogformsResource\Pages;
use App\Models\CounselingLogforms;
use App\Models\Students;
use App\Models\CounselingAppointments;
use App\Models\Term;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class CounselingLogformsResource extends Resource
{
    protected static ?string $model = CounselingLogforms::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationLabel = 'Logforms';

    protected static ?string $navigationGroup = 'Guidance Management';

    protected static ?int $navigationSort = 3;

    /**
     * Resolves the student details used to autofill the anecdotal fields,
     * from either the walk-in student or the scheduled appointment.
     */
    protected static function resolveStudentDetails(?string $type, $walkinId, $appointmentId): array
    {
        if ($type === 'walk_in') {
            $s = $walkinId ? Students::with('program')->find($walkinId) : null;

            return [
                'display_name'    => $s ? trim("{$s->first_name} {$s->middle_name} {$s->last_name}") : '',
                'display_course'  => $s ? trim(($s->program?->name ?? '') . ' ' . ($s->year_level ?? '')) : '',
                'display_contact' => $s?->contact_no ?? '',
                'display_address' => $s?->address ?? '',
            ];
        }

        $a = $appointmentId ? CounselingAppointments::find($appointmentId) : null;

        return [
            'display_name'    => $a ? trim("{$a->first_name} {$a->middle_name} {$a->last_name}") : '',
            'display_course'  => $a?->course_and_year ?? '',
            'display_contact' => $a?->contact_no ?? '',
            'display_address' => $a?->present_address ?? '',
        ];
    }

    /**
     * Refills every anecdotal item already on the form. Called from the
     * type / appointment / walk-in student fields when they change.
     */
    protected static function refillAnecdotals(Get $get, Set $set): void
    {
        $details = static::resolveStudentDetails(
            $get('type'),
            $get('walkin_student_id'),
            $get('counseling_appointments_id'),
        );

        foreach (array_keys($get('anecdotals') ?? []) as $key) {
            foreach ($details as $field => $value) {
                $set("anecdotals.{$key}.{$field}", $value);
            }
        }
    }

    /**
     * Default for a field inside a NEW anecdotal item (repeater item -> form root is ../../).
     */
    protected static function anecdotalDefault(string $field): \Closure
    {
        return fn (Get $get) => static::resolveStudentDetails(
            $get('../../type'),
            $get('../../walkin_student_id'),
            $get('../../counseling_appointments_id'),
        )[$field] ?? '';
    }

    /**
     * Fills a field when editing an existing record whose anecdotal value is empty.
     */
    protected static function anecdotalHydrate(string $field): \Closure
    {
        return function ($component, $state, $record) use ($field) {
            if (! empty($state) || ! $record || ! $record->logform) {
                return;
            }

            $logform = $record->logform;

            $details = $logform->isWalkIn()
                ? static::resolveStudentDetails('walk_in', $logform->walkInStudent?->id, null)
                : static::resolveStudentDetails('scheduled', null, $logform->appointment?->id);

            $component->state($details[$field] ?? '');
        };
    }

    /**
     * Student details for the View page (works for both walk-in and scheduled).
     */
    protected static function viewDetails(CounselingLogforms $record): array
    {
        return static::resolveStudentDetails(
            $record->type,
            $record->walkin_student_id,
            $record->counseling_appointments_id,
        );
    }

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
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                                if ($state !== 'walk_in') {
                                    $set('follow_up_required', false);
                                }

                                static::refillAnecdotals($get, $set);
                            })
                            ->native(false)
                            ->columnSpanFull(),

                        // Auto-assigns the currently active Term on creation so every
                        // logform is tagged with a School Year & Semester for filtering,
                        // without requiring the user to pick it manually.
                        Forms\Components\Hidden::make('term_id')
                            ->default(fn () => Term::active()->first()?->id)
                            ->dehydrated(true),
                    ]),

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
                            ->afterStateUpdated(fn (Get $get, Set $set) => static::refillAnecdotals($get, $set))
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
                            ->afterStateUpdated(fn (Get $get, Set $set) => static::refillAnecdotals($get, $set))
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('walkin_course_display')
                            ->label('Program / Year Level')
                            ->content(function (Get $get) {
                                $student = Students::with('program')->find($get('walkin_student_id'));

                                if (! $student) {
                                    return '—';
                                }

                                return trim(
                                    ($student->program?->name ?? '') .
                                    ' ' .
                                    ($student->year_level ?? '')
                                ) ?: '—';
                            })
                            ->visible(fn (Get $get) => (bool) $get('walkin_student_id')),

                        Forms\Components\Placeholder::make('walkin_contact_display')
                            ->label('Contact Number')
                            ->content(fn (Get $get) =>
                                Students::find($get('walkin_student_id'))?->contact_no ?? '—'
                            )
                            ->visible(fn (Get $get) => (bool) $get('walkin_student_id')),

                        Forms\Components\Placeholder::make('walkin_address_display')
                            ->label('Address')
                            ->content(fn (Get $get) =>
                                Students::find($get('walkin_student_id'))?->address ?? '—'
                            )
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

                        Forms\Components\Toggle::make('follow_up_required')
                            ->label('Follow-up Required')
                            ->helperText(
                                'Enable this when the student needs another counseling session.'
                            )
                            ->default(false)
                            ->inline(false)
                            ->visible(fn (Get $get): bool => $get('type') === 'walk_in')
                            ->dehydrated(true),
                    ]),

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
                                            ->maxLength(500)
                                            ->readOnly()
                                            ->dehydrated()
                                            ->default(static::anecdotalDefault('display_name'))
                                            ->afterStateHydrated(static::anecdotalHydrate('display_name'))
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('display_course')
                                            ->label('Course')
                                            ->maxLength(500)
                                            ->readOnly()
                                            ->dehydrated()
                                            ->default(static::anecdotalDefault('display_course'))
                                            ->afterStateHydrated(static::anecdotalHydrate('display_course')),

                                        Forms\Components\TextInput::make('display_contact')
                                            ->label('Contact')
                                            ->tel()
                                            ->maxLength(20)
                                            ->readOnly()
                                            ->dehydrated()
                                            ->default(static::anecdotalDefault('display_contact'))
                                            ->afterStateHydrated(static::anecdotalHydrate('display_contact')),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Textarea::make('display_address')
                                            ->label('Address')
                                            ->maxLength(500)
                                            ->rows(1)
                                            ->readOnly()
                                            ->dehydrated()
                                            ->default(static::anecdotalDefault('display_address'))
                                            ->afterStateHydrated(static::anecdotalHydrate('display_address')),

                                        Forms\Components\TextInput::make('area_concern')
                                            ->label('Area of Concern')
                                            ->placeholder('e.g., Academic, Social')
                                            ->maxLength(500),
                                    ]),

                                Forms\Components\RichEditor::make('concern')
                                    ->label('Observation')
                                    ->placeholder('Describe observation...')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                    ]),

                                Forms\Components\RichEditor::make('intervention')
                                    ->label('Intervention')
                                    ->placeholder('Action taken...')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                    ]),

                                Forms\Components\Select::make('personnel_id')
                                    ->label('Interviewed By')
                                    ->relationship(
                                        'personnel',
                                        'last_name',
                                        fn ($query) => $query->selectRaw(
                                            "*, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name"
                                        )
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn ($record) => trim(
                                            "{$record->first_name} " .
                                            "{$record->middle_name} " .
                                            "{$record->last_name}"
                                        )
                                    )
                                    ->searchable([
                                        'first_name',
                                        'middle_name',
                                        'last_name',
                                    ])
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select counselor/interviewer')
                                    ->columnSpanFull()
                                    ->native(false),
                            ])
                            ->collapsed()
                            ->itemLabel(
                                fn (array $state): ?string =>
                                    $state['display_name'] ?? 'New Record'
                            )
                            ->addActionLabel('+ Anecdotal')
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->cloneable()
                            ->collapsible()
                            ->deleteAction(
                                fn ($action) =>
                                    $action->requiresConfirmation()->size('sm')
                            )
                            ->addAction(
                                fn ($action) => $action->size('sm')
                            ),
                    ])
                    ->visible(
                        fn ($operation) =>
                            in_array($operation, ['edit', 'create'])
                    )
                    ->collapsible(),
            ]);
    }

    // ── View page (read-only, same look as Applicants) ─────────────────────
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            // ── Appointment Status ─────────────────────────────────
            Infolists\Components\Section::make('Appointment Details')
                ->description('Type of appointment and term')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        TextEntry::make('type')
                            ->label('Appointment Type')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'walk_in'   => 'Walk-in',
                                'scheduled' => 'Scheduled Appointment',
                                default     => $state ?? 'N/A',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'walk_in'   => 'warning',
                                'scheduled' => 'success',
                                default     => 'gray',
                            }),

                        TextEntry::make('term_label')
                            ->label('School Year & Semester')
                            ->icon('heroicon-o-calendar')
                            ->state(fn (CounselingLogforms $record): string =>
                                Term::find($record->term_id)?->label ?? 'N/A'
                            ),

                        Infolists\Components\IconEntry::make('follow_up_required')
                            ->label('Follow-up Required')
                            ->boolean()
                            ->visible(fn (CounselingLogforms $record): bool => $record->type === 'walk_in'),
                    ]),
                ]),

            // ── Student Information ────────────────────────────────
            Infolists\Components\Section::make('Student Information')
                ->description('Student details for this counseling session')
                ->icon('heroicon-o-user')
                ->collapsible()
                ->schema([
                    Infolists\Components\Grid::make(2)->schema([
                        TextEntry::make('student_name')
                            ->label('Name')
                            ->icon('heroicon-o-user')
                            ->size(TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->state(fn (CounselingLogforms $record): string =>
                                static::viewDetails($record)['display_name'] ?: 'N/A'
                            ),

                        TextEntry::make('student_course')
                            ->label('Course & Year')
                            ->icon('heroicon-o-academic-cap')
                            ->state(fn (CounselingLogforms $record): string =>
                                static::viewDetails($record)['display_course'] ?: 'N/A'
                            ),

                        TextEntry::make('student_contact')
                            ->label('Contact Number')
                            ->icon('heroicon-o-phone')
                            ->state(fn (CounselingLogforms $record): string =>
                                static::viewDetails($record)['display_contact'] ?: 'N/A'
                            ),

                        TextEntry::make('student_address')
                            ->label('Address')
                            ->icon('heroicon-o-home')
                            ->state(fn (CounselingLogforms $record): string =>
                                static::viewDetails($record)['display_address'] ?: 'N/A'
                            ),
                    ]),
                ]),

            // ── Counseling Details ─────────────────────────────────
            Infolists\Components\Section::make('Counseling Details')
                ->description('Support needed, concern and remarks')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->collapsible()
                ->schema([
                    TextEntry::make('support_needed_name')
                        ->label('Support Needed')
                        ->icon('heroicon-o-lifebuoy')
                        ->badge()
                        ->color('success')
                        ->state(fn (CounselingLogforms $record): string =>
                            \App\Models\SupportNeeded::find($record->support_needed_id)?->name ?? 'N/A'
                        ),

                    TextEntry::make('concern')
                        ->label('Concern')
                        ->placeholder('N/A')
                        ->columnSpanFull(),

                    TextEntry::make('remarks')
                        ->label('Remarks')
                        ->placeholder('N/A')
                        ->columnSpanFull(),
                ]),

            // ── Anecdotal Records ──────────────────────────────────
            Infolists\Components\Section::make('Anecdotal Records')
                ->description('Observations and interventions recorded')
                ->icon('heroicon-o-clipboard-document-list')
                ->collapsible()
                ->schema([
                    Infolists\Components\RepeatableEntry::make('anecdotals')
                        ->label('')
                        ->contained(true)
                        ->placeholder('No anecdotal records.')
                        ->schema([
                            Infolists\Components\Grid::make(3)->schema([
                                TextEntry::make('display_name')
                                    ->label('Name')
                                    ->icon('heroicon-o-user')
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('N/A'),
                                TextEntry::make('display_course')
                                    ->label('Course')
                                    ->icon('heroicon-o-academic-cap')
                                    ->placeholder('N/A'),
                                TextEntry::make('display_contact')
                                    ->label('Contact')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('N/A'),
                                TextEntry::make('display_address')
                                    ->label('Address')
                                    ->icon('heroicon-o-home')
                                    ->placeholder('N/A')
                                    ->columnSpan(2),
                                TextEntry::make('area_concern')
                                    ->label('Area of Concern')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('N/A'),
                            ]),

                            TextEntry::make('concern')
                                ->label('Observation')
                                ->html()
                                ->placeholder('N/A')
                                ->columnSpanFull(),

                            TextEntry::make('intervention')
                                ->label('Intervention')
                                ->html()
                                ->placeholder('N/A')
                                ->columnSpanFull(),

                            TextEntry::make('interviewed_by')
                                ->label('Interviewed By')
                                ->icon('heroicon-o-user-circle')
                                ->state(fn ($record): string => $record->personnel
                                    ? trim("{$record->personnel->first_name} {$record->personnel->middle_name} {$record->personnel->last_name}")
                                    : 'N/A'
                                ),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Student Name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('term_id')
                    ->label('School Year & Semester')
                    ->options(function () {
                        return Term::orderByDesc('is_active')
                            ->orderByDesc('id')
                            ->get()
                            ->mapWithKeys(fn ($term) => [
                                $term->id => $term->label . ($term->is_active ? ' (Active)' : ''),
                            ]);
                    })
                    ->searchable()
                    ->placeholder('All Terms'),
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
            'view'   => Pages\ViewCounselingLogforms::route('/{record}'),
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