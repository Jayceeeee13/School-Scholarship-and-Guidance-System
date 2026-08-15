<?php

namespace App\Filament\Resources;

use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use App\Filament\Resources\StudentsResource\Pages;
use App\Models\Students;
use App\Models\Term;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class StudentsResource extends Resource
{
    protected static ?string $model = Students::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Generals';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ── Basic Information ──────────────────────────────────────
                Forms\Components\Section::make('Student Information')
                    ->description('Basic identification details of the student.')
                    ->icon('heroicon-o-identification')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('student_id')
                            ->label('Student ID')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(1),
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

                // ── Personal Details ───────────────────────────────────────
                Forms\Components\Section::make('Personal Details')
                    ->description('Demographic and contact information.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('M d, Y'),
                        Forms\Components\Select::make('gender_id')
                            ->label('Sex')
                            ->relationship('gender', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
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
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(200),
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(500)
                                ->extraInputAttributes([
                                'style' => 'text-transform: capitalize;',
                                ])
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('zipcode')
                            ->required()
                            ->maxLength(200),
                    ]),

                // ── Academic Information ───────────────────────────────────
                Forms\Components\Section::make('Academic Information')
                    ->description('Program enrollment and academic standing.')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('year_level')
                            ->required()
                            ->maxLength(200),
                    ]),

                // ── Parents / Guardians ────────────────────────────────────
                Forms\Components\Section::make('Parents / Guardians')
                    ->description("Father's and mother's information.")
                    ->icon('heroicon-o-users')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Fieldset::make("Father's Information")
                            ->columns(3)
                            ->schema([
                                TextInput::make('fathers_firstname')
    ->label('Fathers First Name')
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

TextInput::make('fathers_middlename')
    ->label('Fathers Middle Name')
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

TextInput::make('fathers_lastname')
    ->label('Fathers Last Name')
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
                            ]),

                        Forms\Components\Fieldset::make("Mother's Information")
                            ->columns(3)
                            ->schema([
                                TextInput::make('mothers_firstname')
    ->label('Mothers First Name')
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

TextInput::make('mothers_middlename')
    ->label('Mothers Middle Name')
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

TextInput::make('mothers_lastname')
    ->label('Mothers 
    Last Name')
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
                            ]),
                    ]),

                // ── Additional Information ─────────────────────────────────
                Forms\Components\Section::make('Additional Information')
                    ->description('Optional fields for disability and indigenous people group.')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('disability')
                            ->maxLength(200)
                            ->default(null),
                        Forms\Components\TextInput::make('ip_group')
                            ->label('Indigenous People Group')
                            ->maxLength(500)
                            ->default(null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_id')
                    ->label('Student ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('extension_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender.name')
                    ->label('Sex')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year_level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_no')
                    ->label('Contact No.')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fathers_firstname')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fathers_middlename')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fathers_lastname')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mothers_firstname')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mothers_middlename')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mothers_lastname')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zipcode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('disability')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ip_group')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(
                            new StudentsExport(),
                            'students-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }),

                Tables\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('term_id')
                            ->label('School Year & Semester')
                            ->options(
                                \App\Models\Term::orderByDesc('school_year')
                                    ->orderBy('semester')
                                    ->get()
                                    ->mapWithKeys(fn ($term) => [
                                        $term->id => "{$term->school_year} — {$term->semester}"
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->helperText('Select the term this batch of students belongs to.'),
                        Forms\Components\FileUpload::make('file')
                            ->label('Excel File (.xlsx)')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                            ])
                            ->maxSize(10240)
                            ->required()
                            ->helperText('Use the template format. Data must start from row 4.'),
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

                            $import = new StudentsImport((int) $data['term_id']);
                            Excel::import($import, $fullPath);

                            @unlink($fullPath);

                            $failures = $import->failures();
                            $errors   = $import->errors();

                            if ($failures->count() > 0 || $errors->count() > 0) {
                                $lines = [];
                                foreach ($failures as $failure) {
                                    $lines[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                                }
                                foreach ($errors as $error) {
                                    $lines[] = 'Error: ' . $error->getMessage();
                                }

                                $total   = $failures->count() + $errors->count();
                                $preview = implode("\n", array_slice($lines, 0, 5));
                                $more    = count($lines) > 5 ? (' …and ' . (count($lines) - 5) . ' more.') : '';

                                Notification::make()
                                    ->title("{$total} row(s) skipped")
                                    ->warning()
                                    ->body($preview . $more)
                                    ->persistent()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Import Successful')
                                    ->success()
                                    ->body('All students imported successfully.')
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

                Tables\Actions\Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        return Excel::download(
                            new StudentsTemplateExport(),
                            'students-import-template.xlsx'
                        );
                    }),
            ])
            ->filters([
                // ── Term filter (school year + semester via terms table) ────
                Tables\Filters\SelectFilter::make('term_id')
                    ->label('School Year & Semester')
                    ->options(
                        Term::orderByDesc('school_year')
                            ->orderBy('semester')
                            ->get()
                            ->mapWithKeys(fn ($term) => [
                                $term->id => "{$term->school_year} — {$term->semester}"
                            ])
                    )
                    ->placeholder('All Terms')
                    ->searchable(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->toArray();
                            return Excel::download(
                                new StudentsExport($ids),
                                'students-selected-' . now()->format('Y-m-d') . '.xlsx'
                            );
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudents::route('/create'),
            'edit'   => Pages\EditStudents::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'scholarship']);
    }
}