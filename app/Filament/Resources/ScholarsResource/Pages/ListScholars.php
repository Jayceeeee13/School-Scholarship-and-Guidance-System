<?php

namespace App\Filament\Resources\ScholarsResource\Pages;

use App\Filament\Resources\ScholarsResource;
use App\Models\DailyTimeRecord;
use App\Models\Department;
use App\Models\InstitutionalScholar;
use App\Models\Scholars;
use App\Models\Term;
use App\Models\TypeOfScholarship;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListScholars extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = ScholarsResource::class;

    public function updatedActiveTab(): void
    {
        $this->resetTable();
    }

    protected function scopeDtrQueryToRole(Builder $query): Builder
    {
        $user = auth()->user();

        if ($user->isDepartmentHead()) {
            return $query->whereHas('scholar', fn ($q) => $q->where('department_head_id', $user->id));
        }

        if ($user->hasAnyRole(['admin', 'scholarship'])) {
            return $query->whereIn('status', ['submitted', 'received']);
        }

        return $query;
    }

    protected function recalculateTotalHours(callable $get, callable $set): void
    {
        $total = DailyTimeRecord::calculateTotalHours(
            $get('am_in'),
            $get('am_out'),
            $get('pm_in'),
            $get('pm_out'),
        );

        $set('total_hours', $total);
    }

    /**
     * Finds the Scholars row that corresponds to a given InstitutionalScholar
     * record, so "Assign Department Head" on the Institutional Scholars tab
     * updates the same underlying scholar instead of creating duplicates.
     *
     * Match priority: user_id (most reliable) -> student_id -> name+birthdate,
     * scoped to the same term when available.
     */
    protected static function findMatchingScholar(InstitutionalScholar $record): ?Scholars
    {
        $query = Scholars::query();

        if ($record->user_id) {
            $query->where('user_id', $record->user_id);
        } elseif ($record->student_id) {
            $query->where('student_id', $record->student_id);
        } else {
            $query->where('first_name', $record->first_name)
                  ->where('last_name', $record->last_name)
                  ->where('birthdate', $record->birthdate);
        }

        if ($record->term_id) {
            $query->where('term_id', $record->term_id);
        }

        return $query->first();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => ! in_array($this->activeTab, ['dtr', 'institutional'])),

            Actions\Action::make('newInstitutionalScholar')
                ->label('Add Institutional Scholar')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->visible(fn (): bool => $this->activeTab === 'institutional' && auth()->user()->hasAnyRole(['admin', 'scholarship']))
                ->modalHeading('Add Institutional Scholar')
                ->modalWidth('4xl')
                ->modalSubmitActionLabel('Save Scholar')
                ->form(ScholarsResource::scholarFormSchema())
                ->action(function (array $data): void {
                    // NOTE: writes to InstitutionalScholar, not Scholars —
                    // this tab shows institutional_scholars table rows,
                    // so records added here must land in the same table.
                    $record = InstitutionalScholar::create($data);

                    $this->logCustomActivity(
                        $record,
                        'institutional_scholars',
                        'created',
                        "Added institutional scholar {$record->first_name} {$record->last_name}"
                    );

                    Notification::make()
                        ->title('Institutional Scholar Added')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('newDtr')
                ->label('New DTR Entry')
                ->icon('heroicon-o-plus-circle')
                ->color('gray')
                ->visible(fn (): bool => $this->activeTab === 'dtr')
                ->modalHeading('New Daily Time Record')
                ->modalDescription('Log a scholar\'s time-in/out for a single day. This entry starts as Pending until it\'s approved.')
                ->modalSubmitActionLabel('Save Entry')
                ->form([
                    Forms\Components\Section::make('Who & Where')
                        ->description('Select the scholar and confirm their assigned office.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Select::make('scholar_id')
                                ->label('Scholar (Student Representatives)')
                                ->options(function () {
                                    $query = Scholars::where('type_of_scholarship', 'Student Representatives');

                                    if (auth()->user()->isDepartmentHead()) {
                                        $query->where('department_head_id', auth()->id());
                                    }

                                    return $query->get()->mapWithKeys(fn ($s) => [
                                        $s->id => "{$s->first_name} {$s->last_name}",
                                    ]);
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->native(false)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) {
                                        $set('office_assigned', null);
                                        return;
                                    }

                                    $scholar = Scholars::with('departmentHead.department')->find($state);

                                    if ($scholar?->departmentHead?->department?->name) {
                                        $set('office_assigned', $scholar->departmentHead->department->name);
                                        return;
                                    }

                                    $lastOffice = DailyTimeRecord::where('scholar_id', $state)
                                        ->whereNotNull('office_assigned')
                                        ->latest('date')
                                        ->value('office_assigned');

                                    $set('office_assigned', $lastOffice);
                                }),

                            Forms\Components\Select::make('office_assigned')
                                ->label('Office Assigned')
                                ->helperText('Auto-filled from the scholar\'s department head — adjust if needed.')
                                ->options(fn () => Department::active()->pluck('name', 'name'))
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->required(),
                        ])
                        ->compact(),

                    Forms\Components\Section::make('When')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Forms\Components\DatePicker::make('date')
                                ->label('Date')
                                ->required()
                                ->native(false)
                                ->displayFormat('M d, Y')
                                ->maxDate(now())
                                ->helperText('Month/year for reporting is taken automatically from this date.'),
                        ])
                        ->compact(),

                    Forms\Components\Section::make('Time Log')
                        ->description('Total Hours below updates automatically as you fill these in.')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('am_in')
                                        ->label('AM In')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('am_in_location')
                                        ->label('AM In Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('am_out')
                                        ->label('AM Out')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('am_out_location')
                                        ->label('AM Out Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('pm_in')
                                        ->label('PM In')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('pm_in_location')
                                        ->label('PM In Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TimePicker::make('pm_out')
                                        ->label('PM Out')
                                        ->seconds(false)
                                        ->live()
                                        ->afterStateUpdated(fn (callable $get, callable $set) =>
                                            $this->recalculateTotalHours($get, $set)),

                                    Forms\Components\TextInput::make('pm_out_location')
                                        ->label('PM Out Location')
                                        ->placeholder('e.g., Main Campus')
                                        ->maxLength(150),
                                ]),

                            Forms\Components\TextInput::make('total_hours')
                                ->label('Total Hours')
                                ->numeric()
                                ->step(0.01)
                                ->readOnly()
                                ->default(0)
                                ->suffix('hrs')
                                ->helperText('Automatically computed — you don\'t need to fill this in.')
                                ->dehydrated(true)
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->collapsible(),

                    Forms\Components\Textarea::make('remarks')
                        ->label('Remarks')
                        ->placeholder('Optional notes for this entry...')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $data['total_hours'] = DailyTimeRecord::calculateTotalHours(
                        $data['am_in'] ?? null,
                        $data['am_out'] ?? null,
                        $data['pm_in'] ?? null,
                        $data['pm_out'] ?? null,
                    );

                    $record = DailyTimeRecord::create($data + ['status' => 'pending']);

                    $this->logCustomActivity(
                        $record,
                        'dtr',
                        'created',
                        "Added DTR entry for {$record->date?->format('M d, Y')} ({$record->office_assigned})"
                    );

                    Notification::make()
                        ->title('DTR entry added')
                        ->body('This entry is now Pending and awaiting approval.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ScholarsResource::getTabs();

        $tabs['dtr'] = Tab::make('DTR')
            ->icon('heroicon-o-clock')
            ->badge(function () {
                $user = auth()->user();

                if (! $user->isDepartmentHead() && $user->hasAnyRole(['admin', 'scholarship'])) {
                    return DailyTimeRecord::where('status', 'submitted')->count();
                }

                $query = DailyTimeRecord::where('status', 'pending');

                if ($user->isDepartmentHead()) {
                    $query->whereHas('scholar', fn ($q) => $q->where('department_head_id', $user->id));
                }

                return $query->count();
            })
            ->badgeColor('warning');

        return $tabs;
    }

    public function table(Table $table): Table
    {
        // ── Institutional Scholars tab ──────────────────────────────────
        // Queries the SEPARATE institutional_scholars table directly
        // (via InstitutionalScholar model), not the scholars table.
        // This is where approved Applicant records land — see
        // ApplicantResource's approve action — and where matching
        // Scholars rows get mirrored to (see Scholars::mirrorToInstitutional
        // / syncAllToInstitutional).
        if ($this->activeTab === 'institutional') {
            return $table
                ->query(InstitutionalScholar::query()->where('status', '!=', 'revoked'))
                ->headerActions([
                    Tables\Actions\Action::make('sync_institutional')
                        ->label('Sync from Scholars')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Sync Institutional Scholars')
                        ->modalDescription('Scans every scholar and mirrors anyone whose Type of Scholarship matches a registered Type of Scholarship into this list. Safe to run anytime — existing entries are updated, not duplicated.')
                        ->modalSubmitActionLabel('Run Sync')
                        ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'scholarship']))
                        ->action(function (): void {
                            $count = Scholars::syncAllToInstitutional();

                            Notification::make()
                                ->title('Sync Complete')
                                ->success()
                                ->body("{$count} scholar(s) synced into Institutional Scholars.")
                                ->send();

                            $this->resetTable();
                        }),

                    Tables\Actions\Action::make('view_accomplishment_reports_institutional')
                        ->label('View Accomplishment Reports')
                        ->icon('heroicon-o-document-check')
                        ->color('gray')
                        ->url(fn () => ScholarsResource::getUrl('accomplishment-reports')),

                    Tables\Actions\Action::make('export_institutional')
                        ->label('Export Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function () {
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\InstitutionalScholarsExport(),
                                'institutional-scholars-' . now()->format('Y-m-d') . '.xlsx'
                            );
                        }),

                    Tables\Actions\Action::make('import_institutional')
                        ->label('Import Excel')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('term_id')
                                ->label('Import into Term')
                                ->options(function () {
                                    return Term::orderByDesc('is_active')
                                        ->orderByDesc('id')
                                        ->get()
                                        ->mapWithKeys(fn ($term) => [
                                            $term->id => $term->school_year . ' — ' . $term->semester
                                                . ($term->is_active ? ' (Active)' : ''),
                                        ]);
                                })
                                ->required()
                                ->native(false)
                                ->searchable()
                                ->helperText('Institutional scholars will be tagged to this term. Duplicates in the same term will be skipped.'),

                            Forms\Components\FileUpload::make('file')
                                ->label('Excel File (.xlsx)')
                                ->acceptedFileTypes([
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-excel',
                                ])
                                ->maxSize(10240)
                                ->required()
                                ->helperText('Columns: SEQ, STUDENT ID, LAST NAME, GIVEN NAME, EXT. NAME, MIDDLE NAME, SEX, BIRTHDATE, PROGRAM, YEAR LEVEL, TYPE OF SCHOLARSHIP, BATCH NO., IP GROUP, PWD, BENEFIT, STATUS'),
                        ])
                        ->action(function (array $data) {
                            try {
                                $filePath = $data['file'];

                                if (is_array($filePath)) {
                                    $filePath = reset($filePath);
                                }

                                $possiblePaths = [
                                    storage_path('app/public/' . $filePath),
                                    storage_path('app/' . $filePath),
                                    storage_path('app/livewire-tmp/' . $filePath),
                                    storage_path('app/livewire-tmp/' . basename($filePath)),
                                ];

                                $fullPath = null;
                                foreach ($possiblePaths as $path) {
                                    if (file_exists($path)) {
                                        $fullPath = $path;
                                        break;
                                    }
                                }

                                if (! $fullPath) {
                                    Notification::make()
                                        ->title('File Not Found')
                                        ->danger()
                                        ->body('Could not locate the uploaded file. Please try again.')
                                        ->send();
                                    return;
                                }

                                $import = new \App\Imports\InstitutionalScholarsImport((int) $data['term_id']);
                                \Maatwebsite\Excel\Facades\Excel::import($import, $fullPath);

                                @unlink($fullPath);

                                $failures      = $import->failures()->count();
                                $duplicateRows = $import->duplicateRows;
                                $duplicates    = count($duplicateRows);
                                $term          = Term::find($data['term_id']);
                                $termLabel     = $term
                                    ? $term->school_year . ' — ' . $term->semester
                                    : 'the selected term';

                                if ($duplicates > 0) {
                                    $dupLines = array_map(
                                        fn ($d) => "• {$d['name']}" . ($d['student_id'] ? " (Student ID: {$d['student_id']})" : ' (no Student ID provided)'),
                                        array_slice($duplicateRows, 0, 5)
                                    );
                                    $more = $duplicates > 5
                                        ? "\n…and " . ($duplicates - 5) . ' additional record(s).'
                                        : '';

                                    $body = "{$duplicates} record(s) were not imported because they already exist for {$termLabel}:" .
                                        "\n\n" . implode("\n", $dupLines) . $more;

                                    if ($failures > 0) {
                                        $body .= "\n\nAdditionally, {$failures} row(s) failed validation and were skipped.";
                                    }

                                    Notification::make()
                                        ->title('Import Completed with Duplicates Skipped')
                                        ->warning()
                                        ->body($body)
                                        ->persistent()
                                        ->send();
                                } elseif ($failures > 0) {
                                    Notification::make()
                                        ->title('Import Completed with Notices')
                                        ->warning()
                                        ->body("Institutional scholars imported into {$termLabel}. {$failures} row(s) failed validation and were skipped.")
                                        ->persistent()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Import Successful')
                                        ->success()
                                        ->body("All institutional scholars imported successfully into {$termLabel}.")
                                        ->send();
                                }

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Import Failed')
                                    ->danger()
                                    ->body('Error: ' . $e->getMessage())
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('download_template_institutional')
                        ->label('Download Template')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function () {
                            // Same column structure as Scholars, so the
                            // existing template export is reused as-is.
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\ScholarsTemplateExport(),
                                'institutional-scholars-import-template.xlsx'
                            );
                        }),

                    Tables\Actions\Action::make('print_institutional')
                        ->label('Print Institutional')
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        ->url(fn () => route('scholars.print.institutional'))
                        ->openUrlInNewTab(),
                ])
                ->columns(ScholarsResource::scholarTableColumns())
                ->filters([
                    Tables\Filters\SelectFilter::make('term_id')
                        ->label('School Year & Semester')
                        ->options(function () {
                            return Term::orderByDesc('is_active')
                                ->orderByDesc('id')
                                ->get()
                                ->mapWithKeys(fn ($term) => [
                                    $term->id => $term->school_year . ' — ' . $term->semester,
                                ]);
                        })
                        ->searchable()
                        ->placeholder('All Terms'),

                    Tables\Filters\SelectFilter::make('status')
                        ->label('Status')
                        ->options([
                            'active'       => 'Active',
                            'inactive'     => 'Inactive',
                            'graduated'    => 'Graduated',
                            'discontinued' => 'Discontinued',
                        ])
                        ->multiple()
                        ->placeholder('All Statuses'),

                    Tables\Filters\SelectFilter::make('type_of_scholarship')
                        ->label('Type of Scholarship')
                        ->options(fn () => InstitutionalScholar::query()
                            ->distinct()
                            ->whereNotNull('type_of_scholarship')
                            ->pluck('type_of_scholarship', 'type_of_scholarship')
                            ->sort())
                        ->multiple()
                        ->searchable()
                        ->placeholder('All Scholarships'),

                    Tables\Filters\SelectFilter::make('sex')
                        ->label('Sex')
                        ->options([
                            'Male'   => 'Male',
                            'Female' => 'Female',
                        ])
                        ->placeholder('All'),

                    Tables\Filters\SelectFilter::make('year_level')
                        ->label('Year Level')
                        ->options([
                            '1' => '1st Year',
                            '2' => '2nd Year',
                            '3' => '3rd Year',
                            '4' => '4th Year',
                            '5' => '5th Year',
                        ])
                        ->multiple()
                        ->placeholder('All Years'),

                    Tables\Filters\SelectFilter::make('batch_no')
                        ->label('Batch')
                        ->options(function () {
                            return InstitutionalScholar::query()
                                ->distinct()
                                ->whereNotNull('batch_no')
                                ->pluck('batch_no', 'batch_no')
                                ->sort()
                                ->reverse();
                        })
                        ->placeholder('All Batches'),
                ])
                ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
                ->filtersFormColumns(6)
                ->filtersTriggerAction(
                    fn (Tables\Actions\Action $action) => $action->hidden(),
                )
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        // ── Assign Department Head (Institutional Scholars tab) ──
                        // Promotes/syncs this institutional scholar into the main
                        // scholars table (matched by user_id -> student_id ->
                        // name+birthdate) and sets department_head_id on it.
                        // Carries user_id across so Daily Time Record and other
                        // features that key off scholars.user_id work correctly.
                        Tables\Actions\Action::make('assign_department_head_institutional')
                            ->label('Assign Department Head')
                            ->icon('heroicon-o-user-plus')
                            ->color('info')
                            ->modalHeading('Assign Department Head')
                            ->modalDescription('This creates (or updates) this scholar\'s record in the main Scholars list and assigns their Department Head.')
                            ->modalSubmitActionLabel('Save Assignment')
                            ->visible(fn () => auth()->user()->hasAnyRole(['admin', 'scholarship']))
                            ->form([
                                Forms\Components\Select::make('department_head_id')
                                    ->label('Department Head')
                                    ->options(fn () => \App\Models\User::whereHas('role', fn ($q) => $q->where('name', 'Department Head'))
                                        ->with('department')
                                        ->get()
                                        ->mapWithKeys(fn ($u) => [
                                            $u->id => $u->name . ($u->department ? " — {$u->department->name}" : ''),
                                        ]))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required()
                                    ->placeholder('Select a Department Head'),
                            ])
                            ->fillForm(function (InstitutionalScholar $record): array {
                                $existing = self::findMatchingScholar($record);

                                return [
                                    'department_head_id' => $existing?->department_head_id,
                                ];
                            })
                            ->action(function (InstitutionalScholar $record, array $data): void {
                                $scholar = self::findMatchingScholar($record) ?? new Scholars();

                                $wasNew = ! $scholar->exists;

                                $scholar->fill([
                                    'user_id'             => $record->user_id,
                                    'student_id'          => $record->student_id,
                                    'first_name'          => $record->first_name,
                                    'middle_name'         => $record->middle_name,
                                    'last_name'           => $record->last_name,
                                    'extension_name'      => $record->extension_name,
                                    'sex'                 => $record->sex,
                                    'birthdate'           => $record->birthdate,
                                    'program'             => $record->program,
                                    'year_level'          => $record->year_level,
                                    'type_of_scholarship' => $record->type_of_scholarship,
                                    'batch_no'            => $record->batch_no,
                                    'ip_group'            => $record->ip_group,
                                    'pwd'                 => $record->pwd,
                                    'benefit'             => $record->benefit,
                                    'status'              => $record->status,
                                    'term_id'             => $record->term_id,
                                    'department_head_id'  => $data['department_head_id'],
                                ]);

                                $scholar->save();

                                $headName = \App\Models\User::find($data['department_head_id'])?->name ?? 'the selected head';

                                $this->logCustomActivity(
                                    $scholar,
                                    'scholars',
                                    $wasNew ? 'created' : 'updated',
                                    ($wasNew
                                        ? "Promoted {$record->first_name} {$record->last_name} to Scholars and assigned "
                                        : "Reassigned {$record->first_name} {$record->last_name} to ") . $headName
                                );

                                Notification::make()
                                    ->title($wasNew ? 'Scholar Created & Department Head Assigned' : 'Department Head Assigned')
                                    ->success()
                                    ->body("{$record->first_name} {$record->last_name} is now assigned to {$headName}.")
                                    ->send();
                            }),

                        Tables\Actions\ViewAction::make()
                            ->form(ScholarsResource::scholarFormSchema())
                            ->infolist([
                                \Filament\Infolists\Components\Section::make('Scholar Information')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('full_name')
                                            ->label('Name'),
                                        \Filament\Infolists\Components\TextEntry::make('program')
                                            ->label('Program'),
                                        \Filament\Infolists\Components\TextEntry::make('type_of_scholarship')
                                            ->label('Scholarship Type'),
                                        \Filament\Infolists\Components\TextEntry::make('status')
                                            ->badge(),
                                    ])
                                    ->columns(2),

                                \Filament\Infolists\Components\Section::make('Accomplishment Reports')
                                    ->icon('heroicon-o-document-check')
                                    ->description('Activity logs submitted by this scholar, one report per term.')
                                    ->schema([
                                        \Filament\Infolists\Components\RepeatableEntry::make('accomplishmentReports')
                                            ->label('')
                                            ->schema([
                                                \Filament\Infolists\Components\TextEntry::make('term.school_year')
                                                    ->label('Term')
                                                    ->formatStateUsing(fn ($state, $record) => $record->term
                                                        ? "{$record->term->school_year} — {$record->term->semester}"
                                                        : '—'),
                                                \Filament\Infolists\Components\TextEntry::make('status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'pending'  => 'warning',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        default    => 'gray',
                                                    }),
                                                \Filament\Infolists\Components\TextEntry::make('activities')
                                                    ->label('Activities')
                                                    ->formatStateUsing(fn ($state, $record) => $record->activities->count() . ' activity/activities logged'),
                                            ])
                                            ->columns(3),
                                    ])
                                    ->visible(fn ($record) => $record->isEligibleForAccomplishmentReports()),
                            ]),

                        Tables\Actions\EditAction::make()
                            ->form(ScholarsResource::scholarFormSchema()),

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

        if ($this->activeTab === 'dtr') {
            return $table
                ->query(function () {
                    $query = DailyTimeRecord::query()->with(['scholar', 'approvedBy', 'receivedBy']);

                    return $this->scopeDtrQueryToRole($query);
                })
                ->columns([
                    Tables\Columns\TextColumn::make('scholar.full_name')
                        ->label('Name')
                        ->getStateUsing(fn (DailyTimeRecord $record) => $record->scholar
                            ? trim("{$record->scholar->first_name} {$record->scholar->last_name}")
                            : '—')
                        ->searchable(query: function (Builder $query, string $search) {
                            return $query->whereHas('scholar', function ($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                            });
                        })
                        ->sortable(),

                    Tables\Columns\TextColumn::make('scholar_course_year')
                        ->label('Course & Year')
                        ->getStateUsing(function (DailyTimeRecord $record) {
                            if (! $record->scholar) {
                                return '—';
                            }

                            $yearLabel = match ((string) $record->scholar->year_level) {
                                '1' => '1st Year',
                                '2' => '2nd Year',
                                '3' => '3rd Year',
                                '4' => '4th Year',
                                '5' => '5th Year',
                                default => $record->scholar->year_level,
                            };

                            return trim("{$record->scholar->program} - {$yearLabel}", ' -');
                        })
                        ->toggleable(),

                    Tables\Columns\TextColumn::make('office_assigned')
                        ->label('Office Assigned')
                        ->searchable()
                        ->sortable()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('month_label')
                        ->label('Month')
                        ->getStateUsing(fn (DailyTimeRecord $record) => $record->month_label)
                        ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('date', $direction))
                        ->toggleable(),

                    Tables\Columns\TextColumn::make('date')
                        ->label('Date')
                        ->date('M d, Y')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('am_in')
                        ->label('AM In')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_in, $record->am_in_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('am_out')
                        ->label('AM Out')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_out, $record->am_out_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('pm_in')
                        ->label('PM In')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_in, $record->pm_in_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('pm_out')
                        ->label('PM Out')
                        ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_out, $record->pm_out_location))
                        ->wrap()
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('total_hours')
                        ->label('Total Hrs')
                        ->numeric(2)
                        ->placeholder('—'),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'   => 'warning',
                            'approved'  => 'info',
                            'submitted' => 'primary',
                            'received'  => 'success',
                            'rejected'  => 'danger',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                        ->tooltip(fn (string $state): string => match ($state) {
                            'pending'   => 'Awaiting Department Head approval.',
                            'approved'  => 'Approved by Department Head — not yet sent to Admin/Scholarship.',
                            'submitted' => 'Sent to Admin/Scholarship, awaiting receipt.',
                            'received'  => 'Received and finalized by Admin/Scholarship.',
                            'rejected'  => 'Rejected — see remarks for the reason.',
                            default     => ucfirst($state),
                        })
                        ->sortable(),

                    Tables\Columns\TextColumn::make('approvedBy.name')
                        ->label('Approved By')
                        ->placeholder('—')
                        ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('receivedBy.name')
                        ->label('Received By')
                        ->placeholder('—')
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
                ->filters([
                    Tables\Filters\SelectFilter::make('status')
                        ->options(function () {
                            $user = auth()->user();

                            if (! $user->isDepartmentHead() && $user->hasAnyRole(['admin', 'scholarship'])) {
                                return [
                                    'submitted' => 'Submitted',
                                    'received'  => 'Received',
                                ];
                            }

                            return [
                                'pending'   => 'Pending',
                                'approved'  => 'Approved',
                                'submitted' => 'Submitted',
                                'received'  => 'Received',
                                'rejected'  => 'Rejected',
                            ];
                        }),

                    Tables\Filters\SelectFilter::make('month')
                        ->label('Month')
                        ->options(fn () => DailyTimeRecord::query()
                            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym, DATE_FORMAT(date, '%M %Y') as label")
                            ->distinct()
                            ->orderByDesc('ym')
                            ->pluck('label', 'ym'))
                        ->query(function (Builder $query, array $data) {
                            if (! $data['value']) {
                                return;
                            }

                            [$year, $month] = explode('-', $data['value']);

                            $query->whereYear('date', $year)->whereMonth('date', $month);
                        }),

                    Tables\Filters\SelectFilter::make('office_assigned')
                        ->label('Office Assigned')
                        ->options(fn () => Department::active()->pluck('name', 'name')),
                ])
                ->actions([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make()
                            ->infolist([
                                \Filament\Infolists\Components\Section::make('Scholar Information')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('scholar_name')
                                            ->label('Name')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => $record->scholar
                                                ? trim("{$record->scholar->first_name} {$record->scholar->last_name}")
                                                : '—'),

                                        \Filament\Infolists\Components\TextEntry::make('scholar_course_year')
                                            ->label('Course & Year')
                                            ->getStateUsing(function (DailyTimeRecord $record) {
                                                if (! $record->scholar) {
                                                    return '—';
                                                }

                                                $yearLabel = match ((string) $record->scholar->year_level) {
                                                    '1' => '1st Year',
                                                    '2' => '2nd Year',
                                                    '3' => '3rd Year',
                                                    '4' => '4th Year',
                                                    '5' => '5th Year',
                                                    default => $record->scholar->year_level,
                                                };

                                                return trim("{$record->scholar->program} - {$yearLabel}", ' -');
                                            }),

                                        \Filament\Infolists\Components\TextEntry::make('office_assigned')
                                            ->label('Office Assigned')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('month_label')
                                            ->label('Month')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => $record->month_label),
                                    ])
                                    ->columns(2),

                                \Filament\Infolists\Components\Section::make('Time & Location Log')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('date')
                                            ->label('Date')
                                            ->date('M d, Y'),

                                        \Filament\Infolists\Components\TextEntry::make('total_hours')
                                            ->label('Total Hours')
                                            ->numeric(2)
                                            ->suffix(' hrs')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('am_in')
                                            ->label('AM In')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_in, $record->am_in_location))
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('am_out')
                                            ->label('AM Out')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->am_out, $record->am_out_location))
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('pm_in')
                                            ->label('PM In')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_in, $record->pm_in_location))
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('pm_out')
                                            ->label('PM Out')
                                            ->getStateUsing(fn (DailyTimeRecord $record) => self::formatPunch($record->pm_out, $record->pm_out_location))
                                            ->placeholder('—'),
                                    ])
                                    ->columns(3),

                                \Filament\Infolists\Components\Section::make('Status & Routing')
                                    ->icon('heroicon-o-arrow-path-rounded-square')
                                    ->description('Where this entry currently sits in the approval chain.')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'pending'   => 'warning',
                                                'approved'  => 'info',
                                                'submitted' => 'primary',
                                                'received'  => 'success',
                                                'rejected'  => 'danger',
                                                default     => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                                        \Filament\Infolists\Components\TextEntry::make('approvedBy.name')
                                            ->label('Approved By')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('approved_at')
                                            ->label('Approved At')
                                            ->dateTime('M d, Y h:i A')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('receivedBy.name')
                                            ->label('Received By')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('received_at')
                                            ->label('Received At')
                                            ->dateTime('M d, Y h:i A')
                                            ->placeholder('—'),

                                        \Filament\Infolists\Components\TextEntry::make('remarks')
                                            ->label('Remarks')
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tables\Actions\EditAction::make()
                            ->mutateFormDataUsing(function (array $data): array {
                                $data['total_hours'] = DailyTimeRecord::calculateTotalHours(
                                    $data['am_in'] ?? null,
                                    $data['am_out'] ?? null,
                                    $data['pm_in'] ?? null,
                                    $data['pm_out'] ?? null,
                                );

                                return $data;
                            }),

                        Tables\Actions\Action::make('approve')
                            ->label('Approve')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->requiresConfirmation()
                            ->modalHeading('Approve DTR')
                            ->modalDescription('This marks the entry as Approved. It will then need to be Submitted to Admin/Scholarship separately.')
                            ->modalSubmitActionLabel('Yes, Approve')
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'pending'
                                && (auth()->user()->isDepartmentHead() || auth()->user()->isAdmin())
                            )
                            ->action(function (DailyTimeRecord $record): void {
                                $record->update([
                                    'status'         => 'approved',
                                    'approved_by_id' => auth()->id(),
                                    'approved_at'    => now(),
                                ]);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'approved',
                                    "Approved DTR for {$record->scholar?->first_name} {$record->scholar?->last_name} ({$record->date?->format('M d, Y')})"
                                );

                                Notification::make()
                                    ->title('DTR Approved')
                                    ->body('Next step: Submit this entry to Admin/Scholarship.')
                                    ->success()
                                    ->send();
                            }),

                        Tables\Actions\Action::make('submit')
                            ->label('Submit')
                            ->icon('heroicon-o-paper-airplane')
                            ->color('primary')
                            ->requiresConfirmation()
                            ->modalHeading('Submit DTR to Admin/Scholarship')
                            ->modalDescription('This sends the approved entry onward. It will show as "Submitted" until Admin/Scholarship marks it Received.')
                            ->modalSubmitActionLabel('Yes, Submit')
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'approved'
                                && (auth()->user()->isDepartmentHead() || auth()->user()->isAdmin())
                            )
                            ->action(function (DailyTimeRecord $record): void {
                                $record->update(['status' => 'submitted']);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'submitted',
                                    "Submitted DTR for {$record->scholar?->first_name} {$record->scholar?->last_name} to Admin/Scholarship ({$record->date?->format('M d, Y')})"
                                );

                                Notification::make()
                                    ->title('DTR Submitted')
                                    ->success()
                                    ->body('The DTR has been sent to Admin and Scholarship for receiving.')
                                    ->send();
                            }),

                        Tables\Actions\Action::make('reject')
                            ->label('Reject')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Reject DTR')
                            ->modalDescription('The scholar/department head will need to correct and re-add this entry.')
                            ->form([
                                Forms\Components\Textarea::make('remarks')
                                    ->label('Reason for Rejection')
                                    ->placeholder('e.g., Missing PM Out time, incorrect location...')
                                    ->required()
                                    ->rows(2),
                            ])
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'pending'
                                && (auth()->user()->isDepartmentHead() || auth()->user()->isAdmin())
                            )
                            ->action(function (DailyTimeRecord $record, array $data): void {
                                $record->update([
                                    'status'  => 'rejected',
                                    'remarks' => $data['remarks'],
                                ]);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'rejected',
                                    "Rejected DTR for {$record->scholar?->first_name} {$record->scholar?->last_name} ({$record->date?->format('M d, Y')})",
                                    ['reason' => $data['remarks']]
                                );

                                Notification::make()
                                    ->title('DTR Rejected')
                                    ->danger()
                                    ->send();
                            }),

                        Tables\Actions\Action::make('receive')
                            ->label('Mark Received')
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->color('info')
                            ->requiresConfirmation()
                            ->modalHeading('Mark DTR as Received')
                            ->modalDescription('This finalizes the entry — confirms Admin/Scholarship has it on file.')
                            ->modalSubmitActionLabel('Yes, Mark Received')
                            ->visible(fn (DailyTimeRecord $record): bool =>
                                $record->status === 'submitted'
                                && auth()->user()->hasAnyRole(['admin', 'scholarship'])
                            )
                            ->action(function (DailyTimeRecord $record): void {
                                $record->update([
                                    'status'         => 'received',
                                    'received_by_id' => auth()->id(),
                                    'received_at'    => now(),
                                ]);

                                $this->logCustomActivity(
                                    $record,
                                    'dtr',
                                    'received',
                                    "Marked DTR as received for {$record->scholar?->first_name} {$record->scholar?->last_name} ({$record->date?->format('M d, Y')})"
                                );

                                Notification::make()
                                    ->title('DTR Marked as Received')
                                    ->success()
                                    ->send();
                            }),

                        Tables\Actions\DeleteAction::make(),
                    ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
                ])
                ->defaultSort('date', 'desc');
        }

        // "all" and "revoked" tabs both use the standard Scholars
        // table/actions.
        return ScholarsResource::table($table);
    }

    protected static function formatPunch(?string $time, ?string $location): ?string
    {
        if (! $time) {
            return null;
        }

        $formatted = \Carbon\Carbon::parse($time)->format('h:i A');

        return $location ? "{$formatted} — {$location}" : $formatted;
    }
}