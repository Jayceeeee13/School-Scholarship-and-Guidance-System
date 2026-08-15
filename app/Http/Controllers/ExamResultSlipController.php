<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ExamResultSlipController extends Controller
{
    public function print(ExamAttempt $examAttempt)
    {
        $record = $examAttempt->load([
            'answers.question.examCategory',
            'answers.question.choices',
            'answers.choice',
            'exam',
            'user',
        ]);

        $percentage = (float) $record->percentage;

        // Resolve the discount label directly from ExamAttempt — single source of truth.
        // $record->discount holds the integer code stored at submission time.
        $discountData = ExamAttempt::resolveDiscount((int) $record->discount);
        $discount     = $discountData['label']; // e.g. "75% Tuition Fee Discount"

        $categoryScores = $record->answers
            ->groupBy(fn ($a) => optional($a->question->examCategory)->name ?? 'General')
            ->map(function ($answers) {
                $correct = $answers->filter(fn ($a) => $a->choice && $a->choice->is_correct)->count();
                $total   = $answers->count();
                return [
                    'correct' => $correct,
                    'total'   => $total,
                    'pct'     => $total > 0 ? round(($correct / $total) * 100, 1) : 0,
                ];
            });

        return view('filament.pages.exam-result-slip', compact(
            'record',
            'percentage',
            'discount',
            'categoryScores',
        ));
    }
}