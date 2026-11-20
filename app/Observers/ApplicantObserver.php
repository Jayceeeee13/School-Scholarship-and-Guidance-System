<?php

namespace App\Observers;

use App\Models\Applicant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Sends the student a notification (website bell + mobile app) whenever their
 * scholarship application is approved or rejected — from the Approve/Reject
 * buttons, the bulk actions, or the Edit page. Uses the same
 * `appointment_notifications` table as the "scholarship revoked" notice.
 */
class ApplicantObserver
{
    public function updated(Applicant $applicant): void
    {
        // Only react when the status itself just changed to a decision.
        if (! $applicant->wasChanged('status') || ! $applicant->user_id) {
            return;
        }

        if (! in_array($applicant->status, ['approved', 'rejected'], true)) {
            return;
        }

        $typeName = $applicant->typeOfScholarship?->name;
        $label    = $typeName ? "{$typeName} scholarship" : 'scholarship';

        if ($applicant->status === 'approved') {
            $type    = 'scholarship_approved';
            $message = "Congratulations! Your {$label} application has been approved.";
        } else {
            $type    = 'scholarship_rejected';
            $message = "Your {$label} application was not approved."
                . ($applicant->rejection_reason ? " Reason: {$applicant->rejection_reason}." : '')
                . ' Please contact the scholarship office for more information.';
        }

        try {
            DB::table('appointment_notifications')->insert([
                'user_id'                   => $applicant->user_id,
                'counseling_appointment_id' => null,
                'type'                      => $type,
                'message'                   => $message,
                'created_at'                => now(),
                'updated_at'                => now(),
            ]);
        } catch (\Throwable $e) {
            // Never block an approval/rejection because the notice failed.
            Log::error('Applicant decision notification failed', [
                'applicant_id' => $applicant->id,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}