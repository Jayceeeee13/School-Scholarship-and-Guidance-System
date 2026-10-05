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
        $payload = cache()->get('exam-attempts-print:' . auth()->id(), []);

        // Older versions stored a plain list of IDs; newer ones store
        // ['ids' => [...], 'columns' => [...]]. Accept both.
        if (array_is_list($payload)) {
            $payload = ['ids' => $payload];
        }

        $ids = $payload['ids'] ?? [];

        // Columns the user left visible in the table's column toggle.
        // Falls back to every column if nothing was stored.
        $allColumns = [
            'user.name', 'user.email', 'exam.title', 'score', 'percentage',
            'status', 'scholarship_discount', 'violation_count', 'completed_at',
        ];
        $columns = $payload['columns'] ?? $allColumns;

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
            'columns'     => $columns,
            'printedAt'   => now(),
            'printedBy'   => auth()->user()->name,
            'passedCount' => $attempts->where('percentage', '>=', 75)->count(),
        ]);
    }
}