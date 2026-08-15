<?php

namespace App\Notifications;

use App\Models\ReferralInvitation;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReferralInvitationResponseNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected ReferralInvitation $invitation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $student  = $this->invitation->referral->name ?? 'A student';
        $date     = $this->invitation->session_date?->format('F d, Y') ?? 'the scheduled date';
        $slot     = $this->invitation->timeSlot?->name ?? '';
        $accepted = $this->invitation->status === 'accepted';

        return FilamentNotification::make()
            ->title($accepted ? 'Invitation Accepted' : 'Invitation Declined')
            ->body(
                ($accepted ? "{$student} accepted" : "{$student} declined") .
                " the counseling session on {$date}" . ($slot ? " ({$slot})" : '') . "."
            )
            ->icon($accepted ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            ->color($accepted ? 'success' : 'danger')
            ->getDatabaseMessage();
    }

    public static function send(ReferralInvitation $invitation): void
    {
        $recipients = User::whereHas('role', fn ($q) =>
            $q->whereIn('name', ['Admin', 'Guidance'])
        )->get();

        foreach ($recipients as $user) {
            $user->notify(new self($invitation));
        }
    }
}