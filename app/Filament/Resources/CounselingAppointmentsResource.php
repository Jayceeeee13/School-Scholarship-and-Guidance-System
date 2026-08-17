<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CounselingAppointmentsResource\Pages;
use App\Models\CounselingAppointments;
use App\Models\Endorsement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Table;

class CounselingAppointmentsResource extends Resource
{
    protected static ?string $model = CounselingAppointments::class;

    protected static ?string $navigationIcon  = 'heroicon-s-calendar-days';
    protected static ?string $navigationLabel = 'Counseling Appointments';
    protected static ?string $navigationGroup = 'Guidance Management';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Appointment Details')
                    ->tabs([

                        // ── TAB 1: PERSONAL INFORMATION ──────────────────────────
                        Tabs\Tab::make('Personal Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Section::make('Basic Information')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->live()
                                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                                    ->validationMessages(['regex' => 'First name can only contain letters, spaces, hyphens, and periods.'])
                                                    ->extraInputAttributes(['style' => 'text-transform: capitalize;', 'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)"])
                                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                                                TextInput::make('middle_name')
                                                    ->label('Middle Name')
                                                    ->maxLength(100)
                                                    ->live()
                                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                                    ->validationMessages(['regex' => 'Middle name can only contain letters, spaces, hyphens, and periods.'])
                                                    ->extraInputAttributes(['style' => 'text-transform: capitalize;', 'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)"])
                                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                                                TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->live()
                                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                                    ->validationMessages(['regex' => 'Last name can only contain letters, spaces, hyphens, and periods.'])
                                                    ->extraInputAttributes(['style' => 'text-transform: capitalize;', 'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)"])
                                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('course_and_year')
                                                    ->label('Course & Year')
                                                    ->placeholder('e.g., BS Psychology - 3rd Year')
                                                    ->required()
                                                    ->maxLength(500)
                                                    ->live(),

                                                TextInput::make('contact_no')
                                                    ->label('Contact Number')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(11)
                                                    ->minLength(11)
                                                    ->rule('regex:/^[0-9]+$/')
                                                    ->validationMessages(['regex' => 'Contact number can only contain digits.', 'min' => 'Contact number must be exactly 11 digits.', 'max' => 'Contact number must be exactly 11 digits.'])
                                                    ->extraInputAttributes(['onkeypress' => "return /^[0-9]$/.test(event.key)", 'maxlength' => '11'])
                                                    ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : $state),
                                            ]),

                                        Forms\Components\Textarea::make('present_address')
                                            ->label('Present Address')
                                            ->placeholder('Enter current address')
                                            ->maxLength(200)
                                            ->rows(2)
                                            ->live()
                                            ->columnSpanFull()
                                            ->extraInputAttributes(['style' => 'text-transform: capitalize;']),
                                    ])
                                    ->icon('heroicon-o-identification')
                                    ->compact(),
                            ]),

                        // ── TAB 2: APPOINTMENT DETAILS ────────────────────────────
                        Tabs\Tab::make('Appointment Details')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Forms\Components\Section::make('Schedule & Purpose')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('counseling_date')
                                                    ->label('Preferred Date')
                                                    ->required()
                                                    ->native(false)
                                                    ->displayFormat('F d, Y')
                                                    ->helperText('Select your preferred counseling date')
                                                    ->minDate(now())
                                                    ->closeOnDateSelection()
                                                    ->live()
                                                    ->disabledDates(fn () => \App\Models\InactiveDate::getInactiveDates())
                                                    ->rules([
                                                        function () {
                                                            return function (string $attribute, $value, $fail) {
                                                                if ($value && \App\Models\InactiveDate::isInactive($value)) {
                                                                    $fail('This date is unavailable for scheduling. Please choose another date.');
                                                                }
                                                            };
                                                        },
                                                    ])
                                                    ->afterStateUpdated(fn (callable $set) => $set('time_slot_id', null)),

                                                Forms\Components\Select::make('time_slot_id')
                                                    ->label('Time Slot')
                                                    ->options(function (callable $get) {
                                                        $selectedDate    = $get('counseling_date');
                                                        $currentRecordId = $get('id');
                                                        $timeSlots       = \App\Models\CounselingTimeSlot::where('is_active', true)->orderBy('name')->get();

                                                        if (!$selectedDate) {
                                                            return $timeSlots->mapWithKeys(fn ($s) => [$s->id => $s->name . ' ✅ Available'])->toArray();
                                                        }

                                                        $reserved = \App\Models\CounselingAppointments::query()
                                                            ->whereDate('counseling_date', $selectedDate)
                                                            ->when($currentRecordId, fn ($q) => $q->where('id', '!=', $currentRecordId))
                                                            ->pluck('time_slot_id')->toArray();

                                                        return $timeSlots->mapWithKeys(fn ($s) => [
                                                            $s->id => $s->name . (in_array($s->id, $reserved) ? ' 🔴 Reserved' : ' ✅ Available'),
                                                        ])->toArray();
                                                    })
                                                    ->disableOptionWhen(function ($value, callable $get) {
                                                        $selectedDate = $get('counseling_date');
                                                        if (!$selectedDate) return false;
                                                        return \App\Models\CounselingAppointments::query()
                                                            ->whereDate('counseling_date', $selectedDate)
                                                            ->where('time_slot_id', $value)
                                                            ->when($get('id'), fn ($q) => $q->where('id', '!=', $get('id')))
                                                            ->exists();
                                                    })
                                                    ->required()->searchable()->preload()->live()
                                                    ->placeholder(fn (callable $get) => $get('counseling_date') ? 'Choose an available time slot' : 'Select a date first')
                                                    ->helperText(function (callable $get) {
                                                        $selectedDate = $get('counseling_date');
                                                        if (!$selectedDate) return 'Please select a date first to see time slot availability';
                                                        $total     = \App\Models\CounselingTimeSlot::where('is_active', true)->count();
                                                        $reserved  = \App\Models\CounselingAppointments::query()
                                                            ->whereDate('counseling_date', $selectedDate)
                                                            ->when($get('id'), fn ($q) => $q->where('id', '!=', $get('id')))
                                                            ->distinct('time_slot_id')->count('time_slot_id');
                                                        $available = $total - $reserved;
                                                        return $available === 0
                                                            ? '🔴 All time slots are reserved for this date. Please choose another date.'
                                                            : "✅ {$available} of {$total} slots available | 🔴 Reserved slots are disabled";
                                                    })
                                                    ->disabled(fn (callable $get) => !$get('counseling_date'))
                                                    ->dehydrated()
                                                    ->native(false),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('mode_of_counseling_id')
                                                    ->label('Mode of Counseling')
                                                    ->options(\App\Models\ModeOfCounseling::active()->pluck('name', 'id'))
                                                    ->searchable()->required()->placeholder('Select mode'),

                                                Forms\Components\Select::make('support_needed_id')
                                                    ->label('Support Needed')
                                                    ->options(\App\Models\SupportNeeded::active()->pluck('name', 'id'))
                                                    ->searchable()->required()->placeholder('Select support type'),
                                            ]),

