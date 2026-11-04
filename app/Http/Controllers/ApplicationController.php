<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ExamAttempt;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Students;          // FIX: the model class is "Students" (plural), not "Student"
use App\Models\TypeOfApplication;
use App\Models\TypeOfScholarship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    private const YEAR_LEVELS = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

    // Letters (incl. ñ, accents), spaces, hyphens, periods, apostrophes
    private const NAME_REGEX = "/^[\pL\s\-\.']+$/u";

    // 09XXXXXXXXX or +639XXXXXXXXX (spaces/dashes are stripped before validating)
    private const PHONE_REGEX = '/^(09\d{9}|\+639\d{9})$/';

    /**
     * ID of the Academic scholarship type if the logged-in user has already
     * finished an exam (completed_at filled in); otherwise null.
     * When this returns an ID, the scholarship type is forced to Academic.
     */
    private function forcedAcademicId(): ?int
    {
        try {
            $hasTakenExam = ExamAttempt::where('user_id', Auth::id())
                ->whereNotNull('completed_at')
                ->exists();

            return $hasTakenExam ? ExamAttempt::academicScholarshipId() : null;
        } catch (\Throwable $e) {
            Log::error('Application form: could not check exam attempts - ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Find the logged-in user's student record.
     * 1) students.user_id = logged-in user (set when the student registers / is imported)
     * 2) fallback: students.student_id = users.student_id, if that column exists
     */
    private function findStudent(): ?Students
    {
        $table   = (new Students)->getTable();
        $student = null;

        if (Schema::hasColumn($table, 'user_id')) {
            $student = Students::where('user_id', Auth::id())->first();
        }

        $studentId = Auth::user()->student_id ?? null;

        if (! $student && ! empty($studentId) && Schema::hasColumn($table, 'student_id')) {
            $student = Students::where('student_id', $studentId)->first();
        }

        return $student;
    }

    /**
     * First non-empty value among several possible column names.
     * Uses raw attributes, so a missing column never throws.
     */
    private function pick(array $attrs, array $keys): string
    {
        foreach ($keys as $key) {
            if (isset($attrs[$key]) && trim((string) $attrs[$key]) !== '') {
                return trim((string) $attrs[$key]);
            }
        }

        return '';
    }

    /** "Juan" + "Santos" + "Dela Cruz" -> "Juan Santos Dela Cruz" (empty parts skipped). */
    private function joinNames(array $attrs, array $keys): string
    {
        $parts = [];

        foreach ($keys as $key) {
            $value = trim((string) ($attrs[$key] ?? ''));
            if ($value !== '') {
                $parts[] = $value;
            }
        }

        return implode(' ', $parts);
    }

    /** "JUAN DELA CRUZ" -> "Juan Dela Cruz" (same rule the form applies while typing). */
    private function titleName(string $value): string
    {
        return $value === '' ? '' : mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /** Collapse whitespace + lowercase, for comparing names. */
    private function norm(?string $value): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
    }

    /** "+63 917-123-4567" / "9171234567" -> "09171234567" */
    private function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value);

        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '0' . $digits;
        }

        return substr($digits, 0, 11);
    }

    /**
     * Build the values the application form should start with, taken from
     * the student's record. Missing data is simply left blank.
     *
     * Students columns used:
     *   first_name, middle_name, last_name, extension_name, birth_date,
     *   gender_id, program_id, year_level, contact_no,
     *   fathers_firstname/middlename/lastname, mothers_firstname/middlename/lastname
     *
     * Not on the students table (left blank for the student to fill in):
     *   religion, facebook account, parents' contact numbers, guardian.
     */
    private function buildPrefill($programs, $genders): array
    {
        $student = $this->findStudent();

        if (! $student) {
            return [];
        }

        $a = $student->getAttributes();

        $fathersName = $this->joinNames($a, ['fathers_firstname', 'fathers_middlename', 'fathers_lastname'])
            ?: $this->pick($a, ['fathers_name', 'father_name']);

        $mothersName = $this->joinNames($a, ['mothers_firstname', 'mothers_middlename', 'mothers_lastname'])
            ?: $this->pick($a, ['mothers_name', 'mother_name']);

        $prefill = [
            'first_name'          => $this->titleName($this->pick($a, ['first_name'])),
            'middle_name'         => $this->titleName($this->pick($a, ['middle_name'])),
            'last_name'           => $this->titleName($this->pick($a, ['last_name'])),
            'extension_name'      => $this->pick($a, ['extension_name']),
            'contact_no'          => $this->normalizePhone($this->pick($a, ['contact_no'])),
            'religion'            => '',
            'facebook_account'    => '',
            'fathers_name'        => $this->titleName($fathersName),
            'fathers_contact_no'  => '',
            'mothers_name'        => $this->titleName($mothersName),
            'mothers_contact_no'  => '',
            'guardian'            => '',
            'guardian_contact_no' => '',
        ];

        // ── Birth date (Y-m-d for the date input) ──────────────────────────
        $prefill['birthdate'] = '';
        $rawBirth = $this->pick($a, ['birth_date', 'birthdate']);
        if ($rawBirth !== '') {
            try {
                $prefill['birthdate'] = Carbon::parse($rawBirth)->toDateString();
            } catch (\Throwable $e) {
                // leave blank if the stored value isn't a valid date
            }
        }

        // ── Year level -> "1st Year" ... "5th Year" ────────────────────────
        $prefill['year_level'] = '';
        $rawYear = $this->pick($a, ['year_level']);
        if ($rawYear !== '') {
            if (in_array($rawYear, self::YEAR_LEVELS, true)) {
                $prefill['year_level'] = $rawYear;
            } elseif (preg_match('/([1-5])/', $rawYear, $m)) {
                $prefill['year_level'] = self::YEAR_LEVELS[(int) $m[1] - 1];
            }
        }

        // ── Program -> program_id ──────────────────────────────────────────
        $prefill['program_id'] = '';
        $rawProgramId = $this->pick($a, ['program_id']);
        if ($rawProgramId !== '' && $programs->contains('id', (int) $rawProgramId)) {
            $prefill['program_id'] = (int) $rawProgramId;
        }

        // ── Gender -> gender_id ────────────────────────────────────────────
        $prefill['gender_id'] = '';
        $rawGenderId = $this->pick($a, ['gender_id']);
        if ($rawGenderId !== '' && $genders->contains('id', (int) $rawGenderId)) {
            $prefill['gender_id'] = (int) $rawGenderId;
        }

        return $prefill;
    }

    public function create()
    {
        // If user already has a submitted application, redirect away
        $existing = Applicant::where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('gvc')
                ->with('success', 'You have already submitted an application.')
                ->with('already_applied', true);
        }

        $scholarshipTypes = TypeOfScholarship::where('is_active', 1)->get();
        $applicantTypes   = TypeOfApplication::where('is_active', 1)->get();
        $genders          = Gender::where('is_active', 1)->get();
        $programs         = Program::where('is_active', 1)->get();

        // Exam takers are automatically set to the Academic scholarship type.
        $academicId = $this->forcedAcademicId();

        // Pre-fill the form from the student's record (blank if none found).
        // The pre-fill is optional, so if it fails the form still opens and the
        // reason is written to storage/logs/laravel.log.
        try {
            $prefill = $this->buildPrefill($programs, $genders);
        } catch (\Throwable $e) {
            Log::error('Application form: prefill failed - ' . $e->getMessage());
            $prefill = [];
        }

        return view('application_new', compact(
            'scholarshipTypes',
            'applicantTypes',
            'genders',
            'programs',
            'academicId',
            'prefill'
        ));
    }

    public function store(Request $request)
    {
        // Double-check at store time (race condition / direct POST guard)
        if (Applicant::where('user_id', Auth::id())->exists()) {
            return redirect()->route('gvc')
                ->with('already_applied', true);
        }

        // ── Exam takers are always Academic (ignore whatever was posted) ───
        if ($academicId = $this->forcedAcademicId()) {
            $request->merge(['type_of_scholarship_id' => $academicId]);
        }

        // ── Normalize input before validating ──────────────────────────────
        // Strip spaces/dashes from phone numbers so "0917-123-4567" passes.
        $phoneFields = [
            'contact_no',
            'fathers_contact_no',
            'mothers_contact_no',
            'guardian_contact_no',
        ];

        $normalized = [];
        foreach ($phoneFields as $field) {
            if ($request->filled($field)) {
                $normalized[$field] = preg_replace('/[\s\-]+/', '', $request->input($field));
            }
        }
        $request->merge($normalized);

        // ── Validation ─────────────────────────────────────────────────────
        $validated = $request->validate([
            'type_of_application_id' => [
                'required',
                Rule::exists('type_of_applications', 'id')->where('is_active', 1),
            ],
            'type_of_scholarship_id' => [
                'required',
                Rule::exists('type_of_scholarships', 'id')->where('is_active', 1),
                // Block scholarships with no slots left (also guards direct POSTs)
                function (string $attribute, $value, $fail) {
                    $scholarship = TypeOfScholarship::find($value);
                    if ($scholarship && (int) $scholarship->slots <= 0) {
                        $fail('The selected scholarship has no slots left.');
                    }
                },
            ],
            'picture' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],

            'first_name'     => ['required', 'string', 'max:100', 'regex:' . self::NAME_REGEX],
            'middle_name'    => ['nullable', 'string', 'max:100', 'regex:' . self::NAME_REGEX],
            'last_name'      => ['required', 'string', 'max:100', 'regex:' . self::NAME_REGEX],
            'extension_name' => ['nullable', 'string', 'max:20', "regex:/^[A-Za-z\.\s]+$/"],

            'program_id' => [
                'required',
                Rule::exists('programs', 'id')->where('is_active', 1),
            ],
            'year_level' => ['required', Rule::in(self::YEAR_LEVELS)],
            'gender_id'  => [
                'required',
                Rule::exists('genders', 'id')->where('is_active', 1),
            ],

            'contact_no' => ['nullable', 'string', 'regex:' . self::PHONE_REGEX],
            'birthdate'  => ['nullable', 'date', 'before:today', 'after:1930-01-01'],
            'religion'   => ['nullable', 'string', 'max:100'],
            'facebook_account' => ['nullable', 'string', 'max:100'],

            'fathers_name'       => ['required', 'string', 'max:100', 'regex:' . self::NAME_REGEX],
            'fathers_contact_no' => ['nullable', 'string', 'regex:' . self::PHONE_REGEX],
            'mothers_name'       => ['required', 'string', 'max:100', 'regex:' . self::NAME_REGEX],
            'mothers_contact_no' => ['nullable', 'string', 'regex:' . self::PHONE_REGEX],

            'guardian'            => ['nullable', 'string', 'max:100', 'regex:' . self::NAME_REGEX, 'required_with:guardian_contact_no'],
            'guardian_contact_no' => ['nullable', 'string', 'regex:' . self::PHONE_REGEX],
        ], [
            // ── Custom messages ──
            'type_of_application_id.required' => 'Please select an applicant type.',
            'type_of_application_id.exists'   => 'The selected applicant type is not available.',
            'type_of_scholarship_id.required' => 'Please select a scholarship type.',
            'type_of_scholarship_id.exists'   => 'The selected scholarship is not available.',

            'picture.image' => 'The ID picture must be an image.',
            'picture.mimes' => 'The ID picture must be a .jpg, .jpeg, or .png file.',
            'picture.max'   => 'The ID picture must not be larger than 5 MB.',

            'first_name.required' => 'First name is required.',
            'first_name.regex'    => 'First name may only contain letters, spaces, hyphens, periods, and apostrophes.',
            'middle_name.regex'   => 'Middle name may only contain letters, spaces, hyphens, periods, and apostrophes.',
            'last_name.required'  => 'Last name is required.',
            'last_name.regex'     => 'Last name may only contain letters, spaces, hyphens, periods, and apostrophes.',
            'extension_name.regex' => 'Extension name may only contain letters and periods (e.g. Jr., III).',

            'program_id.required' => 'Please select a program.',
            'program_id.exists'   => 'The selected program is not available.',
            'year_level.required' => 'Please select a year level.',
            'year_level.in'       => 'The selected year level is invalid.',
            'gender_id.required'  => 'Please select a gender.',
            'gender_id.exists'    => 'The selected gender is not available.',

            'contact_no.regex'          => 'Contact number must be a valid PH mobile number (e.g. 09171234567).',
            'fathers_contact_no.regex'  => "Father's contact number must be a valid PH mobile number (e.g. 09171234567).",
            'mothers_contact_no.regex'  => "Mother's contact number must be a valid PH mobile number (e.g. 09171234567).",
            'guardian_contact_no.regex' => 'Guardian contact number must be a valid PH mobile number (e.g. 09171234567).',

            'birthdate.before' => 'Birth date must be a past date.',
            'birthdate.after'  => 'Please enter a valid birth date.',

            'fathers_name.required' => "Father's name is required.",
            'fathers_name.regex'    => "Father's name may only contain letters, spaces, hyphens, periods, and apostrophes.",
            'mothers_name.required' => "Mother's name is required.",
            'mothers_name.regex'    => "Mother's name may only contain letters, spaces, hyphens, periods, and apostrophes.",

            'guardian.regex'         => 'Guardian name may only contain letters, spaces, hyphens, periods, and apostrophes.',
            'guardian.required_with' => 'Please enter the guardian name since a guardian contact number was provided.',
        ]);

        // ── Compute age server-side from birthdate ─────────────────────────
        $age = null;
        if (! empty($validated['birthdate'])) {
            $age = Carbon::parse($validated['birthdate'])->age;
        }

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('applications/pictures', 'public');
        }

        // ── Pull exam discount as benefit ──────────────────────────────────
        // If the student took the exam BEFORE applying, read the discount from
        // their latest finished attempt (completed_at filled in). If they take
        // it AFTER applying, the ExamAttemptObserver::created() hook pushes it
        // into applicant.benefit.
        $examAttempt = ExamAttempt::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->whereNotNull('discount')
            ->latest('completed_at')
            ->first();

        $benefit = $examAttempt ? (int) $examAttempt->discount : null;

        // Optional text columns are NOT NULL in the live DB, so blank → ''.
        Applicant::create([
            'user_id'                => Auth::id(),
            'picture'                => $picturePath,
            'type_of_application_id' => $validated['type_of_application_id'],
            'type_of_scholarship_id' => $validated['type_of_scholarship_id'],
            'first_name'             => $validated['first_name'],
            'middle_name'            => $validated['middle_name'] ?? '',
            'last_name'              => $validated['last_name'],
            'extension_name'         => $validated['extension_name'] ?? '',
            'program_id'             => $validated['program_id'],
            'year_level'             => $validated['year_level'],
            'gender_id'              => $validated['gender_id'],
            'contact_no'             => $validated['contact_no'] ?? '',
            'birthdate'              => $validated['birthdate'] ?? null,
            'age'                    => $age,
            'religion'               => $validated['religion'] ?? '',
            'facebook_account'       => $validated['facebook_account'] ?? '',
            'fathers_name'           => $validated['fathers_name'],
            'fathers_contact_no'     => $validated['fathers_contact_no'] ?? '',
            'mothers_name'           => $validated['mothers_name'],
            'mothers_contact_no'     => $validated['mothers_contact_no'] ?? '',
            'guardian'               => $validated['guardian'] ?? '',
            'guardian_contact_no'    => $validated['guardian_contact_no'] ?? '',
            'status'                 => 'pending',
            'benefit'                => $benefit,
        ]);

        return redirect()->route('scholarship')
            ->with('success', 'Application submitted successfully.');
    }

    public function index()
    {
        $applicant      = Applicant::where('user_id', Auth::id())->first();
        $alreadyApplied = (bool) $applicant;
        return view('scholarship', compact('alreadyApplied', 'applicant'));
    }
}