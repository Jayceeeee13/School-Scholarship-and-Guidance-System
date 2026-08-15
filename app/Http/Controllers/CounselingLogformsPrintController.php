<?php

namespace App\Http\Controllers;

use App\Models\CounselingLogforms;
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

        return view('print.counseling-logforms', compact('logforms'));
    }
}