<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Students;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function show()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $type = $request->input('enrollment_type');

        return $type === 'enrolled'
            ? $this->registerEnrolled($request)
            : $this->registerUnenrolled($request);
    }

    /**
     * Latest allowed birthdate: Dec 31 of last year (nothing from the current year onward).
     */
    private function maxBirthdate(): string
    {
        return now()->subYear()->endOfYear()->toDateString();
    }

    /**
     * Oldest allowed birthdate: 100 years back.
     */
    private function minBirthdate(): string
    {
        return now()->subYears(100)->toDateString();
    }

    private function registerEnrolled(Request $request)
    {
        $validated = $request->validate([
            'student_id'         => ['required', 'string'],
            'last_name_enrolled' => ['required', 'string'],
            'birthdate_enrolled' => [
                'required',
                'date',
                'before_or_equal:' . $this->maxBirthdate(),
                'after_or_equal:' . $this->minBirthdate(),
            ],
            // The email must not already belong to any existing user account.
            'email'              => ['required', 'email', 'unique:users,email'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique'                       => 'This email is already used by an existing account. Please use a different email or log in instead.',
            'birthdate_enrolled.before_or_equal' => 'Please enter a realistic birthdate. It cannot be in the current year or later.',
            'birthdate_enrolled.after_or_equal'  => 'Please enter a realistic birthdate.',
        ]);

        // Verify against students table
        $student = Students::whereRaw('TRIM(student_id) = ?', [trim($validated['student_id'])])
            ->whereRaw('LOWER(TRIM(last_name)) = ?', [strtolower(trim($validated['last_name_enrolled']))])
            ->whereDate('birth_date', $validated['birthdate_enrolled'])
            ->first();

        if (! $student) {
            return back()->withErrors([
                'student_id' => 'No matching enrolled student found. Please check your Student ID, Last Name, and Birthdate.',
            ])->withInput();
        }

        // A student record can only be linked to one account.
        if ($student->user_id) {
            return back()->withErrors([
                'student_id' => 'This student record is already linked to another account.',
            ])->withInput();
        }

        // The contact number copied from the student record must not already
        // belong to another user account.
        if (! empty($student->contact_no) && $this->contactInUse($student->contact_no)) {
            return back()->withErrors([
                'student_id' => 'The contact number on your student record is already used by another account. Please contact the registrar or admin office to resolve this.',
            ])->withInput();
        }

        // Create new account and link to student record
        // Enrolled users: all personal data pulled from the students table automatically
        try {
            $user = User::create([
                'name'       => "{$student->first_name} {$student->last_name}",
                'last_name'  => $student->last_name,
                'email'      => $validated['email'],
                'password'   => $validated['password'],
                'role_id'    => $this->studentRoleId(),
                'birthdate'  => $student->birth_date,
                'gender_id'  => $student->gender_id,
                'contact_no' => $student->contact_no,
                'address'    => $student->address,
            ]);
        } catch (QueryException $e) {
            // 23000 = integrity constraint violation (duplicate key, e.g. a
            // simultaneous sign-up that slipped past the checks above).
            if ($e->getCode() === '23000') {
                return back()->withErrors([
                    'student_id' => 'The contact number or email is already used by another account.',
                ])->withInput();
            }
            throw $e;
        }

        // Also register the role in the roles relationship so the Users
        // resource (and hasRole checks) recognise this account as a student.
        $this->assignStudentRole($user);

        $student->update(['user_id' => $user->id]);

        return redirect()->route('login')
            ->with('success', 'Account created! You can now log in.');
    }

    private function registerUnenrolled(Request $request)
    {
        $validated = $request->validate([
            // Letters only (including accents like ñ) and spaces. No numbers or special characters.
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'last_name'  => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'birthdate'  => [
                'required',
                'date',
                'before_or_equal:' . $this->maxBirthdate(),
                'after_or_equal:' . $this->minBirthdate(),
            ],
            'email_unenrolled'                 => ['required', 'email', 'unique:users,email'],
            'password_unenrolled'              => ['required', 'string', 'min:8'],
            'password_confirmation_unenrolled' => ['required', 'same:password_unenrolled'],

            // Digits only, up to 11 digits, and must not belong to another user.
            'contact_no' => [
                'required',
                'regex:/^[0-9]{1,11}$/',
                function ($attribute, $value, $fail) {
                    if ($this->contactInUse($value, true)) {
                        $fail('This contact number is already used by another account or belongs to a student record. If you are an enrolled student, please register using the Student tab.');
                    }
                },
            ],
            'gender_id'  => ['nullable', 'integer'],
            'address'    => ['required', 'string', 'max:255'],
        ], [
            'first_name.regex'          => 'First name may only contain letters and spaces (no numbers or special characters).',
            'last_name.regex'           => 'Last name may only contain letters and spaces (no numbers or special characters).',
            'email_unenrolled.unique'   => 'This email is already used by an existing account. Please use a different email or log in instead.',
            'contact_no.regex'          => 'Contact number must contain digits only (up to 11 digits).',
            'birthdate.before_or_equal' => 'Please enter a realistic birthdate. It cannot be in the current year or later.',
            'birthdate.after_or_equal'  => 'Please enter a realistic birthdate.',
        ]);

        $user = User::create([
            'name'       => $validated['first_name'] . ' ' . $validated['last_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email_unenrolled'],
            'password'   => $validated['password_unenrolled'],
            'role_id'    => $this->studentRoleId(),
            'birthdate'  => $validated['birthdate'],
            'gender_id'  => $validated['gender_id']  ?? null,
            'contact_no' => $validated['contact_no'],
            'address'    => $validated['address'],
        ]);

        // Same as above: keep the roles relationship in sync.
        $this->assignStudentRole($user);

        return redirect()->route('login')
            ->with('success', 'Account created! You can now log in.');
    }

    /**
     * True if the contact number already belongs to a user account.
     *
     * Checks the users table and the students table. By default only students that
     * already have an account (students.user_id is set) count; pass true to also count
     * student records that have no account yet (used by the visitor form).
     * Formatting is ignored (spaces, dashes, +, brackets)
     * and the common Philippine forms of the same number are treated as equal:
     * 09123456789, 9123456789, 639123456789 and +639123456789.
     */
    private function contactInUse(?string $contact, bool $includeUnlinkedStudents = false): bool
    {
        $digits = preg_replace('/\D/', '', (string) $contact);

        if ($digits === '') {
            return false;
        }

        $last10   = substr($digits, -10);
        $variants = array_values(array_unique([$digits, $last10, '0' . $last10, '63' . $last10]));
        $marks    = implode(',', array_fill(0, count($variants), '?'));

        // Strip spaces, dashes, plus signs and brackets from a stored value before comparing.
        $clean = fn (string $col) =>
            "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$col}, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '')";

        if (User::whereRaw($clean('contact_no') . " IN ({$marks})", $variants)->exists()) {
            return true;
        }

        $students = Students::whereRaw($clean('contact_no') . " IN ({$marks})", $variants);

        if (! $includeUnlinkedStudents) {
            $students->whereNotNull('user_id');
        }

        return $students->exists();
    }

    private function studentRoleId(): int
    {
        return Role::where('name', 'student')->value('id') ?? 4;
    }

    /**
     * Attach the student role through the many-to-many roles relationship.
     * syncWithoutDetaching() never creates a duplicate row.
     */
    private function assignStudentRole(User $user): void
    {
        $user->roles()->syncWithoutDetaching([$this->studentRoleId()]);
    }
}