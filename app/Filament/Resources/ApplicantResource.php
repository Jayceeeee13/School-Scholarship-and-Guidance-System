<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Filament\Resources\ApplicantResource\RelationManagers;
use App\Models\Applicant;
use App\Traits\LogsCustomActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicantResource extends Resource
{
    use LogsCustomActivity;

    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'Scholarship Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Application Information')
                    ->schema([
                        Forms\Components\Select::make('type_of_application_id')
                            ->label('Type of Application')
                            ->options(\App\Models\TypeOfApplication::active()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(1), 
                       Forms\Components\FileUpload::make('picture')
                            ->label('2x2 Picture (Optional)')
                            ->image()
                            ->disk('public')
                            ->directory('pictures')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg','image/png','image/jpg'])
                            ->maxSize(2048)
                            ->nullable(),
                            ])
                            ->columns(2)
                            ->collapsible(),
                Section::make('Personal Information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                    ->validationMessages([
                                        'regex' => 'First name can only contain letters, spaces, hyphens, and periods.',
                                    ])
                                    ->extraInputAttributes([
                                        'style'      => 'text-transform: capitalize;',
                                        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),
                                TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(100)
                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                    ->validationMessages([
                                        'regex' => 'Middle name can only contain letters, spaces, hyphens, and periods.',
                                    ])
                                    ->extraInputAttributes([
                                        'style'      => 'text-transform: capitalize;',
                                        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(100)
                                    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
                                    ->validationMessages([
                                        'regex' => 'Last name can only contain letters, spaces, hyphens, and periods.',
                                    ])
                                    ->extraInputAttributes([
                                        'style'      => 'text-transform: capitalize;',
                                        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),

                                TextInput::make('extension_name')
                                    ->label('Extension Name')
                                    ->placeholder('Jr., Sr., III, etc.')
                                    ->maxLength(200)
                                    ->extraInputAttributes([
                                        'style'      => 'text-transform: capitalize;',
                                        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),
                                                            ]),                        
                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('program_id')
                                    ->label('Program')
                                    ->options(\App\Models\Program::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Program Name')
                                            ->required()
                                            ->maxLength(200)
                                            ->placeholder('e.g., BSIT, BSCS, BSBA'),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $program = \App\Models\Program::create($data);
                                        return $program->id;
                                    }),
                                
                                Forms\Components\Select::make('year_level')
                                    ->label('Year Level')
                                    ->options([
                                        '1' => '1st Year',
                                        '2' => '2nd Year',
                                        '3' => '3rd Year',
                                        '4' => '4th Year',
                                        '5' => '5th Year',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->searchable(),
                                
                                Forms\Components\Select::make('gender_id')
                                    ->label('Gender')
                                    ->options(\App\Models\Gender::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                            ]),
                        
                        Grid::make(3)
                            ->schema([
                                TextInput::make('contact_no')
    ->label('Contact Number')
    ->tel()
    ->required()
    ->maxLength(11)
    ->minLength(11)
    ->rule('regex:/^[0-9]+$/')
    ->validationMessages([
        'regex'     => 'Contact number can only contain digits.',
        'min'       => 'Contact number must be exactly 11 digits.',
        'max'       => 'Contact number must be exactly 11 digits.',
    ])
    ->extraInputAttributes([
        'onkeypress' => "return /^[0-9]$/.test(event.key)",
        'maxlength'  => '11',
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : $state),
                                
                                DatePicker::make('birthdate')
    ->label('Date of Birth')
    ->required()
    ->native(false)
    ->displayFormat('M d, Y')
    ->maxDate(now())
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        if ($state) {
            $age = \Carbon\Carbon::parse($state)->age;
            $set('age', $age);
        } else {
            $set('age', null);
        }
    }),

TextInput::make('age')
    ->label('Age')
    ->numeric()
    ->readOnly()
    ->suffix('years old')
    ->dehydrated(true),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('religion')
                                    ->label('Religion')
                                    ->required()
                                    ->maxLength(100),
                                
                                TextInput::make('facebook_account')
                                    ->label('Facebook Account')
                                    ->required()
                                    ->maxLength(100),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Family Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('fathers_name')
    ->label('Fathers Name')
    ->required()
    ->maxLength(100)
    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
    ->validationMessages([
        'regex' => 'Last name can only contain letters, spaces, hyphens, and periods.',
    ])
    ->extraInputAttributes([
        'style'      => 'text-transform: capitalize;',
        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),
                                
                                TextInput::make('fathers_contact_no')
    ->label('Fathers Contact Number')
    ->tel()
    ->required()
    ->maxLength(11)
    ->minLength(11)
    ->rule('regex:/^[0-9]+$/')
    ->validationMessages([
        'regex'     => 'Contact number can only contain digits.',
        'min'       => 'Contact number must be exactly 11 digits.',
        'max'       => 'Contact number must be exactly 11 digits.',
    ])
    ->extraInputAttributes([
        'onkeypress' => "return /^[0-9]$/.test(event.key)",
        'maxlength'  => '11',
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : $state),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('mothers_name')
    ->label('Mothers Name')
    ->required()
    ->maxLength(100)
    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
    ->validationMessages([
        'regex' => 'Last name can only contain letters, spaces, hyphens, and periods.',
    ])
    ->extraInputAttributes([
        'style'      => 'text-transform: capitalize;',
        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),
                                
                                TextInput::make('mothers_contact_no')
    ->label('Mothers Contact Number')
    ->tel()
    ->required()
    ->maxLength(11)
    ->minLength(11)
    ->rule('regex:/^[0-9]+$/')
    ->validationMessages([
        'regex'     => 'Contact number can only contain digits.',
        'min'       => 'Contact number must be exactly 11 digits.',
        'max'       => 'Contact number must be exactly 11 digits.',
    ])
    ->extraInputAttributes([
        'onkeypress' => "return /^[0-9]$/.test(event.key)",
        'maxlength'  => '11',
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : $state),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('guardian')
    ->label('Guardian Name')
    ->required()
    ->maxLength(100)
    ->rule('regex:/^[a-zA-Z\s\-\.]+$/')
    ->validationMessages([
        'regex' => 'Last name can only contain letters, spaces, hyphens, and periods.',
    ])
    ->extraInputAttributes([
        'style'      => 'text-transform: capitalize;',
        'onkeypress' => "return /^[a-zA-Z\s\'\-\.]$/.test(event.key)",
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? ucwords(strtolower($state)) : $state),
                                
                                TextInput::make('guardian_contact_no')
    ->label('Guardian Contact Number')
    ->tel()
    ->required()
    ->maxLength(11)
    ->minLength(11)
    ->rule('regex:/^[0-9]+$/')
    ->validationMessages([
        'regex'     => 'Contact number can only contain digits.',
        'min'       => 'Contact number must be exactly 11 digits.',
        'max'       => 'Contact number must be exactly 11 digits.',
    ])
    ->extraInputAttributes([
        'onkeypress' => "return /^[0-9]$/.test(event.key)",
        'maxlength'  => '11',
    ])
    ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : $state),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Scholarship Information')
                    ->schema([
                        Forms\Components\Select::make('type_of_scholarship_id')
                            ->label('Type of Scholarship')
                            ->options(function () {
                                return \App\Models\TypeOfScholarship::active()
                                    ->get()
                                    ->mapWithKeys(fn ($type) => [
                                        $type->id => "{$type->name} ({$type->slots} slot" . ($type->slots === 1 ? '' : 's') . " left)",
                                    ]);
                            })
                            ->disableOptionWhen(fn (string $value, ?Applicant $record): bool =>
                                $value !== (string) $record?->type_of_scholarship_id
                                && (\App\Models\TypeOfScholarship::find($value)?->slots ?? 0) <= 0
                            )
                            ->searchable()
                            ->live()
                            ->required()
                            ->helperText(function ($state) {
                                if (! $state) {
                                    return null;
                                }

                                $type = \App\Models\TypeOfScholarship::find($state);

                                return $type
                                    ? "{$type->slots} slot" . ($type->slots === 1 ? '' : 's') . ' currently available.'
                                    : null;
                            }),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Interview Notes & Benefits')
                    ->description('Add interview notes and scholarship benefits (available during edit)')
                    ->schema([
                        Textarea::make('interview')
                            ->label('Interview Notes')
                            ->rows(5)
                            ->placeholder('Enter interview notes, observations, and remarks here...')
                            ->helperText('Document important points from the interview session')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('benefit_display')
                            ->label('Scholarship Benefit')
                            ->content(function ($record) {
                                $discount = \App\Models\ExamAttempt::resolveDiscount((int) $record->benefit);

                                $colorClasses = match ($discount['color']) {
                                    'success' => 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400',
                                    'info'    => 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400',
                                    'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400',
                                    'danger'  => 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400',
                                    default   => 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400',
                                };

                                return new \Illuminate\Support\HtmlString(
                                    '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium ' . $colorClasses . '">'
                                    . e($discount['label']) .
                                    '</span>'
                                );
                            })
                            ->visible(fn ($record) => $record && ! is_null($record->benefit))
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('benefit')
                            ->label('Scholarship Benefit')
                            ->numeric()
                            ->rule('in:100,85,75,50,25,10,0')
                            ->validationMessages([
                                'in' => 'Enter one of the valid discount values: 100, 85, 75, 50, 25, 10, or 0.',
                            ])
                            ->placeholder('e.g. 100, 85, 75, 50, 25, 10, or 0')
                            ->helperText('No benefit set yet — normally filled automatically from the applicant\'s exam score. Enter one of: 100 (100% + Misc.), 85 (100% Tuition), 75, 50, 25, 10, or 0 (No Discount).')
                            ->visible(fn ($record) => ! $record || is_null($record->benefit))
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Applicant::query()->whereNull('archived_at'))
            ->columns([
                TextColumn::make('typeOfApplication.name')
                    ->label('Application Type')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('extension_name')
                    ->label('Ext.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('year_level')
                    ->label('Year Level')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => match((string)$state) {
                        '1' => '1st Year',
                        '2' => '2nd Year',
                        '3' => '3rd Year',
                        '4' => '4th Year',
                        '5' => '5th Year',
                        default => $state,
                    })
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('typeOfScholarship.name')
                    ->label('Scholarship Type')
                    ->sortable()
                    ->searchable(),

                // ── Requirements column ────────────────────────────────────────
                // Only counts rows where is_submitted = true (not all pivot rows)
                TextColumn::make('requirements_count')
                    ->label('Requirements')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $total     = \App\Models\Requirement::where('type_of_application_id', $record->type_of_application_id)->count();
                        $submitted = $record->submittedRequirements()->wherePivot('is_submitted', true)->count();
                        return "{$submitted}/{$total}";
                    })
                    ->color(function ($record) {
                        $total     = \App\Models\Requirement::where('type_of_application_id', $record->type_of_application_id)->count();
                        $submitted = $record->submittedRequirements()->wherePivot('is_submitted', true)->count();

                        if ($total === 0) return 'gray';
                        if ($submitted === $total) return 'success';
                        if ($submitted > 0) return 'warning';
                        return 'danger';
                    })
                    ->tooltip(function ($record) {
                        $total     = \App\Models\Requirement::where('type_of_application_id', $record->type_of_application_id)->count();
                        $submitted = $record->submittedRequirements()->wherePivot('is_submitted', true)->count();
                        return "Submitted: {$submitted} out of {$total} requirements";
                    }),
                // ── End requirements column ────────────────────────────────────
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Applied On')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type_of_application_id')
                    ->label('Application Type')
                    ->relationship('typeOfApplication', 'name')
                    ->placeholder('All Types'),
                
                Tables\Filters\SelectFilter::make('type_of_scholarship_id')
                    ->label('Scholarship Type')
                    ->relationship('typeOfScholarship', 'name')
                    ->placeholder('All Scholarships'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->placeholder('All Statuses'),
                
                Tables\Filters\Filter::make('has_benefit')
                    ->label('Has Benefit')
                    ->query(fn (Builder $query) => $query->whereNotNull('benefit')->where('benefit', '>', 0)),
                
                Tables\Filters\Filter::make('has_interview')
                    ->label('Has Interview Notes')
                    ->query(fn (Builder $query) => $query->whereNotNull('interview')->where('interview', '!=', '')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
    ->label('Approve')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->requiresConfirmation()
    ->modalHeading('Approve Application')
    ->modalDescription('Are you sure you want to approve this application? This will create a scholar record.')
    ->modalSubmitActionLabel('Yes, Approve')
    ->action(function (Applicant $record) {
        // ── Guard: already approved ──────────────────────────────────────
        if ($record->status !== 'pending') {
            Notification::make()
                ->title('Already Processed')
                ->warning()
                ->body('This application has already been processed.')
                ->send();
            return;
        }

        // ── Guard: no remaining slots ─────────────────────────────────────
        $scholarshipTypeModel = $record->typeOfScholarship;

        if ($scholarshipTypeModel && $scholarshipTypeModel->slots <= 0) {
            Notification::make()
                ->title('No Slots Available')
                ->danger()
                ->body("There are no remaining slots for {$scholarshipTypeModel->name}.")
                ->send();
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($record, $scholarshipTypeModel) {

                // ── 1. Mark application as approved ─────────────────────
                $record->update(['status' => 'approved']);

                // ── 2. Resolve related data ──────────────────────────────
                $student    = $record->user_id
                    ? \App\Models\Students::where('user_id', $record->user_id)->first()
                    : null;
                $studentId  = $student?->student_id;

                $program         = $record->program?->name ?? '';
                $sex             = $record->gender?->name ?? '';
                $scholarshipType = $record->typeOfScholarship?->name ?? '';

                // ── 3. Resolve active term ───────────────────────────────
                $activeTerm = \App\Models\Term::where('is_active', true)->first();

                // ── 4. Guard: prevent duplicate scholar records ──────────
                $alreadyScholar = \App\Models\Scholars::where(function ($q) use ($studentId, $record) {
                        if ($studentId) {
                            $q->where('student_id', $studentId);
                        } else {
                            $q->where('first_name', $record->first_name)
                              ->where('last_name',  $record->last_name)
                              ->where('birthdate',  $record->birthdate);
                        }
                    })
                    ->when($activeTerm, fn ($q) => $q->where('term_id', $activeTerm->id))
                    ->exists();

                if ($alreadyScholar) {
                    // Roll back the status update too by throwing inside the transaction
                    throw new \RuntimeException(
                        "{$record->first_name} {$record->last_name} is already a scholar for this term."
                    );
                }

                // ── 5. Derive batch number from active term ──────────────
                $batchNo = $activeTerm
                    ? ($activeTerm->batch_no ?? $activeTerm->id)
                    : null;

                // ── 6. Create scholar record ─────────────────────────────
                \App\Models\Scholars::create([
                    'student_id'          => $studentId,
                    'first_name'          => $record->first_name,
                    'middle_name'         => $record->middle_name,
                    'last_name'           => $record->last_name,
                    'extension_name'      => $record->extension_name,
                    'sex'                 => $sex,
                    'birthdate'           => $record->birthdate,
                    'program'             => $program,
                    'year_level'          => $record->year_level,
                    'type_of_scholarship' => $scholarshipType,
                    'benefit'             => $record->benefit,
                    'status'              => 'active',
                    'term_id'             => $activeTerm?->id,
                    'batch_no'            => $batchNo,
                    // ip_group / pwd — populate from applicant if your form collects them,
                    // otherwise leave null and fill later in the Scholar resource.
                    'ip_group'            => $record->ip_group   ?? null,
                    'pwd'                 => $record->pwd        ?? null,
                ]);

                // ── 7. Decrement the scholarship type's remaining slots ──
                $scholarshipTypeModel?->decrement('slots');

                // ── 8. Log the approval ───────────────────────────────────
                self::logCustomActivity(
                    $record,
                    'applicants',
                    'approved',
                    "Approved application for {$record->first_name} {$record->last_name}",
                    ['scholarship_type' => $scholarshipType, 'term' => $activeTerm?->name]
                );

                // ── 9. Success notification ──────────────────────────────
                $studentLabel = $studentId ? " (Student ID: {$studentId})" : '';
                $termLabel    = $activeTerm ? " for {$activeTerm->name}" : '';

                Notification::make()
                    ->title('Application Approved')
                    ->success()
                    ->body("{$record->first_name} {$record->last_name} is now a scholar{$studentLabel}{$termLabel}.")
                    ->send();
            });

        } catch (\RuntimeException $e) {
            // Known business-rule violations (duplicate, etc.)
            Notification::make()
                ->title('Could Not Approve')
                ->danger()
                ->body($e->getMessage())
                ->send();

        } catch (\Throwable $e) {
            // Unexpected DB/system errors
            \Illuminate\Support\Facades\Log::error('Scholar approval failed', [
                'applicant_id' => $record->id,
                'error'        => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Approval Failed')
                ->danger()
                ->body('An unexpected error occurred. Please try again or contact support.')
                ->send();
        }
    })
    ->disabled(fn (Applicant $record) => !$record->hasCompleteRequirements() || ($record->typeOfScholarship?->slots ?? 1) <= 0)
    ->tooltip(function (Applicant $record) {
        if (($record->typeOfScholarship?->slots ?? 1) <= 0) {
            return "No remaining slots for {$record->typeOfScholarship?->name}";
        }
        if (!$record->hasCompleteRequirements()) {
            $total     = \App\Models\Requirement::where('type_of_application_id', $record->type_of_application_id)->count();
            $submitted = $record->submittedRequirements()->wherePivot('is_submitted', true)->count();
            return "Requirements incomplete ({$submitted}/{$total} submitted)";
        }
        return null;
    })
    ->visible(fn (Applicant $record) => $record->status === 'pending'),

                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Application')
                        ->modalDescription('Are you sure you want to reject this application?')
                        ->modalSubmitActionLabel('Yes, Reject')
                        ->action(function (Applicant $record) {
                            $record->update(['status' => 'rejected']);

                            self::logCustomActivity(
                                $record,
                                'applicants',
                                'rejected',
                                "Rejected application for {$record->first_name} {$record->last_name}"
                            );

                            Notification::make()
                                ->title('Application Rejected')
                                ->danger()
                                ->body("{$record->first_name} {$record->last_name}'s application has been rejected.")
                                ->send();
                        })
                        ->visible(fn (Applicant $record) => $record->status === 'pending'),

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Applicant')
                        ->modalDescription('This will hide the application from this list. You can restore it later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function (Applicant $record): void {
                            $record->update(['archived_at' => now()]);

                            self::logCustomActivity(
                                $record,
                                'applicants',
                                'archived',
                                "Archived application for {$record->first_name} {$record->last_name}"
                            );

                            Notification::make()
                                ->title('Applicant archived')
                                ->success()
                                ->send();
                        }),

                    
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
    ->label('Approve Selected')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->requiresConfirmation()
    ->modalHeading('Approve Selected Applications')
    ->modalDescription('This will approve selected applications and create scholar records.')
    ->action(function ($records) {
        $approved = 0;
        $skippedNoSlots = 0;

        $records->each(function ($record) use (&$approved, &$skippedNoSlots) {
            if ($record->status !== 'pending' || ! $record->hasCompleteRequirements()) {
                return;
            }

            $scholarshipTypeModel = $record->typeOfScholarship;

            if ($scholarshipTypeModel && $scholarshipTypeModel->slots <= 0) {
                $skippedNoSlots++;
                return;
            }

            $record->update(['status' => 'approved']);

            // Fetch student_id from students table via user_id
            $student         = $record->user_id
                ? \App\Models\Students::where('user_id', $record->user_id)->first()
                : null;
            $studentId       = $student ? $student->student_id : null;

            $program         = $record->program ? $record->program->name : '';
            $sex             = $record->gender ? $record->gender->name : '';
            $scholarshipType = $scholarshipTypeModel ? $scholarshipTypeModel->name : '';

            \App\Models\Scholars::create([
                'student_id'         => $studentId,
                'first_name'         => $record->first_name,
                'middle_name'        => $record->middle_name,
                'last_name'          => $record->last_name,
                'extension_name'     => $record->extension_name,
                'sex'                => $sex,
                'birthdate'          => $record->birthdate,
                'program'            => $record->year_level,
                'year_level'         => $record->year_level,
                'type_of_scholarship'=> $scholarshipType,
                'benefit'            => $record->benefit,
                'status'             => 'active',
            ]);

            $scholarshipTypeModel?->decrement('slots');

            self::logCustomActivity(
                $record,
                'applicants',
                'approved',
                "Approved application for {$record->first_name} {$record->last_name}"
            );

            $approved++;
        });

        $body = "{$approved} applications approved and scholar records created.";

        if ($skippedNoSlots > 0) {
            $body .= " {$skippedNoSlots} skipped due to no remaining slots.";
        }

        Notification::make()
            ->title('Applications Approved')
            ->success()
            ->body($body)
            ->send();
    }),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending') {
                                    $record->update(['status' => 'rejected']);

                                    self::logCustomActivity(
                                        $record,
                                        'applicants',
                                        'rejected',
                                        "Rejected application for {$record->first_name} {$record->last_name}"
                                    );
                                }
                            });

                            Notification::make()
                                ->title('Applications Rejected')
                                ->danger()
                                ->body('Selected applications have been rejected.')
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('set_school_year_semester')
                        ->label('Set School Year & Semester')
                        ->icon('heroicon-o-calendar')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('school_year')
                                ->label('School Year')
                                ->options(self::getSchoolYearOptions())
                                ->required(),

                            Forms\Components\Select::make('semester')
                                ->label('Semester')
                                ->options([
                                    '1st Semester' => '1st Semester',
                                    '2nd Semester' => '2nd Semester',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'school_year' => $data['school_year'],
                                    'semester'    => $data['semester'],
                                ]);
                            });

                            Notification::make()
                                ->title('School Year & Semester Updated')
                                ->success()
                                ->body('Selected applicants have been updated.')
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('archive')
                        ->label('Archive Selected')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Selected Applicants')
                        ->modalDescription('This will hide the selected applications from this list.')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['archived_at' => now()]);

                                self::logCustomActivity(
                                    $record,
                                    'applicants',
                                    'archived',
                                    "Archived application for {$record->first_name} {$record->last_name}"
                                );
                            });

                            Notification::make()
                                ->title('Applicants Archived')
                                ->success()
                                ->body('Selected applicants have been archived.')
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('8s'); // ← auto-refresh every 8s so requirements column stays current
    }

    public static function getSchoolYearOptions(): array
    {
        $currentYear = date('Y');
        $years = [];

        for ($i = -5; $i <= 5; $i++) {
            $startYear = $currentYear + $i;
            $endYear   = $startYear + 1;
            $years["{$startYear}-{$endYear}"] = "{$startYear}-{$endYear}";
        }

        return $years;
    }

    public static function getCurrentSchoolYear(): string
    {
        $currentYear  = date('Y');
        $currentMonth = date('n');

        if ($currentMonth >= 6) {
            $startYear = $currentYear;
            $endYear   = $currentYear + 1;
        } else {
            $startYear = $currentYear - 1;
            $endYear   = $currentYear;
        }

        return "{$startYear}-{$endYear}";
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubmittedRequirementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApplicants::route('/'),
            'create' => Pages\CreateApplicant::route('/create'),
            'edit'   => Pages\EditApplicant::route('/{record}/edit'),
            'view'   => Pages\ViewApplicant::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }
}