<?php

namespace App\Filament\Pages;

use App\Models\Applicant;
use App\Models\CounselingAppointments;
use App\Models\CounselingLogforms;
use App\Models\ExamAttempt;
use App\Models\Personnels;
use App\Models\Referrals;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ArchivedRecords extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-s-archive-box';

    protected static ?string $navigationLabel = 'Archived Records';

    protected static string $view = 'filament.pages.archived-records';

    protected static ?string $title = 'Archived Records';

    protected static ?string $slug = 'archived-records';

    public string $activeTab = 'users';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'personnels') {
            return $table
                ->query(Personnels::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('first_name')
                        ->label('First Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('last_name')
                        ->label('Last Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('email')
                        ->label('Email')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('contact_no')
                        ->label('Contact No.')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Personnel')
                        ->modalDescription('This record will reappear in the main Personnels list.')
                        ->action(function (Personnels $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Personnel restored')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->label('Delete Permanently')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Permanently')
                        ->modalDescription('This cannot be undone. The record will be permanently removed.'),
                ]);
        }

        if ($this->activeTab === 'applicants') {
            return $table
                ->query(Applicant::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('first_name')
                        ->label('First Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('last_name')
                        ->label('Last Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('typeOfApplication.name')
                        ->label('Application Type')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Applicant')
                        ->modalDescription('This application will reappear in the main Applicants list.')
                        ->action(function (Applicant $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Applicant restored')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->label('Delete Permanently')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Permanently')
                        ->modalDescription('This cannot be undone. The application will be permanently removed.'),
                ]);
        }

        if ($this->activeTab === 'appointments') {
            return $table
                ->query(CounselingAppointments::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('full_name')
                        ->label('Student Name')
                        ->getStateUsing(fn ($record) => $record->full_name)
                        ->searchable(['first_name', 'last_name', 'middle_name']),

                    Tables\Columns\TextColumn::make('course_and_year')
                        ->label('Course & Year')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('counseling_date')
                        ->label('Date')
                        ->date('M d, Y')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected']),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Appointment')
                        ->modalDescription('This appointment will reappear in the main Counseling Appointments list.')
                        ->action(function (CounselingAppointments $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Appointment restored')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->label('Delete Permanently')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Permanently')
                        ->modalDescription('This cannot be undone. The appointment will be permanently removed.'),
                ]);
        }

        if ($this->activeTab === 'referrals') {
            return $table
                ->query(Referrals::query()->whereNotNull('archived_at'))
                ->columns([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Student Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('course_and_year')
                        ->label('Course & Year')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('referred_by')
                        ->label('Referred By')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'warning',
                        })
                        ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Referral')
                        ->modalDescription('This referral will reappear in the main Referrals list.')
                        ->action(function (Referrals $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Referral restored')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->label('Delete Permanently')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Permanently')
                        ->modalDescription('This cannot be undone. The referral will be permanently removed.'),
                ]);
        }

        if ($this->activeTab === 'logforms') {
            return $table
                ->query(CounselingLogforms::query()->whereNotNull('archived_at')->with('appointment'))
                ->columns([
                    Tables\Columns\TextColumn::make('appointment.first_name')
                        ->label('First Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('appointment.last_name')
                        ->label('Last Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('appointment.course_and_year')
                        ->label('Course & Year')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('remarks')
                        ->label('Remarks')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Logform')
                        ->modalDescription('This logform will reappear in the main Logforms list.')
                        ->action(function (CounselingLogforms $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Logform restored')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->label('Delete Permanently')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Permanently')
                        ->modalDescription('This cannot be undone. The logform will be permanently removed.'),
                ]);
        }

        if ($this->activeTab === 'examinees') {
            return $table
                ->query(ExamAttempt::query()->whereNotNull('archived_at')->with(['exam', 'user']))
                ->columns([
                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Name')
                        ->searchable()
                        ->sortable(),

                    Tables\Columns\TextColumn::make('exam.title')
                        ->label('Exam')
                        ->searchable(),

                    Tables\Columns\TextColumn::make('percentage')
                        ->label('Percentage')
                        ->formatStateUsing(fn ($state) => "{$state}%")
                        ->sortable(),

                    Tables\Columns\TextColumn::make('completed_at')
                        ->label('Completed')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),

                    Tables\Columns\TextColumn::make('archived_at')
                        ->label('Archived On')
                        ->dateTime('M d, Y h:i A')
                        ->sortable(),
                ])
                ->actions([
                    Tables\Actions\Action::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Examinee Record')
                        ->modalDescription('This exam attempt will reappear in the main Examinees list.')
                        ->action(function (ExamAttempt $record): void {
                            $record->update(['archived_at' => null]);

                            Notification::make()
                                ->title('Examinee record restored')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->label('Delete Permanently')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Permanently')
                        ->modalDescription('This cannot be undone. The exam attempt will be permanently removed.'),
                ]);
        }

        return $table
            ->query(User::query()->whereNotNull('archived_at')->with(['personnel', 'role']))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Archived On')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Restore User')
                    ->modalDescription('This user will regain panel access and reappear in the main Users list.')
                    ->action(function (User $record): void {
                        $record->update(['archived_at' => null]);

                        Notification::make()
                            ->title('User restored')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Delete Permanently')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Permanently')
                    ->modalDescription('This cannot be undone. The account will be permanently removed.'),
            ]);
    }
}