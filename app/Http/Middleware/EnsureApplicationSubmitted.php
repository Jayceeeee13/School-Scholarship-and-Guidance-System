<?php

namespace App\Http\Middleware;

use App\Models\Applicant;
use App\Models\Term;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requirements can only be submitted AFTER the student has submitted a
 * scholarship application for the active term. This enforces the
 * step-by-step flow on the server, so typing the URL directly doesn't
 * skip the "Apply Scholarship" step.
 */
class EnsureApplicationSubmitted
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasApplication = Applicant::query()
            ->where('user_id', $request->user()?->id)
            ->whereNull('archived_at')
            ->where('term_id', Term::active()->value('id'))
            ->exists();

        if (! $hasApplication) {
            return redirect()
                ->route('scholarship')
                ->with('error', 'Please submit your scholarship application first before submitting requirements.');
        }

        return $next($request);
    }
}