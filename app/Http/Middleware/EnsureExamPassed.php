<?php

namespace App\Http\Middleware;

use App\Models\Exam;
use App\Models\InstitutionalScholar;
use App\Models\Term;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks the scholarship application routes until the student has taken
 * AND passed the admission exam.
 *
 * - No exam exists            → not enforced.
 * - Scholar from an earlier   → treated as a renewal, exempt from the exam.
 *   term
 */
class EnsureExamPassed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Same exam the scholarship page shows (adjust if your index() picks it differently).
        $exam = Exam::where('is_active', true)->latest('id')->first()
            ?? Exam::latest('id')->first();

        if (! $user || ! $exam) {
            return $next($request);
        }

        // Renewals (scholar from an earlier term) don't have to retake the exam.
        $activeTermId = Term::where('is_active', true)->value('id');

        $isRenewal = InstitutionalScholar::where('user_id', $user->id)
            ->when($activeTermId, fn ($q) => $q->where('term_id', '!=', $activeTermId))
            ->exists();

        if ($isRenewal || $exam->hasBeenPassedBy($user->id)) {
            return $next($request);
        }

        return redirect('/scholarship')
            ->with('error', 'You need to take and pass the admission exam before you can apply.');
    }
}