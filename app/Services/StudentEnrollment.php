<?php

namespace App\Services;

use App\Models\Students;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Works out whether a student account is on the enrolled list of the active term.
 *
 * The Students import is the list of enrolled students for a term, so:
 *
 *   ENROLLED  has a students row in the ACTIVE term            -> keeps access
 *   FORMER    has students rows, but none in the active term   -> no access
 *   VISITOR   has no students rows at all (e.g. exam takers)   -> unaffected
 *
 * An account is matched to its students rows by user_id, or by the same email,
 * or by the same last name + birthdate (the same rules the Students table uses
 * for its "Account" column).
 */
class StudentEnrollment
{
    public const ENROLLED = 'enrolled';
    public const FORMER   = 'former';
    public const VISITOR  = 'visitor';

    public static function status(User $user): string
    {
        $activeTerm = Term::where('is_active', true)->first();

        // Nothing to compare against, so never lock anyone out.
        if (! $activeTerm) {
            return self::ENROLLED;
        }

        $matches = static::matchingStudents($user);

        if ((clone $matches)->where('term_id', $activeTerm->id)->exists()) {
            return self::ENROLLED;
        }

        if ($matches->exists()) {
            return self::FORMER;
        }

        return self::VISITOR;
    }

    /**
     * True when the account has a students row in the ACTIVE term.
     * Visitors (no student rows) and former students both return false.
     * With no active term set, any student row counts.
     */
    public static function isEnrolledInActiveTerm(User $user): bool
    {
        $activeTerm = Term::where('is_active', true)->first();
        $matches    = static::matchingStudents($user);

        return $activeTerm
            ? $matches->where('term_id', $activeTerm->id)->exists()
            : $matches->exists();
    }

    /**
     * Every students row (any term) that belongs to this account.
     */
    protected static function matchingStudents(User $user): Builder
    {
        return Students::query()->where(function (Builder $q) use ($user) {
            $q->where('user_id', $user->id);

            if (filled($user->email)) {
                $q->orWhereRaw(
                    'LOWER(TRIM(email)) COLLATE utf8mb4_unicode_ci = ?',
                    [strtolower(trim($user->email))]
                );
            }

            if (filled($user->last_name) && filled($user->birthdate)) {
                $q->orWhere(function (Builder $n) use ($user) {
                    $n->whereRaw(
                        'LOWER(TRIM(last_name)) COLLATE utf8mb4_unicode_ci = ?',
                        [strtolower(trim($user->last_name))]
                    )->whereDate('birth_date', $user->birthdate);
                });
            }
        });
    }
}