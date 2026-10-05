<?php

namespace App\Http\Controllers;

use App\Filament\Resources\ExamAttemptResource;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ExamAttemptListPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless(auth()->user()?->hasAnyRole(['admin', 'scholarship']), 403);

        // IDs were stored by the "Print List" header action on the Examinees tab
        // and already reflect the search, filters and sort the user had applied.
        $ids = cache()->get('exam-attempts-print:' . auth()->id(), []);

        $attempts = ExamAttempt::query()
            ->with(['user', 'exam'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($attempt) => array_search($attempt->id, $ids))
            ->values()
            ->map(function ($attempt) {
                $discount = ExamAttemptResource::resolveDiscount((float) $attempt->percentage);

                $attempt->print_scholarship = $discount['short'] ?? '—';
                $attempt->print_violations  = (int) $attempt->getRawOriginal('violation_count');

                return $attempt;
            });

        return view('print.exam-attempts-list', [
            'attempts'    => $attempts,
            'printedAt'   => now(),
            'printedBy'   => auth()->user()->name,
            'passedCount' => $attempts->where('percentage', '>=', 75)->count(),
        ]);
    }
}