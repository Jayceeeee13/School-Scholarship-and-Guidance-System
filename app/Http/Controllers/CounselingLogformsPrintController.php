<?php

namespace App\Http\Controllers;

use App\Models\CounselingLogforms;
use App\Models\Term;
use Illuminate\Http\Request;

class CounselingLogformsPrintController extends Controller
{
    /**
     * Print all logforms (or a subset by IDs).
     */
    public function print(Request $request)
    {
        $query = CounselingLogforms::query()->orderBy('created_at');

        if ($request->filled('ids')) {
            $ids = explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        }

        $logforms = $query->get();

        // Auto-fill the "___ Semester A.Y. ___" header line from the active Term.
        $activeTerm     = Term::active()->first();
        $semesterOnly = $activeTerm?->semester
    ? trim(str_ireplace('semester', '', $activeTerm->semester))
    : null;
        $schoolYearOnly = $activeTerm?->school_year;

        return view('print.counseling-logforms', compact(
            'logforms',
            'semesterOnly',
            'schoolYearOnly',
        ));
    }
}