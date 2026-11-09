<?php

namespace App\Support;

use App\Models\CounselingAppointments;
use App\Models\ReferralInvitation;

class SlotAvailability
{
    /**
     * Time slot IDs already taken on a date, by appointments (incl. follow-ups)
     * and referral invitations. Cancelled/rejected appointments, declined
     * invitations and archived records free their slot.
     */
    public static function reservedSlotIds(
        $date,
        ?int $exceptAppointmentId = null,
        ?int $exceptInvitationId = null
    ): array {
        if (! $date) {
            return [];
        }

        $appointments = CounselingAppointments::query()
            ->whereDate('counseling_date', $date)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->whereNull('archived_at')
            ->when($exceptAppointmentId, fn ($q) => $q->where('id', '!=', $exceptAppointmentId))
            ->pluck('time_slot_id');

        $invitations = ReferralInvitation::query()
            ->whereDate('session_date', $date)
            ->where(fn ($q) => $q->whereNull('status')->orWhere('status', '!=', 'declined'))
            ->whereHas('referral', fn ($q) => $q->whereNull('archived_at'))
            ->when($exceptInvitationId, fn ($q) => $q->where('id', '!=', $exceptInvitationId))
            ->pluck('time_slot_id');

        return $appointments
            ->merge($invitations)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public static function isReserved(
        $date,
        $slotId,
        ?int $exceptAppointmentId = null,
        ?int $exceptInvitationId = null
    ): bool {
        return in_array(
            (int) $slotId,
            self::reservedSlotIds($date, $exceptAppointmentId, $exceptInvitationId),
            true
        );
    }
}