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

class ApplicationController extends Controller
{
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
            return redirect()->route('application_new.get')
                ->with('already_applied', true);
        }

        $request->validate([
            'type_of_application_id'  => 'required|exists:type_of_applications,id',
            'type_of_scholarship_id'  => 'required|exists:type_of_scholarships,id',
            'picture'                 => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'first_name'              => 'required|string|max:100',
            'middle_name'             => 'nullable|string|max:100',
            'last_name'               => 'required|string|max:100',
            'extension_name'          => 'nullable|string|max:20',
            'program_id'              => 'required|exists:programs,id',
            'year_level'              => 'required|string|max:20',
            'gender_id'               => 'required|exists:genders,id',
            'contact_no'              => 'nullable|string|max:16',
            'birthdate'               => 'nullable|date',
            'religion'                => 'nullable|string|max:100',
            'facebook_account'        => 'nullable|string|max:100',
            'fathers_name'            => 'required|string|max:100',
            'fathers_contact_no'      => 'nullable|string|max:16',
            'mothers_name'            => 'required|string|max:100',
            'mothers_contact_no'      => 'nullable|string|max:16',
            'guardian'                => 'nullable|string|max:100',
            'guardian_contact_no'     => 'nullable|string|max:16',
        ]);

        // ── Compute age server-side from birthdate ─────────────────────────
        $age = null;
        if ($request->filled('birthdate')) {
            $age = Carbon::parse($request->birthdate)->age;
        }

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('applications/pictures', 'public');
        }

        // ── Pull exam discount as benefit ──────────────────────────────────
        // Scenario: Student took the exam BEFORE submitting this application.
        // We read the discount directly from their latest completed attempt
        // and store it on the applicant record immediately.
        //
        // Scenario: Student takes the exam AFTER applying.
        // The ExamAttemptObserver::created() hook will fire and push the
        // discount into applicant.benefit automatically.
        $examAttempt = ExamAttempt::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'submitted'])
            ->whereNotNull('discount')
            ->latest('completed_at')
            ->first();

        $benefit = $examAttempt ? (int) $examAttempt->discount : null;

        Applicant::create([
            'user_id'                => Auth::id(),
            'picture'                => $picturePath,
            'type_of_application_id' => $request->type_of_application_id,
            'type_of_scholarship_id' => $request->type_of_scholarship_id,
            'first_name'             => $request->first_name,
            'middle_name'            => $request->middle_name,
            'last_name'              => $request->last_name,
            'extension_name'         => $request->extension_name,
            'program_id'             => $request->program_id,
            'year_level'             => $request->year_level,
            'gender_id'              => $request->gender_id,
            'contact_no'             => $request->contact_no,
            'birthdate'              => $request->birthdate,
            'age'                    => $age,
            'religion'               => $request->religion,
            'facebook_account'       => $request->facebook_account,
            'fathers_name'           => $request->fathers_name,
            'fathers_contact_no'     => $request->fathers_contact_no,
            'mothers_name'           => $request->mothers_name,
            'mothers_contact_no'     => $request->mothers_contact_no,
            'guardian'               => $request->guardian,
            'guardian_contact_no'    => $request->guardian_contact_no,
            'status'                 => 'pending',
            'benefit'                => $benefit, // ← auto-filled from entrance exam discount
        ]);

        return redirect()->route('application_new.get')
            ->with('success', 'Application submitted successfully.');
    }

    public function index()
    {
        $applicant      = Applicant::where('user_id', Auth::id())->first();
        $alreadyApplied = (bool) $applicant;
        return view('scholarship', compact('alreadyApplied', 'applicant'));
    }
}