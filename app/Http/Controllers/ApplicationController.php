<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ExamAttempt;
use App\Models\Gender;
use App\Models\Program;
use App\Models\TypeOfApplication;
use App\Models\TypeOfScholarship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    private const YEAR_LEVELS = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

    // Letters (incl. ñ, accents), spaces, hyphens, periods, apostrophes
    private const NAME_REGEX = "/^[\pL\s\-\.']+$/u";

    // 09XXXXXXXXX or +639XXXXXXXXX (spaces/dashes are stripped before validating)
    private const PHONE_REGEX = '/^(09\d{9}|\+639\d{9})$/';

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

        return view('application_new', compact(
            'scholarshipTypes',
            'applicantTypes',
            'genders',
            'programs'
        ));
    }

    public function store(Request $request)
    {
        // Double-check at store time (race condition / direct POST guard)
        if (Applicant::where('user_id', Auth::id())->exists()) {
            return redirect()->route('gvc')
                ->with('already_applied', true);
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
        // their latest completed attempt. If they take it AFTER applying, the
        // ExamAttemptObserver::created() hook pushes it into applicant.benefit.
        $examAttempt = ExamAttempt::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'submitted'])
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