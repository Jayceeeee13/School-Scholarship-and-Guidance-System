<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Students;
use App\Models\User;
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

    private function registerEnrolled(Request $request)
    {
        $validated = $request->validate([
            'student_id'         => ['required', 'string'],
            'last_name_enrolled' => ['required', 'string'],
            'birthdate_enrolled' => ['required', 'date'],
            'email'              => ['required', 'email'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify against students table
        $student = Students::where('student_id', $validated['student_id'])
            ->where('last_name', $validated['last_name_enrolled'])
            ->whereDate('birth_date', $validated['birthdate_enrolled'])
            ->first();

        if (! $student) {
            return back()->withErrors([
                'student_id' => 'No matching enrolled student found. Please check your Student ID, Last Name, and Birthdate.',
            ])->withInput();
        }

        // Check if user already exists with that email
        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            if ($student->user_id && $student->user_id !== $existingUser->id) {
                return back()->withErrors([
                    'student_id' => 'This student record is already linked to another account.',
                ])->withInput();
            }

            $student->update(['user_id' => $existingUser->id]);

            return redirect()->route('login')
                ->with('success', 'Your account has been linked to your student record. You can now log in.');
        }

        // Create new account and link to student record
        // Enrolled users: all personal data pulled from the students table automatically
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

        $student->update(['user_id' => $user->id]);

        return redirect()->route('login')
            ->with('success', 'Account created! You can now log in.');
    }

    private function registerUnenrolled(Request $request)
    {
        $validated = $request->validate([
            'first_name'                       => ['required', 'string', 'max:255'],
            'last_name'                        => ['required', 'string', 'max:255'],
            'birthdate'                        => ['required', 'date'],
            'email_unenrolled'                 => ['required', 'email', 'unique:users,email'],
            'password_unenrolled'              => ['required', 'string', 'min:8'],
            'password_confirmation_unenrolled' => ['required', 'same:password_unenrolled'],

            // ── NOW REQUIRED so admission form can always auto-fill ──
            'contact_no' => ['required', 'string', 'max:20'],
            'gender_id'  => ['nullable', 'integer'],
            'address'    => ['required', 'string', 'max:255'],
        ]);

        User::create([
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

        return redirect()->route('login')
            ->with('success', 'Account created! You can now log in.');
    }

    private function studentRoleId(): int
    {
        return Role::where('name', 'student')->value('id') ?? 4;
    }
}