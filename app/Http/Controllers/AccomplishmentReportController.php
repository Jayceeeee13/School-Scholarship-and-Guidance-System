<?php

namespace App\Http\Controllers;

use App\Models\AccomplishmentReport;
use App\Models\AccomplishmentReportActivity;
use App\Models\Scholars;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccomplishmentReportController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $scholar = Scholars::forUser($user);

        $isEligible = $scholar && $scholar->isEligibleForAccomplishmentReports();

        $activeTerm     = Term::where('is_active', true)->first();
        $existingReport = null;

        if ($isEligible && $activeTerm) {
            $existingReport = AccomplishmentReport::with('activities')
                ->where('scholar_id', $scholar->id)
                ->where('term_id', $activeTerm->id)
                ->first();
        }

        $pastReports = $isEligible
            ? $scholar->accomplishmentReports()->with(['term', 'activities'])->latest()->get()
            : collect();

        return view('scholars.accomplishment-reports', [
            'scholar'        => $scholar,
            'isEligible'     => $isEligible,
            'activeTerm'     => $activeTerm,
            'existingReport' => $existingReport,
            'pastReports'    => $pastReports,
        ]);
    }

    public function store(Request $request)
    {
        $user    = Auth::user();
        $scholar = Scholars::forUser($user);

        abort_unless($scholar && $scholar->isEligibleForAccomplishmentReports(), 403,
            'You are not eligible to submit accomplishment reports.');

        $activeTerm = Term::where('is_active', true)->first();
        abort_unless($activeTerm, 422, 'No active term is currently set. Please contact the scholarship office.');

        $data = $request->validate([
            'activities'                  => 'required|array|min:1',
            'activities.*.activity_date'  => 'nullable|date',
            'activities.*.venue'          => 'nullable|string|max:255',
            'activities.*.activity'       => 'nullable|string|max:1000',
        ]);

        // Drop fully-empty rows (in case the student left blank rows from the dynamic table)
        $rows = collect($data['activities'])->filter(function ($row) {
            return !empty($row['activity_date']) || !empty($row['venue']) || !empty($row['activity']);
        })->values();

        abort_if($rows->isEmpty(), 422, 'Please fill in at least one activity row.');

        DB::transaction(function () use ($scholar, $activeTerm, $rows) {
            $report = AccomplishmentReport::updateOrCreate(
                ['scholar_id' => $scholar->id, 'term_id' => $activeTerm->id],
                ['status' => 'pending', 'submitted_at' => now(), 'remarks' => null]
            );

            // Replace activities fresh each submission for this term
            $report->activities()->delete();

            foreach ($rows as $i => $row) {
                AccomplishmentReportActivity::create([
                    'accomplishment_report_id' => $report->id,
                    'seq'                       => $i + 1,
                    'activity_date'             => $row['activity_date'] ?? null,
                    'venue'                     => $row['venue'] ?? null,
                    'activity'                  => $row['activity'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('accomplishment_reports.get')
            ->with('success', 'Accomplishment report submitted successfully.');
    }
}