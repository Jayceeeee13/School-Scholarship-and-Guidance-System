<?php

namespace App\Http\Middleware;

use App\Services\StudentEnrollment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs out student accounts that are no longer on the enrolled list
 * (the Students import) of the active term, so they cannot stay logged in.
 *
 * - Only accounts whose role is Student are checked; admin, guidance,
 *   scholarship and department head accounts are never affected.
 * - Visitors (accounts with no student records at all, such as people who only
 *   registered to take the qualifying exam) are not affected here. They are
 *   still kept out of enrolled-only features by EnsureUserIsEnrolled.
 */
class BlockFormerStudents
{
    public const MESSAGE = 'Your account is no longer active because you are not on the enrolled students list for the current term. Please contact the guidance office.';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $this->isStudentOnly($user)) {
            return $next($request);
        }

        if (StudentEnrollment::status($user) !== StudentEnrollment::FORMER) {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->hasHeader('X-Livewire')) {
            return response()->json(['message' => self::MESSAGE], 403);
        }

        return redirect()->route('login')->withErrors(['email' => self::MESSAGE]);
    }

    protected function isStudentOnly($user): bool
    {
        return $user->hasRole('student')
            && ! $user->hasAnyRole(['admin', 'guidance', 'scholarship', 'department head']);
    }
}