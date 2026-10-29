<?php

namespace App\Filament\Resources\ApplicantResource\Pages;

use App\Filament\Resources\ApplicantResource;
use App\Models\ExamAttempt;
use App\Models\Students;
use App\Models\Scholars;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewApplicant extends ViewRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square'),

            Actions\Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Application')
                ->modalDescription(fn ($record) => "Are you sure you want to approve {$record->first_name} {$record->last_name}'s application? This will create a scholar record.")
                ->modalSubmitActionLabel('Yes, Approve')
                ->action(function ($record) {
                    $record->update(['status' => 'approved']);

                    // Fetch student_id from students table via user_id
                    $student         = Students::where('user_id', $record->user_id)->first();
                    $studentId       = $student ? $student->student_id : null;

                    $program         = $record->program ? $record->program->name : '';
                    $sex             = $record->gender ? $record->gender->name : '';
                    $scholarshipType = $record->typeOfScholarship ? $record->typeOfScholarship->name : '';

                    Scholars::create([
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
                        'batch_no'            => date('Y'),
                        'benefit'             => $record->benefit,
                        'status'              => 'active',
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Application Approved')
                        ->success()
                        ->body("{$record->first_name} {$record->last_name} is now a scholar" . ($studentId ? " (Student ID: {$studentId})" : '.'))
                        ->send();
                })
                ->visible(fn ($record): bool => $record->status === 'pending')
                ->disabled(fn ($record): bool => ! ApplicantResource::hasCompleteRequirements($record))
                ->tooltip(fn ($record): ?string =>
                    ! ApplicantResource::hasCompleteRequirements($record)
                        ? 'Cannot approve: Requirements incomplete'
                        : null
                ),

            Actions\Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Application')
                ->modalDescription(fn ($record) => "Are you sure you want to reject {$record->first_name} {$record->last_name}'s application?")
                ->modalSubmitActionLabel('Yes, Reject')
                ->action(function ($record) {
                    $record->update(['status' => 'rejected']);

                    \Filament\Notifications\Notification::make()
                        ->title('Application Rejected')
                        ->danger()
                        ->body("{$record->first_name} {$record->last_name}'s application has been rejected.")
                        ->send();
                })
                ->visible(fn ($record): bool => $record->status === 'pending'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Application Status')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->description('Current status and requirements progress')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending'  => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                                    ->icon(fn (string $state): string => match ($state) {
                                        'pending'  => 'heroicon-o-clock',
                                        'approved' => 'heroicon-o-check-circle',
                                        'rejected' => 'heroicon-o-x-circle',
                                    }),

                                Infolists\Components\TextEntry::make('requirements_progress')
                                ->label('Requirements Progress')
                                ->state(function ($record) {
                                    $p = ApplicantResource::requirementProgress($record);

                                    return "{$p['submitted']} / {$p['total']}";
                                })
                                ->badge()
                                ->color(function ($record) {
                                    $p = ApplicantResource::requirementProgress($record);

                                    if ($p['total'] === 0) return 'gray';
                                    if ($p['submitted'] >= $p['total']) return 'success';
                                    if ($p['submitted'] > 0) return 'warning';
                                    return 'danger';
                                })
                                ->icon('heroicon-o-document-check'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Applied On')
                                    ->dateTime('M d, Y h:i A')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Infolists\Components\Section::make('Personal Information')
                    ->icon('heroicon-o-user')
                    ->description('Applicant personal details')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                ImageEntry::make('picture')
                                    ->label('2x2 Picture')
                                    ->disk('public')
                                    ->circular()
                                    ->size(120)
                                    ->defaultImageUrl(fn () => 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#9ca3af">'
        . '<path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd"/>'
        . '</svg>'
    ))
                                    ->columnSpan(1),

                                Infolists\Components\Grid::make(1)
                                    ->schema([
                                        Infolists\Components\Grid::make(2)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('first_name')
                                                    ->label('First Name')
                                                    ->icon('heroicon-o-user')
                                                    ->size('lg')
                                                    ->weight('bold'),

                                                Infolists\Components\TextEntry::make('middle_name')
                                                    ->label('Middle Name')
                                                    ->icon('heroicon-o-user')
                                                    ->placeholder('N/A'),
                                            ]),

                                        Infolists\Components\Grid::make(2)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('last_name')
                                                    ->label('Last Name')
                                                    ->icon('heroicon-o-user')
                                                    ->size('lg')
                                                    ->weight('bold'),

                                                Infolists\Components\TextEntry::make('extension_name')
                                                    ->label('Extension')
                                                    ->icon('heroicon-o-user')
                                                    ->placeholder('N/A')
                                                    ->badge()
                                                    ->color('gray'),
                                            ]),
                                    ])
                                    ->columnSpan(3),
                            ]),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('birthdate')
                                    ->label('Date of Birth')
                                    ->date('M d, Y')
                                    ->icon('heroicon-o-cake'),

                                Infolists\Components\TextEntry::make('age')
                                    ->label('Age')
                                    ->suffix(' years old')
                                    ->icon('heroicon-o-calendar-days'),

                                Infolists\Components\TextEntry::make('gender.name')
                                    ->label('Gender')
                                    ->icon('heroicon-o-user-circle')
                                    ->badge()
                                    ->color(fn ($record): string => match ($record->gender?->name) {
                                        'Male'   => 'info',
                                        'Female' => 'pink',
                                        default  => 'gray',
                                    }),
                            ]),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('contact_no')
                                    ->label('Contact Number')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage('Phone number copied!')
                                    ->copyMessageDuration(1500),

                                Infolists\Components\TextEntry::make('religion')
                                    ->label('Religion')
                                    ->icon('heroicon-o-star')
                                    ->placeholder('N/A'),

                                Infolists\Components\TextEntry::make('facebook_account')
                                    ->label('Facebook Account')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable()
                                    ->copyMessage('Facebook account copied!')
                                    ->placeholder('N/A'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Infolists\Components\Section::make('Academic Information')
                    ->icon('heroicon-o-academic-cap')
                    ->description('Program, year level, and scholarship details')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('typeOfApplication.name')
                                    ->label('Application Type')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-document-text'),

                                Infolists\Components\TextEntry::make('program.name')
                                    ->label('Program')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-book-open'),

                                Infolists\Components\TextEntry::make('year_level')
                                    ->label('Year Level')
                                    ->formatStateUsing(fn ($state) => match((string)$state) {
                                        '1' => '1st Year',
                                        '2' => '2nd Year',
                                        '3' => '3rd Year',
                                        '4' => '4th Year',
                                        '5' => '5th Year',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-academic-cap'),

                                Infolists\Components\TextEntry::make('typeOfScholarship.name')
                                    ->label('Scholarship Type')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-trophy'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Infolists\Components\Section::make('Interview & Benefits')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->description('Interview notes and scholarship benefit details')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('interview')
                                    ->label('Interview Notes')
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('No interview notes yet')
                                    ->markdown()
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('benefit')
                                    ->label('Scholarship Benefit')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->formatStateUsing(function ($state): string {
                                        if (is_null($state)) {
                                            return 'Not set';
                                        }
                                        return ExamAttempt::resolveDiscount((int) $state)['label'];
                                    })
                                    ->badge()
                                    ->color(function ($state): string {
                                        if (is_null($state)) {
                                            return 'gray';
                                        }
                                        return ExamAttempt::resolveDiscount((int) $state)['color'];
                                    }),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                Infolists\Components\Section::make('Family Information')
                    ->icon('heroicon-o-user-group')
                    ->description('Family and guardian contact details')
                    ->schema([
                        Infolists\Components\Fieldset::make('Father\'s Information')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('fathers_name')
                                            ->label('Name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('N/A'),

                                        Infolists\Components\TextEntry::make('fathers_contact_no')
                                            ->label('Contact Number')
                                            ->icon('heroicon-o-phone')
                                            ->copyable()
                                            ->copyMessage('Phone number copied!')
                                            ->placeholder('N/A'),
                                    ]),
                            ]),

                        Infolists\Components\Fieldset::make('Mother\'s Information')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('mothers_name')
                                            ->label('Name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('N/A'),

                                        Infolists\Components\TextEntry::make('mothers_contact_no')
                                            ->label('Contact Number')
                                            ->icon('heroicon-o-phone')
                                            ->copyable()
                                            ->copyMessage('Phone number copied!')
                                            ->placeholder('N/A'),
                                    ]),
                            ]),

                        Infolists\Components\Fieldset::make('Guardian Information')
                            ->schema([
                                Infolists\Components\Grid::make(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('guardian')
                                            ->label('Name')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('N/A'),

                                        Infolists\Components\TextEntry::make('guardian_contact_no')
                                            ->label('Contact Number')
                                            ->icon('heroicon-o-phone')
                                            ->copyable()
                                            ->copyMessage('Phone number copied!')
                                            ->placeholder('N/A'),
                                    ]),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }
}