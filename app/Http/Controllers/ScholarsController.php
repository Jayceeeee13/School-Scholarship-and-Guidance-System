<?php

namespace App\Http\Controllers;

use App\Models\Scholars;
use App\Models\TypeOfScholarship;

class ScholarsController extends Controller
{
    public function printInstitutional()
    {
        $institutionalTypes = TypeOfScholarship::pluck('name')->toArray();

        $scholars = Scholars::whereIn('type_of_scholarship', $institutionalTypes)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('scholars.print-institutional', compact('scholars'));
    }
}