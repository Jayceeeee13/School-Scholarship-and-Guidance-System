<?php

namespace App\Observers;

use App\Models\Applicant;
use App\Models\ExamAttempt;

class ExamAttemptObserver
{
    /**
     * Fires when a new attempt is inserted.
     *
     * ExamController::submit() always creates the attempt already in
     * 'completed' status inside a DB transaction, so this is the
     * primary hook that syncs the discount → applicant benefit.
     */
    public function created(ExamAttempt $attempt): void
    {
        $this->syncBenefit($attempt);
    }

    /**
     * Fires when an existing attempt is updated.
     *
     * Covers edge cases where the status or discount changes after
     * initial creation (e.g. admin manually updates the record).
     */
    public function updated(ExamAttempt $attempt): void
    {
        // Only re-sync if a relevant field actually changed
        if (! $attempt->wasChanged(['status', 'discount'])) {
            return;
        }

        $this->syncBenefit($attempt);
    }

    // ── Private helper ─────────────────────────────────────────────────────

    private function syncBenefit(ExamAttempt $attempt): void
    {
        // Only act on completed / submitted attempts
        if (! in_array($attempt->status, ['completed', 'submitted'])) {
            return;
        }

        // Nothing to sync if there is no discount value
        if (is_null($attempt->discount)) {
            return;
        }

        $applicant = Applicant::where('user_id', $attempt->user_id)->first();

        if (! $applicant) {
            // Student has not submitted a scholarship application yet.
            // The benefit will be pulled at application time via
            // ApplicationController::store() using the exam_attempts record.
            return;
        }

        // Avoid an unnecessary DB write if the value has not changed
        if ((int) $applicant->benefit === (int) $attempt->discount) {
            return;
        }

        $applicant->benefit = (int) $attempt->discount;
        $applicant->save();
    }
}