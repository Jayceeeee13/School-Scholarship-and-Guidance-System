<?php

namespace App\Http\Controllers;

use App\Models\InstitutionalScholar;

class ScholarsController extends Controller
{
    public function printInstitutional()
    {
        // Print the exact same data source the "Institutional Scholars" tab
        // shows — the institutional_scholars table — not the scholars table.
        // institutional_scholars includes scholars mirrored from Scholars
        // AND scholars added directly (e.g. approved Applicant records),
        // so filtering scholars by type_of_scholarship here was missing
        // anyone who only ever existed in institutional_scholars.
        $scholars = InstitutionalScholar::where('status', '!=', 'revoked')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('scholars.print-institutional', compact('scholars'));
    }
}