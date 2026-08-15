<?php

namespace App\Filament\Resources\ReferralsResource\Pages;

use App\Filament\Resources\ReferralsResource;
use App\Models\Referrals;
use App\Models\AppointmentNotification;
use App\Traits\LogsCustomActivity;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ListReferrals extends ListRecords
{
    use LogsCustomActivity;

    protected static string $resource = ReferralsResource::class;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery()->whereNull('archived_at');

        if ($this->activeTab === 'endorsed') {
            $query->whereHas('endorsement')->with(['endorsement.personnel']);
        }

        if ($this->activeTab === 'invited') {
            $query->whereHas('invitation')->with(['invitation.personnel']);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Referrals')
                ->icon('heroicon-o-arrow-right-circle'),

            'endorsed' => Tab::make('Endorsements')
                ->icon('heroicon-o-paper-airplane')
                ->badgeColor('info')
                ->badge(fn () => Referrals::whereNull('archived_at')->whereHas('endorsement')->count()),

            'invited' => Tab::make('Invitations')
                ->icon('heroicon-o-envelope')
                ->badgeColor('warning')
                ->badge(fn () => Referrals::whereNull('archived_at')->whereHas('invitation')->count()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // ── Shared columns ─────────────────────────────────────
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->hidden(fn () => in_array($this->activeTab, ['endorsed', 'invited'])),

                Tables\Columns\TextColumn::make('name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->numeric()
                    ->sortable()
                    ->hidden(fn () => in_array($this->activeTab, ['endorsed', 'invited'])),

                Tables\Columns\TextColumn::make('course_and_year')
                    ->label('Course & Year')
                    ->searchable()
                    ->sortable(),

                // ── Referrals-only columns ──────────────────────────────
                Tables\Columns\TextColumn::make('referred_by')
                    ->label('Referred By')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->hidden(fn () => in_array($this->activeTab, ['endorsed', 'invited'])),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable()
                    ->hidden(fn () => in_array($this->activeTab, ['endorsed', 'invited'])),

                Tables\Columns\TextColumn::make('logforms_count')
                    ->label('Logforms')
                    ->counts('logforms')
                    ->badge()
                    ->color('success')
                    ->hidden(fn () => in_array($this->activeTab, ['endorsed', 'invited'])),

                // ── Endorsements-only columns ───────────────────────────
                Tables\Columns\TextColumn::make('endorsement.to_where')
                    ->label('Endorsed To')
                    ->default('-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),

                Tables\Columns\TextColumn::make('endorsement.date')
                    ->label('Endorse Date')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('M d, Y') : '-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),

                Tables\Columns\TextColumn::make('endorsement.issue')
                    ->label('Issue')
                    ->limit(50)
                    ->default('-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),

                Tables\Columns\TextColumn::make('endorsed_by')
                    ->label('Endorsed By')
                    ->getStateUsing(fn ($record) => $record->endorsement?->personnel
                        ? trim("{$record->endorsement->personnel->first_name} {$record->endorsement->personnel->middle_name} {$record->endorsement->personnel->last_name}")
                        : '-')
                    ->hidden(fn () => $this->activeTab !== 'endorsed'),

                // ── Invitations-only columns ────────────────────────────
                Tables\Columns\TextColumn::make('invitation.session_date')
                    ->label('Session Date')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('M d, Y') : '-')
                    ->hidden(fn () => $this->activeTab !== 'invited'),

                Tables\Columns\TextColumn::make('invitation_time_slot')
                    ->label('Time Slot')
                    ->getStateUsing(fn ($record) => $record->invitation?->timeSlot?->name ?? '-')
                    ->hidden(fn () => $this->activeTab !== 'invited'),

                Tables\Columns\TextColumn::make('invitation.purpose')
                    ->label('Purpose')
                    ->limit(50)
                    ->default('-')
                    ->hidden(fn () => $this->activeTab !== 'invited'),

                Tables\Columns\TextColumn::make('invitation_counselor')
                    ->label('Counselor')
                    ->getStateUsing(fn ($record) => $record->invitation?->personnel
                        ? trim("{$record->invitation->personnel->first_name} {$record->invitation->personnel->middle_name} {$record->invitation->personnel->last_name}")
                        : '-')
                    ->hidden(fn () => $this->activeTab !== 'invited'),

                Tables\Columns\TextColumn::make('invitation.status')
                    ->label('Invitation Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'accepted' => 'success',
                        'declined' => 'danger',
                        default    => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : '-')
                    ->hidden(fn () => $this->activeTab !== 'invited'),
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
                        \Filament\Forms\Components\DatePicker::make('date_from')
                            ->label('From Date')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('date_until')
                            ->label('Until Date')
                            ->native(false),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['date_from'] ?? null, fn ($q) => $q->whereDate('date', '>=', $data['date_from']))
                        ->when($data['date_until'] ?? null, fn ($q) => $q->whereDate('date', '<=', $data['date_until']))),

                Tables\Filters\SelectFilter::make('referred_by')
                    ->label('Referred By')
                    ->options(fn () => Referrals::query()
                        ->distinct()
                        ->orderBy('referred_by')
                        ->pluck('referred_by', 'referred_by')
                        ->toArray()),
            ])
            ->actions([
                // ── Referrals tab actions ───────────────────────────────
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\EditAction::make()
                        ->url(fn ($record) => ReferralsResource::getUrl('edit', [
                            'record' => $record->id,
                            'tab'    => '-referral-information-tab',
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

                            $this->logCustomActivity(
                                $record,
                                'referrals',
                                'approved',
                                "Approved referral for {$record->name}"
                            );

                            // ── notify the student ──────────────────────
                            AppointmentNotification::notifyReferralStatus(
                                $record,
                                'approved',
                                "Your referral has been approved."
                            );

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

                            $this->logCustomActivity(
                                $record,
                                'referrals',
                                'rejected',
                                "Rejected referral for {$record->name}"
                            );

                            // ── notify the student ──────────────────────
                            AppointmentNotification::notifyReferralStatus(
                                $record,
                                'rejected',
                                "Your referral has been rejected."
                            );

                            Notification::make()
                                ->title('Referral Rejected')
                                ->body("The referral for {$record->name} has been rejected.")
                                ->danger()
                                ->send();
                        }),

                    Tables\Actions\Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Referral')
                        ->modalDescription('This will hide the referral from this list. You can restore it later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function (Referrals $record): void {
                            $record->update(['archived_at' => now()]);

                            $this->logCustomActivity(
                                $record,
                                'referrals',
                                'archived',
                                "Archived referral for {$record->name}"
                            );

                            Notification::make()
                                ->title('Referral archived')
                                ->success()
                                ->send();
                        }),

                    
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
                ->hidden(fn () => in_array($this->activeTab, ['endorsed', 'invited'])),

                // ── Endorsements tab actions ────────────────────────────
                // NOTE: action names suffixed with "_endorsement" to avoid
                // colliding with identically-named actions (edit, archive,
                // view) in the other ActionGroups on this table — Filament
                // requires unique action names across the whole table, even
                // if only one group is visible at a time via ->hidden().
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('print')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->url(fn ($record) => route('referral.endorsement.print', $record->id))
                        ->openUrlInNewTab(),

                    Tables\Actions\EditAction::make('edit_endorsement')
                        ->url(fn ($record) => ReferralsResource::getUrl('edit', [
                            'record' => $record->id,
                            'tab'    => '-endorsement-tab',
                        ])),

                    Tables\Actions\Action::make('archive_endorsement')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Referral')
                        ->modalDescription('This will hide the referral from this list. You can restore it later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function (Referrals $record): void {
                            $record->update(['archived_at' => now()]);

                            $this->logCustomActivity(
                                $record,
                                'referrals',
                                'archived',
                                "Archived referral for {$record->name}"
                            );

                            Notification::make()
                                ->title('Referral archived')
                                ->success()
                                ->send();
                        }),


                    Tables\Actions\ViewAction::make('view_endorsement')
                        ->infolist([
                            \Filament\Infolists\Components\Section::make('Student Information')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('name')
                                        ->label('Student Name'),

                                    \Filament\Infolists\Components\TextEntry::make('course_and_year')
                                        ->label('Course & Year'),
                                ])
                                ->columns(2),

                            \Filament\Infolists\Components\Section::make('Endorsement Details')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('endorsement.to_where')
                                        ->label('Endorsed To')
                                        ->default('-'),

                                    \Filament\Infolists\Components\TextEntry::make('endorsement.from_where')
                                        ->label('From')
                                        ->default('-'),

                                    \Filament\Infolists\Components\TextEntry::make('endorsement.date')
                                        ->label('Endorse Date')
                                        ->formatStateUsing(fn ($state) => $state
                                            ? \Carbon\Carbon::parse($state)->format('M d, Y')
                                            : '-'),

                                    \Filament\Infolists\Components\TextEntry::make('endorsed_by')
                                        ->label('Endorsed By')
                                        ->getStateUsing(fn ($record) => $record->endorsement?->personnel
                                            ? trim("{$record->endorsement->personnel->first_name} {$record->endorsement->personnel->middle_name} {$record->endorsement->personnel->last_name}")
                                            : '-'),

                                    \Filament\Infolists\Components\TextEntry::make('endorsement.issue')
                                        ->label('Issue')
                                        ->default('-')
                                        ->columnSpanFull(),

                                    \Filament\Infolists\Components\TextEntry::make('endorsement.received_by')
                                        ->label('Received By')
                                        ->default('-'),

                                    \Filament\Infolists\Components\TextEntry::make('endorsement.receive_date')
                                        ->label('Date Received')
                                        ->formatStateUsing(fn ($state) => $state
                                            ? \Carbon\Carbon::parse($state)->format('M d, Y')
                                            : '-'),
                                ])
                                ->columns(2),
                        ]),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
                ->hidden(fn () => $this->activeTab !== 'endorsed'),

                // ── Invitations tab actions ─────────────────────────────
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make('edit_invitation')
                        ->url(fn ($record) => ReferralsResource::getUrl('edit', [
                            'record' => $record->id,
                            'tab'    => '-invitation-tab',
                        ])),

                    Tables\Actions\Action::make('mark_accepted')
                        ->label('Mark Accepted')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Mark Invitation as Accepted')
                        ->modalDescription('Are you sure you want to mark this invitation as accepted?')
                        ->modalSubmitActionLabel('Yes, Accept')
                        ->visible(fn (Referrals $record): bool => $record->invitation?->status !== 'accepted')
                        ->action(function (Referrals $record): void {
                            $record->invitation()?->update(['status' => 'accepted']);

                            $this->logCustomActivity(
                                $record,
                                'referrals',
                                'invitation_accepted',
                                "Marked invitation as accepted for {$record->name}"
                            );

                            Notification::make()
                                ->title('Invitation Accepted')
                                ->body("The invitation for {$record->name} has been marked as accepted.")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('mark_declined')
                        ->label('Mark Declined')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Mark Invitation as Declined')
                        ->modalDescription('Are you sure you want to mark this invitation as declined?')
                        ->modalSubmitActionLabel('Yes, Decline')
                        ->visible(fn (Referrals $record): bool => $record->invitation?->status !== 'declined')
                        ->action(function (Referrals $record): void {
                            $record->invitation()?->update(['status' => 'declined']);

                            $this->logCustomActivity(
                                $record,
                                'referrals',
                                'invitation_declined',
                                "Marked invitation as declined for {$record->name}"
                            );

                            Notification::make()
                                ->title('Invitation Declined')
                                ->body("The invitation for {$record->name} has been marked as declined.")
                                ->danger()
                                ->send();
                        }),

                    Tables\Actions\Action::make('archive_invitation')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Referral')
                        ->modalDescription('This will hide the referral from this list. You can restore it later from Settings → Archived Records.')
                        ->modalSubmitActionLabel('Yes, Archive')
                        ->action(function (Referrals $record): void {
                            $record->update(['archived_at' => now()]);

                            $this->logCustomActivity(
                                $record,
                                'referrals',
                                'archived',
                                "Archived referral for {$record->name}"
                            );

                            Notification::make()
                                ->title('Referral archived')
                                ->success()
                                ->send();
                        }),


                    Tables\Actions\ViewAction::make('view_invitation')
                        ->infolist([
                            \Filament\Infolists\Components\Section::make('Student Information')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('name')
                                        ->label('Student Name'),

                                    \Filament\Infolists\Components\TextEntry::make('course_and_year')
                                        ->label('Course & Year'),
                                ])
                                ->columns(2),

                            \Filament\Infolists\Components\Section::make('Invitation Details')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('invitation.session_date')
                                        ->label('Session Date')
                                        ->formatStateUsing(fn ($state) => $state
                                            ? \Carbon\Carbon::parse($state)->format('M d, Y')
                                            : '-'),

                                    \Filament\Infolists\Components\TextEntry::make('invitation_time_slot')
                                        ->label('Time Slot')
                                        ->getStateUsing(fn ($record) => $record->invitation?->timeSlot?->name ?? '-'),

                                    \Filament\Infolists\Components\TextEntry::make('invitation.status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn ($state): string => match ($state) {
                                            'accepted' => 'success',
                                            'declined' => 'danger',
                                            default    => 'warning',
                                        })
                                        ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : '-'),

                                    \Filament\Infolists\Components\TextEntry::make('invitation_counselor')
                                        ->label('Counselor')
                                        ->getStateUsing(fn ($record) => $record->invitation?->personnel
                                            ? trim("{$record->invitation->personnel->first_name} {$record->invitation->personnel->middle_name} {$record->invitation->personnel->last_name}")
                                            : '-'),

                                    \Filament\Infolists\Components\TextEntry::make('invitation.purpose')
                                        ->label('Purpose')
                                        ->default('-')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
                ->hidden(fn () => $this->activeTab !== 'invited'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}