                                        Forms\Components\Textarea::make('concern')
                                            ->label('Concern/Issue (Optional)')
                                            ->placeholder('Describe what you would like to discuss...')
                                            ->maxLength(500)->rows(3)->columnSpanFull(),
                                    ])
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->compact(),
                            ]),

                        // ── TAB 3: COUNSELING RECORDS ─────────────────────────────
                        Tabs\Tab::make('Records')
                            ->icon('heroicon-o-document-text')
                            ->badge(fn ($get) => count($get('logforms') ?? []))
                            ->badgeColor('success')
                            ->schema([
                                Forms\Components\Repeater::make('logforms')
                                    ->relationship('logforms')
                                    ->label('Session Logforms')
                                    ->schema([
                                        Forms\Components\Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('display_name')->label('Full Name')->required()->maxLength(500)->disabled()
                                                ->default(function (Get $get) {
                                                    return trim($get('../../first_name') . ' ' . $get('../../middle_name') . ' ' . $get('../../last_name'));
                                                })
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if (!empty($state)) return;
                                                    if ($record && $record->appointment) {
                                                        $app = $record->appointment;
                                                        $component->state(trim("{$app->first_name} {$app->middle_name} {$app->last_name}"));
                                                    }
                                                })->columnSpan(2),
                                        ]),

                                        Forms\Components\Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('display_course')->label('Course & Year')->maxLength(500)->disabled()
                                                ->default(fn (Get $get) => $get('../../course_and_year'))
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if (!empty($state)) return;
                                                    if ($record && $record->appointment) {
                                                        $component->state($record->appointment->course_and_year);
                                                    }
                                                }),
                                            Forms\Components\TextInput::make('display_contact')->label('Contact')->tel()->maxLength(20)->disabled()
                                                ->default(fn (Get $get) => $get('../../contact_no'))
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if (!empty($state)) return;
                                                    if ($record && $record->appointment) {
                                                        $component->state($record->appointment->contact_no);
                                                    }
                                                }),
                                            Forms\Components\Textarea::make('display_address')->label('Address')->maxLength(500)->rows(1)->disabled()
                                                ->default(fn (Get $get) => $get('../../present_address'))
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if (!empty($state)) return;
                                                    if ($record && $record->appointment) {
                                                        $component->state($record->appointment->present_address);
                                                    }
                                                }),
                                        ]),

                                        Forms\Components\Hidden::make('support_needed_id')
                                            ->default(fn (Get $get) => $get('../../support_needed_id'))
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if (!empty($state)) return;
                                                if ($record && $record->appointment) {
                                                    $component->state($record->appointment->support_needed_id);
                                                }
                                            })
                                            ->dehydrated(),

                                        Forms\Components\Textarea::make('concern')->label('Concern')->placeholder('Document session concerns...')->maxLength(500)->rows(2)->columnSpanFull(),
                                        Forms\Components\TextInput::make('remarks')->label('Remarks')->placeholder('Optional'),

                                        Forms\Components\Repeater::make('anecdotals')
                                            ->relationship('anecdotals')->label('Anecdotal Records')
                                            ->schema([
                                                Forms\Components\Grid::make(4)->schema([
                                                    Forms\Components\TextInput::make('display_name')->label('Name')->required()->maxLength(500)->disabled()
                                                        ->default(function (Get $get) {
                                                            return trim($get('../../../../first_name') . ' ' . $get('../../../../middle_name') . ' ' . $get('../../../../last_name'));
                                                        })
                                                        ->afterStateHydrated(function ($component, $state, $record) {
                                                            if (!empty($state)) return;
                                                            if ($record && $record->logform && $record->logform->appointment) {
                                                                $app = $record->logform->appointment;
                                                                $component->state(trim("{$app->first_name} {$app->middle_name} {$app->last_name}"));
                                                            }
                                                        })->columnSpan(2),
                                                    Forms\Components\TextInput::make('display_course')->label('Course')->maxLength(500)->disabled()
                                                        ->default(fn (Get $get) => $get('../../../../course_and_year'))
                                                        ->afterStateHydrated(function ($component, $state, $record) {
                                                            if (!empty($state)) return;
                                                            if ($record && $record->logform && $record->logform->appointment) {
                                                                $component->state($record->logform->appointment->course_and_year);
                                                            }
                                                        }),
                                                    Forms\Components\TextInput::make('display_contact')->label('Contact')->tel()->maxLength(20)->disabled()
                                                        ->default(fn (Get $get) => $get('../../../../contact_no'))
                                                        ->afterStateHydrated(function ($component, $state, $record) {
                                                            if (!empty($state)) return;
                                                            if ($record && $record->logform && $record->logform->appointment) {
                                                                $component->state($record->logform->appointment->contact_no);
                                                            }
                                                        }),
                                                ]),
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\Textarea::make('display_address')->label('Address')->maxLength(500)->rows(1)->disabled()
                                                        ->default(fn (Get $get) => $get('../../../../present_address'))
                                                        ->afterStateHydrated(function ($component, $state, $record) {
                                                            if (!empty($state)) return;
                                                            if ($record && $record->logform && $record->logform->appointment) {
                                                                $component->state($record->logform->appointment->present_address);
                                                            }
                                                        }),
                                                    Forms\Components\TextInput::make('area_concern')->label('Area of Concern')->placeholder('e.g., Academic, Social')->maxLength(500)->required(),
                                                ]),
                                                Forms\Components\RichEditor::make('concern')->label('Observation')->placeholder('Describe observation...')->columnSpanFull()->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])->disableToolbarButtons(['attachFiles']),
                                                Forms\Components\RichEditor::make('intervention')->label('Intervention')->placeholder('Action taken...')->columnSpanFull()->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])->disableToolbarButtons(['attachFiles']),
                                                Forms\Components\Select::make('personnel_id')->label('Interviewed By')
                                                    ->relationship('personnel', 'last_name', fn ($query) => $query->selectRaw("*, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name"))
                                                    ->getOptionLabelFromRecordUsing(fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->last_name}"))
                                                    ->searchable(['first_name', 'middle_name', 'last_name'])->preload()->required()->placeholder('Select counselor/interviewer')->columnSpanFull()->native(false),
                                            ])
                                            ->collapsed()->itemLabel(fn (array $state): ?string => $state['display_name'] ?? 'New Record')
                                            ->addActionLabel('+ Anecdotal')->defaultItems(0)->columnSpanFull()->cloneable()->collapsible()
                                            ->deleteAction(fn ($action) => $action->requiresConfirmation()->size('sm'))
                                            ->addAction(fn ($action) => $action->size('sm')),
                                    ])
                                    ->collapsed()->itemLabel(fn (array $state): ?string => $state['display_name'] ?? 'New Session')
                                    ->addActionLabel('+ Add Session')->defaultItems(0)->columnSpanFull()->cloneable()->collapsible()
                                    ->deleteAction(fn ($action) => $action->requiresConfirmation()->modalHeading('Delete Session?')->modalDescription('This will delete the session and all associated anecdotal records.'))
                                    ->reorderableWithButtons()->orderColumn('id'),
                            ])
                            ->visible(fn ($operation) => in_array($operation, ['edit', 'create'])),

                        // ── TAB 4: ENDORSEMENT ────────────────────────────────────
                        Tabs\Tab::make('Endorsement')
                            ->icon('heroicon-o-paper-airplane')
                            ->visible(fn ($operation) => in_array($operation, ['edit', 'create']))
                            ->schema([
                                Forms\Components\Section::make('Endorsement')
                                    ->description('Choose whether to endorse this appointment.')
                                    ->icon('heroicon-o-document-arrow-up')
                                    ->schema([

                                        Forms\Components\Toggle::make('has_endorsement')
                                            ->label('Endorse this appointment?')
                                            ->helperText('Enable to fill in and save an endorsement slip for this appointment.')
                                            ->live()
                                            ->default(false)
                                            ->afterStateHydrated(function (callable $set, $record) {
                                                if ($record instanceof \App\Models\CounselingAppointments) {
                                                    $set('has_endorsement', $record->endorsement()->exists());
                                                }
                                            }),

                                        Forms\Components\Fieldset::make('Endorsement Form')
                                            ->relationship('endorsement')
                                            ->visible(fn (callable $get) => (bool) $get('has_endorsement'))
                                            ->schema([

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Placeholder::make('')->content('')->columnSpan(2),
                                                        Forms\Components\DatePicker::make('date')
                                                            ->label('Date')
                                                            ->native(false)
                                                            ->displayFormat('F d, Y')
                                                            ->default(now()),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('to_where')
                                                            ->label('To')
                                                            ->placeholder('Recipient name / office')
                                                            ->maxLength(200),

                                                        Forms\Components\TextInput::make('from_where')
                                                            ->label('From')
                                                            ->placeholder('Sender name / office')
                                                            ->maxLength(200),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Placeholder::make('student_name')
                                                            ->label('Name')
                                                            ->content(function (Get $get, $record) {
                                                                if ($record instanceof \App\Models\CounselingAppointments) {
                                                                    return trim("{$record->first_name} {$record->middle_name} {$record->last_name}");
                                                                }
                                                                return trim($get('../first_name') . ' ' . $get('../middle_name') . ' ' . $get('../last_name'));
                                                            }),

                                                        Forms\Components\Placeholder::make('student_course')
                                                            ->label('Course/Year')
                                                            ->content(function (Get $get, $record) {
                                                                if ($record instanceof \App\Models\CounselingAppointments) {
                                                                    return $record->course_and_year;
                                                                }
                                                                return $get('../course_and_year');
                                                            }),
                                                    ]),

                                                Forms\Components\Textarea::make('issue')
                                                    ->label('Issue')
                                                    ->placeholder('Describe the issue or reason for endorsement...')
                                                    ->rows(4)
                                                    ->columnSpanFull(),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('personnel_id')
                                                            ->label('Endorse By')
                                                            ->relationship('personnel', 'last_name',
                                                                fn ($query) => $query->selectRaw("*, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name")
                                                            )
                                                            ->getOptionLabelFromRecordUsing(
                                                                fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->last_name}")
                                                            )
                                                            ->searchable(['first_name', 'middle_name', 'last_name'])
                                                            ->preload()
                                                            ->placeholder('Select counselor/interviewer')
                                                            ->native(false),

                                                        Forms\Components\TextInput::make('received_by')
                                                            ->label('Received by')
                                                            ->placeholder('Name of receiver')
                                                            ->maxLength(200),
                                                    ]),

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Placeholder::make('')->content('')->columnSpan(2),
                                                        Forms\Components\DatePicker::make('receive_date')
                                                            ->label('Date Received')
                                                            ->native(false)
                                                            ->displayFormat('F d, Y'),
                                                    ]),
                                            ])
                                            ->columns(1),

                                    ])
                                    ->compact()
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->contained(false),
            ])
            ->columns(1);
    }

    /**
     * NOTE: This method is required by Filament's Resource base class,
     * but is NOT what actually renders the index page's table.
     * Pages\ListCounselingAppointments::table() overrides it with the
     * real tabbed (Appointments / Follow-ups / Endorsements) table —
     * see that class for the live implementation, filters, and actions.
     * Kept minimal here to avoid the two definitions drifting apart.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student Name')
                    ->getStateUsing(fn ($record) => $record->full_name),

                Tables\Columns\TextColumn::make('counseling_date')
                    ->label('Date')
                    ->date('M d, Y'),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCounselingAppointments::route('/'),
            'create' => Pages\CreateCounselingAppointments::route('/create'),
            'edit'   => Pages\EditCounselingAppointments::route('/{record}/edit'),
            'view'   => Pages\ViewCounselingAppointments::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool   { return auth()->user()->hasAnyRole(['Admin', 'Guidance']); }
    public static function canCreate(): bool    { return auth()->user()->hasAnyRole(['Admin', 'Guidance']); }
    public static function canEdit($record): bool   { return auth()->user()->hasAnyRole(['Admin', 'Guidance']); }
    public static function canDelete($record): bool { return auth()->user()->hasAnyRole(['Admin', 'Guidance']); }
    public static function shouldRegisterNavigation(): bool { return auth()->user()->hasAnyRole(['Admin', 'Guidance']); }
}