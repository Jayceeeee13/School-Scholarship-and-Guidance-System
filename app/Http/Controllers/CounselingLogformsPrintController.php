<?php

namespace App\Http\Controllers;

use App\Models\CounselingLogforms;
use App\Models\Term;
use Illuminate\Http\Request;

class CounselingLogformsPrintController extends Controller
{
    /**
     * Print all logforms (or a subset by IDs), respecting the
     * filters currently applied on the Logforms table.
     */
    public function print(Request $request)
    {
        $query = CounselingLogforms::query()
            ->with(['appointment', 'walkInStudent.program', 'referral'])
            ->whereNull('archived_at') // match the table, which hides archived logforms
            ->orderBy('created_at');

        if ($request->filled('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        }

        // Filters passed from the table
        $query
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('until'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('until')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->input('term_id')));

        $logforms = $query->get();

        // Auto-fill the "___ Semester A.Y. ___" header line.
        // If a term filter is applied, use that term; otherwise use the active term.
        $term = $request->filled('term_id')
            ? Term::find($request->input('term_id'))
            : Term::active()->first();

        $semesterOnly = $term?->semester
            ? trim(str_ireplace('semester', '', $term->semester))
            : null;
        $schoolYearOnly = $term?->school_year;

        // Date range, so the printout can show the period it covers
        $from  = $request->input('from');
        $until = $request->input('until');

        return view('print.counseling-logforms', compact(
            'logforms',
            'semesterOnly',
            'schoolYearOnly',
            'from',
            'until',
        ));
    }
}