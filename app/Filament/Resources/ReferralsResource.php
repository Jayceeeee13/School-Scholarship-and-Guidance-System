<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralsResource\Pages;
use App\Models\Referrals;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ReferralsResource extends Resource
{
    protected static ?string $model = Referrals::class;

    protected static ?string $navigationIcon = 'heroicon-s-arrow-right-circle';

    protected static ?string $navigationLabel = 'Referrals';

    protected static ?string $navigationGroup = 'Guidance Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Referral Details')
                    ->tabs([

                        // ── TAB 1: REFERRAL INFORMATION ───────────────────────────
                        Tabs\Tab::make('Referral Information')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Basic Information')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\DatePicker::make('date')
                                                    ->label('Referral Date')
                                                    ->required()
                                                    ->default(now())
                                                    ->maxDate(now())
                                                    ->native(false)
                                                    ->displayFormat('F d, Y')
                                                    ->closeOnDateSelection(),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('Student Name')
                                                    ->required()
                                                    ->maxLength(500)
                                                    ->placeholder('Full name')
                                                    ->live(),

                                                Forms\Components\TextInput::make('age')
                                                    ->label('Age')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(150)
                                                    ->live(),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('course_and_year')
                                                    ->label('Course & Year')
                                                    ->required()
                                                    ->maxLength(500)
                                                    ->placeholder('e.g., BS Psychology - 3rd Year')
                                                    ->live(),

                                                Forms\Components\TextInput::make('referred_by')
                                                    ->label('Referred By')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->placeholder('Teacher/Personnel name')
                                                    ->live(),
                                            ]),

                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'pending'  => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                            ])
                                            ->default('pending')
                                            ->required()
                                            ->native(false)
                                            ->visible(fn ($operation) => $operation === 'edit'),

                                        Forms\Components\Textarea::make('case_presented')
                                            ->label('Case Presented')
                                            ->required()
                                            ->rows(5)
                                            ->placeholder('Describe the case or concern being referred...')
                                            ->columnSpanFull()
                                            ->live(),
                                    ])
                                    ->icon('heroicon-o-information-circle')
                                    ->compact(),
                            ]),

                        // ── TAB 2: RECORDS ────────────────────────────────────────
                        Tabs\Tab::make('Records')
                            ->icon('heroicon-o-document-text')
                            ->badge(fn ($get) => count($get('logforms') ?? []))
                            ->badgeColor('success')
                            ->schema([
                                Forms\Components\Repeater::make('logforms')
                                    ->relationship('logforms')
                                    ->label('Session Logforms')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
    ->schema([
        Forms\Components\TextInput::make('name')
            ->label('Full Name')
            ->required()
            ->maxLength(500)
            ->disabled()
            ->dehydrated()
            ->default(fn (Get $get) => $get('../../name'))
            ->afterStateHydrated(function ($component, $state, $record) {
                if (!empty($state)) return;
                if ($record && $record->referral) {
                    $component->state($record->referral->name);
                }
            })
            ->columnSpan(2),
    ]),

Forms\Components\Grid::make(3)
    ->schema([
        Forms\Components\TextInput::make('course_and_year')
            ->label('Course & Year')
            ->maxLength(500)
            ->disabled()
            ->dehydrated()
            ->default(fn (Get $get) => $get('../../course_and_year'))
            ->afterStateHydrated(function ($component, $state, $record) {
                if (!empty($state)) return;
                if ($record && $record->referral) {
                    $component->state($record->referral->course_and_year);
                }
            }),
                                                Forms\Components\TextInput::make('contact_no')
                                                    ->label('Contact')
                                                    ->tel()
                                                    ->maxLength(20),

                                                Forms\Components\Textarea::make('address')
                                                    ->label('Address')
                                                    ->maxLength(500)
                                                    ->rows(1),
                                            ]),

                                        Forms\Components\Textarea::make('concern')
                                            ->label('Session Notes')
                                            ->placeholder('Document session concerns...')
                                            ->maxLength(500)
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('remarks')
                                            ->label('Remarks')
                                            ->placeholder('Optional'),

                                        Forms\Components\Repeater::make('anecdotals')
                                            ->relationship('anecdotals')
                                            ->label('Anecdotal Records')
                                            ->schema([
                                                Forms\Components\Grid::make(4)
    ->schema([
        Forms\Components\TextInput::make('name')
            ->label('Name')
            ->required()
            ->maxLength(500)
            ->disabled()
            ->dehydrated()
            ->default(fn (Get $get) => $get('../../../../name'))
            ->afterStateHydrated(function ($component, $state, $record) {
                if (!empty($state)) return;
                if ($record && $record->logform && $record->logform->referral) {
                    $component->state($record->logform->referral->name);
                }
            })
            ->columnSpan(2),

        Forms\Components\TextInput::make('course_and_year')
            ->label('Course')
            ->maxLength(500)
            ->disabled()
            ->dehydrated()
            ->default(fn (Get $get) => $get('../../../../course_and_year'))
            ->afterStateHydrated(function ($component, $state, $record) {
                if (!empty($state)) return;
                if ($record && $record->logform && $record->logform->referral) {
                    $component->state($record->logform->referral->course_and_year);
                }
            }),

                                                        Forms\Components\TextInput::make('contact_no')
                                                            ->label('Contact')
                                                            ->tel()
                                                            ->maxLength(20),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Textarea::make('address')
                                                            ->label('Address')
                                                            ->maxLength(500)
                                                            ->rows(1),

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
                                                    ->relationship(
                                                        'personnel',
                                                        'last_name',
                                                        fn ($query) => $query->selectRaw("*, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name")
                                                    )
                                                    ->getOptionLabelFromRecordUsing(fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->last_name}"))
                                                    ->searchable(['first_name', 'middle_name', 'last_name'])
                                                    ->preload()
                                                    ->placeholder('Select counselor/interviewer')
                                                    ->columnSpanFull()
                                                    ->native(false),
                                            ])
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Record')
                                            ->addActionLabel('+ Anecdotal')
                                            ->defaultItems(0)
                                            ->columnSpanFull()
                                            ->cloneable()
                                            ->collapsible()
                                            ->deleteAction(fn ($action) => $action->requiresConfirmation()->size('sm'))
                                            ->addAction(fn ($action) => $action->size('sm')),
                                    ])
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Session')
                                    ->addActionLabel('+ Add Session')
                                    ->defaultItems(0)
                                    ->columnSpanFull()
                                    ->cloneable()
                                    ->collapsible()
                                    ->deleteAction(fn ($action) => $action
                                        ->requiresConfirmation()
                                        ->modalHeading('Delete Session?')
                                        ->modalDescription('This will delete the session and all associated anecdotal records.'))
                                    ->reorderableWithButtons()
                                    ->orderColumn('id'),
                            ])
                            ->visible(fn ($operation) => in_array($operation, ['edit', 'create'])),

                        // ── TAB 3: ENDORSEMENT ────────────────────────────────────
                        Tabs\Tab::make('Endorsement')
                            ->icon('heroicon-o-paper-airplane')
                            ->visible(fn ($operation) => in_array($operation, ['edit', 'create']))
                            ->schema([
                                Forms\Components\Section::make('Endorsement')
                                    ->description('Choose whether to endorse this referral.')
                                    ->icon('heroicon-o-document-arrow-up')
                                    ->schema([

                                        Forms\Components\Toggle::make('has_endorsement')
                                            ->label('Endorse this referral?')
                                            ->helperText('Enable to fill in and save an endorsement slip for this referral.')
                                            ->live()
                                            ->default(false)
                                            ->afterStateHydrated(function (callable $set, $record) {
                                                if ($record instanceof \App\Models\Referrals) {
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
                                                                if ($record instanceof \App\Models\Referrals) {
                                                                    return $record->name;
                                                                }
                                                                return $get('../name');
                                                            }),

                                                        Forms\Components\Placeholder::make('student_course')
                                                            ->label('Course/Year')
                                                            ->content(function (Get $get, $record) {
                                                                if ($record instanceof \App\Models\Referrals) {
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

                        // ── TAB 4: INVITATION ─────────────────────────────────────
                        Tabs\Tab::make('Invitation')
                            ->icon('heroicon-o-envelope')
                            ->visible(fn ($operation) => in_array($operation, ['edit', 'create']))
                            ->schema([
                                Forms\Components\Section::make('Counseling Session Invitation')
                                    ->description('Invite this student for a counseling session. The invitation will appear on their portal dashboard.')
                                    ->icon('heroicon-o-calendar-days')
                                    ->schema([

                                        Forms\Components\Toggle::make('has_invitation')
                                            ->label('Send invitation to student?')
                                            ->helperText('Enable to create a counseling session invitation visible on the student portal.')
                                            ->live()
                                            ->default(false)
                                            ->afterStateHydrated(function (callable $set, $record) {
                                                if ($record instanceof \App\Models\Referrals) {
                                                    $set('has_invitation', $record->invitation()->exists());
                                                }
                                            }),

                                        Forms\Components\Fieldset::make('Invitation Details')
                                            ->relationship('invitation')
                                            ->visible(fn (callable $get) => (bool) $get('has_invitation'))
                                            ->schema([

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Placeholder::make('student_name')
                                                            ->label('Student Name')
                                                            ->content(function (Get $get, $record) {
                                                                if ($record instanceof \App\Models\Referrals) {
                                                                    return $record->name;
                                                                }
                                                                return $get('../name');
                                                            }),

                                                        Forms\Components\Placeholder::make('student_course')
                                                            ->label('Course/Year')
                                                            ->content(function (Get $get, $record) {
                                                                if ($record instanceof \App\Models\Referrals) {
                                                                    return $record->course_and_year;
                                                                }
                                                                return $get('../course_and_year');
                                                            }),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('session_date')
    ->label('Preferred Session Date')
    ->required()
    ->native(false)
    ->displayFormat('F d, Y')
    ->minDate(now())
    ->closeOnDateSelection()
    ->live()
    ->helperText('Select the date for the counseling session')
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
                                                            ->options(fn () => \App\Models\CounselingTimeSlot::where('is_active', true)
                                                                ->orderBy('name')
                                                                ->pluck('name', 'id')
                                                                ->toArray())
                                                            ->required()
                                                            ->searchable()
                                                            ->preload()
                                                            ->native(false)
                                                            ->placeholder('Select time slot'),
                                                    ]),

                                                Forms\Components\Textarea::make('purpose')
                                                    ->label('Purpose / Reason for Session')
                                                    ->placeholder('Describe the purpose of this counseling session...')
                                                    ->rows(4)
                                                    ->required()
                                                    ->maxLength(500)
                                                    ->columnSpanFull(),

                                                Forms\Components\Select::make('personnel_id')
                                                    ->label('Counselor')
                                                    ->relationship('personnel', 'last_name',
                                                        fn ($query) => $query->selectRaw("*, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as full_name")
                                                    )
                                                    ->getOptionLabelFromRecordUsing(
                                                        fn ($record) => trim("{$record->first_name} {$record->middle_name} {$record->last_name}")
                                                    )
                                                    ->searchable(['first_name', 'middle_name', 'last_name'])
                                                    ->preload()
                                                    ->placeholder('Select counselor')
                                                    ->native(false)
                                                    ->columnSpanFull(),

                                                Forms\Components\Select::make('status')
                                                    ->label('Invitation Status')
                                                    ->options([
                                                        'pending'  => 'Pending',
                                                        'accepted' => 'Accepted',
                                                        'declined' => 'Declined',
                                                    ])
                                                    ->default('pending')
                                                    ->native(false)
                                                    ->columnSpanFull(),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course_and_year')
                    ->label('Course & Year')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('referred_by')
                    ->label('Referred By')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('logforms_count')
                    ->label('Logforms')
                    ->counts('logforms')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('invitation_exists')
                    ->label('Invited')
                    ->getStateUsing(fn ($record) => $record->invitation()->exists())
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('endorsement.to_where')
                    ->label('Endorsed To')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('endorsement.date')
                    ->label('Endorse Date')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('M d, Y') : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('From Date')
                            ->native(false),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Until Date')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'],
                                fn ($q) => $q->whereDate('date', '>=', $data['date_from']))
                            ->when($data['date_until'],
                                fn ($q) => $q->whereDate('date', '<=', $data['date_until']));
                    }),

                Tables\Filters\SelectFilter::make('referred_by')
                    ->label('Referred By')
                    ->options(fn () => \App\Models\Referrals::query()
                        ->distinct()
                        ->orderBy('referred_by')
                        ->pluck('referred_by', 'referred_by')
                        ->toArray()),

                Tables\Filters\TernaryFilter::make('has_invitation')
                    ->label('Has Invitation')
                    ->queries(
                        true:  fn ($query) => $query->whereHas('invitation'),
                        false: fn ($query) => $query->whereDoesntHave('invitation'),
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\EditAction::make()
                        ->url(fn ($record) => ReferralsResource::getUrl('edit', [
                            'record' => $record->id,
                            'tab'    => $record->endorsement()->exists() ? '-endorsement-tab' : '-referral-information-tab',
                        ])),

                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Referral')
                        ->modalDescription('Are you sure you want to approve this referral?')
                        ->modalSubmitActionLabel('Yes, Approve')
                        ->visible(fn (Referrals $record): bool => $record->status !== 'approved')
                        ->action(function (Referrals $record): void {
                            $record->update(['status' => 'approved']);
                            Notification::make()
                                ->title('Referral Approved')
                                ->body("The referral for {$record->name} has been approved.")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Referral')
                        ->modalDescription('Are you sure you want to reject this referral?')
                        ->modalSubmitActionLabel('Yes, Reject')
                        ->visible(fn (Referrals $record): bool => $record->status !== 'rejected')
                        ->action(function (Referrals $record): void {
                            $record->update(['status' => 'rejected']);
                            Notification::make()
                                ->title('Referral Rejected')
                                ->body("The referral for {$record->name} has been rejected.")
                                ->danger()
                                ->send();
                        }),

                    Tables\Actions\Action::make('print_endorsement')
                        ->label('Print Endorsement')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->visible(fn (Referrals $record): bool => $record->endorsement()->exists())
                        ->url(fn ($record) => route('referral.endorsement.print', $record->id))
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReferrals::route('/'),
            'create' => Pages\CreateReferrals::route('/create'),
            'view'   => Pages\ViewReferrals::route('/{record}'),
            'edit'   => Pages\EditReferrals::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'guidance']);
    }
}