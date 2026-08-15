<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequirementSubmissionController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /apply/requirements
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $applicant = Applicant::with([
                'typeOfApplication',
                'typeOfScholarship',
                'program',
                'gender',
            ])
            ->where('user_id', Auth::id())
            ->first(); // returns null if they haven't applied yet

        $requirements = collect();
        $submitted    = [];

        if ($applicant) {
            $requirements = Requirement::where('type_of_application_id', $applicant->type_of_application_id)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();

            // ✅ AFTER — only counts rows where a file was actually submitted
$submitted = DB::table('applicant_requirement')
    ->where('applicant_id', $applicant->id)
    ->where('is_submitted', 1)
    ->whereNotNull('file_path')   // extra guard: must have an actual file
    ->pluck('requirement_id')
    ->toArray();
        }

        return view('requirements_submission', compact('applicant', 'requirements', 'submitted'));
    }

    // ─────────────────────────────────────────────────────────────
    // POST /apply/requirements
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Guard: applicant must exist
        $applicant = Applicant::where('user_id', Auth::id())->firstOrFail();

        // Fetch this applicant's required documents
        $requirements = Requirement::where('type_of_application_id', $applicant->type_of_application_id)
            ->where('is_active', 1)
            ->get();

        // ── Build validation rules ─────────────────────────────
        // All file fields are nullable — partial submission is allowed.
        // file_name is the only hard requirement.
        $rules = [
            'file_name' => 'required|string|max:100',
        ];

        foreach ($requirements as $req) {
            $rules['req_' . $req->id] = 'nullable|file|mimes:pdf|max:2048';
        }

        $request->validate($rules, [
            'file_name.required' => 'Please provide a file name prefix.',
            'file_name.max'      => 'File name prefix must not exceed 100 characters.',
        ]);

        // ── Store only the files that were actually uploaded ───
        $prefix  = Str::slug($request->file_name);
        $saved   = 0;

        foreach ($requirements as $req) {
            $fieldKey = 'req_' . $req->id;

            if ($request->hasFile($fieldKey) && $request->file($fieldKey)->isValid()) {
                $file = $request->file($fieldKey);
                $slug = Str::slug($req->name);
                $ext  = $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    'scholarship',
                    "{$prefix}_{$slug}.{$ext}",
                    'public'
                );

                // Upsert pivot row — insert on first upload, update on re-upload
                DB::table('applicant_requirement')->updateOrInsert(
                    [
                        'applicant_id'   => $applicant->id,
                        'requirement_id' => $req->id,
                    ],
                    [
                        'file_path'    => $path,
                        'is_submitted' => 1,
                        'updated_at'   => now(),
                    ]
                );

                $saved++;
            }
        }

        // Friendly feedback based on how many files were saved
        if ($saved === 0) {
            return back()->with('success', 'No new files were uploaded. Your previously submitted documents are unchanged.');
        }

        $total   = $requirements->count();
        // ✅ AFTER
$nowDone = DB::table('applicant_requirement')
    ->where('applicant_id', $applicant->id)
    ->where('is_submitted', 1)
    ->whereNotNull('file_path')
    ->count();

        if ($nowDone >= $total) {
            $message = "All {$total} requirements have been submitted successfully!";
        } else {
            $remaining = $total - $nowDone;
            $message   = "{$saved} file(s) saved successfully. You still have {$remaining} document(s) pending — you can submit them anytime.";
        }

        return back()->with('success', $message);
    }
}