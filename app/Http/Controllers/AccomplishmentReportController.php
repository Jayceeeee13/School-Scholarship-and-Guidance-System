<?php

namespace App\Http\Controllers;

use App\Models\AccomplishmentReport;
use App\Models\AccomplishmentReportActivity;
use App\Models\InstitutionalScholar;
use App\Models\Scholars;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccomplishmentReportController extends Controller
{
    /**
     * Resolve whichever scholar profile (Scholars or InstitutionalScholar)
     * belongs to the given portal user. Checks Scholars first, then
     * InstitutionalScholar, since a user should only ever match one.
     */
    protected function resolveScholar($user): Scholars|InstitutionalScholar|null
    {
        return Scholars::forUser($user) ?? InstitutionalScholar::forUser($user);
    }

    public function index()
    {
        $user    = Auth::user();
        $scholar = $this->resolveScholar($user);

        $isEligible = $scholar && $scholar->isEligibleForAccomplishmentReports();

        $activeTerm     = Term::where('is_active', true)->first();
        $existingReport = null;

        if ($isEligible && $activeTerm) {
            $existingReport = $scholar->accomplishmentReports()
                ->with('activities')
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
        $scholar = $this->resolveScholar($user);

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
            // updateOrCreate on a MorphMany relation automatically sets
            // scholar_type + scholar_id correctly for whichever model
            // $scholar actually is (Scholars or InstitutionalScholar).
            $report = $scholar->accomplishmentReports()->updateOrCreate(
                ['term_id' => $activeTerm->id],
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

    /**
     * Download an APPROVED accomplishment report as a PDF (official form FM-GUI-027).
     * Only the scholar who owns the report may download it.
     */
    public function download(AccomplishmentReport $report)
    {
        $scholar = $this->resolveScholar(Auth::user());

        abort_unless($scholar, 403);

        // The report is polymorphic (scholar_type + scholar_id), so check both.
        abort_unless(
            $report->scholar_type === $scholar->getMorphClass()
                && (int) $report->scholar_id === (int) $scholar->getKey(),
            403
        );

        abort_unless($report->status === 'approved', 403, 'Only approved reports can be downloaded.');

        $report->load(['term', 'activities']);

        $pdf = Pdf::loadView('pdf.accomplishment-report', [
            'report'  => $report,
            'scholar' => $scholar,
            'term'    => $report->term,
        ])->setPaper('a4', 'portrait');

        $filename = 'accomplishment-report-'
            . Str::slug($scholar->full_name) . '-'
            . Str::slug(($report->term?->semester ?? '') . ' ' . ($report->term?->school_year ?? ''))
            . '.pdf';

        return $pdf->download($filename);
    }
}