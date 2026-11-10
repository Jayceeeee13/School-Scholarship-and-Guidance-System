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
     *  - term            : the active term (or null)
     *  - scholarThisTerm : scholarship already granted for the active term
     *  - returning       : was a scholar / approved in an earlier term
     *  - isRenewal       : returning AND a "Renewal" application type exists
     *  - renewalTypeId   : set only when the Applicant Type must be locked to Renewal
     *  - applicant       : this TERM's application (null = nothing submitted yet this term)
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

        // The application only counts for the term it was made in.
        $applications = Applicant::where('user_id', $user->id);

        $applicant = $term
            ? (clone $applications)->where('term_id', $term->id)->latest('id')->first()
            : (clone $applications)->latest('id')->first();

        // Returning = no scholarship this term, but already got one before,
        // either as a scholar record or as an approved application.
        $approvedBefore = $term
            ? (clone $applications)->where('status', 'approved')
                ->where(function ($q) use ($term) {
                    $q->where('term_id', '!=', $term->id)->orWhereNull('term_id');
                })->exists()
            : false;

        $returning = (bool) $term
            && ! $scholarThisTerm
            && ! $applicant
            && ($scholars->isNotEmpty() || $approvedBefore);

        $renewalTypeId = $returning
            ? TypeOfApplication::where('is_active', 1)->where('name', 'like', '%renewal%')->value('id')
            : null;

        if ($returning && ! $renewalTypeId) {
            Log::warning('Renewal: scholar is returning but no active "Renewal" application type exists.');
        }

        $isRenewal = (bool) $renewalTypeId;

        return compact('term', 'scholarThisTerm', 'returning', 'isRenewal', 'renewalTypeId', 'applicant');
    }
}