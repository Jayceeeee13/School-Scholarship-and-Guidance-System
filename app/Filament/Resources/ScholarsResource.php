<?php

namespace App\Filament\Resources;

use App\Exports\ScholarsExport;
use App\Imports\ScholarsImport;
use App\Filament\Resources\ScholarsResource\Pages;
use App\Models\Scholars;
use App\Models\Term;
use App\Models\TypeOfScholarship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Maatwebsite\Excel\Facades\Excel;

class ScholarsResource extends Resource
{
    protected static ?string $model = Scholars::class;

    protected static ?string $navigationIcon = 'heroicon-s-academic-cap';

    protected static ?string $navigationLabel = 'Scholars';

    protected static ?string $navigationGroup = 'Scholarship Management';

    protected static ?int $navigationSort = 2;

    /**
     * Department Heads only get to see and act on scholars assigned to
     * them; admin/scholarship staff keep full visibility.
     */
    protected static function isRestrictedToOwnScholars(): bool
    {
        return auth()->user()->hasRole('department head')
            && ! auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Scholar Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('student_id')
                                    ->label('Student ID')
                                    ->numeric()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Optional - can be assigned later')
                                    ->helperText('Leave empty to assign later'),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active'       => 'Active',
                                        'inactive'     => 'Inactive',
                                        'graduated'    => 'Graduated',
                                        'discontinued' => 'Discontinued',
                                        'revoked'      => 'Revoked',
                                    ])
                                    ->default('active')
                                    ->required()
                                    ->native(false),
                            ]),

                        Forms\Components\Select::make('term_id')
                            ->label('Term')
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
                            ->helperText('Select the school term for this scholar'),
                    ])
                    ->collapsible(),

                Section::make('Personal Information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->required()
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('extension_name')
                                    ->label('Extension')
                                    ->placeholder('Jr., Sr., III')
                                    ->maxLength(200),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('sex')
                                    ->label('Sex')
                                    ->options([
                                        'Male'   => 'Male',
                                        'Female' => 'Female',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\DatePicker::make('birthdate')
                                    ->label('Birthdate')
                                    ->required()
                                    ->native(false)
                                    ->maxDate(now()),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Academic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('program')
                                    ->label('Program')
                                    ->required()
                                    ->maxLength(200)
                                    ->placeholder('e.g., BSIT, BSCS, BSBA'),

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
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Scholarship Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('type_of_scholarship')
                                    ->label('Type of Scholarship')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('batch_no')
                                    ->label('Batch Number')
                                    ->numeric()
                                    ->placeholder('Optional'),

                                Forms\Components\TextInput::make('ip_group')
                                    ->label('IP Group')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('pwd')
                                    ->label('PWD')
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('benefit')
                                    ->label('Scholarship Benefit')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->placeholder('0.00'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_id')
                    ->label('Student ID')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? 'Not Assigned')
                    ->badge()
                    ->color(fn ($state) => $state ? 'primary' : 'gray'),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('extension_name')
                    ->label('Ext.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sex')
                    ->label('Sex')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Male'   => 'info',
                        'Female' => 'danger',
                        default  => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('birthdate')
                    ->label('Birthdate')
                    ->date('Y-m-d')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('program')
                    ->label('Program')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('year_level')
                    ->label('Year')
                    ->formatStateUsing(fn ($state) => match ((string) $state) {
                        '1'     => '1st Year',
                        '2'     => '2nd Year',
                        '3'     => '3rd Year',
                        '4'     => '4th Year',
                        '5'     => '5th Year',
                        default => $state,
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('type_of_scholarship')
                    ->label('Scholarship')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->wrap(),

                Tables\Columns\TextColumn::make('batch_no')
                    ->label('Batch No')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? 'Not Set')
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('ip_group')
                    ->label('IP Group')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('pwd')
                    ->label('PWD')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Yes'   => 'warning',
                        'No'    => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('benefit')
                    ->label('Benefit')
                    ->sortable()
                    ->formatStateUsing(function ($state): string {
                        if (is_null($state)) return 'Not set';
                        return \App\Models\ExamAttempt::resolveDiscount((int) $state)['label'];
                    })
                    ->badge()
                    ->color(function ($state): string {
                        if (is_null($state)) return 'gray';
                        return \App\Models\ExamAttempt::resolveDiscount((int) $state)['color'];
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'       => 'success',
                        'inactive'     => 'warning',
                        'graduated'    => 'info',
                        'discontinued' => 'danger',
                        'revoked'      => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('departmentHead.name')
                    ->label('Department Head')
                    ->placeholder('— Not Assigned —')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('revocation_reason')
                    ->label('Reason for Discontinuance')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('revoked_at')
                    ->label('Revoked On')
                    ->dateTime('M d, Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('term.school_year')
                    ->label('Term')
                    ->formatStateUsing(function ($state, $record) {
                        if (! $record->term) return '—';
                        return $record->term->school_year . ' ' . $record->term->semester;
                    })
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('view_accomplishment_reports')
                    ->label('View Accomplishment Reports')
                    ->icon('heroicon-o-document-check')
                    ->color('gray')
                    ->url(fn () => static::getUrl('accomplishment-reports'))
                    ->visible(fn () => ! static::isRestrictedToOwnScholars()),

                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(
                            new ScholarsExport(),
                            'scholars-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    })
                    ->visible(fn () => ! static::isRestrictedToOwnScholars()),

                Tables\Actions\Action::make('import')
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
                            ->helperText('Scholars will be tagged to this term. Duplicates in the same term will be skipped.'),

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

                            $import = new ScholarsImport((int) $data['term_id']);
                            Excel::import($import, $fullPath);

                            @unlink($fullPath);

                            $failures   = $import->failures()->count();
                            $duplicates = $import->getSkippedDuplicates();
                            $term       = Term::find($data['term_id']);
                            $termLabel  = $term
                                ? $term->school_year . ' — ' . $term->semester
                                : 'selected term';

                            $body = "Scholars imported into: {$termLabel}.";
                            if ($duplicates > 0) $body .= " {$duplicates} duplicate(s) skipped.";
                            if ($failures > 0)   $body .= " {$failures} row(s) failed validation.";

                            Notification::make()
                                ->title($failures > 0 || $duplicates > 0 ? 'Import Completed with Notices' : 'Import Successful')
                                ->color($failures > 0 ? 'warning' : 'success')
                                ->body($body)
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Failed')
                                ->danger()
                                ->body('Error: ' . $e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn () => ! static::isRestrictedToOwnScholars()),

                Tables\Actions\Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        return Excel::download(
                            new \App\Exports\ScholarsTemplateExport(),
                            'scholars-import-template.xlsx'
                        );
                    })
                    ->visible(fn () => ! static::isRestrictedToOwnScholars()),

                Tables\Actions\Action::make('print_institutional')
                    ->label('Print Institutional')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->visible(fn () => request()->query('activeTab') === 'institutional' && ! static::isRestrictedToOwnScholars())
                    ->url(fn () => route('scholars.print.institutional'))
                    ->openUrlInNewTab(),
            ])
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
                        'revoked'      => 'Revoked',
                    ])
                    ->multiple()
                    ->placeholder('All Statuses'),

                Tables\Filters\SelectFilter::make('type_of_scholarship')
                    ->label('Type of Scholarship')
                    ->options(function () {
                        return Scholars::query()
                            ->distinct()
                            ->whereNotNull('type_of_scholarship')
                            ->pluck('type_of_scholarship', 'type_of_scholarship')
                            ->sort();
                    })
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
                        return Scholars::query()
                            ->distinct()
                            ->whereNotNull('batch_no')
                            ->pluck('batch_no', 'batch_no')
                            ->sort()
                            ->reverse();
                    })
                    ->placeholder('All Batches'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(6)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action->hidden(),
            )
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('update_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'active'       => 'Active',
                                    'inactive'     => 'Inactive',
                                    'graduated'    => 'Graduated',
                                    'discontinued' => 'Discontinued',
                                    'revoked'      => 'Revoked',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Scholars $record, array $data) {
                            $record->update(['status' => $data['status']]);

                            Notification::make()
                                ->title('Status Updated')
                                ->success()
                                ->body('Scholar status updated to: ' . ucfirst($data['status']))
                                ->send();
                        })
                        ->visible(fn () => ! static::isRestrictedToOwnScholars()),

                    Tables\Actions\Action::make('revoke')
                        ->label('Revoke Scholarship')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Revoke Scholarship')
                        ->modalDescription('This will mark the scholar as revoked and free up their scholarship slot. This action can be reviewed later in the Revoked tab.')
                        ->modalSubmitActionLabel('Yes, Revoke')
                        ->form([
                            Forms\Components\Textarea::make('revocation_reason')
                                ->label('Reason for Discontinuance')
                                ->required()
                                ->rows(3)
                                ->placeholder('e.g. Failure to meet GPA requirement, disciplinary action...'),
                        ])
                        ->action(function (Scholars $record, array $data) {
                            $record->update([
                                'status'            => 'revoked',
                                'revocation_reason' => $data['revocation_reason'],
                                'revoked_at'        => now(),
                            ]);

                            $scholarshipType = TypeOfScholarship::where('name', $record->type_of_scholarship)->first();
                            $scholarshipType?->increment('slots');

                            Notification::make()
                                ->title('Scholarship Revoked')
                                ->danger()
                                ->body("{$record->first_name} {$record->last_name}'s scholarship has been revoked and the slot has been restored.")
                                ->send();
                        })
                        ->visible(fn (Scholars $record): bool => $record->status !== 'revoked' && ! static::isRestrictedToOwnScholars()),

                    Tables\Actions\Action::make('assign_department_head')
                        ->label('Assign Department Head')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->modalHeading('Assign Department Head')
                        ->modalDescription('This scholar\'s Department Head will approve and submit their DTR entries.')
                        ->modalSubmitActionLabel('Save Assignment')
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
                        ->fillForm(fn (Scholars $record): array => [
                            'department_head_id' => $record->department_head_id,
                        ])
                        ->action(function (Scholars $record, array $data): void {
                            $record->update(['department_head_id' => $data['department_head_id']]);

                            $headName = \App\Models\User::find($data['department_head_id'])?->name ?? 'the selected head';

                            Notification::make()
                                ->title('Department Head Assigned')
                                ->success()
                                ->body("{$record->first_name} {$record->last_name} is now assigned to {$headName}.")
                                ->send();
                        })
                        ->visible(fn (Scholars $record): bool =>
                            str_contains(strtolower(trim($record->type_of_scholarship ?? '')), 'student representative')
                            && ! static::isRestrictedToOwnScholars()
                        ),

                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\EditAction::make()
                        ->visible(fn () => ! static::isRestrictedToOwnScholars()),

                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => ! static::isRestrictedToOwnScholars()),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions(static::isRestrictedToOwnScholars() ? [] : [
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('update_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'active'       => 'Active',
                                    'inactive'     => 'Inactive',
                                    'graduated'    => 'Graduated',
                                    'discontinued' => 'Discontinued',
                                    'revoked'      => 'Revoked',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(fn ($r) => $r->update(['status' => $data['status']]));

                            Notification::make()
                                ->title('Status Updated')
                                ->success()
                                ->body('Selected scholars updated successfully.')
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->toArray();
                            return Excel::download(
                                new ScholarsExport($ids),
                                'scholars-selected-' . now()->format('Y-m-d') . '.xlsx'
                            );
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (static::isRestrictedToOwnScholars()) {
            $query->where('department_head_id', auth()->id());
        }

        if (request()->query('activeTab') === 'institutional') {
            $institutionalTypes = \App\Models\TypeOfScholarship::pluck('name')->toArray();
            $query->whereIn('type_of_scholarship', $institutionalTypes);
        }

        return $query;
    }

    public static function getTabs(): array
    {
        $institutionalTypes = \App\Models\TypeOfScholarship::pluck('name')->toArray();

        return [
            'all' => Tab::make('All Scholars')
                ->icon('heroicon-m-academic-cap')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', 'revoked')),

            'institutional' => Tab::make('Institutional Scholars')
                ->icon('heroicon-m-building-library')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereIn('type_of_scholarship', $institutionalTypes)
                    ->where('status', '!=', 'revoked'))
                ->badge(
                    Scholars::whereIn('type_of_scholarship', $institutionalTypes)
                        ->where('status', '!=', 'revoked')
                        ->count()
                )
                ->badgeColor('success'),

            'revoked' => Tab::make('Revoked Scholars')
                ->icon('heroicon-m-no-symbol')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'revoked'))
                ->badge(Scholars::where('status', 'revoked')->count())
                ->badgeColor('danger'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'                    => Pages\ListScholars::route('/'),
            'create'                   => Pages\CreateScholars::route('/create'),
            'edit'                     => Pages\EditScholars::route('/{record}/edit'),
            'accomplishment-reports'   => Pages\AccomplishmentReports::route('/accomplishment-reports'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship', 'department head']);
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
        return auth()->user()->hasAnyRole(['admin', 'scholarship', 'department head']);
    }
}       