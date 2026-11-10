<?php

namespace App\Traits;

use App\Models\Applicant;
use App\Models\InstitutionalScholar;
use App\Models\Students;
use App\Models\Term;
use App\Models\TypeOfApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Works out where the logged-in student stands for the ACTIVE term, so every
 * controller dealing with scholarship applications agrees on it.
 */
trait ResolvesApplicationContext
{
    /**
     * Renewal context for the logged-in user.
     *  - term            : the active term (or null)
     *  - scholarThisTerm : standing scholarship already granted for the active term
     *  - returning       : scholar from an earlier term (so they renew)
     *  - isRenewal       : returning AND a "Renewal" application type exists
     *  - renewalTypeId   : set only when the Applicant Type must be locked to Renewal
     *  - applicant       : the application that counts for "already applied"
     *                      (this term's for returning scholars, latest otherwise)
     */
    protected function applicationContext(): array
    {
        $user      = Auth::user();
        $term      = Term::active()->first();
        $studentId = Students::where('user_id', $user->id)->value('student_id');

        $scholars = InstitutionalScholar::where('status', 'active')
            ->whereNull('revoked_at')
            ->where(function ($q) use ($user, $studentId) {
                $q->where('user_id', $user->id);
                if ($studentId) {
                    $q->orWhere('student_id', $studentId);
                }
            })
            ->get();

        $scholarThisTerm = $term ? $scholars->firstWhere('term_id', $term->id) : $scholars->first();
        $returning       = $term && $scholars->isNotEmpty() && ! $scholarThisTerm;

        $renewalTypeId = $returning
            ? TypeOfApplication::where('is_active', 1)->where('name', 'like', '%renewal%')->value('id')
            : null;

        if ($returning && ! $renewalTypeId) {
            Log::warning('Renewal: scholar is returning but no active "Renewal" application type exists.');
        }

        $applications = Applicant::where('user_id', $user->id);
        $applicant = $returning
            ? $applications->where('term_id', $term->id)->latest('id')->first()
            : $applications->latest('id')->first();

        $isRenewal = (bool) $renewalTypeId;

        return compact('term', 'scholarThisTerm', 'returning', 'isRenewal', 'renewalTypeId', 'applicant');
    }
}