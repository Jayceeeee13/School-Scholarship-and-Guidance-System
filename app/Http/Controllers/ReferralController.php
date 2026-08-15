<?php

namespace App\Http\Controllers;

use App\Models\Referrals;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function create()
    {
        return view('referral');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'            => 'required|date',
            'name'            => 'required|string|max:500',
            'course_and_year' => 'nullable|string|max:500',
            'age'             => 'nullable|integer|min:1|max:99',
            'case_presented'  => 'nullable|string',
            'referred_by'     => 'nullable|string|max:100',
        ]);

        // ── Determine if this user is enrolled ────────────────────────────
        // Unenrolled users (no linked student record) skip verification entirely.
        $isEnrolled = Auth::check() && Auth::user()->student !== null;

        if ($isEnrolled) {
            // ── Program acronym map (full name → acronym) ─────────────────
            $programMap = [
                'bachelor of elementary education'                                                             => 'BEEd',
                'bachelor of early childhood education'                                                        => 'BECEd',
                'bachelor of secondary education major in english'                                             => 'BSEd-Eng',
                'bachelor of secondary education major in mathematics'                                         => 'BSEd-Math',
                'bachelor of technology and livelihood education major in home economics'                      => 'BTLEd-HE',
                'bachelor of technology and livelihood education major in industrial arts'                     => 'BTLEd-IA',
                'bachelor of technology and livelihood education major in information communication technology' => 'BTLEd-ICT',
                'bachelor of science in mechanical engineering'                                                => 'BSME',
                'bachelor of science in information technology'                                                => 'BSIT',
                'associate in computer technology'                                                             => 'ACT',
                'bachelor of science in criminology'                                                           => 'BSCrim',
                'bachelor of science in industrial security management'                                        => 'BSISM',
                'bachelor of science in business administration major in financial management'                  => 'BSBA-FM',
                'bachelor of science in business administration major in marketing management'                  => 'BSBA-MM',
                'bachelor of science in business administration major in operations management'                 => 'BSBA-OM',
                'bachelor of science in tourism management'                                                    => 'BSTM',
                'bachelor of science in hospitality management'                                                => 'BSHM',
            ];

            // Reverse map: lowercase acronym → lowercase full name
            $acronymToFull = [];
            foreach ($programMap as $full => $acronym) {
                $acronymToFull[strtolower($acronym)] = strtolower($full);
            }

            // Year level aliases
            $yearAliases = [
                '1st' => ['1st', 'first',  '1'],
                '2nd' => ['2nd', 'second', '2'],
                '3rd' => ['3rd', 'third',  '3'],
                '4th' => ['4th', 'fourth', '4'],
                '5th' => ['5th', 'fifth',  '5'],
                '1'   => ['1st', 'first',  '1'],
                '2'   => ['2nd', 'second', '2'],
                '3'   => ['3rd', 'third',  '3'],
                '4'   => ['4th', 'fourth', '4'],
                '5'   => ['5th', 'fifth',  '5'],
            ];

            // ── Name query: last name anchor + flexible first/middle match ──
            $nameParts = array_values(array_filter(explode(' ', trim($validated['name']))));

            $student = Students::with('program')
                ->where(function ($q) use ($nameParts) {
                    if (count($nameParts) === 1) {
                        $part = strtolower($nameParts[0]);
                        $q->whereRaw('LOWER(first_name)  LIKE ?', ['%' . $part . '%'])
                          ->orWhereRaw('LOWER(last_name)   LIKE ?', ['%' . $part . '%'])
                          ->orWhereRaw('LOWER(middle_name) LIKE ?', ['%' . $part . '%']);
                    } else {
                        $lastName   = strtolower(end($nameParts));
                        $otherParts = array_slice($nameParts, 0, -1);

                        $q->whereRaw('LOWER(last_name) LIKE ?', ['%' . $lastName . '%']);

                        $q->where(function ($inner) use ($otherParts) {
                            foreach ($otherParts as $part) {
                                $p = strtolower($part);
                                $inner->orWhereRaw('LOWER(first_name)  LIKE ?', ['%' . $p . '%'])
                                      ->orWhereRaw('LOWER(middle_name) LIKE ?', ['%' . $p . '%']);
                            }
                        });
                    }
                })
                ->get()
                ->first(function ($s) use ($validated, $programMap, $acronymToFull, $yearAliases) {

                    if (empty($validated['course_and_year'])) return true;

                    $input       = strtolower(trim($validated['course_and_year']));
                    $programName = strtolower($s->program?->name ?? '');
                    $yearLevel   = strtolower(trim($s->year_level ?? ''));

                    // Program matching
                    $programMatch = $programName && str_contains($input, $programName);

                    if (! $programMatch) {
                        foreach ($acronymToFull as $acronym => $fullName) {
                            if (str_contains($input, $acronym)) {
                                if ($fullName === $programName) {
                                    $programMatch = true;
                                    break;
                                }
                            }
                        }
                    }

                    if (! $programMatch) {
                        $storedAcronym = strtolower($programMap[$programName] ?? '');
                        if ($storedAcronym && str_contains($input, $storedAcronym)) {
                            $programMatch = true;
                        }
                    }

                    // Year level matching
                    $hasYearInInput = (bool) preg_match(
                        '/\b(1st|2nd|3rd|4th|5th|first|second|third|fourth|fifth|[1-5])\b/i',
                        $input
                    );

                    $yearMatch = false;
                    if ($hasYearInInput && $yearLevel && isset($yearAliases[$yearLevel])) {
                        foreach ($yearAliases[$yearLevel] as $alias) {
                            if (str_contains($input, strtolower($alias))) {
                                $yearMatch = true;
                                break;
                            }
                        }
                    }

                    if ($hasYearInInput) {
                        return $programMatch && $yearMatch;
                    }

                    return $programMatch;
                });

            if (! $student) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'name' => 'No enrolled student found matching the provided name and course/year. Please verify the details — you may use either the full program name (e.g. "Bachelor of Science in Information Technology") or its acronym (e.g. "BSIT").',
                    ]);
            }
        }
        // ── End verification ──────────────────────────────────────────────

        $referral = Referrals::create($validated);

        \App\Notifications\ReferralStatusNotification::send($referral, 'new_referral');

        return redirect()
            ->route('guidance_referrals.get')
            ->with('success', 'Referral submitted successfully! (ID: REF-' . date('Y') . '-' . str_pad($referral->id, 4, '0', STR_PAD_LEFT) . ')');
    }

    public function index()
    {
        $referrals = Referrals::latest()->paginate(15);
        return view('referral', compact('referrals'));
    }
}