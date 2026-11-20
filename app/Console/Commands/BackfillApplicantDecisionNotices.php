<?php

namespace App\Console\Commands;

use App\Models\Applicant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time helper: creates the "approved / not approved" notification for
 * applications that were decided BEFORE the ApplicantObserver existed.
 * Safe to run more than once — a student never gets the same notice twice.
 *
 *   php artisan applicants:backfill-notices
 */
class BackfillApplicantDecisionNotices extends Command
{
    protected $signature = 'applicants:backfill-notices';

    protected $description = 'Create bell notifications for already approved/rejected scholarship applications';

    public function handle(): int
    {
        $created = 0;

        Applicant::query()
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNull('archived_at')
            ->whereNotNull('user_id')
            ->with('typeOfScholarship')
            ->get()
            ->each(function (Applicant $a) use (&$created) {
                $type = "scholarship_{$a->status}";

                $alreadyHasNotice = DB::table('appointment_notifications')
                    ->where('user_id', $a->user_id)
                    ->where('type', $type)
                    ->exists();

                if ($alreadyHasNotice) {
                    return;
                }

                $label = $a->typeOfScholarship?->name
                    ? $a->typeOfScholarship->name . ' scholarship'
                    : 'scholarship';

                $message = $a->status === 'approved'
                    ? "Congratulations! Your {$label} application has been approved."
                    : "Your {$label} application was not approved."
                        . ($a->rejection_reason ? " Reason: {$a->rejection_reason}." : '')
                        . ' Please contact the scholarship office for more information.';

                DB::table('appointment_notifications')->insert([
                    'user_id'                   => $a->user_id,
                    'counseling_appointment_id' => null,
                    'type'                      => $type,
                    'message'                   => $message,
                    'created_at'                => $a->updated_at ?? now(),
                    'updated_at'                => now(),
                ]);

                $created++;
            });

        $this->info("Done. {$created} notification(s) created.");

        return self::SUCCESS;
    }
}