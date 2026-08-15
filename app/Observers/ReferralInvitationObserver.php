<?php
// app/Observers/ReferralInvitationObserver.php

namespace App\Observers;

use App\Models\ReferralInvitation;
use App\Models\AppointmentNotification;
use App\Notifications\ReferralInvitationResponseNotification;

class ReferralInvitationObserver
{
    public function created(ReferralInvitation $invitation): void
    {
        $date       = $invitation->session_date?->format('F d, Y') ?? 'a scheduled date';
        $slot       = $invitation->timeSlot?->name ?? '';
        $slotSuffix = $slot ? " at {$slot}" : '';

        AppointmentNotification::notifyReferral(
            $invitation,
            'referral_invitation',
            "You have been invited for a counseling session on {$date}{$slotSuffix}. Please check your guidance portal."
        );
    }

    public function updated(ReferralInvitation $invitation): void
    {
        if (!$invitation->wasChanged('status')) return;

        $date       = $invitation->session_date?->format('F d, Y') ?? 'your scheduled date';
        $slot       = $invitation->timeSlot?->name ?? '';
        $slotSuffix = $slot ? " at {$slot}" : '';

        $message = match ($invitation->status) {
            'accepted' => "Your counseling session on {$date}{$slotSuffix} has been confirmed.",
            'declined' => "Your counseling session on {$date}{$slotSuffix} was declined. Please contact the guidance office.",
            default    => "Your counseling invitation status was updated to: {$invitation->status}.",
        };

        AppointmentNotification::notifyReferral($invitation, $invitation->status, $message);

        if (in_array($invitation->status, ['accepted', 'declined'])) {
            ReferralInvitationResponseNotification::send($invitation);
        }
    }
}